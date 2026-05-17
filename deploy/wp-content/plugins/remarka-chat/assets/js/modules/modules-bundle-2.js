/* ============================================================
   REMARKA MODULES BUNDLE 2 v1.0
   Содержит 5 модулей:
   9.  LoyaltyProgram   — программа лояльности и скидки
   10. MarketEntry      — выход на новый рынок
   11. FormatConverter  — подсказчик форматов файлов
   12. Reengagement     — реактивация вернувшихся клиентов
   15. PartnerFlow      — партнёрская программа
   ============================================================ */


/* ────────────────────────────────────────────────────────────
   9. LOYALTY PROGRAM
   ──────────────────────────────────────────────────────────── */
const LoyaltyProgram = (() => {
  'use strict';

  const TIERS = [
    { name: 'Новый клиент',   minOrders: 0,  minTotal: 0,      discount: 0,  color: '#8fa0d8', emoji: '🆕', perks: [] },
    { name: 'Серебро',        minOrders: 3,  minTotal: 10000,  discount: 5,  color: '#c0c0c0', emoji: '🥈', perks: ['Скидка 5%', 'Приоритет в очереди'] },
    { name: 'Золото',         minOrders: 10, minTotal: 50000,  discount: 10, color: '#ffd700', emoji: '🥇', perks: ['Скидка 10%', 'Персональный менеджер', 'Бесплатная вычитка 1×/мес'] },
    { name: 'Платина',        minOrders: 25, minTotal: 150000, discount: 15, color: '#e5e4e2', emoji: '💎', perks: ['Скидка 15%', 'Дедлайн-гарантия', 'Постоплата 30 дней', 'Глоссарий компании'] },
    { name: 'VIP',            minOrders: 50, minTotal: 500000, discount: 20, color: '#c4922a', emoji: '👑', perks: ['Скидка 20%', 'White-label переводы', 'SLA 99.9%', 'Выделенная команда'] },
  ];

  function load() {
    try {
      const raw = localStorage.getItem('remarka_loyalty');
      return raw ? JSON.parse(raw) : { orders: 0, totalSpent: 0, joinedAt: Date.now() };
    } catch { return { orders: 0, totalSpent: 0, joinedAt: Date.now() }; }
  }

  function save(data) {
    try { localStorage.setItem('remarka_loyalty', JSON.stringify(data)); } catch {}
  }

  function getTier(data) {
    let tier = TIERS[0];
    for (const t of TIERS) {
      if (data.orders >= t.minOrders && data.totalSpent >= t.minTotal) tier = t;
    }
    return tier;
  }

  function getNextTier(data) {
    const cur = getTier(data);
    const idx = TIERS.indexOf(cur);
    return idx < TIERS.length - 1 ? TIERS[idx + 1] : null;
  }

  function addOrder(amount) {
    const data = load();
    data.orders++;
    data.totalSpent += amount;
    save(data);
    const tier    = getTier(data);
    const oldTier = getTier({ orders: data.orders - 1, totalSpent: data.totalSpent - amount });
    if (tier.name !== oldTier.name) {
      // Повышение уровня!
      _celebrateTierUp(tier);
    }
    return tier;
  }

  function checkIntent(text) {
    return /\b(скидк|бонус|програм.*лояльн|loyalty|постоянн.*клиент|мой.*уровень|мои.*заказы|накопил)\b/i.test(text);
  }

  function showStatus() {
    const data = load();
    const tier  = getTier(data);
    const next  = getNextTier(data);
    const pct   = next
      ? Math.min(100, Math.round(Math.max(
          (data.orders / next.minOrders) * 100,
          (data.totalSpent / next.minTotal) * 100
        )))
      : 100;

    _shared_appendBotRich(
      '🎁 Ваш статус в программе лояльности',
      `<div>
        <!-- Текущий уровень -->
        <div style="text-align:center;padding:14px 0;border-bottom:1px solid rgba(82,108,255,0.1);margin-bottom:12px">
          <div style="font-size:3rem;margin-bottom:4px">${tier.emoji}</div>
          <div style="font-size:1.4rem;font-weight:800;color:${tier.color}">${tier.name}</div>
          ${tier.discount > 0 ? `<div style="display:inline-block;background:rgba(34,212,110,0.15);border-radius:20px;padding:4px 16px;font-size:15px;font-weight:800;color:#22d46e;margin-top:6px">-${tier.discount}% на все заказы</div>` : ''}
        </div>
        <!-- Статистика -->
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px;margin-bottom:12px">
          <div style="background:rgba(82,108,255,0.07);border-radius:8px;padding:8px;text-align:center">
            <div style="font-size:1.3rem;font-weight:800;color:#e8eeff">${data.orders}</div>
            <div style="font-size:10px;color:rgba(140,155,210,0.6)">Заказов</div>
          </div>
          <div style="background:rgba(82,108,255,0.07);border-radius:8px;padding:8px;text-align:center">
            <div style="font-size:1.1rem;font-weight:800;color:#06c0c8">${data.totalSpent.toLocaleString('ru-RU')} ₽</div>
            <div style="font-size:10px;color:rgba(140,155,210,0.6)">Потрачено</div>
          </div>
          <div style="background:rgba(82,108,255,0.07);border-radius:8px;padding:8px;text-align:center">
            <div style="font-size:1.3rem;font-weight:800;color:#c4922a">${tier.discount}%</div>
            <div style="font-size:10px;color:rgba(140,155,210,0.6)">Скидка</div>
          </div>
        </div>
        <!-- Привилегии -->
        ${tier.perks.length ? `<div style="background:rgba(0,0,0,0.2);border-radius:8px;padding:10px 12px;margin-bottom:10px">
          <div style="font-size:10px;color:rgba(140,155,210,0.6);text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">Ваши привилегии</div>
          ${tier.perks.map(p=>`<div style="font-size:12.5px;color:#22d46e;margin-bottom:3px">✓ ${p}</div>`).join('')}
        </div>` : ''}
        <!-- Прогресс до следующего -->
        ${next ? `<div style="background:rgba(82,108,255,0.07);border-radius:8px;padding:10px 12px">
          <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:6px">
            <span style="color:rgba(140,155,210,0.7)">До уровня ${next.emoji} ${next.name}</span>
            <span style="color:#a5b4fc;font-weight:600">${pct}%</span>
          </div>
          <div style="height:5px;background:rgba(82,108,255,0.12);border-radius:3px;overflow:hidden">
            <div style="height:100%;width:${pct}%;background:linear-gradient(90deg,#4f6aff,#7c5cfc);border-radius:3px"></div>
          </div>
          <div style="font-size:11px;color:rgba(140,155,210,0.5);margin-top:5px">
            Ещё ${next.minOrders - data.orders} заказов или ${(next.minTotal - data.totalSpent).toLocaleString('ru-RU')} ₽
          </div>
        </div>` : `<div style="text-align:center;font-size:13px;color:#c4922a;font-weight:600;padding:8px">👑 Вы на максимальном уровне!</div>`}
      </div>`,
      ['🔤 Новый заказ', '💰 Рассчитать стоимость', '💬 Задать вопрос']
    );
  }

  function _celebrateTierUp(tier) {
    setTimeout(() => {
      _shared_appendBotRich(
        `🎉 Поздравляем! Вы достигли уровня ${tier.emoji} ${tier.name}!`,
        `<div style="text-align:center;padding:10px 0">
          <div style="font-size:3rem;margin-bottom:8px">${tier.emoji}</div>
          <div style="font-size:15px;font-weight:700;color:${tier.color};margin-bottom:10px">${tier.name}</div>
          ${tier.discount ? `<div style="background:rgba(34,212,110,0.1);border-radius:8px;padding:8px 12px;display:inline-block;font-size:16px;font-weight:800;color:#22d46e;margin-bottom:10px">-${tier.discount}% на все заказы</div>` : ''}
          ${tier.perks.length ? `<div style="text-align:left">${tier.perks.map(p=>`<div style="font-size:12.5px;color:#22d46e;margin-bottom:4px">✓ ${p}</div>`).join('')}</div>` : ''}
        </div>`,
        ['🎁 Отлично!', '🔤 Новый заказ']
      );
    }, 1500);
  }

  function getDiscount() { return getTier(load()).discount; }

  return { checkIntent, showStatus, addOrder, getDiscount, load, getTier };
})();


