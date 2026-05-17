/* ============================================================
   REMARKA — AI ENGINE v2.0
   Модули: IntentService · SlotExtractor · StateMachine · ResponseBuilder · ClaudeAPI
   ============================================================ */

/* ────────────────────────────────────────────────────────────
   1. INTENT SERVICE
   ──────────────────────────────────────────────────────────── */
const IntentService = (() => {

  const PATTERNS = {
    order_translation: /перевест|перевод[^а]|translate|tradurr|нужен перевод|хочу перевод|заказ.*перевод|перевод.*заказ/i,
    pricing_request:   /цен|стоим|сколько|почём|тариф|прайс|рассчит|калькул|cost|price|quanto/i,
    upload_file:       /загруз|файл|прикреп|докумен|upload|file|allegat/i,
    check_quality:     /качество|вычитк|проверк|редактур|корректур|quality|revision|proofread/i,
    website_translation: /сайт|лендинг|страниц|website|landing|pagina|локализ/i,
    market_entry:      /выход.*рынок|рынок.*выход|локализ.*бизнес|market entry|expansion/i,
    rush_order:        /срочн|экспресс|быстр|asap|urgent|urgente|24 час|сегодня|завтра/i,
    nda_request:       /конфиденц|nda|секрет|тайн|confiden/i,
    payment:           /оплат|платёж|счёт|invoice|payment|pagamento/i,
    support:           /помог|вопрос|не понима|помощ|help|aiuto|поддержк/i,
    greeting:          /привет|здравствуй|добр|hello|ciao|hi\b|buongiorno/i,
  };

  function classify(text) {
    const t = text.trim();
    for (const [intent, re] of Object.entries(PATTERNS)) {
      if (re.test(t)) return intent;
    }
    return 'general';
  }

  function classifyMultiple(text) {
    const t = text.trim();
    return Object.entries(PATTERNS)
      .filter(([, re]) => re.test(t))
      .map(([intent]) => intent);
  }

  return { classify, classifyMultiple, PATTERNS };
})();


/* ────────────────────────────────────────────────────────────
   2. SLOT EXTRACTOR
   ──────────────────────────────────────────────────────────── */
