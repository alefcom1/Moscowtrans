/* ============================================================
   REMARKA MODULES BUNDLE v1.0
   Содержит 4 модуля:
   1. B2BFlow          — корпоративная воронка
   2. NDAFlow          — конфиденциальность и NDA
   3. CertifiedFlow    — нотариальные переводы / апостиль
   4. OrderTracker     — статус заказа в чате
   ============================================================ */

/* ────────────────────────────────────────────────────────────
   1. B2B FLOW — Корпоративная воронка
   Определяет юрлицо → собирает реквизиты → предлагает договор
   ──────────────────────────────────────────────────────────── */
const B2BFlow = (() => {
  'use strict';

  let state = { active: false, step: null, data: {} };

  const STEPS = ['company', 'inn', 'contact_name', 'contact_email', 'contact_phone', 'volume_month', 'domains', 'languages', 'submit'];

  const QUESTIONS = {
    company:       '🏢 Название вашей компании / организации:',
    inn:           '📋 ИНН организации (для подготовки договора):',
    contact_name:  '👤 Имя и должность ответственного сотрудника:',
    contact_email: '📧 Корпоративный email для связи:',
    contact_phone: '📞 Телефон для связи:',
    volume_month:  '📄 Примерный ежемесячный объём переводов:',
    domains:       '📚 Основные направления (тип документов):',
    languages:     '🌍 Нужные языковые пары:',
  };

  const REPLIES = {
    volume_month: ['До 50 стр/мес', '50–200 стр/мес', '200–500 стр/мес', 'Более 500 стр/мес'],
    domains:      ['📄 Технические', '⚖️ Юридические', '🏥 Медицинские', '💻 IT/ПО', '📊 Финансовые', '🌐 Маркетинг', 'Несколько направлений'],
    languages:    ['RU ↔ EN', 'RU ↔ DE', 'RU ↔ IT', 'RU ↔ FR', 'Несколько пар'],
  };

  function checkIntent(text) {
    return /\b(ооо|ип|зао|пао|ао|юрлицо|юридическое лицо|компания|организация|корпоратив|b2b|постоянн|регулярн.*заказ|договор.*бюро|контракт.*перевод)\b/i.test(text);
  }

  function start() {
    state = { active: true, step: 'company', data: {} };
    _bot(
      '🏢 Отлично! Для корпоративных клиентов у нас специальные условия:\n\n' +
      '✅ Персональный менеджер\n' +
      '✅ Скидка от объёма (от 10%)\n' +
      '✅ Договор и закрывающие документы\n' +
      '✅ Постоплата для постоянных клиентов\n' +
      '✅ Глоссарий и стайлгайд компании\n\n' +
      'Заполним краткую анкету — займёт 2 минуты.',
      ['✅ Начать', '❌ Отмена']
    );
    _override();
  }

  function _handleInput(text) {
    if (/отмена|❌/i.test(text)) { state.active = false; _restore(); _bot('Хорошо! Чем могу помочь?', ['🔤 Нужен перевод', '💰 Стоимость']); return; }
    if (text === '✅ Начать') { _askStep('company'); return; }
    if (state.step === 'submit') return;
    state.data[state.step] = text;
    const idx = STEPS.indexOf(state.step);
    state.step = STEPS[idx + 1];
    if (state.step === 'submit') { _submit(); } else { _askStep(state.step); }
  }

  function _askStep(step) {
    state.step = step;
    _bot(QUESTIONS[step], REPLIES[step] || []);
  }

  function _submit() {
    const d = state.data;
    // Скидка по объёму
    const discountMap = { 'До 50 стр/мес': 5, '50–200 стр/мес': 10, '200–500 стр/мес': 15, 'Более 500 стр/мес': 20 };
    const discount = discountMap[d.volume_month] || 5;

    _botRich('Отлично! Вот ваше предложение:', `
      <div style="background:rgba(79,106,255,0.08);border:1px solid rgba(82,108,255,0.25);border-radius:12px;padding:14px;margin-bottom:12px">
        <div style="font-size:13px;font-weight:700;color:#e8eeff;margin-bottom:10px">🏢 ${d.company || 'Компания'}</div>
        ${[
          ['ИНН', d.inn], ['Контакт', d.contact_name], ['Email', d.contact_email],
          ['Телефон', d.contact_phone], ['Объём', d.volume_month],
          ['Направления', d.domains], ['Языки', d.languages],
        ].map(([l,v]) => v ? `<div style="display:flex;justify-content:space-between;font-size:12px;padding:3px 0;border-bottom:1px solid rgba(82,108,255,0.08)">
          <span style="color:rgba(140,155,210,0.7)">${l}</span>
          <span style="color:#dde4ff;font-weight:500">${v}</span>
        </div>` : '').join('')}
        <div style="margin-top:10px;text-align:center;background:rgba(34,212,110,0.1);border-radius:8px;padding:8px">
          <div style="font-size:11px;color:rgba(140,155,210,0.7)">Ваша скидка</div>
          <div style="font-size:1.8rem;font-weight:800;color:#22d46e">-${discount}%</div>
        </div>
      </div>
      <div style="font-size:13px;color:rgba(160,170,220,0.8);line-height:1.6">
        Наш менеджер подготовит договор и коммерческое предложение в течение <b style="color:#e8eeff">2 часов</b> в рабочее время.
      </div>`,
      ['📧 Ожидаю КП на email', '📞 Лучше позвоните', '💬 Есть вопросы']
    );

    // Сохраняем
    _saveToWP({ type: 'b2b', ...d, discount });
    state.active = false;
    _restore();
  }

  function _saveToWP(data) {
    if (typeof RemarkaConfig === 'undefined') return;
    const body = new URLSearchParams({ action:'remarka_save_b2b', nonce:RemarkaConfig.nonce, data:JSON.stringify(data) });
    fetch(RemarkaConfig.ajaxUrl, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: body.toString() }).catch(()=>{});
  }

  let _orig = null;
  function _override() {
    if (_orig || typeof ChatEngine === 'undefined') return;
    _orig = ChatEngine.handleUserInput.bind(ChatEngine);
    ChatEngine.handleUserInput = (t) => { if (state.active) _handleInput(t); else { _restore(); _orig(t); } };
  }
  function _restore() { if (_orig && typeof ChatEngine !== 'undefined') { ChatEngine.handleUserInput = _orig; _orig = null; } }
  const _sharedBot = (fn) => fn;

  function _bot(text, replies) { _shared_appendBot(text, replies); }
  function _botRich(text, html, replies) { _shared_appendBotRich(text, html, replies); }

  return { checkIntent, start };
})();


