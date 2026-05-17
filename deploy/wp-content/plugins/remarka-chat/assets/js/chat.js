/* ============================================================
   REMARKA — CHAT ENGINE v2.0
   UI · Voice · EmailJS · Geo · Воронка
   ============================================================ */

const ChatEngine = (() => {

  // ── STATE ──────────────────────────────────────────────────
  let uiLang       = 'ru';
  let micActive    = false;
  let recognition  = null;
  let selectedTariff = null;
  let isThinking   = false;
  let conversationHistory = [];
  let geo          = null;
  let pageContextKey = 'general';

  // DOM refs
  const $ = id => document.getElementById(id);

  // ── INIT ───────────────────────────────────────────────────
  async function init() {
    // 1. Детект контекста страницы (context.js)
    if (typeof PageContext !== 'undefined') {
      pageContextKey = PageContext.detect();
      // Предустанавливаем слоты из контекста (например domain: 'technical')
      const autoSlots = PageContext.getAutoSlots(pageContextKey);
      if (Object.keys(autoSlots).length > 0) {
        StateMachine.updateSlots(autoSlots);
      }
    }

    initSpeech();
    await detectGeo();
    await runGreeting();
  }

  // ── GEO DETECTION ─────────────────────────────────────────
  async function detectGeo() {
    try {
      const r = await fetch('https://ipapi.co/json/');
      if (r.ok) {
        geo = await r.json();
        StateMachine.getProfile().geo = geo;
        StateMachine.save();
      }
    } catch { /* silent */ }
  }

  // ── GREETING SEQUENCE ──────────────────────────────────────
  async function runGreeting() {
    if (typeof PageContext !== 'undefined') {
      // Контекстное приветствие через PageContext
      // Если гео известен — добавляем в первое сообщение
      const geoHint = (geo && geo.city) ? ' Вижу, вы из ' + geo.city + '.' : '';

      // Для general контекста — добавляем geo в первое сообщение
      if (pageContextKey === 'general' && geoHint) {
        await delay(600);
        appendBotMessage('Здравствуйте! 👋\nЯ Ольга, буду вашим персональным менеджером.' + geoHint + '\nЧем могу помочь?', []);
        // Остальные сообщения general из PageContext (начиная со второго)
        const ctx = PageContext.get('general');
        ctx.greetings.slice(1).forEach(function(item, idx) {
          const isLast = idx === ctx.greetings.length - 2;
          setTimeout(function() {
            appendBotMessage(item.text, isLast ? ctx.suggestions : []);
          }, item.delay);
        });
      } else {
        // Для всех остальных контекстов — стандартная последовательность PageContext
        PageContext.runGreetings(pageContextKey, appendBotMessage, setQuickReplies);
      }
    } else {
      // Fallback — оригинальное приветствие без PageContext
      const geoHint = (geo && geo.city) ? ' Вижу, вы из ' + geo.city + '.' : '';
      await delay(600);
      appendBotMessage('Здравствуйте! 👋\nЯ Ольга, буду вашим персональным менеджером.' + geoHint + '\nЧем могу помочь?', []);
      await delay(3500);
      appendBotMessage('Вы можете общаться со мной голосовыми сообщениями.', []);
      await delay(2500);
      appendBotMessage('Кроме русского, я понимаю английский и итальянский 🇷🇺 🇬🇧 🇮🇹', []);
      await delay(2200);
      appendBotMessage('Опишите задачу — я предложу лучшее решение и рассчитаю стоимость.', [
        '🔤 Нужен перевод', '✅ Вычитка AI-текста', '💰 Узнать стоимость',
        '🌐 Перевод сайта', '⚡ Срочный заказ',
      ]);
    }

    StateMachine.setState(StateMachine.STATES.INTENT);
  }

  // ── MESSAGE APPEND ─────────────────────────────────────────

  function appendBotMessage(text, replies, htmlContent) {
    const wrap = $('messages');
    const id   = 'msg-' + Date.now() + Math.random().toString(36).slice(2);

    const div = document.createElement('div');
    div.className = 'msg msg--bot';
    div.id = id;

    const time = now();
    const bubbleContent = htmlContent
      ? htmlContent
      : `<div class="bubble bubble--bot">${formatText(text)}</div>`;

    div.innerHTML = `
      ${bubbleContent}
      <div class="msg-time">${time}</div>`;

    wrap.appendChild(div);

    if (replies && replies.length > 0) {
      setQuickReplies(replies);
    }

    scrollBottom();
    conversationHistory.push({ role: 'bot', content: text || '[rich content]' });
    StateMachine.addToHistory('bot', text || '[rich content]');
    return id;
  }

  function appendUserMessage(text) {
    const wrap = $('messages');
    const div  = document.createElement('div');
    div.className = 'msg msg--user';
    div.innerHTML = `
      <div class="bubble bubble--user">${formatText(text)}</div>
      <div class="msg-time">${now()}</div>`;
    wrap.appendChild(div);
    setQuickReplies([]);
    scrollBottom();
    conversationHistory.push({ role: 'user', content: text });
    StateMachine.addToHistory('user', text);
  }

  function appendPriceCards(results, slots) {
    const html = ResponseBuilder.buildPriceCards(results, slots);
    appendBotMessage('Вот расчёт стоимости для вашего заказа 👇', [], `
      <div class="bubble bubble--bot bubble--rich">
        <div class="bubble-lead">Вот расчёт стоимости для вашего заказа 👇</div>
        ${html}
      </div>`);
    // Устанавливаем CTA — следующие сообщения не будут пересчитывать автоматически
    StateMachine.setState(StateMachine.STATES.CTA);
    // Quick replies для выхода из воронки
    setQuickReplies([
      '📧 Оформить заказ',
      '🔄 Изменить параметры',
      '🆕 Новый расчёт',
      '💬 Задать вопрос',
    ]);
  }

  function appendFileResult(fileInfo) {
    const html = ResponseBuilder.buildFileResult(fileInfo);
    const text = `Файл получен: ${fileInfo.fileName}, ${fileInfo.chars} знаков, ${fileInfo.pages} стр.`;
    appendBotMessage(text, [], `
      <div class="bubble bubble--bot bubble--rich">
        <div class="bubble-lead">📄 Файл получен и проанализирован!</div>
        ${html}
      </div>`);
  }

  function showTyping() {
    if ($('typing-indicator')) return;
    const wrap = $('messages');
    const div  = document.createElement('div');
    div.className = 'msg msg--bot'; div.id = 'typing-indicator';
    div.innerHTML = `<div class="typing-bubble"><span></span><span></span><span></span></div>`;
    wrap.appendChild(div);
    scrollBottom();
  }

  function hideTyping() {
    $('typing-indicator')?.remove();
  }

  // ── QUICK REPLIES ──────────────────────────────────────────

  function setQuickReplies(arr) {
    const qr = $('quick-replies');
    qr.innerHTML = '';
    arr.forEach(text => {
      const btn = document.createElement('button');
      btn.className  = 'qr-btn';
      btn.textContent = text;
      btn.onclick = () => handleUserInput(text);
      qr.appendChild(btn);
    });
  }

  // ── MAIN INPUT HANDLER ─────────────────────────────────────

  async function handleUserInput(text) {
    if (!text?.trim() || isThinking) return;
    const t = text.trim();

    // Сообщаем PageContext что пользователь начал диалог → отменяем proactive
    if (typeof PageContext !== 'undefined') PageContext.onUserInteraction();

    appendUserMessage(t);

    // Маппинг quick replies → слоты
    const quickSlots = mapQuickReply(t);
    if (Object.keys(quickSlots).length > 0) {
      StateMachine.updateSlots(quickSlots);
    }

    // Извлечь слоты из свободного текста
    const extracted = SlotExtractor.extract(t);
    StateMachine.updateSlots(extracted);

    // Классифицировать intent
    const intent = IntentService.classify(t);
    if (intent !== 'general') StateMachine.setIntent(intent);

    // Маршрутизация
    await route(t, intent);
  }

  async function route(text, intent) {
    const state  = StateMachine.getState();
    const slots  = StateMachine.getSlots();
    const STATES = StateMachine.STATES;

    // ── Сброс после завершённого расчёта ──
    const resetTriggers = [
      'новый заказ', 'другой вопрос', 'новый расчёт', 'изменить параметры',
      'другой документ', 'ещё один', 'reset', 'начать заново', '🆕 новый расчёт',
      '🔄 изменить параметры', 'рассчитать ещё раз',
    ];
    const isReset = resetTriggers.some(r => text.toLowerCase().includes(r.toLowerCase()));

    if (isReset) {
      StateMachine.reset();
      StateMachine.setState(STATES.INTENT);
      showTyping(); await delay(500); hideTyping();
      appendBotMessage(
        'Хорошо, начнём новый расчёт! Опишите задачу — тип документа, языки, объём.',
        ['🔤 Нужен перевод', '📎 Загрузить файл', '💰 Назвать параметры']
      );
      return;
    }

    // Явный запрос нового заказа после CTA
    if ((state === STATES.CTA || state === STATES.DONE) &&
        (intent === 'order_translation' || intent === 'pricing_request')) {
      StateMachine.reset();
      StateMachine.setState(STATES.INTENT);
    }

    // ── Специальные быстрые маршруты ──

    // Срочный заказ
    if (intent === 'rush_order' && !slots.urgency) {
      StateMachine.updateSlots({ urgency: 'express' });
    }

    // Запрос тарифов
    if (text.toLowerCase().includes('тариф') || text.toLowerCase().includes('план')) {
      showTyping();
      await delay(700);
      hideTyping();
      await showTariffInfo();
      return;
    }

    // Сайт → marketing domain
    if (intent === 'website_translation' && !slots.domain) {
      StateMachine.updateSlots({ domain: 'marketing' });
    }

    // NDA
    if (intent === 'nda_request') {
      showTyping(); await delay(800); hideTyping();
      appendBotMessage(
        'Мы работаем строго конфиденциально. 🔒\nПо запросу подписываем NDA перед началом работы.\nВсе переводчики подписывают соглашение о неразглашении.',
        ['✅ Хорошо, продолжим', '📋 Нужен перевод']
      );
      return;
    }

    // Уже есть все данные для расчёта → цена
    // НО только если state не CTA/DONE (иначе зацикливание)
    const forbiddenStates = [STATES.CTA, STATES.ORDER_EMAIL, STATES.ORDER_PHONE, STATES.DONE];
    if (PricingEngine.isReady(slots) && !forbiddenStates.includes(state)) {
      showTyping(); await delay(900); hideTyping();
      const results = PricingEngine.calculate(slots);
      appendPriceCards(results, slots);
      // Переводим в CTA — больше не пересчитываем автоматически
      StateMachine.setState(STATES.CTA);
      return;
    }

    // Нужно собрать слот
    const missing = SlotExtractor.getMissing(slots);
    if (
      missing &&
      (intent === 'order_translation' || intent === 'pricing_request' ||
       intent === 'website_translation' || intent === 'rush_order' ||
       state === STATES.COLLECT_LANG || state === STATES.COLLECT_DOM ||
       state === STATES.COLLECT_VOL  || state === STATES.COLLECT_URG)
    ) {
      const nextState = StateMachine.decideCollectState(slots);
      StateMachine.setState(nextState);
      const q = ResponseBuilder.getSlotQuestion(missing);
      showTyping(); await delay(650); hideTyping();
      appendBotMessage(q.text, q.replies);
      return;
    }

    // Fallback → Claude AI
    await callAI(text);
  }

  // ── CALL CLAUDE AI ─────────────────────────────────────────

  async function callAI(text) {
    isThinking = true;
    showTyping();
    try {
      const reply = await ClaudeAPI.send(
        text,
        StateMachine.getSlots(),
        StateMachine.getProfile().intent,
        uiLang,
        conversationHistory,
        pageContextKey,
      );
      hideTyping();
      isThinking = false;

      // Попробовать извлечь слоты из ответа тоже
      const aiSlots = SlotExtractor.extract(reply);
      StateMachine.updateSlots(aiSlots);

      // Если после ответа AI слоты готовы → показать цену
      // Но только если ещё не в состоянии CTA/DONE (избегаем зацикливания)
      const curState = StateMachine.getState();
      const noAutoPrice = [
        StateMachine.STATES.CTA,
        StateMachine.STATES.ORDER_EMAIL,
        StateMachine.STATES.ORDER_PHONE,
        StateMachine.STATES.DONE,
      ];
      if (PricingEngine.isReady(StateMachine.getSlots()) && !noAutoPrice.includes(curState)) {
        appendBotMessage(reply, []);
        await delay(400);
        showTyping(); await delay(800); hideTyping();
        const results = PricingEngine.calculate(StateMachine.getSlots());
        appendPriceCards(results, StateMachine.getSlots());
        StateMachine.setState(StateMachine.STATES.CTA);
      } else {
        const missing = SlotExtractor.getMissing(StateMachine.getSlots());
        const contextReplies = missing
          ? ResponseBuilder.getSlotQuestion(missing).replies
          : getContextReplies();
        appendBotMessage(reply, contextReplies.slice(0, 5));
      }
    } catch (e) {
      hideTyping();
      isThinking = false;
      appendBotMessage(
        'Небольшой сбой связи 😔 Попробуйте ещё раз или опишите задачу подробнее.',
        ['🔄 Повторить', '💰 Стоимость', '📋 Тарифы']
      );
    }
  }

  // ── TARIFF INFO ────────────────────────────────────────────

  async function showTariffInfo() {
    appendBotMessage('', [], `
      <div class="bubble bubble--bot bubble--rich">
        <div class="bubble-lead">Наши тарифы — выберите подходящий 👇</div>
        <div class="tariff-info-cards">
          <div class="tic tic--mtpe" onclick="ChatEngine.selectTariff('mtpe')">
            <div class="tic-icon">🤖</div>
            <div class="tic-name">MTPE</div>
            <div class="tic-price">от 350 ₽/стр.</div>
            <div class="tic-desc">Вычитка AI-перевода специалистом</div>
          </div>
          <div class="tic tic--human" onclick="ChatEngine.selectTariff('human')">
            <div class="tic-icon">👨‍💼</div>
            <div class="tic-name">Профессиональный</div>
            <div class="tic-price">от 750 ₽/стр.</div>
            <div class="tic-desc">Отраслевой переводчик-специалист</div>
          </div>
          <div class="tic tic--premium" onclick="ChatEngine.selectTariff('premium')">
            <div class="tic-icon">⭐</div>
            <div class="tic-name">Premium Expert</div>
            <div class="tic-price">от 1 350 ₽/стр.</div>
            <div class="tic-desc">Переводчик + носитель языка</div>
          </div>
        </div>
      </div>`, );
    setQuickReplies(['🧮 Рассчитать стоимость', '📎 Загрузить документ', '💬 Задать вопрос']);
  }

  // ── TARIFF SELECTION ───────────────────────────────────────

  function selectTariff(tariff) {
    selectedTariff = tariff;
    StateMachine.updateSlots({ tariff });
    const names = { mtpe: 'MTPE', human: 'Профессиональный', premium: 'Premium Expert' };
    appendUserMessage(`Выбираю тариф: ${names[tariff]}`);

    // Если цена уже рассчитана — показать CTA
    const slots = StateMachine.getSlots();
    if (PricingEngine.isReady(slots)) {
      showTyping();
      setTimeout(() => {
        hideTyping();
        const ctaHtml = ResponseBuilder.buildCTA(tariff);
        appendBotMessage('Отличный выбор!', [], `
          <div class="bubble bubble--bot bubble--rich">
            <div class="bubble-lead">Отличный выбор! Оформим заказ?</div>
            ${ctaHtml}
          </div>`);
      }, 600);
    } else {
      // Нужно уточнить параметры
      const missing = SlotExtractor.getMissing(slots);
      if (missing) {
        const q = ResponseBuilder.getSlotQuestion(missing);
        showTyping();
        setTimeout(() => {
          hideTyping();
          appendBotMessage(q.text, q.replies);
        }, 600);
      }
    }
  }

  // ── ORDER FLOW ─────────────────────────────────────────────

  function startOrder(channel) {
    const STATES = StateMachine.STATES;
    if (channel === 'email') {
      StateMachine.setState(STATES.ORDER_EMAIL);
      appendBotMessage('Укажите ваш email — вышлю подробное КП и счёт в течение 15 минут 📧', []);
    } else if (channel === 'phone') {
      StateMachine.setState(STATES.ORDER_PHONE);
      appendBotMessage('Укажите номер телефона — перезвоним в течение 5 минут 📞', []);
    } else if (channel === 'telegram') {
      window.open('https://t.me/remarka_bureau', '_blank');
      appendBotMessage('Открываю Telegram... Там вас уже ждёт менеджер! 💬', []);
    }
  }

  function handleContactInput(text) {
    const state = StateMachine.getState();
    const STATES = StateMachine.STATES;

    if (state === STATES.ORDER_EMAIL && text.includes('@')) {
      sendOrderEmail(text);
      return true;
    }
    if (state === STATES.ORDER_PHONE && /[\d\s\+\-\(\)]{7,}/.test(text)) {
      sendOrderPhone(text);
      return true;
    }
    return false;
  }

  // ── EMAIL VIA EMAILJS ──────────────────────────────────────

  async function sendOrderEmail(email) {
    const slots  = StateMachine.getSlots();
    const tariff = selectedTariff || 'human';
    const result = PricingEngine.isReady(slots)
      ? PricingEngine.calcOne(tariff, slots)
      : null;

    showTyping();

    try {
      // EmailJS (подключить в index.html: emailjs.init("YOUR_PUBLIC_KEY"))
      if (typeof emailjs !== 'undefined') {
        await emailjs.send('remarka_service', 'order_template', {
          to_email:   'orders@remarka-bureau.ru',
          client_email: email,
          tariff:     tariff,
          lang_pair:  slots.langPair || '—',
          domain:     slots.domain || '—',
          pages:      result?.totalPages || slots.pages || '—',
          chars:      slots.chars ? PricingEngine.fmt(slots.chars) : '—',
          urgency:    slots.urgency || '—',
          total:      result ? PricingEngine.fmt(result.total) + ' ₽' : '—',
          deadline:   result?.deadlineStr || '—',
          geo_city:   geo?.city || '—',
          geo_country: geo?.country_name || '—',
        });
      }

      StateMachine.addOrder({ email, tariff, slots, result });
      hideTyping();
      StateMachine.setState(StateMachine.STATES.DONE);
      appendBotMessage(
        `✅ Заказ принят!\n\nДетали отправлены на ${email}\nВаш менеджер свяжется в течение 15 минут.\n\nСпасибо, что выбрали Ремарку! 🙏`,
        ['📞 Позвоните мне', '➕ Новый заказ']
      );
    } catch (e) {
      hideTyping();
      appendBotMessage(
        `Записала ваш email: ${email}\nМенеджер свяжется в течение 15 минут. Спасибо! 🙏`,
        ['➕ Новый заказ']
      );
    }
  }

  async function sendOrderPhone(phone) {
    showTyping();
    try {
      if (typeof emailjs !== 'undefined') {
        await emailjs.send('remarka_service', 'callback_template', {
          to_email:    'orders@remarka-bureau.ru',
          client_phone: phone,
          geo_city:    geo?.city || '—',
        });
      }
      StateMachine.addOrder({ phone, slots: StateMachine.getSlots() });
      hideTyping();
      StateMachine.setState(StateMachine.STATES.DONE);
      appendBotMessage(
        `✅ Принято!\n\nПерезвоним на ${phone} в течение 5 минут.\nВремя работы: пн–пт 9:00–19:00 МСК`,
        ['➕ Новый заказ']
      );
    } catch {
      hideTyping();
      appendBotMessage(`Записала номер ${phone}. Перезвоним скоро! 📞`, ['➕ Новый заказ']);
    }
  }

  // ── FILE UPLOAD ────────────────────────────────────────────

  async function handleFileUpload(file) {
    appendUserMessage(`📎 Загружен файл: ${file.name} (${(file.size / 1024).toFixed(0)} КБ)`);
    showTyping();

    try {
      const fileInfo = await PricingEngine.readFile(file);
      hideTyping();

      if (fileInfo.chars > 0) {
        StateMachine.updateSlots({ chars: fileInfo.chars, pages: fileInfo.pages });
        appendFileResult(fileInfo);

        // Если остальные слоты заполнены → сразу считаем
        await delay(500);
        const missing = SlotExtractor.getMissing(StateMachine.getSlots());
        if (missing) {
          const q = ResponseBuilder.getSlotQuestion(missing);
          showTyping(); await delay(600); hideTyping();
          appendBotMessage(q.text, q.replies);
        } else {
          showTyping(); await delay(800); hideTyping();
          const results = PricingEngine.calculate(StateMachine.getSlots());
          appendPriceCards(results, StateMachine.getSlots());
        }
      } else {
        hideTyping();
        appendBotMessage(
          `Получила файл ${file.name}. Не удалось автоматически подсчитать объём. Укажите примерное количество страниц?`,
          ResponseBuilder.getSlotQuestion('volume').replies
        );
      }
    } catch (e) {
      hideTyping();
      appendBotMessage(
        `Файл получен! Укажите количество страниц (или знаков) для расчёта стоимости.`,
        ResponseBuilder.getSlotQuestion('volume').replies
      );
    }
  }

  // ── QUICK REPLY MAPPER ─────────────────────────────────────

  function mapQuickReply(text) {
    const slots = {};
    const t = text.toLowerCase();

    // Lang pairs
    if (t.includes('ru→en') || t.includes('ru-en') || t.includes('ру→ен')) slots.langPair = 'ru-en';
    else if (t.includes('en→ru') || t.includes('en-ru')) slots.langPair = 'en-ru';
    else if (t.includes('ru→de') || t.includes('ru-de')) slots.langPair = 'ru-de';
    else if (t.includes('ru→it') || t.includes('ru-it')) slots.langPair = 'ru-it';
    else if (t.includes('ru→fr') || t.includes('ru-fr')) slots.langPair = 'ru-fr';
    else if (t.includes('ru→es') || t.includes('ru-es')) slots.langPair = 'ru-es';

    // Domains
    if (t.includes('технич')) slots.domain = 'technical';
    else if (t.includes('юридич')) slots.domain = 'legal';
    else if (t.includes('медицин')) slots.domain = 'medical';
    else if (t.includes('it') || t.includes('ит/')) slots.domain = 'it';
    else if (t.includes('финанс')) slots.domain = 'finance';
    else if (t.includes('сайт') || t.includes('маркетинг')) slots.domain = 'marketing';

    // Urgency
    if (t.includes('стандарт') || t.includes('3–7')) slots.urgency = 'standard';
    else if (t.includes('срочно') || t.includes('1–2')) slots.urgency = 'urgent';
    else if (t.includes('экспресс') || t.includes('24')) slots.urgency = 'express';
    else if (t.includes('суперэкспресс') || t.includes('4–6')) slots.urgency = 'superexp';
    else if (t.includes('не срочн') || t.includes('без срочн')) slots.urgency = 'flexible';

    // Volume
    if (t.includes('до 5 стр')) slots.pages = 3;
    else if (t.includes('5–20 стр')) slots.pages = 10;
    else if (t.includes('20–50 стр')) slots.pages = 30;
    else if (t.includes('50–100 стр')) slots.pages = 70;
    else if (t.includes('более 100 стр')) slots.pages = 120;

    // Tariffs
    if (t.includes('mtpe') || t.includes('вычитка ai')) slots.tariff = 'mtpe';
    else if (t.includes('профессионал')) slots.tariff = 'human';
    else if (t.includes('premium') || t.includes('премиум')) slots.tariff = 'premium';

    return slots;
  }

  // ── NEW ORDER ──────────────────────────────────────────────

  function newOrder() {
    StateMachine.reset();
    selectedTariff = null;
    conversationHistory = [];
    $('messages').innerHTML = '';
    setQuickReplies([]);
    appendBotMessage('Начнём новый заказ! 🆕\nОпишите задачу — направление, тип документа, объём и сроки.', [
      '🔤 Нужен перевод', '✅ Вычитка AI', '🌐 Перевод сайта', '📎 Загрузить файл',
    ]);
  }

  // ── HELPER: CONTEXT REPLIES ────────────────────────────────

  function getContextReplies() {
    const s = StateMachine.getSlots();
    if (!s.tariff)  return ['🤖 MTPE (от 350₽)', '👨‍💼 Профессиональный', '⭐ Premium Expert'];
    if (!s.langPair) return ['🇷🇺→🇬🇧 RU→EN', '🇬🇧→🇷🇺 EN→RU', '🇷🇺→🇩🇪 RU→DE', '🌍 Другой'];
    if (!s.pages && !s.chars) return ['📃 До 5 стр.', '5–20 стр.', '20–50 стр.', '📎 Прикрепить файл'];
    if (!s.urgency) return ['📅 Стандарт', '🔥 Срочно', '⚡ Экспресс'];
    return ['📧 Оформить заказ', '🔄 Новый расчёт'];
  }

  // ── VOICE INPUT ────────────────────────────────────────────

  function initSpeech() {
    const SRec = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SRec) return;
    recognition = new SRec();
    recognition.continuous = false;
    recognition.interimResults = true;

    recognition.onstart = () => {
      micActive = true;
      updateMicUI(true);
    };

    recognition.onresult = e => {
      let interim = '', final = '';
      for (const res of e.results) {
        if (res.isFinal) final += res[0].transcript;
        else interim += res[0].transcript;
      }
      const inp = $('chat-input');
      if (inp) inp.value = final || interim;
    };

    recognition.onend = () => {
      micActive = false;
      updateMicUI(false);
      // Если есть текст — отправить
      const val = $('chat-input')?.value?.trim();
      if (val) {
        setTimeout(() => sendInput(), 200);
      }
    };

    recognition.onerror = () => { micActive = false; updateMicUI(false); };
  }

  function toggleMic() {
    if (!recognition) {
      appendBotMessage('Голосовой ввод поддерживается в Chrome и Edge. В других браузерах используйте текстовый ввод.', []);
      return;
    }
    const langMap = { ru: 'ru-RU', en: 'en-US', it: 'it-IT' };
    recognition.lang = langMap[uiLang] || 'ru-RU';

    if (micActive) { recognition.stop(); }
    else           { recognition.start(); }
  }

  function updateMicUI(active) {
    const btn = $('mic-btn');
    if (!btn) return;
    if (active) {
      btn.classList.add('mic-active');
      btn.title = 'Остановить запись';
      const wave = $('wave-orb');
      if (wave) wave.classList.add('active');
    } else {
      btn.classList.remove('mic-active');
      btn.title = 'Голосовой ввод';
      const wave = $('wave-orb');
      if (wave) wave.classList.remove('active');
    }
  }

  // ── UI LANGUAGE ────────────────────────────────────────────

  function setLang(lang) {
    uiLang = lang;
    StateMachine.getProfile().lang = lang;
    StateMachine.save();
    document.querySelectorAll('.lang-btn').forEach(b => {
      b.classList.toggle('active', b.dataset.lang === lang);
    });
  }

  // ── SEND ───────────────────────────────────────────────────

  function sendInput() {
    const inp = $('chat-input');
    if (!inp) return;
    const text = inp.value.trim();
    if (!text) return;
    inp.value = ''; inp.style.height = 'auto';

    // Проверить контактные данные
    if (handleContactInput(text)) return;
    if (text.toLowerCase().includes('новый заказ') || text.toLowerCase().includes('new order')) {
      newOrder(); return;
    }

    handleUserInput(text);
  }

  // ── HELPERS ────────────────────────────────────────────────

  function formatText(t) {
    if (!t) return '';
    return t
      .replace(/\n/g, '<br>')
      .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
      .replace(/_(.*?)_/g,       '<em>$1</em>');
  }

  function now() {
    return new Date().toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
  }

  function delay(ms) { return new Promise(r => setTimeout(r, ms)); }

  function scrollBottom() {
    const m = $('messages');
    if (m) setTimeout(() => m.scrollTop = m.scrollHeight, 60);
  }

  // ── PUBLIC API ─────────────────────────────────────────────
  return {
    init, sendInput, toggleMic, setLang, handleUserInput,
    handleFileUpload, selectTariff, startOrder, newOrder,
  };

})();

window.ChatEngine = ChatEngine;