const SlotExtractor = (() => {

  // Языки → код
  const LANG_MAP = {
    'русск': 'ru', 'russian': 'ru', 'russo': 'ru',
    'английск': 'en', 'english': 'en', 'inglese': 'en', 'англ': 'en',
    'немецк': 'de', 'german': 'de', 'tedesco': 'de', 'нем': 'de',
    'французск': 'fr', 'french': 'fr', 'francese': 'fr', 'франц': 'fr',
    'итальянск': 'it', 'italian': 'it', 'italiano': 'it', 'итал': 'it',
    'испанск': 'es', 'spanish': 'es', 'spagnolo': 'es', 'испан': 'es',
    'китайск': 'zh', 'chinese': 'zh', 'cinese': 'zh', 'китайс': 'zh',
    'японск': 'ja', 'japanese': 'ja', 'giapponese': 'ja',
    'арабск': 'ar', 'arabic': 'ar', 'arabo': 'ar',
    'корейск': 'ko', 'korean': 'ko', 'coreano': 'ko',
  };

  // Домены
  const DOMAIN_MAP = {
    'технич': 'technical', 'technical': 'technical', 'tecnico': 'technical',
    'инструкц': 'technical', 'руководств': 'technical', 'чертёж': 'technical',
    'юридич': 'legal', 'legal': 'legal', 'legale': 'legal',
    'договор': 'legal', 'контракт': 'legal', 'нотариал': 'legal',
    'медицин': 'medical', 'medical': 'medical', 'медицин': 'medical',
    'клинич': 'medical', 'фармац': 'medical', 'протокол': 'medical',
    'it': 'it', 'программ': 'it', 'software': 'it', 'интерфейс': 'it',
    'финанс': 'finance', 'finance': 'finance', 'finanziario': 'finance',
    'маркетинг': 'marketing', 'marketing': 'marketing', 'реклам': 'marketing',
    'патент': 'patent', 'patent': 'patent',
    'литератур': 'literary', 'literary': 'literary', 'книг': 'literary',
    'сайт': 'marketing', 'website': 'marketing', 'лендинг': 'marketing',
  };

  // Срочность
  const URGENCY_MAP = {
    'экспресс': 'express', 'express': 'express', 'urgente': 'express',
    '24 час': 'express', '24h': 'express',
    'срочн': 'urgent', 'urgent': 'urgent',
    '1-2 дн': 'urgent', '1–2 дн': 'urgent',
    'стандарт': 'standard', 'standard': 'standard',
    'не срочн': 'flexible', 'гибк': 'flexible', 'flexible': 'flexible',
    'суперэкспресс': 'superexp', '4 час': 'superexp', '6 час': 'superexp',
  };

  // SEO
  const SEO_MAP = {
    'seo': 'full', 'сео': 'full', 'поисков оптимизац': 'full',
    'ключевые слов': 'basic', 'мета': 'basic',
  };

  // Сложность
  const COMPLEXITY_MAP = {
    'простой': 'simple', 'simple': 'simple',
    'сложный': 'complex', 'complex': 'complex',
    'очень сложный': 'highly_complex', 'highly complex': 'highly_complex',
  };

  /**
   * Найти языки в тексте и определить пару src→dst
   */
  function extractLangPair(text) {
    const t = text.toLowerCase();

    // Паттерн: "с X на Y" / "X на Y" / "X → Y" / "X to Y"
    const arrowMatch = t.match(/([а-яёa-z]+)\s*(?:→|->|to|на)\s*([а-яёa-z]+)/);
    if (arrowMatch) {
      const src = matchLang(arrowMatch[1]);
      const dst = matchLang(arrowMatch[2]);
      if (src && dst) return `${src}-${dst}`;
    }

    // Паттерн "с X на Y"
    const fromToMatch = t.match(/с\s+([а-яёa-z]+(?:ого|ого|ского|кого))\s+на\s+([а-яёa-z]+(?:ий|ий|ский|кий|ийский))/);
    if (fromToMatch) {
      const src = matchLang(fromToMatch[1]);
      const dst = matchLang(fromToMatch[2]);
      if (src && dst) return `${src}-${dst}`;
    }

    // Если упоминается только один язык + есть "на" / русский контекст
    const langs = [];
    for (const [key, code] of Object.entries(LANG_MAP)) {
      if (t.includes(key)) langs.push(code);
    }
    if (langs.length >= 2) return `${langs[0]}-${langs[1]}`;
    if (langs.length === 1) {
      // предположить с русского если один язык
      const other = langs[0];
      if (other !== 'ru') return `ru-${other}`;
    }

    return null;
  }

  function matchLang(word) {
    if (!word) return null;
    const w = word.toLowerCase();
    for (const [key, code] of Object.entries(LANG_MAP)) {
      if (w.startsWith(key) || key.startsWith(w)) return code;
    }
    return null;
  }

  function extractDomain(text) {
    const t = text.toLowerCase();
    for (const [key, val] of Object.entries(DOMAIN_MAP)) {
      if (t.includes(key)) return val;
    }
    return null;
  }

  function extractUrgency(text) {
    const t = text.toLowerCase();
    for (const [key, val] of Object.entries(URGENCY_MAP)) {
      if (t.includes(key)) return val;
    }
    return null;
  }

  function extractPages(text) {
    const t = text.toLowerCase();
    // "15 страниц" / "15 стр" / "15 pages"
    const m = t.match(/(\d+(?:[.,]\d+)?)\s*(?:стр(?:аниц)?\.?|pages?|листов?|page)/);
    if (m) return parseFloat(m[1].replace(',', '.'));
    return null;
  }

  function extractChars(text) {
    const t = text.toLowerCase();
    // "5000 знаков" / "5000 символов" / "5 000 символов"
    const m = t.match(/(\d[\d\s]*)\s*(?:знаков?|символов?|characters?|chars?)/);
    if (m) return parseInt(m[1].replace(/\s/g, ''));
    return null;
  }

  function extractSeo(text) {
    const t = text.toLowerCase();
    for (const [key, val] of Object.entries(SEO_MAP)) {
      if (t.includes(key)) return val;
    }
    return null;
  }

  function extractComplexity(text) {
    const t = text.toLowerCase();
    for (const [key, val] of Object.entries(COMPLEXITY_MAP)) {
      if (t.includes(key)) return val;
    }
    return null;
  }

  /**
   * Извлечь все слоты из текста
   */
  function extract(text) {
    const slots = {};
    const lp = extractLangPair(text);
    if (lp) slots.langPair = lp;

    const dom = extractDomain(text);
    if (dom) slots.domain = dom;

    const urg = extractUrgency(text);
    if (urg) slots.urgency = urg;

    const pg = extractPages(text);
    if (pg) slots.pages = pg;

    const ch = extractChars(text);
    if (ch) slots.chars = ch;

    const seo = extractSeo(text);
    if (seo) slots.seo = seo;

    const cplx = extractComplexity(text);
    if (cplx) slots.complexity = cplx;

    return slots;
  }

  /**
   * Смёрджить слоты (новые перекрывают старые, null/undefined игнорируются)
   */
  function merge(existing, extracted) {
    const merged = { ...existing };
    for (const [k, v] of Object.entries(extracted)) {
      if (v !== null && v !== undefined) merged[k] = v;
    }
    return merged;
  }

  /**
   * Какого слота не хватает (для уточняющего вопроса)?
   */
  function getMissing(slots) {
    if (!slots.langPair) return 'langPair';
    if (!slots.domain)   return 'domain';
    if (!slots.pages && !slots.chars) return 'volume';
    if (!slots.urgency)  return 'urgency';
    return null;
  }

  return { extract, merge, getMissing, extractLangPair, extractDomain, DOMAIN_MAP, LANG_MAP };
})();