/* ────────────────────────────────────────────────────────────
   10. MARKET ENTRY — Выход на новый рынок
   ──────────────────────────────────────────────────────────── */
const MarketEntry = (() => {
  'use strict';

  let state = { active: false, step: null, data: {} };
  const STEPS = ['target_market', 'industry', 'materials', 'website', 'deadline', 'budget', 'contact'];

  const QUESTIONS = {
    target_market: '🌍 На какой рынок планируете выйти? (страна / регион):',
    industry:      '🏭 В какой отрасли работает ваша компания?',
    materials:     '📦 Какие материалы нужно локализовать?',
    website:       '🌐 Есть ли сайт для локализации? (URL или «нет»)',
    deadline:      '📅 Планируемые сроки выхода на рынок:',
    budget:        '💰 Примерный бюджет на локализацию:',
    contact:       '📧 Email или телефон для отправки коммерческого предложения:',
  };

  const REPLIES = {
    target_market: ['🇩🇪 Германия', '🇮🇹 Италия', '🇫🇷 Франция', '🇺🇸 США / Англия', '🇨🇳 Китай', '🇦🇪 ОАЭ / Арабские страны', '🌍 Несколько рынков'],
    industry:      ['🏭 Производство', '💊 Фармацевтика', '⚖️ Юриспруденция', '💻 IT / SaaS', '🛒 E-commerce', '🏗️ Строительство', '🎓 Образование'],
    materials:     ['🌐 Сайт / лендинг', '📄 Документация', '🛍️ Каталог продукции', '📱 Приложение', '📹 Видео / субтитры', '📋 Юридические документы', '🗂 Всё перечисленное'],
    deadline:      ['🚀 Срочно (до 1 мес.)', '📅 1–3 месяца', '📆 3–6 месяцев', '🗓 Более 6 месяцев'],
    budget:        ['До 50 000 ₽', '50–200 000 ₽', '200–500 000 ₽', 'Более 500 000 ₽', 'Уточним с менеджером'],
  };

  function checkIntent(text) {
    return /\b(выход.*рынок|рынок.*выход|локализ.*бизнес|market.entry|expansion|международн.*рынок|зарубежн.*рынок|иностранн.*рынок)\b/i.test(text);
  }

  function start() {
    state = { active: true, step: 'target_market', data: {} };
    _shared_appendBot(
      '🌍 Выход на международный рынок — это комплексная задача!\n\nМы поможем с:\n\n' +
      '✅ Локализацией сайта и документов\n' +
      '✅ SEO-адаптацией под целевую аудиторию\n' +
      '✅ Переводом маркетинговых материалов\n' +
      '✅ Юридической документацией\n' +
      '✅ Культурной адаптацией контента\n\n' +
      'Расскажите о вашем проекте — подготовлю персональное предложение.',
      ['✅ Начать', '❌ Отмена']
    );
    _override();
  }

  let _orig = null;
  function _override() {
    if (_orig || typeof ChatEngine === 'undefined') return;
    _orig = ChatEngine.handleUserInput.bind(ChatEngine);
    ChatEngine.handleUserInput = (text) => {
      if (!state.active) { _restore(); _orig(text); return; }
      if (/отмена|❌/i.test(text)) { state.active=false; _restore(); _shared_appendBot('Хорошо! Чем могу помочь?', ['🔤 Нужен перевод']); return; }
      if (text === '✅ Начать') { _ask('target_market'); return; }
      state.data[state.step] = text;
      const idx = STEPS.indexOf(state.step);
      if (idx < STEPS.length - 1) { state.step = STEPS[idx+1]; _ask(state.step); }
      else { state.active=false; _restore(); _submit(); }
    };
  }
  function _restore() { if (_orig && typeof ChatEngine !== 'undefined') { ChatEngine.handleUserInput = _orig; _orig = null; } }

  function _ask(step) {
    state.step = step;
    _shared_appendBot(QUESTIONS[step], REPLIES[step] || []);
  }

  function _submit() {
    const d = state.data;
    _shared_appendBotRich(
      '🚀 Отличный проект! Вот что мы предлагаем:',
      `<div>
        <div style="background:rgba(79,106,255,0.08);border:1px solid rgba(82,108,255,0.2);border-radius:12px;padding:14px;margin-bottom:12px">
          <div style="font-size:13px;font-weight:700;color:#e8eeff;margin-bottom:10px">📋 Ваш проект</div>
          ${Object.entries(d).map(([k,v]) => v ? `<div style="display:flex;justify-content:space-between;font-size:12px;padding:4px 0;border-bottom:1px solid rgba(82,108,255,0.08)">
            <span style="color:rgba(140,155,210,0.7)">${QUESTIONS[k]?.replace(/[🌍🏭📦🌐📅💰📧]/,'').split('(')[0].trim()}</span>
            <span style="color:#dde4ff;font-weight:500;text-align:right;max-width:55%">${v}</span>
          </div>` : '').join('')}
        </div>
        <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:12px">
          ${[
            ['🌐 Локализация сайта + SEO', 'Адаптация под поисковые запросы целевой страны'],
            ['📄 Перевод документации', 'Технические, юридические и маркетинговые материалы'],
            ['🎨 Культурная адаптация', 'Учёт менталитета и норм целевого рынка'],
            ['📊 Глоссарий бренда', 'Единая терминология на всех языках'],
          ].map(([title, desc]) => `<div style="display:flex;gap:10px;background:rgba(8,16,50,0.5);border-radius:8px;padding:9px 11px">
            <div style="font-size:16px">${title.split(' ')[0]}</div>
            <div><div style="font-size:12.5px;font-weight:600;color:#e8eeff">${title.split(' ').slice(1).join(' ')}</div>
            <div style="font-size:11px;color:rgba(140,155,210,0.6)">${desc}</div></div>
          </div>`).join('')}
        </div>
        <div style="font-size:13px;color:rgba(160,170,220,0.8);line-height:1.6">
          Наш менеджер подготовит детальное КП с планом локализации и сметой в течение <b style="color:#e8eeff">24 часов</b>.
        </div>
      </div>`,
      ['📧 Жду КП на email', '📞 Перезвоните мне', '💬 Ещё вопросы']
    );
    _saveToWP({ type: 'market_entry', ...d });
  }

  function _saveToWP(data) {
    if (typeof RemarkaConfig === 'undefined') return;
    const body = new URLSearchParams({ action:'remarka_save_b2b', nonce:RemarkaConfig.nonce, data: JSON.stringify(data) });
    fetch(RemarkaConfig.ajaxUrl, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: body.toString() }).catch(()=>{});
  }

  return { checkIntent, start };
})();


