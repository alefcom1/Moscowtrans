/* ============================================================
   REMARKA MODULE 4: proactive-discount.js v1.0
   Умные триггеры скидок:
   • Молчание >30 сек после показа цены → скидка
   • Exit-intent (мышь уходит вверх из окна)
   • Повторный визит без заказа → промокод
   • Ограниченное время → «Только сегодня»
   ============================================================ */
const ProactiveDiscount = (() => {
  'use strict';

  const PROMOS = [
    { code: 'TODAY10',   pct: 10, label: 'только сегодня',      expires: 'сегодня до 23:59' },
    { code: 'WELCOME7',  pct: 7,  label: 'для новых клиентов',  expires: '48 часов' },
    { code: 'BACK5',     pct: 5,  label: 'рады снова видеть вас', expires: '7 дней' },
    { code: 'QUICK15',   pct: 15, label: 'при заказе за 10 мин', expires: '10 минут' },
  ];

  let _silenceTimer    = null;
  let _exitBound       = false;
  let _shown           = {};   // какие триггеры уже сработали
  let _priceShownAt    = 0;
  let _sessionStart    = Date.now();
  let _lastTriggerAt   = 0;

  const COOLDOWN = 3 * 60 * 1000; // мин. 3 мин между триггерами

  // ── ИНИЦИАЛИЗАЦИЯ ────────────────────────────────────────
  function init() {
    // Ждём загрузки чата
    setTimeout(() => {
      _watchPriceShow();
      _initExitIntent();
      _checkReturning();
    }, 2000);
  }

  // ── ТРИГГЕР 1: Молчание после показа цены ────────────────
  function _watchPriceShow() {
    const obs = new MutationObserver(() => {
      const msgs = document.getElementById('messages');
      if (!msgs) return;
      const last = msgs.lastElementChild;
      if (!last) return;
      const text = last.textContent || '';
      // Видим блок с ценой
      if ((text.includes('₽') && text.includes('стр')) ||
          last.querySelector('.price-card') ||
          last.querySelector('.pc-price')) {
        _priceShownAt = Date.now();
        clearTimeout(_silenceTimer);
        _silenceTimer = setTimeout(_triggerSilence, 32000); // 32 сек молчания
      }
    });
    const msgs = document.getElementById('messages');
    if (msgs) obs.observe(msgs, { childList: true, subtree: false });

    // Сбрасываем таймер при любом вводе
    document.getElementById('chat-input')?.addEventListener('input', () => {
      clearTimeout(_silenceTimer);
    });
  }

  function _triggerSilence() {
    if (_shown.silence || _notReady()) return;
    _shown.silence = true;
    _lastTriggerAt = Date.now();
    const promo = _pickPromo(0); // TODAY10
    _showOffer(
      '⏰ Подождите секунду!',
      `Вижу, вы изучаете стоимость. Хочу предложить вам персональную скидку:\n\n` +
      `Используйте промокод при заказе <b style="color:#06c0c8">сегодня</b> и получите`,
      promo,
      '🎁 Применить скидку',
      '❌ Не нужно'
    );
  }

  // ── ТРИГГЕР 2: Exit-intent ───────────────────────────────
  function _initExitIntent() {
    if (_exitBound) return;
    _exitBound = true;
    document.addEventListener('mouseleave', (e) => {
      if (e.clientY > 5) return; // только уход вверх
      if (_shown.exit || _notReady()) return;
      _shown.exit = true;
      _lastTriggerAt = Date.now();
      const promo = _pickPromo(1); // WELCOME7
      _showOffer(
        '🚪 Подождите, не уходите!',
        `Прежде чем закрыть — специальное предложение только для вас:`,
        promo,
        '🎁 Хочу скидку!',
        'Всё равно уйти'
      );
    });
  }

  // ── ТРИГГЕР 3: Повторный визит ───────────────────────────
  function _checkReturning() {
    const key     = 'remarka_visits';
    const visits  = parseInt(localStorage.getItem(key) || '0') + 1;
    localStorage.setItem(key, visits);

    const lastOrder = localStorage.getItem('remarka_last_order_date');
    const hasOrdered = !!lastOrder;

    if (visits >= 3 && !hasOrdered && !_shown.returning) {
      setTimeout(() => {
        if (_notReady()) return;
        _shown.returning = true;
        const promo = _pickPromo(2); // BACK5
        _showOffer(
          '👋 Рады снова вас видеть!',
          `Это ваш ${visits}-й визит. Специально для вас — персональный промокод:`,
          promo,
          '🎁 Использовать скидку',
          'Пропустить'
        );
      }, 15000);
    }
  }

  // ── ТРИГГЕР 4: Быстрый заказ (10 минут) ─────────────────
  function triggerQuick() {
    if (_shown.quick || _notReady()) return;
    _shown.quick = true;
    const promo = _pickPromo(3); // QUICK15
    _showOffer(
      '⚡ Специальное предложение!',
      `Оформите заказ в течение следующих 10 минут и получите скидку:`,
      promo,
      '⚡ Заказать со скидкой 15%',
      'Отказаться'
    );
    // Таймер обратного отсчёта
    _startCountdown(10 * 60);
  }

  // ── UI: ПОКАЗ ПРЕДЛОЖЕНИЯ ────────────────────────────────
  function _showOffer(title, text, promo, ctaYes, ctaNo) {
    const html = `
      <div style="text-align:center;padding:8px 0 12px">
        <div style="font-size:13px;color:rgba(160,170,220,0.85);line-height:1.6;margin-bottom:14px">${text}</div>
        <!-- Промокод -->
        <div style="background:rgba(6,192,200,0.1);border:2px dashed rgba(6,192,200,0.4);
          border-radius:12px;padding:14px 20px;margin-bottom:12px;position:relative">
          <div style="font-size:10px;color:rgba(140,155,210,0.6);text-transform:uppercase;letter-spacing:.1em;margin-bottom:4px">Промокод</div>
          <div style="font-size:2rem;font-weight:900;color:#06c0c8;letter-spacing:.12em;cursor:pointer"
            onclick="ProactiveDiscount.copyCode('${promo.code}')" title="Нажмите чтобы скопировать">
            ${promo.code}
          </div>
          <div style="font-size:1.6rem;font-weight:800;color:#22d46e;margin:4px 0">-${promo.pct}%</div>
          <div style="font-size:11px;color:rgba(140,155,210,0.55)">${promo.label} · действует ${promo.expires}</div>
          <div id="lq-copied-${promo.code}" style="display:none;position:absolute;top:8px;right:10px;
            background:#22d46e;color:#fff;font-size:11px;font-weight:700;
            padding:3px 8px;border-radius:6px">✓ Скопировано</div>
        </div>
        <!-- CTA -->
        <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap">
          <button onclick="ProactiveDiscount.applyPromo('${promo.code}', ${promo.pct})"
            style="padding:10px 20px;background:linear-gradient(135deg,#4f6aff,#7c5cfc);
            border:none;border-radius:20px;color:#fff;font-size:13px;font-weight:700;cursor:pointer">
            ${ctaYes}
          </button>
          <button onclick="ProactiveDiscount.dismiss()"
            style="padding:10px 14px;background:rgba(82,108,255,0.08);
            border:1px solid rgba(82,108,255,0.2);border-radius:20px;
            color:rgba(140,155,210,0.6);font-size:12px;cursor:pointer">
            ${ctaNo}
          </button>
        </div>
      </div>`;

    _shared_appendBotRich(title, html, []);
  }

  function _startCountdown(seconds) {
    const bar = document.createElement('div');
    bar.id = 'lq-countdown';
    bar.style.cssText = 'position:fixed;top:0;left:0;right:0;height:3px;background:rgba(82,108,255,0.15);z-index:99998';
    bar.innerHTML = `<div id="lq-cd-fill" style="height:100%;width:100%;background:linear-gradient(90deg,#4f6aff,#06c0c8);transition:width 1s linear"></div>`;
    document.body.appendChild(bar);

    let left = seconds;
    const iv = setInterval(() => {
      left--;
      const pct = (left / seconds) * 100;
      const fill = document.getElementById('lq-cd-fill');
      if (fill) fill.style.width = pct + '%';
      if (left <= 0) { clearInterval(iv); bar.remove(); _shown.quick = false; }
    }, 1000);
  }

  // ── ПУБЛИЧНЫЕ МЕТОДЫ ─────────────────────────────────────
  function copyCode(code) {
    navigator.clipboard?.writeText(code).catch(() => {});
    const el = document.getElementById('lq-copied-' + code);
    if (el) { el.style.display = 'block'; setTimeout(() => el.style.display = 'none', 2000); }
  }

  function applyPromo(code, pct) {
    // Сохраняем промокод в StateMachine
    if (typeof StateMachine !== 'undefined') {
      StateMachine.updateSlots({ promoCode: code, promoDiscount: pct });
    }
    localStorage.setItem('remarka_promo', JSON.stringify({ code, pct, appliedAt: Date.now() }));

    _shared_appendBot(
      `🎉 Промокод <b style="color:#06c0c8">${code}</b> применён! Скидка -${pct}% будет учтена в расчёте.\n\nОформить заказ?`,
      ['✅ Да, оформить заказ', '💰 Рассчитать со скидкой', '🔤 Нужен перевод']
    );
    // Пересчитать live-quote
    if (typeof LiveQuote !== 'undefined') LiveQuote.refresh();
  }

  function dismiss() {
    _shared_appendBot('Хорошо! Если понадоблюсь — я здесь 😊', ['🔤 Нужен перевод', '💰 Стоимость']);
  }

  function _pickPromo(idx) { return PROMOS[idx % PROMOS.length]; }

  function _notReady() {
    return Date.now() - _lastTriggerAt < COOLDOWN;
  }

  function _shared_appendBot(text, replies) {
    if (typeof window._shared_appendBot === 'function') { window._shared_appendBot(text, replies); return; }
  }
  function _shared_appendBotRich(title, html, replies) {
    const msgs = document.getElementById('messages');
    if (!msgs) return;
    const d = document.createElement('div'); d.className = 'msg msg--bot';
    const t = new Date().toLocaleTimeString('ru-RU',{hour:'2-digit',minute:'2-digit'});
    d.innerHTML = `<div class="bubble bubble--bot bubble--rich"><div class="bubble-lead">${title}</div>${html}</div><div class="msg-time">${t}</div>`;
    msgs.appendChild(d);
    const qr = document.getElementById('quick-replies');
    if (qr) { qr.innerHTML=''; (replies||[]).forEach(r => { const b=document.createElement('button'); b.className='qr-btn'; b.textContent=r; b.onclick=()=>{ if(typeof ChatEngine!=='undefined') ChatEngine.handleUserInput(r); }; qr.appendChild(b); }); }
    msgs.scrollTop = msgs.scrollHeight;
  }

  return { init, triggerQuick, copyCode, applyPromo, dismiss };
})();

window.ProactiveDiscount = ProactiveDiscount;
document.addEventListener('DOMContentLoaded', () => ProactiveDiscount.init());