/* ────────────────────────────────────────────────────────────
   3. STATE MACHINE — воронка продаж
   ──────────────────────────────────────────────────────────── */
const StateMachine = (() => {

  // Состояния воронки
  const STATES = {
    GREETING:      'greeting',
    INTENT:        'intent',
    COLLECT_LANG:  'collect_lang',
    COLLECT_DOM:   'collect_domain',
    COLLECT_VOL:   'collect_volume',
    COLLECT_URG:   'collect_urgency',
    PRICING:       'pricing',
    CTA:           'cta',
    ORDER_EMAIL:   'order_email',
    ORDER_PHONE:   'order_phone',
    DONE:          'done',
    FREE_CHAT:     'free_chat',
  };

  let currentState = STATES.GREETING;
  let profile = loadProfile();

  function loadProfile() {
    try {
      return JSON.parse(localStorage.getItem('remarka_v2') || 'null') || emptyProfile();
    } catch { return emptyProfile(); }
  }

  function emptyProfile() {
    return {
      id: crypto.randomUUID(),
      slots: {},
      intent: null,
      history: [],
      geo: null,
      orders: [],
      createdAt: Date.now(),
    };
  }

  function save() {
    try { localStorage.setItem('remarka_v2', JSON.stringify(profile)); } catch {}
  }

  function getState()    { return currentState; }
  function getProfile()  { return profile; }
  function getSlots()    { return profile.slots; }

  function setState(s) {
    currentState = s;
  }

  function updateSlots(newSlots) {
    profile.slots = SlotExtractor.merge(profile.slots, newSlots);
    save();
  }

  function setIntent(intent) {
    profile.intent = intent;
    save();
  }

  function addToHistory(role, content) {
    profile.history.push({ role, content, ts: Date.now() });
    if (profile.history.length > 40) profile.history.shift();
    save();
  }

  function addOrder(order) {
    profile.orders.push({ ...order, id: crypto.randomUUID(), date: new Date().toISOString() });
    save();
  }

  function reset() {
    profile.slots   = {};
    profile.intent  = null;
    currentState    = STATES.GREETING;
    save();
  }

  /**
   * Следующее состояние после intent-классификации
   */
  function nextFromIntent(intent) {
    switch (intent) {
      case 'order_translation':
      case 'pricing_request':
      case 'website_translation':
      case 'rush_order':
        return decideCollectState(profile.slots);
      case 'upload_file':    return STATES.FREE_CHAT;
      case 'check_quality':  return STATES.FREE_CHAT;
      case 'nda_request':    return STATES.FREE_CHAT;
      case 'payment':        return STATES.FREE_CHAT;
      case 'support':        return STATES.FREE_CHAT;
      default:               return STATES.FREE_CHAT;
    }
  }

  /**
   * Определить какой слот собирать дальше
   */
  function decideCollectState(slots) {
    const missing = SlotExtractor.getMissing(slots);
    switch (missing) {
      case 'langPair': return STATES.COLLECT_LANG;
      case 'domain':   return STATES.COLLECT_DOM;
      case 'volume':   return STATES.COLLECT_VOL;
      case 'urgency':  return STATES.COLLECT_URG;
      default:         return STATES.PRICING;
    }
  }

  return {
    STATES, getState, setState, getProfile, getSlots,
    updateSlots, setIntent, addToHistory, addOrder, reset, save,
    nextFromIntent, decideCollectState,
  };
})();