/* ────────────────────────────────────────────────────────────
   11. FORMAT CONVERTER — Подсказчик форматов файлов
   ──────────────────────────────────────────────────────────── */
const FormatConverter = (() => {
  'use strict';

  const FORMAT_INFO = {
    pdf: { label:'PDF', icon:'📄', problems:['Сканы требуют OCR (+15% к цене)','Потеря форматирования при конвертации','Таблицы и колонки могут смещаться'], tips:['Присылайте PDF только из Word — не сканы','Если скан — качество 300 DPI+'], accepts: true },
    doc:  { label:'Word (.doc)', icon:'📝', problems:[], tips:['Идеальный формат — всё сохраняется'], accepts: true },
    docx: { label:'Word (.docx)', icon:'📝', problems:[], tips:['Лучший формат для перевода'], accepts: true },
    xlsx: { label:'Excel (.xlsx)', icon:'📊', problems:['Формулы не переводятся','Скрытые ячейки могут содержать текст'], tips:['Укажите какие столбцы переводить'], accepts: true },
    pptx: { label:'PowerPoint', icon:'📊', problems:['Текст в изображениях не переводится','Анимации сохраняются'], tips:['Лучше присылать с разблокированным редактированием'], accepts: true },
    html: { label:'HTML', icon:'🌐', problems:['Нужно не трогать теги','Атрибуты alt/title тоже переводятся'], tips:['Можно перевести отдельные строки'], accepts: true },
    txt:  { label:'TXT', icon:'📃', problems:['Нет форматирования'], tips:['Хороший формат для простых текстов'], accepts: true },
    srt:  { label:'Субтитры (.srt)', icon:'🎬', problems:['Нельзя менять тайминги','Длина строк важна'], tips:['Укажите целевой язык и аудиторию'], accepts: true },
    jpg:  { label:'Изображение', icon:'🖼️', problems:['Текст только через OCR (+25% к цене)','Результат зависит от качества скана'], tips:['Минимум 300 DPI, чёткое изображение'], accepts: true },
    png:  { label:'Изображение PNG', icon:'🖼️', problems:['Требуется OCR если есть текст'], tips:['Хорошо для скриншотов интерфейсов'], accepts: true },
    zip:  { label:'Архив ZIP', icon:'🗜️', problems:['Нужно распаковать и проверить форматы внутри'], tips:['Распакуйте и пришлите файлы напрямую'], accepts: true },
    unknown: { label:'Неизвестный формат', icon:'❓', problems:['Формат не распознан'], tips:['Конвертируйте в DOCX или PDF'], accepts: false },
  };

  function checkIntent(text) {
    return /\b(формат|конверт|pdf.*word|word.*pdf|какой.*формат|какие.*форматы|принимаете.*файл|загруз.*какой)\b/i.test(text);
  }

  function analyzeFile(fileName) {
    const ext = fileName.split('.').pop().toLowerCase();
    const info = FORMAT_INFO[ext] || FORMAT_INFO.unknown;

    _shared_appendBotRich(
      `📎 Анализ файла: ${fileName}`,
      `<div>
        <div style="display:flex;align-items:center;gap:10px;background:rgba(${info.accepts ? '34,212,110' : '239,68,68'},0.08);border:1px solid rgba(${info.accepts ? '34,212,110' : '239,68,68'},0.25);border-radius:10px;padding:11px 13px;margin-bottom:12px">
          <span style="font-size:2rem">${info.icon}</span>
          <div>
            <div style="font-size:13px;font-weight:700;color:#e8eeff">${info.label}</div>
            <div style="font-size:12px;color:${info.accepts ? '#22d46e' : '#ef4444'};font-weight:600">${info.accepts ? '✅ Принимаем этот формат' : '❌ Формат не поддерживается'}</div>
          </div>
        </div>
        ${info.problems.length ? `<div style="margin-bottom:10px">
          <div style="font-size:10px;color:rgba(239,68,68,0.7);text-transform:uppercase;letter-spacing:.05em;margin-bottom:5px">⚠️ Возможные сложности</div>
          ${info.problems.map(p=>`<div style="font-size:12.5px;color:#fca5a5;margin-bottom:3px">• ${p}</div>`).join('')}
        </div>` : ''}
        ${info.tips.length ? `<div style="background:rgba(79,106,255,0.08);border-radius:8px;padding:8px 12px">
          <div style="font-size:10px;color:rgba(140,155,210,0.6);text-transform:uppercase;letter-spacing:.05em;margin-bottom:5px">💡 Советы</div>
          ${info.tips.map(t=>`<div style="font-size:12.5px;color:#a5b4fc;margin-bottom:3px">• ${t}</div>`).join('')}
        </div>` : ''}
      </div>`,
      info.accepts
        ? ['✅ Загрузить этот файл', '💰 Рассчитать стоимость']
        : ['🔄 Конвертировать и загрузить', '💬 Нужна помощь']
    );
  }

  function showSupportedFormats() {
    _shared_appendBotRich(
      '📂 Поддерживаемые форматы файлов',
      `<div style="display:grid;grid-template-columns:1fr 1fr;gap:6px">
        ${Object.entries(FORMAT_INFO).filter(([k])=>k!=='unknown').map(([ext,info])=>`
          <div style="background:rgba(82,108,255,0.06);border-radius:8px;padding:7px 10px;display:flex;align-items:center;gap:7px">
            <span style="font-size:16px">${info.icon}</span>
            <div>
              <div style="font-size:12px;font-weight:600;color:#e8eeff">.${ext.toUpperCase()}</div>
              <div style="font-size:10.5px;color:rgba(140,155,210,0.6)">${info.label}</div>
            </div>
          </div>`).join('')}
      </div>
      <div style="margin-top:10px;font-size:12px;color:rgba(140,155,210,0.6)">
        ℹ️ Сканы (JPG/PNG/PDF) принимаются с дополнительной наценкой за OCR
      </div>`,
      ['📎 Загрузить файл', '💰 Рассчитать стоимость']
    );
  }

  return { checkIntent, analyzeFile, showSupportedFormats };
})();