/* ────────────────────────────────────────────────────────────
   2. NDA FLOW — Конфиденциальность
   ──────────────────────────────────────────────────────────── */
const NDAFlow = (() => {
  'use strict';

  function checkIntent(text) {
    return /\b(nda|ндa|конфиденц|секретн|неразглашен|тайн|закрытый.*документ|sensitive|confidential)\b/i.test(text);
  }

  function start() {
    _shared_appendBotRich(
      '🔒 Работаем строго конфиденциально!\n\nВот наши гарантии:',
      `<div style="display:flex;flex-direction:column;gap:8px;margin:8px 0">
        ${[
          ['🔐', 'NDA с каждым переводчиком', 'Все сотрудники подписывают соглашение о неразглашении'],
          ['🛡️', 'Защищённая передача файлов', 'Шифрование при загрузке и хранении документов'],
          ['🗑️', 'Удаление после выполнения', 'Файлы удаляются через 30 дней после сдачи работы'],
          ['📋', 'NDA с вами', 'По запросу подпишем соглашение о конфиденциальности с вашей компанией'],
          ['⚖️', 'Юридическая ответственность', 'Ответственность закреплена в договоре'],
        ].map(([icon, title, desc]) => `<div style="display:flex;gap:10px;align-items:flex-start;background:rgba(8,18,52,0.5);border-radius:10px;padding:10px 12px">
          <span style="font-size:20px;flex-shrink:0">${icon}</span>
          <div><div style="font-size:13px;font-weight:600;color:#e8eeff;margin-bottom:2px">${title}</div>
          <div style="font-size:11.5px;color:rgba(140,155,210,0.7)">${desc}</div></div>
        </div>`).join('')}
      </div>
      <div style="margin-top:10px;background:rgba(34,212,110,0.08);border-radius:8px;padding:10px 12px;font-size:12.5px;color:#22d46e">
        ✅ Вы можете приступить к заказу — конфиденциальность гарантирована
      </div>`,
      ['✅ Понятно, продолжим', '📋 Нужен NDA-договор', '💬 Есть вопросы']
    );
  }

  function requestNDA() {
    _shared_appendBot(
      '📋 Для подписания NDA нам понадобится:\n• Ваш email\n• Название организации\n\nНапишите их — подготовим соглашение в течение 1 часа.',
      []
    );
  }

  return { checkIntent, start, requestNDA };
})();


/* ────────────────────────────────────────────────────────────
   3. CERTIFIED FLOW — Нотариальные переводы и апостиль
   ──────────────────────────────────────────────────────────── */