/* ────────────────────────────────────────────────────────────
   4. RESPONSE BUILDER — строит UI-блоки ответа
   ──────────────────────────────────────────────────────────── */
const ResponseBuilder = (() => {

  // Локализация слотов → читаемые названия
  const DOMAIN_NAMES = {
    general: 'Общий', technical: 'Технический', legal: 'Юридический',
    medical: 'Медицинский', it: 'IT / ПО', finance: 'Финансовый',
    marketing: 'Маркетинговый', patent: 'Патентный', certified: 'С нотариальным заверением',
  };
  const URGENCY_NAMES = {
    flexible: 'Без срочности', standard: 'Стандарт (3–7 дн.)',
    urgent: 'Срочно (1–2 дн.)', express: 'Экспресс (24 ч)',
    superexp: 'Суперэкспресс (4–6 ч)',
  };
  const LANG_NAMES = {
    ru: '🇷🇺 Русский', en: '🇬🇧 Английский', de: '🇩🇪 Немецкий',
    fr: '🇫🇷 Французский', it: '🇮🇹 Итальянский', es: '🇪🇸 Испанский',
    zh: '🇨🇳 Китайский', ja: '🇯🇵 Японский', ar: '🇸🇦 Арабский', ko: '🇰🇷 Корейский',
  };

  function langPairLabel(pair) {
    if (!pair) return '—';
    const [src, dst] = pair.split('-');
    return `${LANG_NAMES[src] || src} → ${LANG_NAMES[dst] || dst}`;
  }

  // ── ВОПРОСЫ ДЛЯ СБОРА СЛОТОВ ──

  const SLOT_QUESTIONS = {
    langPair: {
      text: 'На какие языки нужен перевод?',
      replies: ['🇷🇺→🇬🇧 RU→EN', '🇬🇧→🇷🇺 EN→RU', '🇷🇺→🇩🇪 RU→DE', '🇷🇺→🇮🇹 RU→IT', '🇷🇺→🇫🇷 RU→FR', '🌍 Другой язык'],
    },
    domain: {
      text: 'Какой тип документа нужно перевести?',
      replies: ['📄 Технический', '⚖️ Юридический', '🏥 Медицинский', '💻 IT / ПО', '📊 Финансовый', '🌐 Сайт / Маркетинг', '📝 Другое'],
    },
    volume: {
      text: 'Примерный объём текста?\n_(1 стандартная страница = 1 800 знаков с пробелами)_',
      replies: ['📃 До 5 стр.', '📄 5–20 стр.', '📚 20–50 стр.', '📦 50–100 стр.', '🗂 Более 100 стр.', '📎 Прикрепить файл'],
    },
    urgency: {
      text: 'Когда нужна готовая работа?',
      replies: ['📅 Стандарт (3–7 дн.)', '🔥 Срочно (1–2 дня)', '⚡ Экспресс (24 ч)', '🚀 Суперэкспресс (4–6 ч)', '📆 Не срочно'],
    },
  };

  function getSlotQuestion(slotName) {
    return SLOT_QUESTIONS[slotName] || { text: 'Уточните детали заказа.', replies: [] };
  }

  // ── PRICE CARDS ──

  /**
   * Сгенерировать HTML для 3 ценовых карточек
   */
  function buildPriceCards(results, slots) {
    const { mtpe, human, premium } = results;

    const pairLabel = langPairLabel(slots.langPair);
    const domLabel  = DOMAIN_NAMES[slots.domain] || '—';
    const urgLabel  = URGENCY_NAMES[slots.urgency] || '—';
    const volLabel  = slots.pages
      ? `${slots.pages} стр.`
      : slots.chars
        ? `${PricingEngine.fmt(slots.chars)} зн. (${human.totalPages} стр.)`
        : '—';

    return `
<div class="price-summary-bar">
  <span>🌍 ${pairLabel}</span>
  <span>📋 ${domLabel}</span>
  <span>📄 ${volLabel}</span>
  <span>⏱ ${urgLabel}</span>
</div>
<div class="price-cards-wrap">

  <div class="price-card price-card--mtpe" onclick="ChatEngine.selectTariff('mtpe')">
    <div class="pc-badge">Эконом</div>
    <div class="pc-name">🤖 MTPE</div>
    <div class="pc-desc">AI-перевод + вычитка специалистом</div>
    <div class="pc-price">${PricingEngine.fmt(mtpe.perPage)} ₽<span>/стр.</span></div>
    <div class="pc-total">Итого: <strong>${PricingEngine.fmt(mtpe.total)} ₽</strong></div>
    <div class="pc-deadline">📅 до ${mtpe.deadlineStr}</div>
    ${mtpe.volumeDiscount ? `<div class="pc-discount">Скидка ${mtpe.volumeDiscount}</div>` : ''}
    <ul class="pc-features">
      <li>✓ Постредактура AI</li>
      <li>✓ Проверка терминологии</li>
      <li>✓ Базовое форматирование</li>
    </ul>
    <button class="pc-cta">Выбрать</button>
  </div>

  <div class="price-card price-card--human price-card--popular" onclick="ChatEngine.selectTariff('human')">
    <div class="pc-popular-tag">Популярный</div>
    <div class="pc-badge">Стандарт</div>
    <div class="pc-name">👨‍💼 Профессиональный</div>
    <div class="pc-desc">Переводчик-специалист в вашей отрасли</div>
    <div class="pc-price">${PricingEngine.fmt(human.perPage)} ₽<span>/стр.</span></div>
    <div class="pc-total">Итого: <strong>${PricingEngine.fmt(human.total)} ₽</strong></div>
    <div class="pc-deadline">📅 до ${human.deadlineStr}</div>
    ${human.volumeDiscount ? `<div class="pc-discount">Скидка ${human.volumeDiscount}</div>` : ''}
    <ul class="pc-features">
      <li>✓ Отраслевой переводчик</li>
      <li>✓ Глоссарий и стиль</li>
      <li>✓ Редакторская проверка</li>
      <li>✓ Полное форматирование</li>
    </ul>
    <button class="pc-cta pc-cta--primary">Выбрать</button>
  </div>

  <div class="price-card price-card--premium" onclick="ChatEngine.selectTariff('premium')">
    <div class="pc-badge">Премиум</div>
    <div class="pc-name">⭐ Premium Expert</div>
    <div class="pc-desc">Переводчик + редактор-носитель языка</div>
    <div class="pc-price">${PricingEngine.fmt(premium.perPage)} ₽<span>/стр.</span></div>
    <div class="pc-total">Итого: <strong>${PricingEngine.fmt(premium.total)} ₽</strong></div>
    <div class="pc-deadline">📅 до ${premium.deadlineStr}</div>
    ${premium.volumeDiscount ? `<div class="pc-discount">Скидка ${premium.volumeDiscount}</div>` : ''}
    <ul class="pc-features">
      <li>✓ Переводчик + носитель</li>
      <li>✓ 2 этапа проверки</li>
      <li>✓ Адаптация под ЦА</li>
      <li>✓ Приоритет + менеджер</li>
      <li>✓ Гарантия результата</li>
    </ul>
    <button class="pc-cta">Выбрать</button>
  </div>

</div>`;
  }

  // ── FILE RESULT BLOCK ──

  function buildFileResult(fileInfo) {
    const { fileName, chars, pages, fileType } = fileInfo;
    const pagesH = Math.ceil(chars / PricingEngine.PAGE_SIZE) || pages;
    return `
<div class="file-result-block">
  <div class="fr-icon">📄</div>
  <div class="fr-info">
    <div class="fr-name">${fileName}</div>
    <div class="fr-stats">
      <span>🔤 ${PricingEngine.fmt(chars)} знаков с пробелами</span>
      <span>📄 ${pagesH} стандартных страниц</span>
    </div>
  </div>
</div>`;
  }

  // ── CTA BLOCK ──

  function buildCTA(tariffName) {
    const names = { mtpe: 'MTPE', human: 'Профессиональный', premium: 'Premium Expert' };
    return `
<div class="cta-block">
  <div class="cta-title">Тариф: <strong>${names[tariffName] || tariffName}</strong></div>
  <div class="cta-text">Как вам удобнее оформить заказ?</div>
  <div class="cta-btns">
    <button class="cta-btn cta-btn--primary" onclick="ChatEngine.startOrder('email')">📧 Отправить на email</button>
    <button class="cta-btn" onclick="ChatEngine.startOrder('phone')">📞 Перезвонить мне</button>
    <button class="cta-btn" onclick="ChatEngine.startOrder('telegram')">💬 Написать в Telegram</button>
  </div>
</div>`;
  }

  // ── GEO GREETING ──

  function buildGeoGreeting(geo) {
    if (!geo) return '';
    return `Рад видеть вас из ${geo.city || geo.country || 'вашего региона'}! `;
  }

  return {
    getSlotQuestion, buildPriceCards, buildFileResult, buildCTA,
    langPairLabel, DOMAIN_NAMES, URGENCY_NAMES, LANG_NAMES, buildGeoGreeting,
  };
})();