/* ────────────────────────────────────────────────────────────
   12. REENGAGEMENT — Реактивация вернувшихся клиентов
   ──────────────────────────────────────────────────────────── */
const Reengagement = (() => {
  'use strict';

  const KEY = 'remarka_returning';

  function check() {
    const profile = typeof StateMachine !== 'undefined' ? StateMachine.getProfile() : null;
    const stored  = _load();

    if (!stored.lastVisit) { _save({ lastVisit: Date.now(), visits: 1 }); return; }

    const daysSince = (Date.now() - stored.lastVisit) / (1000 * 60 * 60 * 24);
    stored.visits  = (stored.visits || 0) + 1;
    stored.lastVisit = Date.now();
    _save(stored);

    // Вернулся после 7+ дней — реактивация
    if (daysSince > 7 && stored.visits > 1) {
      setTimeout(() => _greetReturning(stored, daysSince), 8000);
    }
    // Вернулся сегодня второй+ раз — тёплый привет
    else if (stored.visits > 2 && daysSince < 1) {
      setTimeout(() => _greetWarm(stored), 6000);
    }
  }

  function _greetReturning(stored, days) {
    const daysAgo = Math.round(days);
    const loyalty = typeof LoyaltyProgram !== 'undefined' ? LoyaltyProgram.load() : null;

    let text = `С возвращением! 👋\n\nМы не виделись ${daysAgo} ${_declDay(daysAgo)}. `;
    if (loyalty && loyalty.orders > 0) {
      text += `У вас ${loyalty.orders} заказ(ов) в истории и скидка ${LoyaltyProgram.getDiscount()}%.`;
    } else {
      text += 'Рады снова видеть вас!';
    }

    _shared_appendBot(text, [
      '🔤 Нужен перевод', '📦 Мой последний заказ', '💰 Рассчитать стоимость', '🎁 Моя скидка'
    ]);
  }

  function _greetWarm(stored) {
    _shared_appendBot(
      'Снова здравствуйте! 😊 Продолжим с того места, где остановились?',
      ['🔤 Продолжить заказ', '💬 Новый вопрос']
    );
  }

  function _declDay(n) {
    const m = n % 10;
    if (m === 1 && n !== 11) return 'день';
    if ([2,3,4].includes(m) && ![12,13,14].includes(n)) return 'дня';
    return 'дней';
  }

  function _load() { try { return JSON.parse(localStorage.getItem(KEY) || '{}'); } catch { return {}; } }
  function _save(d) { try { localStorage.setItem(KEY, JSON.stringify(d)); } catch {} }

  return { check };
})();