const CertifiedFlow = (() => {
  'use strict';

  let state = { active: false, step: null, docType: null };

  const DOC_REQUIREMENTS = {
    passport:       { notary: true,  apostille: false, authority: 'МВД, ЗАГС, Консульство', note: 'Каждая страница переводится и заверяется отдельно' },
    diploma:        { notary: true,  apostille: true,  authority: 'Минобрнауки, Университет', note: 'Требуется нотариальная копия оригинала' },
    birth_cert:     { notary: true,  apostille: true,  authority: 'ЗАГС, Консульство', note: 'Апостиль на оригинале + перевод с нотариусом' },
    marriage_cert:  { notary: true,  apostille: true,  authority: 'ЗАГС, Консульство', note: '' },
    court_decision: { notary: true,  apostille: true,  authority: 'Суд, Минюст', note: 'Апостиль проставляется Минюстом России' },
    power_attorney: { notary: true,  apostille: false, authority: 'Нотариус', note: 'Доверенность уже должна быть нотариально оформлена' },
    charter:        { notary: false, apostille: false, authority: 'ИФНС, Минюст', note: 'Обычно достаточно профессионального перевода' },
    general:        { notary: false, apostille: false, authority: '', note: '' },
  };

  function checkIntent(text) {
    return /\b(нотариал|апостил|заверен|apostille|notari|sworn|certified.*translat|official.*translat)\b/i.test(text);
  }

  function start(docType) {
    state = { active: true, step: 'doc_type', docType: docType || null };
    if (docType && DOC_REQUIREMENTS[docType]) {
      _showDocInfo(docType);
    } else {
      _shared_appendBot(
        '📜 Нотариальный перевод — выберите тип документа:',
        ['📘 Паспорт / удостоверение', '🎓 Диплом / аттестат', '👶 Свидетельство о рождении',
         '💍 Свидетельство о браке', '⚖️ Судебное решение', '📄 Доверенность', '🏢 Устав / учредительный']
      );
      _overrideForSelection();
    }
  }

  function _overrideForSelection() {
    if (typeof ChatEngine === 'undefined') return;
    const orig = ChatEngine.handleUserInput.bind(ChatEngine);
    ChatEngine.handleUserInput = (text) => {
      const map = {
        '📘 Паспорт / удостоверение': 'passport',
        '🎓 Диплом / аттестат': 'diploma',
        '👶 Свидетельство о рождении': 'birth_cert',
        '💍 Свидетельство о браке': 'marriage_cert',
        '⚖️ Судебное решение': 'court_decision',
        '📄 Доверенность': 'power_attorney',
        '🏢 Устав / учредительный': 'charter',
      };
      if (map[text]) {
        ChatEngine.handleUserInput = orig;
        _showDocInfo(map[text]);
      } else {
        ChatEngine.handleUserInput = orig;
        orig(text);
      }
    };
  }

  function _showDocInfo(docType) {
    state.docType = docType;
    const req = DOC_REQUIREMENTS[docType] || DOC_REQUIREMENTS.general;
    const prices = { notary: 400, apostille: 3500, translation: 750 };
    const pages = 2; // типовой документ

    const totalEst = prices.translation * pages
      + (req.notary ? prices.notary * pages : 0)
      + (req.apostille ? prices.apostille : 0);

    _shared_appendBotRich(
      '📜 Информация о нотариальном переводе',
      `<div style="display:flex;flex-direction:column;gap:8px">
        <!-- Требования -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px">
          ${_badge('Перевод', '✅ Обязателен', '#22d46e')}
          ${_badge('Нотариус', req.notary ? '✅ Требуется' : '➖ Не нужен', req.notary ? '#22d46e' : '#3d4f80')}
          ${_badge('Апостиль', req.apostille ? '✅ Может потребоваться' : '➖ Обычно не нужен', req.apostille ? '#c4922a' : '#3d4f80')}
          ${_badge('Орган', req.authority || '—', '#4f6aff')}
        </div>
        ${req.note ? `<div style="background:rgba(196,146,42,0.1);border:1px solid rgba(196,146,42,0.25);border-radius:8px;padding:8px 12px;font-size:12px;color:#e8b84b">ℹ️ ${req.note}</div>` : ''}
        <!-- Примерная стоимость -->
        <div style="background:rgba(8,16,50,0.6);border-radius:10px;padding:12px">
          <div style="font-size:11px;color:rgba(140,155,210,0.6);margin-bottom:8px;text-transform:uppercase;letter-spacing:.05em">Примерная стоимость (2 стр.)</div>
          ${_priceRow('Перевод', prices.translation + ' ₽/стр. × ' + pages, prices.translation * pages)}
          ${req.notary ? _priceRow('Нотариус', prices.notary + ' ₽/стр. × ' + pages, prices.notary * pages) : ''}
          ${req.apostille ? _priceRow('Апостиль', 'по тарифу Минюста', prices.apostille) : ''}
          <div style="border-top:1px solid rgba(82,108,255,0.15);margin-top:8px;padding-top:8px;display:flex;justify-content:space-between">
            <span style="font-size:13px;font-weight:700;color:#e8eeff">ИТОГО (примерно)</span>
            <span style="font-size:16px;font-weight:800;color:#06c0c8">${totalEst.toLocaleString('ru-RU')} ₽</span>
          </div>
        </div>
        <div style="font-size:12px;color:rgba(140,155,210,0.6)">⏱ Срок: 2–5 рабочих дней (включая нотариуса)</div>
      </div>`,
      ['✅ Заказать', '📎 Загрузить документ', '💬 Уточнить детали']
    );
  }

  function _badge(label, val, color) {
    return `<div style="background:rgba(8,16,50,0.5);border-radius:8px;padding:7px 10px">
      <div style="font-size:10px;color:rgba(140,155,210,0.6);margin-bottom:2px">${label}</div>
      <div style="font-size:12px;font-weight:600;color:${color}">${val}</div>
    </div>`;
  }

  function _priceRow(label, sub, amount) {
    return `<div style="display:flex;justify-content:space-between;align-items:center;padding:3px 0;font-size:12px">
      <div><span style="color:#dde4ff">${label}</span> <span style="color:rgba(140,155,210,0.5);font-size:11px">${sub}</span></div>
      <span style="color:#dde4ff;font-weight:600">${amount.toLocaleString('ru-RU')} ₽</span>
    </div>`;
  }

  return { checkIntent, start };
})();


