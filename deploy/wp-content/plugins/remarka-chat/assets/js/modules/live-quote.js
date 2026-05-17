/* ============================================================
   REMARKA MODULE 1: live-quote.js v1.0
   Мгновенная смета под полем ввода:
   • Парсит текст по мере набора → показывает цену
   • Обновляется при изменении слотов
   • Исчезает когда данных недостаточно
   ============================================================ */
const LiveQuote = (() => {
  'use strict';
  let _bar = null, _timer = null;

  function init() {
    const poll = setInterval(() => {
      const inp = document.getElementById('chat-input');
      if (!inp) return;
      clearInterval(poll);
      _createBar(inp);
      inp.addEventListener('input', () => {
        clearTimeout(_timer);
        _timer = setTimeout(() => _recalc(inp.value), 350);
      });
      // Пересчёт при изменении слотов (после QR-ответов)
      document.addEventListener('remarka:slots-updated', () => _recalc(inp.value));
    }, 400);
  }

  function _createBar(inp) {
    _bar = document.createElement('div');
    _bar.id = 'lq-bar';
    Object.assign(_bar.style, {
      display: 'none', alignItems: 'center', gap: '10px',
      padding: '5px 20px 3px', fontSize: '12px',
      color: 'rgba(140,155,210,0.75)', flexShrink: '0',
      transition: 'all 0.25s', borderTop: '1px solid rgba(82,108,255,0.08)',
      background: 'rgba(5,10,30,0.3)',
    });
    const area = inp.closest('.remarka-input-area') || inp.parentElement?.parentElement;
    if (area) area.before(_bar);
  }

  function _recalc(text) {
    if (!text || text.length < 8) { _hide(); return; }
    if (typeof SlotExtractor === 'undefined' || typeof PricingEngine === 'undefined') return;

    const extracted = SlotExtractor.extract(text);
    const current   = typeof StateMachine !== 'undefined' ? StateMachine.getSlots() : {};
    const slots     = { ...current, ...extracted };
    const pages     = slots.pages || (slots.chars ? Math.ceil(slots.chars / 1800) : 0);
    if (!pages) { _hide(); return; }

    const tariff  = slots.tariff || 'human';
    const result  = PricingEngine.calcOne(tariff, { ...slots, pages });
    const fmt     = n => n.toLocaleString('ru-RU');
    const tNames  = { mtpe:'MTPE', human:'Проф.', premium:'Premium' };

    _bar.style.display = 'flex';
    _bar.innerHTML = `
      <span style="opacity:.55">💰 Примерно:</span>
      <span style="color:#06c0c8;font-weight:700;font-size:13px">${fmt(result.total)} ₽</span>
      <span style="opacity:.45">(${fmt(result.perPage)} ₽/стр. · ${pages} стр. · ${tNames[tariff]})</span>
      <span style="margin-left:auto;opacity:.4;cursor:pointer;font-size:15px" onclick="LiveQuote.hide()">×</span>`;
  }

  function _hide() {
    if (_bar) _bar.style.display = 'none';
  }

  // Обновление снаружи (вызывается из wp-adapter / chat.js)
  function refresh() {
    const inp = document.getElementById('chat-input');
    if (inp) _recalc(inp.value);
  }

  function hide() { _hide(); }

  return { init, refresh, hide };
})();

window.LiveQuote = LiveQuote;

// Авто-инициализация
document.addEventListener('DOMContentLoaded', () => LiveQuote.init());
