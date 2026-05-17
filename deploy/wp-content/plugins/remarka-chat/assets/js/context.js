/* ============================================================
   REMARKA — PAGE CONTEXT MODULE v1.0
   Определяет контекст страницы и адаптирует поведение чата:
   • Автодетект по URL, мета-тегам, data-атрибутам
   • Динамические приветствия Ольги
   • Контекстные quick-reply сценарии
   • Предустановка слота domain
   • Proactive trigger (через 5 сек → умная подсказка)
   • Передача контекста в system prompt OpenAI
   ============================================================ */

const PageContext = (() => {

  /* ──────────────────────────────────────────────────────────
     1. КАРТА КОНТЕКСТОВ
     Каждый контекст определяет полный сценарий поведения чата
  ────────────────────────────────────────────────────────── */
  const CONTEXTS = {

    technical: {
      domain:       'technical',
      label:        'Технический перевод',
      icon:         '⚙️',
      color:        '#7c5cfc',

      // Приветственная последовательность (массив сообщений с задержками)
      greetings: [
        { text: 'Интересуетесь техническим переводом? 👋', delay: 600 },
        { text: 'Я Ольга, ваш персональный менеджер. Работаем с инструкциями, чертежами, руководствами и технической документацией.', delay: 2800 },
        { text: 'Могу сразу рассчитать стоимость — просто опишите задачу или загрузите файл.', delay: 5000 },
      ],

      // Начальные quick replies
      suggestions: [
        '📘 Перевести инструкцию',
        '📐 Перевести чертёж',
        '📋 Техническая документация',
        '🔧 Руководство по эксплуатации',
        '⚡ Узнать стоимость',
      ],

      // Proactive сообщение через N секунд
      proactive: {
        delay:   5000,
        text:    'Могу рассчитать стоимость технического перевода прямо сейчас — укажите язык и объём.',
        replies: ['📐 Рассчитать стоимость', '📘 Загрузить файл', '💬 Задать вопрос'],
      },

      // Что добавлять в system prompt
      systemHint: `КОНТЕКСТ СТРАНИЦЫ: Пользователь находится на странице технического перевода.
Приоритет — технические тексты: инструкции, руководства, чертежи, спецификации, стандарты (ISO, ГОСТ), патенты.
Сразу предлагай технический тариф. Упоминай опыт в технической документации.`,

      // Автопредустановка слота
      autoSlots: { domain: 'technical' },
    },

    legal: {
      domain:  'legal',
      label:   'Юридический перевод',
      icon:    '⚖️',
      color:   '#4f6aff',

      greetings: [
        { text: 'Нужен юридический перевод? ⚖️', delay: 600 },
        { text: 'Я Ольга. Работаем с договорами, контрактами, NDA, корпоративной документацией — строго конфиденциально.', delay: 3000 },
        { text: 'При необходимости подписываем NDA перед началом. Опишите вашу задачу.', delay: 5500 },
      ],

      suggestions: [
        '📄 Перевести договор',
        '🔒 Перевести NDA',
        '🏢 Учредительные документы',
        '⚖️ Судебные материалы',
        '🔐 Нужна конфиденциальность',
      ],

      proactive: {
        delay:   5000,
        text:    'Для юридических переводов важна точность терминологии. Готова рассчитать стоимость и сроки — какой документ нужно перевести?',
        replies: ['📄 Договор', '🔒 NDA', '🏢 Корпоративный документ', '💰 Стоимость'],
      },

      systemHint: `КОНТЕКСТ СТРАНИЦЫ: Пользователь на странице юридического перевода.
Приоритет — юридические документы: договоры, контракты, NDA, уставы, доверенности, судебные решения.
Подчёркивай: юридическая точность, конфиденциальность, при необходимости — нотариальное заверение.
Сразу уточняй тип документа и языковую пару.`,

      autoSlots: { domain: 'legal' },
    },

    medical: {
      domain:  'medical',
      label:   'Медицинский перевод',
      icon:    '🏥',
      color:   '#06c0c8',

      greetings: [
        { text: 'Нужен медицинский перевод? 🏥', delay: 600 },
        { text: 'Я Ольга. Переводим клинические исследования, инструкции к препаратам, медицинские карты и протоколы.', delay: 3000 },
        { text: 'Все переводчики — специалисты с медицинским образованием. Опишите вашу задачу.', delay: 5500 },
      ],

      suggestions: [
        '💊 Инструкция к препарату',
        '🔬 Клиническое исследование',
        '📋 Медицинская карта',
        '🩺 Эпикриз / выписка',
        '📊 Медицинский протокол',
      ],

      proactive: {
        delay:   5000,
        text:    'Медицинские переводы требуют специальной квалификации. Могу подобрать переводчика с профильным образованием — что нужно перевести?',
        replies: ['💊 Препарат/инструкция', '🔬 Исследование', '🩺 Мед. документ', '💰 Стоимость'],
      },

      systemHint: `КОНТЕКСТ СТРАНИЦЫ: Пользователь на странице медицинского перевода.
Приоритет — медицинская документация: клинические исследования, фармацевтика, медкарты, эпикризы, протоколы.
Подчёркивай: переводчики с медицинским образованием, соответствие стандартам GCP/GMP, точность терминологии.`,

      autoSlots: { domain: 'medical' },
    },

    it: {
      domain:  'it',
      label:   'IT-перевод / Локализация',
      icon:    '💻',
      color:   '#3884ff',

      greetings: [
        { text: 'Нужна локализация IT-продукта? 💻', delay: 600 },
        { text: 'Я Ольга. Переводим интерфейсы, документацию, строки кода, README и маркетинговые материалы для IT-компаний.', delay: 3000 },
        { text: 'Работаем с форматами: JSON, XML, XLIFF, PO, iOS Strings, Android XML. Опишите проект.', delay: 5500 },
      ],

      suggestions: [
        '🖥️ Локализация интерфейса',
        '📖 Техническая документация',
        '🔤 Строки перевода (i18n)',
        '📦 README / changelog',
        '🌐 Перевод сайта',
      ],

      proactive: {
        delay:   5000,
        text:    'Для IT-локализации важен контекст. Могу рассчитать стоимость и сроки — какой тип контента нужно перевести?',
        replies: ['🖥️ UI/интерфейс', '📖 Документация', '🌐 Сайт', '💰 Рассчитать'],
      },

      systemHint: `КОНТЕКСТ СТРАНИЦЫ: Пользователь на странице IT-перевода и локализации.
Приоритет — IT и software: интерфейсы, UI-строки, документация API, README, changelog, маркетинговые тексты для IT.
Упоминай поддержку i18n-форматов, опыт с GitHub, Figma, Lokalise. Предлагай специализированный IT-тариф.`,

      autoSlots: { domain: 'it' },
    },

    website: {
      domain:  'marketing',
      label:   'Перевод сайтов',
      icon:    '🌐',
      color:   '#a855f7',

      greetings: [
        { text: 'Хотите перевести сайт? 🌐', delay: 600 },
        { text: 'Я Ольга. Делаем полную локализацию: перевод контента, SEO-адаптацию, мета-теги — под реальную аудиторию целевого рынка.', delay: 3000 },
        { text: 'Сколько страниц примерно на сайте и на какой язык?', delay: 5500 },
      ],

      suggestions: [
        '🌍 Перевести лендинг',
        '🛒 Интернет-магазин',
        '📝 Блог / статьи',
        '🔍 SEO-адаптация',
        '📊 Сколько это стоит?',
      ],

      proactive: {
        delay:   5000,
        text:    'Для перевода сайта нужно учесть SEO — правильная локализация увеличивает трафик. Расскажите о вашем проекте?',
        replies: ['🌍 Полный сайт', '📝 Отдельные страницы', '🔍 Только SEO', '💰 Стоимость'],
      },

      systemHint: `КОНТЕКСТ СТРАНИЦЫ: Пользователь на странице перевода сайтов.
Приоритет — локализация веб-проектов: лендинги, интернет-магазины, блоги, SaaS-интерфейсы.
Обязательно уточняй SEO-требования. Предлагай SEO-адаптацию как апсейл. Спрашивай CMS (WordPress, Tilda, etc.).`,

      autoSlots: { domain: 'marketing', seo: 'basic' },
    },

    finance: {
      domain:  'finance',
      label:   'Финансовый перевод',
      icon:    '📊',
      color:   '#22d46e',

      greetings: [
        { text: 'Нужен финансовый перевод? 📊', delay: 600 },
        { text: 'Я Ольга. Переводим финансовую отчётность, проспекты эмиссии, инвестиционные меморандумы и банковскую документацию.', delay: 3000 },
        { text: 'Переводчики с финансовым образованием и опытом работы с МСФО/GAAP. Опишите задачу.', delay: 5500 },
      ],

      suggestions: [
        '📈 Финансовая отчётность',
        '🏦 Банковская документация',
        '💼 Инвестиционный меморандум',
        '📋 МСФО / GAAP отчёт',
        '💰 Узнать стоимость',
      ],

      proactive: {
        delay:   5000,
        text:    'Финансовые переводы требуют специфической терминологии МСФО/GAAP. Что нужно перевести?',
        replies: ['📈 Отчётность', '🏦 Банк. документ', '💼 Меморандум', '💰 Стоимость'],
      },

      systemHint: `КОНТЕКСТ СТРАНИЦЫ: Пользователь на странице финансового перевода.
Приоритет — финансовая документация: отчётность МСФО/GAAP, проспекты, меморандумы, банковские документы, аудиторские заключения.
Подчёркивай: переводчики с финансовым образованием, точность терминологии, соответствие международным стандартам.`,

      autoSlots: { domain: 'finance' },
    },

    // Контекст по умолчанию
    general: {
      domain:  'general',
      label:   'Бюро переводов',
      icon:    '🌍',
      color:   '#4f6aff',

      greetings: [
        { text: 'Здравствуйте! 👋\nЯ Ольга, буду вашим персональным менеджером.\nЧем могу помочь?', delay: 600 },
        { text: 'Вы можете общаться со мной голосовыми сообщениями.', delay: 3500 },
        { text: 'Кроме русского, я понимаю английский и итальянский 🇷🇺 🇬🇧 🇮🇹', delay: 5500 },
        { text: 'Опишите задачу — я предложу лучшее решение и рассчитаю стоимость.', delay: 7500 },
      ],

      suggestions: [
        '🔤 Нужен перевод',
        '✅ Вычитка AI-текста',
        '💰 Узнать стоимость',
        '🌐 Перевод сайта',
        '⚡ Срочный заказ',
      ],

      proactive: {
        delay:   8000,
        text:    'Кстати, могу рассчитать стоимость перевода прямо сейчас — просто опишите задачу или загрузите файл. 📎',
        replies: ['🔤 Нужен перевод', '📎 Загрузить файл', '💰 Стоимость'],
      },

      systemHint: '',
      autoSlots: {},
    },
  };

  /* ──────────────────────────────────────────────────────────
     2. ДЕТЕКТОР КОНТЕКСТА СТРАНИЦЫ
  ────────────────────────────────────────────────────────── */

  // Паттерны URL → контекст
  const URL_PATTERNS = [
    { patterns: [/tehnich|technical|techn|texnik|технич/i],              context: 'technical' },
    { patterns: [/yuridich|legal|juridic|юридич|pravo|право/i],          context: 'legal'     },
    { patterns: [/medic|medits|медицин|klinic|клиник/i],                 context: 'medical'   },
    { patterns: [/\bit\b|software|lokali|lokal|програм|локализ/i],       context: 'it'        },
    { patterns: [/sayt|website|web-site|сайт|landing|лендинг/i],         context: 'website'   },
    { patterns: [/financ|finans|финанс|msfo|gaap|бухгалт/i],             context: 'finance'   },
  ];

  // Паттерны мета-keywords/description → контекст
  const META_PATTERNS = [
    { keywords: ['технический перевод', 'technical translation', 'инструкция'],   context: 'technical' },
    { keywords: ['юридический перевод', 'legal translation', 'договор', 'NDA'],   context: 'legal'     },
    { keywords: ['медицинский перевод', 'medical translation', 'клинический'],     context: 'medical'   },
    { keywords: ['it перевод', 'локализация', 'software', 'интерфейс'],           context: 'it'        },
    { keywords: ['перевод сайта', 'website translation', 'seo локализация'],       context: 'website'   },
    { keywords: ['финансовый перевод', 'financial translation', 'мсфо'],           context: 'finance'   },
  ];

  /**
   * Определить контекст по текущей странице.
   * Приоритет: data-атрибут > URL > мета-теги > referrer > general
   */
  function detect() {
    // 1. data-атрибут на body или #chat-widget: data-page-context="technical"
    const dataCtx = document.body.dataset.pageContext
      || document.getElementById('chat-widget')?.dataset?.pageContext
      || document.querySelector('[data-page-context]')?.dataset?.pageContext;
    if (dataCtx && CONTEXTS[dataCtx]) return dataCtx;

    // 2. URL pathname + search
    const url = (window.location.pathname + window.location.search + window.location.hash).toLowerCase();
    for (const { patterns, context } of URL_PATTERNS) {
      if (patterns.some(re => re.test(url))) return context;
    }

    // 3. Мета-теги keywords и description
    const metaKw   = document.querySelector('meta[name="keywords"]')?.content || '';
    const metaDesc = document.querySelector('meta[name="description"]')?.content || '';
    const metaText = (metaKw + ' ' + metaDesc).toLowerCase();
    for (const { keywords, context } of META_PATTERNS) {
      if (keywords.some(kw => metaText.includes(kw.toLowerCase()))) return context;
    }

    // 4. Page title
    const title = document.title.toLowerCase();
    for (const { patterns, context } of URL_PATTERNS) {
      if (patterns.some(re => re.test(title))) return context;
    }

    // 5. Referrer (откуда пришёл пользователь)
    const ref = document.referrer.toLowerCase();
    for (const { patterns, context } of URL_PATTERNS) {
      if (patterns.some(re => re.test(ref))) return context;
    }

    return 'general';
  }

  /**
   * Получить полный объект контекста
   */
  function get(contextKey) {
    return CONTEXTS[contextKey] || CONTEXTS.general;
  }

  /* ──────────────────────────────────────────────────────────
     3. СИСТЕМА ПРИВЕТСТВИЙ
  ────────────────────────────────────────────────────────── */

  /**
   * Запустить последовательность приветственных сообщений.
   * @param {string}   contextKey  — ключ контекста
   * @param {Function} appendBot   — chat.js:appendBotMessage(text, replies)
   * @param {Function} setReplies  — chat.js:setQuickReplies(arr)
   */
  function runGreetings(contextKey, appendBot, setReplies) {
    const ctx = get(contextKey);
    const msgs = ctx.greetings;

    msgs.forEach((item, idx) => {
      const isLast   = idx === msgs.length - 1;
      const replies  = isLast ? ctx.suggestions : [];
      setTimeout(() => {
        appendBot(item.text, replies);
      }, item.delay);
    });

    // Запустить proactive trigger
    scheduleProactive(contextKey, appendBot, setReplies);
  }

  /* ──────────────────────────────────────────────────────────
     4. PROACTIVE TRIGGER
     Если пользователь на странице > N секунд и ещё не писал
  ────────────────────────────────────────────────────────── */

  let proactiveTimer  = null;
  let userHasTyped    = false;

  function scheduleProactive(contextKey, appendBot, setReplies) {
    const ctx = get(contextKey);
    if (!ctx.proactive) return;

    // Ждём последнего приветствия + delay proactive
    const lastGreetingDelay = Math.max(...ctx.greetings.map(g => g.delay));
    const totalDelay = lastGreetingDelay + ctx.proactive.delay;

    proactiveTimer = setTimeout(() => {
      if (!userHasTyped) {
        appendBot(ctx.proactive.text, ctx.proactive.replies);
      }
    }, totalDelay);
  }

  /** Вызвать при первом вводе пользователя — отменяет proactive */
  function onUserInteraction() {
    userHasTyped = true;
    if (proactiveTimer) {
      clearTimeout(proactiveTimer);
      proactiveTimer = null;
    }
  }

  /* ──────────────────────────────────────────────────────────
     5. SYSTEM PROMPT BUILDER
     Добавляет контекстный хинт к основному промпту
  ────────────────────────────────────────────────────────── */

  /**
   * Вернуть строку-расширение для system prompt.
   * Вызывается из OpenAIAPI.buildSystemPrompt()
   */
  function getSystemHint(contextKey) {
    return get(contextKey).systemHint || '';
  }

  /**
   * Вернуть автоматические слоты для предустановки.
   * Вызывается из ChatEngine.init()
   */
  function getAutoSlots(contextKey) {
    return get(contextKey).autoSlots || {};
  }

  /* ──────────────────────────────────────────────────────────
     6. ВСПОМОГАТЕЛЬНЫЕ
  ────────────────────────────────────────────────────────── */

  /** Список всех контекстных слов для quick-reply маппинга */
  function getSuggestions(contextKey) {
    return get(contextKey).suggestions || [];
  }

  /** Человекочитаемое название */
  function getLabel(contextKey) {
    return get(contextKey).label || 'Бюро переводов';
  }

  /** Цвет акцента контекста (для UI тематизации) */
  function getColor(contextKey) {
    return get(contextKey).color || '#4f6aff';
  }

  /* ──────────────────────────────────────────────────────────
     PUBLIC API
  ────────────────────────────────────────────────────────── */
  return {
    detect,
    get,
    runGreetings,
    onUserInteraction,
    getSystemHint,
    getAutoSlots,
    getSuggestions,
    getLabel,
    getColor,
    CONTEXTS,
  };

})();

window.PageContext = PageContext;