/* ────────────────────────────────────────────────────────────
   4. ORDER TRACKER — Статус заказа
   ──────────────────────────────────────────────────────────── */
const OrderTracker = (() => {
  'use strict';

  let _waitingForId = false;

  function checkIntent(text) {
    return /\b(где.*заказ|статус.*заказ|заказ.*готов|когда.*готов|мой.*заказ|track|order.*status|где.*перевод)\b/i.test(text);
  }

  function start() {
    _waitingForId = true;
    _shared_appendBot(
      '📦 Проверю статус вашего заказа!\n\nВведите номер заказа (например: #RM-2025-0042) или email, который вы указывали при заказе:',
      []
    );
    _override();
  }

  function _override() {
    if (typeof ChatEngine === 'undefined') return;
    const orig = ChatEngine.handleUserInput.bind(ChatEngine);
    ChatEngine.handleUserInput = (text) => {
      if (_waitingForId) {
        _waitingForId = false;
        ChatEngine.handleUserInput = orig;
        _lookup(text);
      } else {
        orig(text);
      }
    };
  }

  async function _lookup(query) {
    _shared_appendBot('🔍 Ищу заказ…', []);

    if (typeof RemarkaConfig === 'undefined') {
      _showDemo(query);
      return;
    }

    try {
      const body = new URLSearchParams({
        action: 'remarka_track_order',
        nonce:  RemarkaConfig.nonce,
        query:  query.trim(),
      });
      const resp = await fetch(RemarkaConfig.ajaxUrl, {
        method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body.toString(),
      });
      const data = await resp.json();

      if (data.success && data.data) {
        _showStatus(data.data);
      } else {
        _shared_appendBot(
          `❌ Заказ по запросу «${query}» не найден.\n\nПроверьте номер заказа или email — они указаны в письме-подтверждении.`,
          ['🔄 Попробовать снова', '💬 Написать менеджеру']
        );
      }
    } catch {
      _showDemo(query);
    }
  }

  function _showDemo(query) {
    // Демо-режим (без WP)
    _showStatus({
      id: query,
      status: 'in_work',
      tariff: 'Профессиональный',
      lang_pair: 'RU → EN',
      pages: 12,
      deadline: new Date(Date.now() + 2 * 24 * 3600000).toLocaleDateString('ru-RU'),
      manager: 'Ольга',
      progress: 65,
    });
  }

  function _showStatus(order) {
    const STATUS = {
      new:        { label: '🆕 Принят',        color: '#4f6aff', pct: 5  },
      in_work:    { label: '⚙️ В работе',       color: '#c4922a', pct: order.progress || 50 },
      review:     { label: '🔍 На проверке',    color: '#a855f7', pct: 85 },
      ready:      { label: '✅ Готов',           color: '#22d46e', pct: 100 },
      delivered:  { label: '📤 Отправлен',      color: '#22d46e', pct: 100 },
      cancelled:  { label: '❌ Отменён',         color: '#ef4444', pct: 0  },
    };
    const s   = STATUS[order.status] || STATUS.new;
    const pct = s.pct;

    _shared_appendBotRich(
      `Нашла ваш заказ ${order.id || ''}`,
      `<div>
        <!-- Статус -->
        <div style="text-align:center;padding:10px 0 14px;border-bottom:1px solid rgba(82,108,255,0.1);margin-bottom:12px">
          <div style="font-size:1.6rem;font-weight:800;color:${s.color};margin-bottom:4px">${s.label}</div>
          <div style="height:8px;background:rgba(82,108,255,0.12);border-radius:4px;overflow:hidden;margin:8px 0">
            <div style="height:100%;width:${pct}%;background:${s.color};border-radius:4px;transition:width .6s"></div>
          </div>
          <div style="font-size:11px;color:rgba(140,155,210,0.6)">${pct}% выполнено</div>
        </div>
        <!-- Детали -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-bottom:12px">
          ${_statCell('📋 Тариф', order.tariff || '—')}
          ${_statCell('🌍 Языки', order.lang_pair || '—')}
          ${_statCell('📄 Объём', (order.pages || '—') + ' стр.')}
          ${_statCell('📅 Дедлайн', order.deadline || '—')}
          ${order.manager ? _statCell('👩‍💼 Менеджер', order.manager) : ''}
        </div>
        ${order.status === 'ready' || order.status === 'delivered'
          ? `<div style="background:rgba(34,212,110,0.1);border-radius:8px;padding:10px 12px;text-align:center">
               <div style="font-size:13px;font-weight:700;color:#22d46e">🎉 Перевод готов!</div>
               <div style="font-size:12px;color:rgba(140,155,210,0.7);margin-top:4px">Проверьте email — файл уже отправлен</div>
             </div>` : ''}
      </div>`,
      order.status === 'ready'
        ? ['📧 Где мой файл?', '🌟 Оставить отзыв', '🔤 Новый заказ']
        : ['💬 Написать менеджеру', '🔄 Обновить статус', '🔤 Новый заказ']
    );
  }

  function _statCell(label, val) {
    return `<div style="background:rgba(82,108,255,0.07);border-radius:8px;padding:7px 10px">
      <div style="font-size:10px;color:rgba(140,155,210,0.6);margin-bottom:2px">${label}</div>
      <div style="font-size:12.5px;font-weight:600;color:#dde4ff">${val}</div>
    </div>`;
  }

  return { checkIntent, start };
})();