/* ────────────────────────────────────────────────────────────
   5. OPENAI API (через PHP-прокси api/gpt.php)
   Прокси принимает: { text: string, system?: string }
   Прокси возвращает ответ OpenAI Responses API (gpt-4o)
   Поддерживает context-aware system prompt из PageContext
   ──────────────────────────────────────────────────────────── */
const OpenAIAPI = (() => {

  const PROXY_URL = 'api/gpt.php';

  /* ── System prompt с контекстом страницы ── */
  function buildSystemPrompt(slots, intent, uiLang, pageContextKey) {
    const langInstr = {
      ru: 'Отвечай только на русском языке.',
      en: 'Always reply in English.',
      it: 'Rispondi sempre in italiano.',
    }[uiLang] || 'Отвечай только на русском языке.';

    // Динамический хинт из PageContext (context.js)
    const ctxHint = (typeof PageContext !== 'undefined' && pageContextKey && pageContextKey !== 'general')
      ? PageContext.getSystemHint(pageContextKey)
      : '';

    const ctxLine = ctxHint ? '\n' + ctxHint + '\n' : '';

    return 'Ты Ольга — опытный персональный менеджер бюро переводов «Ремарка». ' + langInstr + '\n\n'
      + 'РОЛЬ: Ты одновременно AI-консультант, AI-продажник и AI-калькулятор. НЕ просто чатбот.\n'
      + ctxLine + '\n'
      + 'УСЛУГИ И ТАРИФЫ:\n'
      + '• MTPE (Вычитка AI) — от 350 ₽/стр. — постредактура машинного перевода\n'
      + '• Профессиональный — от 750 ₽/стр. — отраслевой переводчик-специалист\n'
      + '• Premium Expert — от 1350 ₽/стр. — переводчик + редактор-носитель языка\n\n'
      + 'СПЕЦИАЛИЗАЦИИ: технический, юридический, медицинский, IT/ПО, финансовый, патентный, маркетинговый.\n'
      + 'ЯЗЫКИ: RU↔EN/DE/FR/IT/ES/ZH/JA/AR/KO и другие.\n'
      + 'СРОЧНОСТЬ: Стандарт 3–7 дн., Срочно +35% за 1–2 дн., Экспресс +70% за 24 ч, Суперэкспресс +120% за 4–6 ч.\n'
      + 'ОБЪЁМ: 1 стандартная страница = 1800 знаков с пробелами.\n'
      + 'СКИДКИ: от 10 стр. −5%, от 20 стр. −8%, от 50 стр. −12%, от 100 стр. −15%.\n\n'
      + 'ТЕКУЩИЙ КОНТЕКСТ:\n'
      + '• Страница: ' + (pageContextKey || 'general') + '\n'
      + '• Намерение: ' + (intent || 'не определено') + '\n'
      + '• Слоты: ' + JSON.stringify(slots) + '\n\n'
      + 'ПРАВИЛА ПОВЕДЕНИЯ:\n'
      + '1. Задавай ТОЛЬКО ОДИН уточняющий вопрос за раз\n'
      + '2. Не перечисляй все возможные вопросы сразу\n'
      + '3. Предугадывай из контекста страницы\n'
      + '4. Если слоты заполнены — сразу предложи цену\n'
      + '5. Веди к конкретному действию (оформить заказ)\n'
      + '6. Будь дружелюбной, профессиональной, краткой (2–3 предложения)\n'
      + '7. Если клиент называет сумму — работай в рамках бюджета\n'
      + '8. При упоминании NDA — подтверди конфиденциальность\n\n'
      + 'ФОРМАТ ОТВЕТА: только текст, без markdown, без эмодзи-спама. Можно 1–2 эмодзи в сообщении.';
  }

  /**
   * Собрать текст диалога для поля text.
   * Прокси принимает одну строку — форматируем историю как чат-лог.
   */
  function buildDialogText(userText, conversationHistory) {
    const last12 = conversationHistory.slice(-12);
    if (last12.length === 0) return userText;

    const lines = last12.map(h => {
      const who = h.role === 'bot' ? 'Ольга' : 'Клиент';
      return '[' + who + ']: ' + h.content;
    });

    const lastEntry = last12[last12.length - 1];
    if (!lastEntry || lastEntry.content !== userText || lastEntry.role !== 'user') {
      lines.push('[Клиент]: ' + userText);
    }

    return lines.join('\n');
  }

  /**
   * Парсинг ответа — поддерживаем оба формата OpenAI.
   */
  function extractText(data) {
    // OpenAI Responses API: output[].content[].text
    if (data && data.output) {
      return data.output
        .filter(function(b) { return b.type === 'message'; })
        .flatMap(function(b) { return b.content || []; })
        .filter(function(c) { return c.type === 'output_text'; })
        .map(function(c) { return c.text; })
        .join('') || null;
    }
    // Chat Completions API fallback
    if (data && data.choices) {
      return (data.choices[0] && data.choices[0].message && data.choices[0].message.content) || null;
    }
    if (typeof data === 'string') return data;
    return null;
  }

  /**
   * Основной вызов.
   * pageContextKey передаётся из ChatEngine (новый аргумент).
   * chat.js вызывает без него — совместимость сохранена (undefined → general).
   */
  async function send(userText, slots, intent, uiLang, conversationHistory, pageContextKey) {
    const system     = buildSystemPrompt(slots, intent, uiLang, pageContextKey);
    const dialogText = buildDialogText(userText, conversationHistory);

    const response = await fetch(PROXY_URL, {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify({
        text:   dialogText,
        system: system,
      }),
    });

    if (!response.ok) {
      const errBody = await response.text().catch(function() { return ''; });
      throw new Error('Proxy error ' + response.status + ': ' + errBody);
    }

    const data = await response.json();
    if (data && data.error) throw new Error(data.error);

    const text = extractText(data);
    if (!text) throw new Error('Empty response from API');

    return text;
  }

  return { send, buildSystemPrompt };
})();

// Алиас — chat.js обращается к ClaudeAPI, не меняем его
const ClaudeAPI = OpenAIAPI;


/* ── ГЛОБАЛЬНЫЙ ЭКСПОРТ ── */
window.IntentService    = IntentService;
window.SlotExtractor    = SlotExtractor;
window.StateMachine     = StateMachine;
window.ResponseBuilder  = ResponseBuilder;
window.ClaudeAPI        = ClaudeAPI;   // = OpenAIAPI
window.OpenAIAPI        = OpenAIAPI;