/* ────────────────────────────────────────────────────────────
   15. PARTNER FLOW — Партнёрская программа
   ──────────────────────────────────────────────────────────── */
const PartnerFlow = (() => {
  'use strict';

  let state = { active: false, step: null, data: {}, partnerType: null };

  const PARTNER_TYPES = {
    agency: {
      label: '🏢 Агентство',
      desc:  'Переводческое агентство ищет субподряд или партнёрство',
      perks: ['Приоритетное выполнение', 'Оптовые цены', 'API-интеграция', 'White-label переводы', 'Постоплата 30 дней'],
    },
    freelancer: {
      label: '👤 Фрилансер / реферал',
      desc:  'Рекомендуете нас клиентам и получаете комиссию',
      perks: ['10% с каждого заказа реферала', 'Личный кабинет', 'Выплата раз в месяц', 'Промокод для клиентов'],
    },
    whitelabel: {
      label: '🔖 White-label',
      desc:  'Переводы под вашим брендом',
      perks: ['Документы с вашим логотипом', 'NDA обязателен', 'Полная конфиденциальность', 'Персональная команда'],
    },
  };

  function checkIntent(text) {
    return /\b(партнёр|партнер|партнерств|субподряд|реферал|реферер|агентств.*перевод|white.?label|оптов.*цен|комисси|affiliate)\b/i.test(text);
  }

  function start() {
    state = { active: true, step: 'type', data: {} };
    _shared_appendBotRich(
      '🤝 Партнёрская программа «Ремарка»\n\nВыберите тип сотрудничества:',
      `<div style="display:flex;flex-direction:column;gap:8px;margin-top:8px">
        ${Object.entries(PARTNER_TYPES).map(([key, pt]) => `
          <div onclick="PartnerFlow.selectType('${key}')"
            style="background:rgba(8,16,50,0.6);border:1.5px solid rgba(82,108,255,0.2);border-radius:12px;
            padding:12px 14px;cursor:pointer;transition:all .2s"
            onmouseover="this.style.borderColor='rgba(82,108,255,0.6)'"
            onmouseout="this.style.borderColor='rgba(82,108,255,0.2)'">
            <div style="font-size:13px;font-weight:700;color:#e8eeff;margin-bottom:3px">${pt.label}</div>
            <div style="font-size:11.5px;color:rgba(140,155,210,0.7)">${pt.desc}</div>
          </div>`).join('')}
      </div>`,
      ['❌ Отмена']
    );
    _override();
  }

  function selectType(type) {
    state.partnerType = type;
    const pt = PARTNER_TYPES[type];

    _shared_appendBotRich(
      `${pt.label} — что вы получаете:`,
      `<div>
        <div style="display:flex;flex-direction:column;gap:5px;margin-bottom:14px">
          ${pt.perks.map(p => `<div style="display:flex;align-items:center;gap:8px;background:rgba(34,212,110,0.06);border-radius:8px;padding:7px 11px">
            <span style="color:#22d46e;font-size:14px">✓</span>
            <span style="font-size:13px;color:#dde4ff">${p}</span>
          </div>`).join('')}
        </div>
        <div style="font-size:12.5px;color:rgba(160,170,220,0.8)">Для оформления партнёрства нам нужны ваши контакты.</div>
      </div>`,
      ['✅ Оформить партнёрство', '◀ Выбрать другой тип']
    );
    state.step = 'name';
    _askStep();
  }

  const STEPS_Q = {
    name:    '👤 Имя и должность:',
    company: '🏢 Название компании или «фрилансер»:',
    email:   '📧 Email для связи:',
    phone:   '📞 Телефон:',
    volume:  '📄 Примерный ежемесячный объём (в страницах или ₽):',
    comment: '💬 Дополнительная информация (или «нет»):',
  };
  const STEPS_LIST = ['name','company','email','phone','volume','comment'];

  function _askStep() {
    const q = STEPS_Q[state.step];
    if (q) _shared_appendBot(q, []);
  }

  let _orig = null;
  function _override() {
    if (_orig || typeof ChatEngine === 'undefined') return;
    _orig = ChatEngine.handleUserInput.bind(ChatEngine);
    ChatEngine.handleUserInput = (text) => {
      if (!state.active) { _restore(); _orig(text); return; }
      if (/отмена|❌/i.test(text)) { state.active=false; _restore(); _shared_appendBot('Хорошо!', ['🔤 Нужен перевод']); return; }
      if (text === '◀ Выбрать другой тип') { state.step='type'; start(); return; }
      if (text === '✅ Оформить партнёрство' && state.step === 'name') { _askStep(); return; }
      if (STEPS_LIST.includes(state.step)) {
        state.data[state.step] = text;
        const idx = STEPS_LIST.indexOf(state.step);
        if (idx < STEPS_LIST.length - 1) { state.step = STEPS_LIST[idx+1]; _askStep(); }
        else { state.active=false; _restore(); _submit(); }
      } else { _restore(); _orig(text); }
    };
  }
  function _restore() { if (_orig && typeof ChatEngine !== 'undefined') { ChatEngine.handleUserInput = _orig; _orig = null; } }

  function _submit() {
    const pt = PARTNER_TYPES[state.partnerType] || {};
    _shared_appendBotRich(
      '🎉 Заявка на партнёрство отправлена!',
      `<div style="text-align:center;padding:10px 0 14px">
        <div style="font-size:2.5rem;margin-bottom:8px">🤝</div>
        <div style="font-size:14px;font-weight:600;color:#e8eeff;margin-bottom:8px">${pt.label || 'Партнёрство'}</div>
        <div style="font-size:12.5px;color:rgba(160,170,220,0.8);line-height:1.6">
          Наш менеджер рассмотрит заявку и свяжется с вами в течение <b style="color:#e8eeff">24 часов</b>.<br>
          Пришлём договор о партнёрстве и детальные условия.
        </div>
      </div>`,
      ['💬 Задать вопрос', '🔤 Нужен перевод']
    );
    if (typeof RemarkaConfig !== 'undefined') {
      const body = new URLSearchParams({ action:'remarka_save_b2b', nonce:RemarkaConfig.nonce, data: JSON.stringify({ type:'partner', partnerType:state.partnerType, ...state.data }) });
      fetch(RemarkaConfig.ajaxUrl, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: body.toString() }).catch(()=>{});
    }
  }

  return { checkIntent, start, selectType };
})();