/* ────────────────────────────────────────────────────────────
   SHARED HELPERS — общие для всех 4 модулей
   ──────────────────────────────────────────────────────────── */
function _shared_appendBot(text, replies) {
  const msgs = document.getElementById('messages');
  if (!msgs) return;
  const d = document.createElement('div');
  d.className = 'msg msg--bot';
  const t = new Date().toLocaleTimeString('ru-RU', { hour:'2-digit', minute:'2-digit' });
  d.innerHTML = `<div class="bubble bubble--bot">${text.replace(/\n/g,'<br>')}</div><div class="msg-time">${t}</div>`;
  msgs.appendChild(d);
  _shared_setQR(replies);
  msgs.scrollTop = msgs.scrollHeight;
}

function _shared_appendBotRich(text, html, replies) {
  const msgs = document.getElementById('messages');
  if (!msgs) return;
  const d = document.createElement('div');
  d.className = 'msg msg--bot';
  const t = new Date().toLocaleTimeString('ru-RU', { hour:'2-digit', minute:'2-digit' });
  d.innerHTML = `<div class="bubble bubble--bot bubble--rich">${text ? `<div class="bubble-lead">${text.replace(/\n/g,'<br>')}</div>` : ''}${html}</div><div class="msg-time">${t}</div>`;
  msgs.appendChild(d);
  _shared_setQR(replies);
  msgs.scrollTop = msgs.scrollHeight;
}

function _shared_setQR(arr) {
  const qr = document.getElementById('quick-replies');
  if (!qr) return;
  qr.innerHTML = '';
  (arr || []).forEach(text => {
    const btn = document.createElement('button');
    btn.className = 'qr-btn'; btn.textContent = text;
    btn.onclick = () => { if (typeof ChatEngine !== 'undefined') ChatEngine.handleUserInput(text); };
    qr.appendChild(btn);
  });
}

window.B2BFlow      = B2BFlow;
window.NDAFlow      = NDAFlow;
window.CertifiedFlow = CertifiedFlow;
window.OrderTracker  = OrderTracker;
