/* ============================================================
   REMARKA — MODULES ORCHESTRATOR v2.0
   Все 28 модулей зарегистрированы.
   Порядок intent-проверок — от специфичных к общим.
   ============================================================ */
(function () {
  'use strict';

  function ready(fn) {
    if (document.readyState !== 'loading') fn();
    else document.addEventListener('DOMContentLoaded', fn);
  }

  ready(function () {
    if (typeof ChatEngine === 'undefined') {
      console.warn('[Remarka v2] ChatEngine not found'); return;
    }

    /* ── 1. ПЕРЕХВАТ handleUserInput ── */
    const _orig = ChatEngine.handleUserInput.bind(ChatEngine);
    ChatEngine.handleUserInput = function (text) {
      if (!text?.trim()) return;
      if (_route(text.trim())) return;
      _orig(text.trim());
    };

    /* ── 2. ПЕРЕХВАТ handleFileUpload ── */
    if (typeof DocumentChecker !== 'undefined') {
      const _origUp = ChatEngine.handleFileUpload?.bind(ChatEngine);
      ChatEngine.handleFileUpload = async (file) => {
        try { await DocumentChecker.checkFile(file); }
        catch { if (_origUp) _origUp(file); }
      };
    }

    /* ── 3. АВТО-ИНИЦИАЛИЗАЦИЯ ── */
    if (typeof Reengagement       !== 'undefined') Reengagement.check();
    if (typeof VoiceSynthesis     !== 'undefined') VoiceSynthesis.init();
    if (typeof LiveQuote          !== 'undefined') LiveQuote.init();
    if (typeof ProactiveDiscount  !== 'undefined') ProactiveDiscount.init();

    // Синхронизация лояльности (если авторизован)
    if (typeof LoyaltyProgram !== 'undefined' &&
        typeof RemarkaConfig  !== 'undefined' && RemarkaConfig.isLoggedIn) {
      _syncLoyalty();
    }

    /* ── 4. ФАЙЛОВЫЙ INPUT — предупреждение об OCR ── */
    document.addEventListener('change', (e) => {
      if (e.target?.id !== 'remarka-file-inp' || !e.target.files?.[0]) return;
      const ext = e.target.files[0].name.split('.').pop().toLowerCase();
      if (['jpg','jpeg','png','gif','bmp','tiff'].includes(ext)) {
        setTimeout(() => _bot(
          `ℹ️ Загружаете изображение (${ext.toUpperCase()}). Текст на нём распознаётся через OCR — это +15–25% к стоимости. Продолжить?`,
          ['✅ Да, загрузить', '📄 Лучше загружу DOCX', '❓ Что такое OCR?']
        ), 500);
      }
      if (typeof FormatConverter !== 'undefined') {
        FormatConverter.analyzeFile(e.target.files[0].name);
      }
    });

    /* ── 5. LOYALTY: начисление после заказа ── */
    if (typeof LoyaltyProgram !== 'undefined' && typeof window.remarkaWpSaveOrder === 'function') {
      const _origSave = window.remarkaWpSaveOrder;
      window.remarkaWpSaveOrder = async (params) => {
        await _origSave(params);
        const total = params.result?.total || 0;
        if (total > 0) {
          const tier = LoyaltyProgram.addOrder(total);
          if (tier.discount > 0) {
            setTimeout(() => _bot(
              `🎁 Ваша накопленная скидка: -${tier.discount}% (${tier.emoji} ${tier.name})\nАвтоматически применяется к следующему заказу.`,
              ['🎁 Мои привилегии', '🔗 Пригласить друга', '🔤 Новый заказ']
            ), 3000);
          }
          // Триггер проактивной скидки при первом заказе
          if (typeof FeedbackCollector !== 'undefined') {
            FeedbackCollector.scheduleAuto(params.session_id || 'order', 3);
          }
        }
      };
    }

    console.log('[Remarka Orchestrator v2] ✓ Все модули подключены');
  });

  /* ══════════════════════════════════════════════════════
     INTENT ROUTER — порядок важен!
  ══════════════════════════════════════════════════════ */
  function _route(text) {

    // ── Служебные команды ──
    if (/^\/(start|help|reset|debug)$/i.test(text)) {
      _handleCommand(text.toLowerCase()); return true;
    }

    // 1. Анкета переводчика — перехватываем ДО appendUserMessage
    if (typeof TranslatorSurvey !== 'undefined' &&
        TranslatorSurvey.checkTranslatorIntent(text)) {
      // appendUserMessage вызывается внутри TranslatorSurvey.startSurvey()
      return true;
    }

    // 2. NDA
    if (typeof NDAFlow !== 'undefined' && NDAFlow.checkIntent(text)) {
      NDAFlow.start(); return true;
    }

    // 3. Нотариальный перевод / апостиль
    if (typeof CertifiedFlow !== 'undefined' && CertifiedFlow.checkIntent(text)) {
      CertifiedFlow.start(); return true;
    }

    // 4. Статус заказа
    if (typeof OrderTracker !== 'undefined' && OrderTracker.checkIntent(text)) {
      OrderTracker.start(); return true;
    }

    // 5. Качество перевода
    if (typeof QualityChecker !== 'undefined' && QualityChecker.checkIntent(text)) {
      QualityChecker.start(); return true;
    }

    // 6. Сложность текста
    if (typeof ComplexityMeter !== 'undefined' && ComplexityMeter.checkIntent(text)) {
      ComplexityMeter.start(); return true;
    }

    // 7. B2B
    if (typeof B2BFlow !== 'undefined' && B2BFlow.checkIntent(text)) {
      B2BFlow.start(); return true;
    }

    // 8. Выход на рынок
    if (typeof MarketEntry !== 'undefined' && MarketEntry.checkIntent(text)) {
      MarketEntry.start(); return true;
    }

    // 9. Партнёрство / реферал
    if (typeof PartnerFlow !== 'undefined' && PartnerFlow.checkIntent(text)) {
      PartnerFlow.start(); return true;
    }

    // 10. Лояльность / скидки
    if (typeof LoyaltyProgram !== 'undefined' && LoyaltyProgram.checkIntent(text)) {
      LoyaltyProgram.showStatus(); return true;
    }

    // 11. Отзыв
    if (typeof FeedbackCollector !== 'undefined' && FeedbackCollector.checkIntent(text)) {
      FeedbackCollector.start(); return true;
    }

    // 12. Форматы файлов
    if (typeof FormatConverter !== 'undefined' && FormatConverter.checkIntent(text)) {
      FormatConverter.showSupportedFormats(); return true;
    }

    // 13. Сравнение тарифов
    if (typeof ComparisonTable !== 'undefined' && ComparisonTable.checkIntent(text)) {
      ComparisonTable.show(); return true;
    }

    // 14. Личный кабинет
    if (typeof ClientPortal !== 'undefined' && ClientPortal.checkIntent(text)) {
      ClientPortal.show(); return true;
    }

    // 15. Реферальная программа
    if (typeof ReferralGenerator !== 'undefined' && ReferralGenerator.checkIntent(text)) {
      ReferralGenerator.show(); return true;
    }

    // 16. Экспорт переписки
    if (typeof ChatExport !== 'undefined' && ChatExport.checkIntent(text)) {
      ChatExport.export(); return true;
    }

    // 17. Дедлайн / выбор даты
    if (typeof DeadlineCalendar !== 'undefined' && DeadlineCalendar.checkIntent(text)) {
      DeadlineCalendar.show(); return true;
    }

    // 18. Голосовые ответы (переключение)
    if (/\b(включ.*голос|выключ.*голос|говор.*голос|voice.*on|voice.*off|тихий режим)\b/i.test(text)) {
      if (typeof VoiceSynthesis !== 'undefined') VoiceSynthesis.toggle();
      return true;
    }

    // 19. OCR вопрос
    if (/\bocr|оцр|распознавание текста\b/i.test(text)) {
      _bot(
        'OCR (Optical Character Recognition) — технология распознавания текста с изображений и сканов.\n\n' +
        'Если вы прислали скан или фото документа, нам нужно сначала извлечь текст, прежде чем переводить.\n\n' +
        '📊 Стоимость OCR: +15–25% к базовой цене перевода.\n' +
        '📌 Рекомендуем присылать документы в формате DOCX или текстовый PDF — без наценки.',
        ['📎 Загрузить файл', '💰 Рассчитать стоимость', '📂 Какие форматы принимаете?']
      );
      return true;
    }

    // 20. Промокод
    if (/промокод|promo.?code|купон|coupon/i.test(text)) {
      const match = text.match(/\b([A-Z]{2,12}\d{0,6})\b/);
      if (match) {
        if (typeof ProactiveDiscount !== 'undefined') {
          ProactiveDiscount.applyPromo(match[1], 10);
        } else {
          _bot(`Промокод ${match[1]} будет применён при оформлении заказа ✅`, ['🔤 Оформить заказ']);
        }
      } else {
        _bot('Введите промокод (например: TODAY10) или перейдите к оформлению заказа.', []);
      }
      return true;
    }

    return false;
  }

  /* ── КОМАНДЫ ── */
  function _handleCommand(cmd) {
    if (cmd === '/reset') {
      if (typeof StateMachine !== 'undefined') StateMachine.reset();
      if (typeof ChatEngine   !== 'undefined') ChatEngine.newOrder();
      return;
    }
    if (cmd === '/help') {
      _botRich('Доступные команды и возможности', `
        <div style="display:flex;flex-direction:column;gap:5px;font-size:12.5px">
          ${[
            ['🔤', 'Нужен перевод', 'Запустить воронку заказа'],
            ['💰', 'Стоимость / цена', 'Рассчитать стоимость перевода'],
            ['📊', 'Сравнить тарифы', 'Таблица сравнения MTPE / Проф / Premium'],
            ['✅', 'Проверить качество', 'Оценить готовый перевод'],
            ['📊', 'Сложность текста', 'Анализ сложности исходника'],
            ['📄', 'Загрузить файл', 'Анализ документа + мгновенная цена'],
            ['📅', 'Выбрать дату', 'Календарь сроков'],
            ['📜', 'Нотариальный перевод', 'Заверение и апостиль'],
            ['🏢', 'Корпоративный заказ', 'B2B воронка с договором'],
            ['🌍', 'Выход на рынок', 'Комплексная локализация'],
            ['🤝', 'Партнёрство', 'Субподряд, реферал, white-label'],
            ['⭐', 'Оставить отзыв', 'Оценить качество работы'],
            ['🎁', 'Моя скидка', 'Программа лояльности'],
            ['🔗', 'Пригласить друга', 'Реферальный промокод'],
            ['👤', 'Личный кабинет', 'Заказы, история, профиль'],
            ['📤', 'Экспортировать чат', 'Сохранить переписку'],
          ].map(([icon, cmd, desc]) =>
            `<div style="display:flex;gap:8px;padding:5px 0;border-bottom:1px solid rgba(82,108,255,0.07)">
              <span>${icon}</span>
              <div>
                <span style="color:#a5b4fc;font-weight:600;cursor:pointer"
                  onclick="ChatEngine && ChatEngine.handleUserInput('${cmd}')">${cmd}</span>
                <span style="color:rgba(140,155,210,0.6)"> — ${desc}</span>
              </div>
            </div>`
          ).join('')}
        </div>`, ['🔤 Нужен перевод', '💰 Рассчитать стоимость']);
      return;
    }
    if (cmd === '/debug') {
      const modules = [
        'TranslatorSurvey','NDAFlow','CertifiedFlow','OrderTracker',
        'QualityChecker','ComplexityMeter','DocumentChecker',
        'B2BFlow','MarketEntry','PartnerFlow','LoyaltyProgram',
        'FeedbackCollector','FormatConverter','ComparisonTable',
        'ClientPortal','ReferralGenerator','ChatExport',
        'DeadlineCalendar','VoiceSynthesis','LiveQuote','ProactiveDiscount',
      ].map(m => `${typeof window[m] !== 'undefined' ? '✅' : '❌'} ${m}`).join('\n');
      _bot('Debug — загруженные модули:\n\n' + modules, []);
    }
  }

  /* ── HELPERS ── */
  function _bot(text, replies) {
    if (typeof window._shared_appendBot === 'function') {
      window._shared_appendBot(text, replies); return;
    }
    const msgs = document.getElementById('messages');
    if (!msgs) return;
    const d = document.createElement('div'); d.className = 'msg msg--bot';
    const t = new Date().toLocaleTimeString('ru-RU',{hour:'2-digit',minute:'2-digit'});
    d.innerHTML = `<div class="bubble bubble--bot">${text.replace(/\n/g,'<br>')}</div><div class="msg-time">${t}</div>`;
    msgs.appendChild(d);
    const qr = document.getElementById('quick-replies');
    if (qr) { qr.innerHTML=''; (replies||[]).forEach(r=>{const b=document.createElement('button');b.className='qr-btn';b.textContent=r;b.onclick=()=>ChatEngine?.handleUserInput(r);qr.appendChild(b);}); }
    msgs.scrollTop = msgs.scrollHeight;
  }

  function _botRich(title, html, replies) {
    const msgs = document.getElementById('messages');
    if (!msgs) return;
    const d = document.createElement('div'); d.className = 'msg msg--bot';
    const t = new Date().toLocaleTimeString('ru-RU',{hour:'2-digit',minute:'2-digit'});
    d.innerHTML = `<div class="bubble bubble--bot bubble--rich"><div class="bubble-lead">${title}</div>${html}</div><div class="msg-time">${t}</div>`;
    msgs.appendChild(d);
    const qr = document.getElementById('quick-replies');
    if (qr) { qr.innerHTML=''; (replies||[]).forEach(r=>{const b=document.createElement('button');b.className='qr-btn';b.textContent=r;b.onclick=()=>ChatEngine?.handleUserInput(r);qr.appendChild(b);}); }
    msgs.scrollTop = msgs.scrollHeight;
  }

  async function _syncLoyalty() {
    if (typeof LoyaltyProgram === 'undefined' || typeof RemarkaConfig === 'undefined') return;
    try {
      const body = new URLSearchParams({
        action: 'remarka_sync_loyalty', nonce: RemarkaConfig.nonce,
        loyalty: JSON.stringify(LoyaltyProgram.load()),
      });
      const r = await fetch(RemarkaConfig.ajaxUrl, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: body.toString() });
      const d = await r.json();
      if (d.success && d.data) localStorage.setItem('remarka_loyalty', JSON.stringify({ ...LoyaltyProgram.load(), ...d.data }));
    } catch {}
  }

  // Экспортируем shared helpers для всех модулей
  window._shared_appendBot = _bot;
  window._shared_appendBotRich = _botRich;

})();
