/* ============================================================
   REMARKA MODULE: feedback-collector.js v1.0
   Сбор отзывов после выполнения заказа:
   • Звёздный рейтинг (1–5) прямо в чате
   • Текстовый комментарий
   • Сохранение в WP (CPT remarka_review)
   • Опциональная публикация на сайте
   • Автозапуск через N дней после заказа
   ============================================================ */

const FeedbackCollector = (() => {
  'use strict';

  let state = { active: false, rating: 0, orderId: null };

  function checkIntent(text) {
    return /\b(отзыв|оценк|оставить.*отзыв|review|feedback|понравилось|доволен|недоволен|рекоменд)\b/i.test(text);
  }

  function start(orderId) {
    state = { active: true, rating: 0, orderId: orderId || null, step: 'rating' };
    _botRich(
      '🌟 Оставьте отзыв о нашей работе!\n\nЭто займёт 1 минуту и поможет нам стать лучше.',
      _buildRatingWidget(),
      ['⭐⭐⭐⭐⭐ Отлично!', '⭐⭐⭐⭐ Хорошо', '⭐⭐⭐ Нормально', '❌ Отмена']
    );
  }

  function _buildRatingWidget() {
    return `<div id="fb-rating-wrap" style="text-align:center;padding:16px 0">
      <div style="font-size:13px;color:rgba(160,170,220,0.8);margin-bottom:14px">Оцените качество перевода:</div>
      <div style="display:flex;justify-content:center;gap:8px;font-size:2.5rem;margin-bottom:12px">
        ${[1,2,3,4,5].map(n => `<span id="fb-star-${n}" onclick="FeedbackCollector.setRating(${n})"
          style="cursor:pointer;transition:transform .15s;opacity:0.35;filter:grayscale(100%)"
          onmouseover="FeedbackCollector.hoverRating(${n})"
          onmouseout="FeedbackCollector.resetHover()">⭐</span>`).join('')}
      </div>
      <div id="fb-rating-label" style="font-size:13px;font-weight:600;color:rgba(140,155,210,0.5);min-height:20px"></div>
      <button id="fb-next-btn" onclick="FeedbackCollector.submitRating()" disabled
        style="margin-top:12px;padding:9px 22px;background:#444;border:none;border-radius:20px;color:#fff;font-size:13px;font-weight:600;cursor:not-allowed;transition:all .2s">
        Далее →
      </button>
    </div>`;
  }

  function hoverRating(n) {
    const labels = ['','😞 Плохо','😐 Не очень','🙂 Нормально','😊 Хорошо','🤩 Отлично!'];
    [1,2,3,4,5].forEach(i => {
      const s = document.getElementById('fb-star-' + i);
      if (s) { s.style.opacity = i <= n ? '1' : '0.35'; s.style.filter = i <= n ? 'none' : 'grayscale(100%)'; s.style.transform = i <= n ? 'scale(1.2)' : 'scale(1)'; }
    });
    const lbl = document.getElementById('fb-rating-label');
    if (lbl) lbl.textContent = labels[n] || '';
  }

  function resetHover() {
    if (state.rating > 0) { hoverRating(state.rating); } else {
      [1,2,3,4,5].forEach(i => {
        const s = document.getElementById('fb-star-' + i);
        if (s) { s.style.opacity='0.35'; s.style.filter='grayscale(100%)'; s.style.transform='scale(1)'; }
      });
      const lbl = document.getElementById('fb-rating-label'); if (lbl) lbl.textContent = '';
    }
  }

  function setRating(n) {
    state.rating = n;
    hoverRating(n);
    const btn = document.getElementById('fb-next-btn');
    if (btn) { btn.disabled=false; btn.style.background='linear-gradient(135deg,#4f6aff,#7c5cfc)'; btn.style.cursor='pointer'; }
  }

  function submitRating() {
    if (!state.rating) return;
    state.step = 'comment';
    _botRich(
      `${['','😞','😐','🙂','😊','🤩'][state.rating]} Спасибо за оценку ${state.rating}/5!\n\nРасскажите подробнее — что понравилось или что можно улучшить?`,
      `<div style="margin-top:8px">
        <textarea id="fb-comment" placeholder="Ваш комментарий (необязательно)…"
          style="width:100%;min-height:80px;background:rgba(8,18,52,0.7);border:1.5px solid rgba(82,108,255,0.3);
          border-radius:10px;color:#dde4ff;padding:10px 14px;font-size:13px;resize:vertical;
          outline:none;font-family:inherit;line-height:1.5;box-sizing:border-box"></textarea>
        <div style="display:flex;gap:8px;margin-top:8px;flex-wrap:wrap">
          <button onclick="FeedbackCollector.submitComment(true)"
            style="padding:9px 16px;background:linear-gradient(135deg,#4f6aff,#7c5cfc);border:none;border-radius:20px;color:#fff;font-size:13px;font-weight:600;cursor:pointer">
            ✅ Отправить отзыв
          </button>
          <button onclick="FeedbackCollector.submitComment(false)"
            style="padding:9px 16px;background:rgba(82,108,255,0.1);border:1px solid rgba(82,108,255,0.3);border-radius:20px;color:#a5b4fc;font-size:13px;cursor:pointer">
            Без комментария →
          </button>
        </div>
      </div>`,
      []
    );
  }

  function submitComment(withComment) {
    const ta = document.getElementById('fb-comment');
    const comment = withComment && ta ? ta.value.trim() : '';
    state.comment = comment;
    state.step = 'publish';

    _botRich(
      'Опубликовать отзыв на сайте?',
      `<div style="font-size:13px;color:rgba(160,170,220,0.8);margin-bottom:10px">
        Ваш отзыв поможет другим клиентам сделать выбор. Вы можете опубликовать его анонимно.
      </div>
      <div style="display:flex;gap:8px;flex-wrap:wrap">
        <button onclick="FeedbackCollector.finalize(true, false)"
          style="padding:9px 16px;background:linear-gradient(135deg,#22d46e,#1aaa55);border:none;border-radius:20px;color:#fff;font-size:13px;font-weight:600;cursor:pointer">
          ✅ Да, опубликовать
        </button>
        <button onclick="FeedbackCollector.finalize(true, true)"
          style="padding:9px 14px;background:rgba(34,212,110,0.1);border:1px solid rgba(34,212,110,0.3);border-radius:20px;color:#22d46e;font-size:13px;cursor:pointer">
          👤 Анонимно
        </button>
        <button onclick="FeedbackCollector.finalize(false, false)"
          style="padding:9px 14px;background:rgba(82,108,255,0.08);border:1px solid rgba(82,108,255,0.2);border-radius:20px;color:#a5b4fc;font-size:13px;cursor:pointer">
          Только для нас
        </button>
      </div>`,
      []
    );
  }

  function finalize(save, anonymous) {
    state.publish = save;
    state.anonymous = anonymous;
    state.active = false;

    _save();

    const stars = '⭐'.repeat(state.rating) + '☆'.repeat(5 - state.rating);
    _botRich(
      '🙏 Спасибо за отзыв!',
      `<div style="text-align:center;padding:10px 0">
        <div style="font-size:2rem;margin-bottom:6px">${['','😞','😐','🙂','😊','🤩'][state.rating]}</div>
        <div style="font-size:1.3rem;margin-bottom:4px">${stars}</div>
        <div style="font-size:14px;font-weight:600;color:#e8eeff;margin-bottom:8px">${state.rating}/5</div>
        ${state.comment ? `<div style="background:rgba(0,0,0,0.2);border-radius:8px;padding:8px 12px;font-size:12.5px;color:rgba(160,170,220,0.8);font-style:italic;text-align:left">"${state.comment}"</div>` : ''}
        <div style="margin-top:10px;font-size:12px;color:#22d46e">✅ ${save ? (anonymous ? 'Опубликовано анонимно' : 'Опубликовано на сайте') : 'Сохранено для команды'}</div>
      </div>`,
      ['🔤 Новый заказ', '💬 Задать вопрос']
    );
  }

  function _save() {
    if (typeof RemarkaConfig === 'undefined') return;
    const body = new URLSearchParams({
      action:    'remarka_save_review',
      nonce:     RemarkaConfig.nonce,
      rating:    state.rating,
      comment:   state.comment || '',
      publish:   state.publish ? '1' : '0',
      anonymous: state.anonymous ? '1' : '0',
      order_id:  state.orderId || '',
    });
    fetch(RemarkaConfig.ajaxUrl, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: body.toString() }).catch(()=>{});
  }

  // Автозапуск через N дней после заказа
  function scheduleAuto(orderId, delayDays = 3) {
    const key = 'remarka_feedback_' + orderId;
    if (localStorage.getItem(key)) return; // уже спрашивали
    const triggerAt = Date.now() + delayDays * 24 * 3600 * 1000;
    localStorage.setItem(key + '_at', triggerAt);
    // Проверяем при каждой загрузке
    setTimeout(() => {
      const at = parseInt(localStorage.getItem(key + '_at') || '0');
      if (Date.now() >= at && !localStorage.getItem(key)) {
        localStorage.setItem(key, '1');
        setTimeout(() => start(orderId), 5000); // через 5 сек после открытия чата
      }
    }, 3000);
  }

  function _bot(t, r) { _shared_appendBot(t, r); }
  function _botRich(t, h, r) { _shared_appendBotRich(t, h, r); }

  return { checkIntent, start, scheduleAuto, setRating, hoverRating, resetHover, submitRating, submitComment, finalize };
})();

window.FeedbackCollector = FeedbackCollector;