/* ════════════════════════════════════════════════════════════
   ГЛОБАЛЬНАЯ МАРШРУТИЗАЦИЯ INTENT
   Вызывается из chat.js handleUserInput перед route()
════════════════════════════════════════════════════════════ */
function checkAllModuleIntents(text) {
  if (typeof ComplexityMeter !== 'undefined' && ComplexityMeter.checkIntent(text)) { ComplexityMeter.start(); return true; }
  if (typeof QualityChecker  !== 'undefined' && QualityChecker.checkIntent(text))  { QualityChecker.start();  return true; }
  if (typeof B2BFlow         !== 'undefined' && B2BFlow.checkIntent(text))         { B2BFlow.start();         return true; }
  if (typeof NDAFlow         !== 'undefined' && NDAFlow.checkIntent(text))         { NDAFlow.start();         return true; }
  if (typeof CertifiedFlow   !== 'undefined' && CertifiedFlow.checkIntent(text))   { CertifiedFlow.start();   return true; }
  if (typeof OrderTracker    !== 'undefined' && OrderTracker.checkIntent(text))    { OrderTracker.start();    return true; }
  if (typeof MarketEntry     !== 'undefined' && MarketEntry.checkIntent(text))     { MarketEntry.start();     return true; }
  if (typeof PartnerFlow     !== 'undefined' && PartnerFlow.checkIntent(text))     { PartnerFlow.start();     return true; }
  if (typeof LoyaltyProgram  !== 'undefined' && LoyaltyProgram.checkIntent(text))  { LoyaltyProgram.showStatus(); return true; }
  if (typeof FeedbackCollector !== 'undefined' && FeedbackCollector.checkIntent(text)) { FeedbackCollector.start(); return true; }
  return false;
}

window.LoyaltyProgram   = LoyaltyProgram;
window.MarketEntry      = MarketEntry;
window.FormatConverter  = FormatConverter;
window.Reengagement     = Reengagement;
window.PartnerFlow      = PartnerFlow;
window.checkAllModuleIntents = checkAllModuleIntents;
