/* ============================================================
   REMARKA MODULE 7: deadline-calendar.js v1.0
   Визуальный выбор даты прямо в чате:
   • Мини-календарь в bubble
   • Автоопределение категории срочности по дате
   • Подсветка дней: стандарт/срочно/экспресс
   • Показ стоимости срочности при выборе
   ============================================================ */
const DeadlineCalendar = (() => {
  'use strict';

  let _selectedDate = null;
  let _resolve      = null; // Promise resolver

  function checkIntent(text) {
    return /\b(выбрать дату|когда.*готов|дедлайн|срок.*перевод|calendar|дату.*заказ|нужно.*к)\b/i.test(text);
  }

  // ── ПОКАЗ КАЛЕНДАРЯ ──────────────────────────────────────
  function show() {
    return new Promise((resolve) => {
      _resolve = resolve;
      _render();
    });
  }

  function _render() {
    const today    = new Date();
    const year     = today.getFullYear();
    const month    = today.getMonth();

    const html = `
      <div id="dc-cal-wrap" style="user-select:none">
        <div style="font-size:12px;color:rgba(140,155,210,0.7);margin-bottom:10px">
          📅 Выберите дату — когда нужен готовый перевод:
        </div>
        <!-- Легенда -->
        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:10px;font-size:11px">
          ${_legendBadge('#22d46e','Стандарт (3–7 дн.)')}
          ${_legendBadge('#c4922a','Срочно (1–2 дня)')}
          ${_legendBadge('#ef4444','Экспресс (24 ч)')}
        </div>
        <!-- Навигация -->
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
          <button onclick="DeadlineCalendar._prevMonth()" id="dc-prev"
            style="background:rgba(82,108,255,0.12);border:none;border-radius:8px;
            color:#a5b4fc;width:30px;height:30px;cursor:pointer;font-size:16px;line-height:1">‹</button>
          <span id="dc-month-label" style="font-size:13px;font-weight:700;color:#e8eeff"></span>
          <button onclick="DeadlineCalendar._nextMonth()" id="dc-next"
            style="background:rgba(82,108,255,0.12);border:none;border-radius:8px;
            color:#a5b4fc;width:30px;height:30px;cursor:pointer;font-size:16px;line-height:1">›</button>
        </div>
        <!-- Дни недели -->
        <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:2px;margin-bottom:4px">
          ${['Пн','Вт','Ср','Чт','Пт','Сб','Вс'].map(d =>
            `<div style="text-align:center;font-size:10px;color:rgba(140,155,210,0.5);padding:2px">${d}</div>`
          ).join('')}
        </div>
        <!-- Ячейки дней -->
        <div id="dc-days" style="display:grid;grid-template-columns:repeat(7,1fr);gap:2px"></div>
        <!-- Результат выбора -->
        <div id="dc-result" style="display:none;margin-top:12px;background:rgba(79,106,255,0.1);
          border:1px solid rgba(82,108,255,0.3);border-radius:10px;padding:10px 12px;font-size:13px">
        </div>
        <div style="display:flex;gap:8px;margin-top:10px">
          <button id="dc-confirm-btn" onclick="DeadlineCalendar._confirm()" disabled
            style="padding:9px 20px;background:#444;border:none;border-radius:20px;
            color:#fff;font-size:13px;font-weight:600;cursor:not-allowed;transition:all .2s;flex:1">
            Выбрать дату
          </button>
          <button onclick="DeadlineCalendar._cancel()"
            style="padding:9px 14px;background:rgba(82,108,255,0.08);
            border:1px solid rgba(82,108,255,0.2);border-radius:20px;
            color:rgba(140,155,210,0.7);font-size:12px;cursor:pointer">
            Отмена
          </button>
        </div>
      </div>`;

    _shared_appendBotRich('Выберите желаемую дату готовности:', html, []);
    _currentYear  = year;
    _currentMonth = month;
    _renderDays(year, month, new Date());
  }

  let _currentYear, _currentMonth;

  function _renderDays(year, month, today) {
    const container = document.getElementById('dc-days');
    if (!container) return;

    const label = document.getElementById('dc-month-label');
    if (label) {
      label.textContent = new Date(year, month).toLocaleDateString('ru-RU', { month:'long', year:'numeric' });
    }

    const firstDay = new Date(year, month, 1).getDay(); // 0=Вс
    const startPad  = firstDay === 0 ? 6 : firstDay - 1; // Пн=0
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    container.innerHTML = '';

    // Пустые ячейки
    for (let i = 0; i < startPad; i++) {
      container.appendChild(_emptyCell());
    }

    for (let d = 1; d <= daysInMonth; d++) {
      const date    = new Date(year, month, d);
      const isPast  = date < new Date(today.getFullYear(), today.getMonth(), today.getDate());
      const diffDays = Math.ceil((date - today) / 86400000);
      const isWeekend = date.getDay() === 0 || date.getDay() === 6;

      let urgency = null, color = null, bg = null;
      if (!isPast) {
        if (diffDays <= 1)      { urgency = 'express';  color = '#ef4444'; bg = 'rgba(239,68,68,0.12)'; }
        else if (diffDays <= 2) { urgency = 'urgent';   color = '#c4922a'; bg = 'rgba(196,146,42,0.12)'; }
        else if (diffDays <= 7) { urgency = 'standard'; color = '#22d46e'; bg = 'rgba(34,212,110,0.12)'; }
        else                    { urgency = 'flexible'; color = '#4f6aff'; bg = 'rgba(79,106,255,0.08)'; }
      }

      const cell = document.createElement('div');
      const isSelected = _selectedDate &&
        _selectedDate.getDate() === d &&
        _selectedDate.getMonth() === month &&
        _selectedDate.getFullYear() === year;

      cell.style.cssText = [
        'text-align:center', 'padding:6px 2px', 'border-radius:8px',
        'font-size:12.5px', 'font-weight:500',
        isPast ? 'opacity:.25;cursor:default;color:rgba(140,155,210,0.4)' :
          isWeekend ? `cursor:pointer;color:${color || '#8fa0d8'};background:${bg || 'transparent'}` :
          `cursor:pointer;color:${color || '#dde4ff'};background:${bg || 'transparent'}`,
        isSelected ? `outline:2px solid ${color || '#4f6aff'};font-weight:800` : '',
      ].join(';');

      cell.textContent = d;
      if (!isPast) {
        cell.setAttribute('data-date', date.toISOString());
        cell.setAttribute('data-urgency', urgency);
        cell.setAttribute('data-diff', diffDays);
        cell.onclick = () => _selectDay(cell, date, urgency, diffDays);
        cell.onmouseover = () => { if (!isSelected) cell.style.opacity = '.7'; };
        cell.onmouseout  = () => { if (!isSelected) cell.style.opacity = '1'; };
      }

      container.appendChild(cell);
    }
  }

  function _emptyCell() {
    const d = document.createElement('div'); return d;
  }

  function _selectDay(cell, date, urgency, diffDays) {
    _selectedDate = date;
    // Сброс всех стилей выделения
    document.querySelectorAll('#dc-days > div').forEach(c => {
      c.style.outline = 'none'; c.style.fontWeight = '500';
    });
    // Выделить выбранную
    const urgColors = { express:'#ef4444', urgent:'#c4922a', standard:'#22d46e', flexible:'#4f6aff' };
    cell.style.outline = `2px solid ${urgColors[urgency] || '#4f6aff'}`;
    cell.style.fontWeight = '800';

    // Показать результат
    const urgLabels = {
      express: '⚡ Экспресс (+70%) — 24 часа',
      urgent:  '🔥 Срочно (+35%) — 1–2 дня',
      standard:'📅 Стандарт — 3–7 дней',
      flexible:'📆 Без срочности (-10%)',
    };
    const result = document.getElementById('dc-result');
    if (result) {
      result.style.display = 'block';
      result.innerHTML = `
        <div style="display:flex;justify-content:space-between;align-items:center">
          <div>
            <div style="font-weight:700;color:#e8eeff">${date.toLocaleDateString('ru-RU',{day:'numeric',month:'long',year:'numeric'})}</div>
            <div style="font-size:12px;color:${urgColors[urgency]};margin-top:2px">${urgLabels[urgency]}</div>
          </div>
          <div style="text-align:right;font-size:11px;color:rgba(140,155,210,0.6)">${diffDays > 0 ? diffDays + ' дн.' : 'сегодня'}</div>
        </div>`;
    }

    const btn = document.getElementById('dc-confirm-btn');
    if (btn) {
      btn.disabled = false;
      btn.style.background = `linear-gradient(135deg,${urgColors[urgency]},${urgency==='express'?'#c0392b':urgency==='urgent'?'#a87800':'#2e7d32'})`;
      btn.style.cursor = 'pointer';
    }
  }

  function _prevMonth() {
    _currentMonth--;
    if (_currentMonth < 0) { _currentMonth = 11; _currentYear--; }
    _renderDays(_currentYear, _currentMonth, new Date());
  }

  function _nextMonth() {
    _currentMonth++;
    if (_currentMonth > 11) { _currentMonth = 0; _currentYear++; }
    _renderDays(_currentYear, _currentMonth, new Date());
  }

  function _confirm() {
    if (!_selectedDate) return;
    const date  = _selectedDate;
    const today = new Date();
    const diff  = Math.ceil((date - today) / 86400000);
    const urgency = diff <= 1 ? 'express' : diff <= 2 ? 'urgent' : diff <= 7 ? 'standard' : 'flexible';
    const urgNames = { express:'Экспресс (24ч)', urgent:'Срочно (1–2 дня)', standard:'Стандарт (3–7 дней)', flexible:'Без срочности' };

    if (typeof StateMachine !== 'undefined') StateMachine.updateSlots({ urgency, deadline: date.toLocaleDateString('ru-RU') });

    if (_resolve) { _resolve({ date, urgency, diff }); _resolve = null; }

    if (typeof ChatEngine !== 'undefined') {
      ChatEngine.handleUserInput(`Дедлайн: ${date.toLocaleDateString('ru-RU')} — ${urgNames[urgency]}`);
    }
  }

  function _cancel() {
    if (_resolve) { _resolve(null); _resolve = null; }
    if (typeof ChatEngine !== 'undefined') ChatEngine.handleUserInput('Выбрать срок позже');
  }

  function _legendBadge(color, label) {
    return `<div style="display:flex;align-items:center;gap:5px">
      <span style="width:10px;height:10px;border-radius:50%;background:${color};display:inline-block"></span>
      <span style="color:rgba(140,155,210,0.7)">${label}</span>
    </div>`;
  }

  function _shared_appendBotRich(title, html, replies) {
    const msgs = document.getElementById('messages');
    if (!msgs) return;
    const d = document.createElement('div'); d.className = 'msg msg--bot';
    const t = new Date().toLocaleTimeString('ru-RU',{hour:'2-digit',minute:'2-digit'});
    d.innerHTML = `<div class="bubble bubble--bot bubble--rich"><div class="bubble-lead">${title}</div>${html}</div><div class="msg-time">${t}</div>`;
    msgs.appendChild(d);
    const qr = document.getElementById('quick-replies');
    if (qr) { qr.innerHTML=''; (replies||[]).forEach(r=>{const b=document.createElement('button');b.className='qr-btn';b.textContent=r;b.onclick=()=>{if(typeof ChatEngine!=='undefined')ChatEngine.handleUserInput(r);};qr.appendChild(b);}); }
    msgs.scrollTop = msgs.scrollHeight;
  }

  return { checkIntent, show, _prevMonth, _nextMonth, _confirm, _cancel };
})();

window.DeadlineCalendar = DeadlineCalendar;
