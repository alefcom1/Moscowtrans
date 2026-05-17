/* ============================================================
   REMARKA CHAT — WordPress Adapter
   Подключается ПОСЛЕ chat.js и переопределяет:
   • ClaudeAPI.send()  → wp_ajax remarka_chat
   • ChatEngine.init() → читает RemarkaConfig из wp_localize_script
   • Сессии           → синхронизируются в БД через wp_ajax
   • Geo              → через wp_ajax remarka_geo (server-side)
   • Заказы           → wp_ajax remarka_save_order (CPT + log)
   ============================================================ */

(function () {
  'use strict';

  /* ── Ждём загрузки всех модулей ── */
  function ready(fn) {
    if (document.readyState !== 'loading') fn();
    else document.addEventListener('DOMContentLoaded', fn);
  }

  ready(function () {
    if (typeof RemarkaConfig === 'undefined') {
      console.warn('[Remarka] RemarkaConfig not found. Is wp_localize_script connected?');
      return;
    }

    const cfg = RemarkaConfig;

    /* ══════════════════════════════════════════════════════
       1. OVERRIDE: ClaudeAPI.send → wp-ajax proxy
    ══════════════════════════════════════════════════════ */
    if (typeof OpenAIAPI !== 'undefined') {
      const _origSend = OpenAIAPI.send.bind(OpenAIAPI);

      OpenAIAPI.send = async function (userText, slots, intent, uiLang, conversationHistory, pageContextKey) {
        const system     = OpenAIAPI.buildSystemPrompt(slots, intent, uiLang, pageContextKey);
        const dialogText = buildDialogText(userText, conversationHistory);

        const body = new URLSearchParams({
          action: 'remarka_chat',
          nonce:  cfg.nonce,
          text:   dialogText,
          system: system,
        });

        const response = await fetch(cfg.ajaxUrl, {
          method:  'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body:    body.toString(),
        });

        if (!response.ok) throw new Error('WP Ajax error: ' + response.status);

        const data = await response.json();

        // wp_send_json_error
        if (data && data.success === false) {
          throw new Error(data.data?.message || 'Ajax error');
        }

        // gpt.php пробрасывает ответ напрямую через class-ajax.php
        const payload = data.data || data;
        const text = extractText(payload);
        if (!text) throw new Error('Empty response');
        return text;
      };

      // Обновляем алиас
      window.ClaudeAPI = OpenAIAPI;
    }

    /* ══════════════════════════════════════════════════════
       2. OVERRIDE: Geo detection → server-side
    ══════════════════════════════════════════════════════ */
    if (typeof ChatEngine !== 'undefined') {

      // Переопределяем detectGeo внутри ChatEngine через monkey-patch init
      const _origInit = ChatEngine.init.bind(ChatEngine);

      ChatEngine.init = async function () {
        // a) Применяем тарифы из WP
        if (typeof PricingEngine !== 'undefined' && cfg.tariffs) {
          Object.entries(cfg.tariffs).forEach(([key, t]) => {
            if (PricingEngine.BASE_RATES && t.price) {
              PricingEngine.BASE_RATES[key] = parseInt(t.price, 10);
            }
          });
        }

        // b) Применяем задержку proactive из настроек WP
        if (typeof PageContext !== 'undefined' && cfg.proactiveDelay) {
          Object.keys(PageContext.CONTEXTS).forEach(k => {
            if (PageContext.CONTEXTS[k].proactive) {
              PageContext.CONTEXTS[k].proactive.delay = parseInt(cfg.proactiveDelay, 10);
            }
          });
        }

        // c) Получить гео через WP (server-side, кешируется transient)
        try {
          const geoBody = new URLSearchParams({ action: 'remarka_geo', nonce: cfg.nonce });
          const geoResp = await fetch(cfg.ajaxUrl, {
            method:  'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body:    geoBody.toString(),
          });
          if (geoResp.ok) {
            const geoData = await geoResp.json();
            if (geoData.success && geoData.data) {
              // Инжектим гео в ChatEngine через window
              window._remarkaGeo = geoData.data;
            }
          }
        } catch (_) { /* silent */ }

        // d) Загрузить/восстановить сессию из БД
        await loadSession();

        // e) Запустить оригинальный init (greeting, speech, etc.)
        return _origInit();
      };
    }

    /* ══════════════════════════════════════════════════════
       3. SESSION SYNC → wp_ajax remarka_save_session
       Сохраняем при каждом обновлении StateMachine
    ══════════════════════════════════════════════════════ */
    let _syncTimer = null;

    function scheduleSessionSync() {
      clearTimeout(_syncTimer);
      _syncTimer = setTimeout(syncSession, 2000);
    }

    async function syncSession() {
      if (typeof StateMachine === 'undefined') return;

      const profile = StateMachine.getProfile();
      const geo     = window._remarkaGeo || {};

      const body = new URLSearchParams({
        action:       'remarka_save_session',
        nonce:        cfg.nonce,
        session_id:   profile.id || '',
        slots:        JSON.stringify(profile.slots     || {}),
        intent:       profile.intent                  || '',
        messages:     JSON.stringify((profile.history || []).slice(-20)),
        page_context: profile.pageContext              || 'general',
        geo_city:     geo.city                        || '',
        geo_country:  geo.country                     || '',
      });

      try {
        await fetch(cfg.ajaxUrl, {
          method:  'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body:    body.toString(),
        });
      } catch (_) { /* silent */ }
    }

    async function loadSession() {
      if (typeof StateMachine === 'undefined') return;

      const profile    = StateMachine.getProfile();
      const session_id = profile.id;
      if (!session_id) return;

      const body = new URLSearchParams({
        action:     'remarka_load_session',
        nonce:      cfg.nonce,
        session_id: session_id,
      });

      try {
        const resp = await fetch(cfg.ajaxUrl, {
          method:  'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body:    body.toString(),
        });
        const data = await resp.json();

        if (data.success && data.data) {
          const saved = data.data;
          // Восстановить слоты и intent
          if (saved.slots && Object.keys(saved.slots).length > 0) {
            StateMachine.updateSlots(saved.slots);
          }
          if (saved.intent) StateMachine.setIntent(saved.intent);
          // Гео
          if (saved.geo_city) {
            window._remarkaGeo = { city: saved.geo_city, country: saved.geo_country };
          }
        }
      } catch (_) { /* silent */ }
    }

    // Патчим StateMachine.save для триггера sync
    if (typeof StateMachine !== 'undefined') {
      const _origSave = StateMachine.save.bind(StateMachine);
      StateMachine.save = function () {
        _origSave();
        scheduleSessionSync();
      };
    }

    /* ══════════════════════════════════════════════════════
       4. OVERRIDE: ORDER SAVE → wp_ajax + CPT
    ══════════════════════════════════════════════════════ */
    if (typeof ChatEngine !== 'undefined') {
      // Переопределяем sendOrderEmail и sendOrderPhone
      // ChatEngine — IIFE, поэтому патчим через startOrder
      const _origStartOrder = ChatEngine.startOrder.bind(ChatEngine);

      ChatEngine.startOrder = function (channel) {
        // Сохраняем channel для обработчика контактов
        window._remarkaOrderChannel = channel;
        _origStartOrder(channel);
      };
    }

    // Глобальная функция сохранения заказа — вызывается из chat.js sendOrderEmail/Phone
    window.remarkaWpSaveOrder = async function ({ email, phone, tariff, slots, result }) {
      const contact      = email || phone || '';
      const contact_type = email ? 'email' : 'phone';
      const profile      = typeof StateMachine !== 'undefined' ? StateMachine.getProfile() : {};

      const body = new URLSearchParams({
        action:       'remarka_save_order',
        nonce:        cfg.nonce,
        session_id:   profile.id   || '',
        tariff:       tariff       || '',
        contact:      contact,
        contact_type: contact_type,
        slots:        JSON.stringify(slots || {}),
        total:        result ? result.total : 0,
      });

      try {
        const resp = await fetch(cfg.ajaxUrl, {
          method:  'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body:    body.toString(),
        });
        const data = await resp.json();
        if (data.success) {
          console.log('[Remarka] Order saved. Post ID:', data.data?.post_id);
        }
      } catch (e) {
        console.warn('[Remarka] Order save failed:', e);
      }
    };

    /* ══════════════════════════════════════════════════════
       5. SIDEBAR — навигация
    ══════════════════════════════════════════════════════ */
    document.querySelectorAll('.js-sb-item').forEach(function (btn) {
      btn.addEventListener('click', function () {
        document.querySelectorAll('.js-sb-item').forEach(function (b) {
          b.classList.remove('remarka-sb-item--active');
          b.setAttribute('aria-pressed', 'false');
        });
        btn.classList.add('remarka-sb-item--active');
        btn.setAttribute('aria-pressed', 'true');

        const section = btn.dataset.section;

        // Спец. действия для некоторых секций
        if (section === 'upload') {
          document.getElementById('remarka-file-inp')?.click();
        } else if (section === 'calc') {
          if (typeof ChatEngine !== 'undefined') {
            ChatEngine.handleUserInput('💰 Рассчитать стоимость');
          }
        } else if (section === 'history') {
          if (typeof ChatEngine !== 'undefined') {
            ChatEngine.handleUserInput('История моих заказов');
          }
        } else if (section === 'quality') {
          if (typeof ChatEngine !== 'undefined') {
            ChatEngine.handleUserInput('Проверить качество перевода');
          }
        } else if (section === 'complexity') {
          if (typeof ChatEngine !== 'undefined') {
            ChatEngine.handleUserInput('Определить сложность текста');
          }
        } else if (section === 'blog') {
          window.open(cfg.siteUrl + '/blog', '_blank');
        }
      });
    });

    /* ══════════════════════════════════════════════════════
       6. LANG SWITCHER
    ══════════════════════════════════════════════════════ */
    document.querySelectorAll('.remarka-lang-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        document.querySelectorAll('.remarka-lang-btn').forEach(function (b) {
          b.classList.remove('remarka-lang-btn--active');
        });
        btn.classList.add('remarka-lang-btn--active');
        if (typeof ChatEngine !== 'undefined') {
          ChatEngine.setLang(btn.dataset.lang);
        }
      });
    });

    /* ══════════════════════════════════════════════════════
       HELPERS (дублируем из ai.js для изоляции)
    ══════════════════════════════════════════════════════ */
    function buildDialogText(userText, conversationHistory) {
      const last12 = (conversationHistory || []).slice(-12);
      if (last12.length === 0) return userText;
      const lines = last12.map(function (h) {
        return '[' + (h.role === 'bot' ? 'Ольга' : 'Клиент') + ']: ' + h.content;
      });
      const last = last12[last12.length - 1];
      if (!last || last.content !== userText || last.role !== 'user') {
        lines.push('[Клиент]: ' + userText);
      }
      return lines.join('\n');
    }

    function extractText(data) {
      if (data && data.output) {
        return data.output
          .filter(function (b) { return b.type === 'message'; })
          .flatMap(function (b) { return b.content || []; })
          .filter(function (c) { return c.type === 'output_text'; })
          .map(function (c) { return c.text; })
          .join('') || null;
      }
      if (data && data.choices) {
        return (data.choices[0] && data.choices[0].message && data.choices[0].message.content) || null;
      }
      if (typeof data === 'string') return data;
      return null;
    }

  }); // ready

})();
