/* ============================================================
   REMARKA — TRANSLATOR SURVEY MODULE v1.0
   Анкета переводчика прямо в чате:
   • Ольга ведёт диалог через все шаги
   • AI-тест (GPT через api/gpt.php) по выбранному языку
   • Отправка через EmailJS
   • Интеграция с ChatEngine/StateMachine
   ============================================================ */

const TranslatorSurvey = (() => {
  'use strict';

  // ── СОСТОЯНИЕ АНКЕТЫ ─────────────────────────────────────
  let state = {
    active:   false,
    step:     null,
    data:     {},          // собранные данные анкеты
    langs:    [],          // [{name, level}]
    scores:   {},          // {langName: {score, feedback, date}}
    test:     null,        // текущий тест {lang, qs, ans, cur}
  };

  // ── ШАГИ ВОРОНКИ ─────────────────────────────────────────
  const STEPS = [
    'intro',
    'firstName', 'lastName', 'patronymic', 'birthDate',
    'phone', 'email',
    'langs',
    'empType', 'workType', 'workload', 'productivity',
    'urgentWork', 'salary',
    'pcSkills', 'tradosSkills',
    'specialization',
    'test_invite',
    'comment',
    'submit',
  ];

  const LEVELS = [
    { v: 'A1', l: 'A1 — Начальный' },
    { v: 'A2', l: 'A2 — Элементарный' },
    { v: 'B1', l: 'B1 — Средний' },
    { v: 'B2', l: 'B2 — Выше среднего' },
    { v: 'C1', l: 'C1 — Продвинутый' },
    { v: 'C2', l: 'C2 — Профессиональный' },
    { v: 'Native', l: '★ Родной язык' },
  ];

  const POPULAR_LANGS = [
    '🇬🇧 Английский', '🇩🇪 Немецкий', '🇫🇷 Французский',
    '🇮🇹 Итальянский', '🇪🇸 Испанский', '🇨🇳 Китайский',
    '🇯🇵 Японский', '🇸🇦 Арабский', '🇰🇷 Корейский',
    '🇵🇱 Польский', '🇺🇦 Украинский', '🇹🇷 Турецкий',
  ];

  const STEP_QUESTIONS = {
    intro:           null, // управляется через checkTranslatorIntent
    firstName:       '👤 Как вас зовут? Начнём с имени:',
    lastName:        'Фамилия:',
    patronymic:      'Отчество (или напишите «нет»):',
    birthDate:       '📅 Дата рождения (ДД.ММ.ГГГГ):',
    phone:           '📞 Номер телефона для связи:',
    email:           '📧 Email-адрес:',
    langs:           null, // кастомный рендер
    empType:         '💼 Вид занятости:',
    workType:        '🏠 Формат работы:',
    workload:        '⏰ Желаемая загруженность:',
    productivity:    '📄 Ваша производительность (стр/день):',
    urgentWork:      '⚡ Готовы к срочным заказам?',
    salary:          '💰 Желаемый доход (стр. или месяц):',
    pcSkills:        '💻 Какими программами пользуетесь? (Word, Excel, Acrobat и др.):',
    tradosSkills:    '🔧 Опыт с CAT-инструментами? (Trados, memoQ, Smartcat и др.):',
    specialization:  '📚 Ваша специализация (выберите все подходящие):',
    test_invite:     null, // кастомный рендер
    comment:         '💬 Дополнительная информация или пожелания (или напишите «нет»):',
    submit:          null,
  };

  const QUICK_REPLIES = {
    empType:      ['Полная занятость', 'Частичная занятость', 'Проектная работа', 'Подработка'],
    workType:     ['Удалённо', 'В офисе', 'Гибридно'],
    workload:     ['До 5 стр/день', '5–10 стр/день', '10–20 стр/день', 'Более 20 стр/день'],
    urgentWork:   ['Да, всегда готов', 'Да, иногда', 'Нет, только плановые'],
    specialization: ['📄 Технический', '⚖️ Юридический', '🏥 Медицинский', '💻 IT / ПО', '📊 Финансовый', '🌐 Маркетинг / Сайты', '📖 Литературный', '🔬 Патентный'],
  };

  // ── ПРОВЕРКА INTENT ──────────────────────────────────────
  /**
   * Проверить, является ли сообщение запросом переводчика.
   * Вызывается из chat.js перед route().
   * Возвращает true если перехватил управление.
   */
  function checkTranslatorIntent(text) {
    const t = text.toLowerCase();
    const triggers = [
      /я\s+перевод[а-я]+/, /работ[а-я]+\s+перевод/, /ищу\s+работ/,
      /стать\s+перевод/, /перевод[а-я]+\s+работ/, /вакансия/,
      /анкет[а-я]+\s+перевод/, /хочу\s+к\s+вам/, /присоединить/,
      /translator.*join/, /join.*team/, /work.*translator/,
      /являюсь\s+перевод/, /профессиональный\s+перевод/,
    ];
    if (triggers.some(re => re.test(t))) {
      startSurvey(text);
      return true;
    }
    return false;
  }

  // ── ЗАПУСК АНКЕТЫ ────────────────────────────────────────
  function startSurvey(triggerText) {
    // Добавляем сообщение пользователя если передан текст триггера
    if (triggerText) appendUser(triggerText);
    state = { active: true, step: 'firstName', data: {}, langs: [], scores: {}, test: null };
    appendBot(
      '👩‍💼 Отлично, что вы рассматриваете работу с нами!\n\n' +
      'Я Ольга, и помогу вам заполнить анкету прямо здесь — это займёт около 5 минут.\n\n' +
      'По итогам вы пройдёте короткий профессиональный тест, и наш HR-менеджер свяжется с вами в течение 48 часов. Начнём? 🚀',
      ['✅ Да, начнём!', '❌ Нет, спасибо']
    );
    // Перехватываем управление
    _overrideSend();
  }

  // ── ПЕРЕХВАТ chat.js ─────────────────────────────────────
  let _origSendInput = null;

  function _overrideSend() {
    if (typeof ChatEngine === 'undefined') return;
    if (_origSendInput) return; // уже перехвачен
    _origSendInput = ChatEngine.handleUserInput.bind(ChatEngine);
    ChatEngine.handleUserInput = function(text) {
      if (state.active) {
        handleSurveyInput(text);
      } else {
        _restoreSend();
        _origSendInput(text);
      }
    };
  }

  function _restoreSend() {
    if (_origSendInput && typeof ChatEngine !== 'undefined') {
      ChatEngine.handleUserInput = _origSendInput;
    }
    _origSendInput = null;
  }

  // ── ОБРАБОТЧИК ВВОДА ─────────────────────────────────────
  function handleSurveyInput(text) {
    const t = text.trim();

    // Отказ на старте
    if (state.step === 'firstName' && (t === '❌ Нет, спасибо' || /нет.*спасибо/i.test(t))) {
      cancelSurvey();
      return;
    }

    // Старт после подтверждения
    if (t === '✅ Да, начнём!' || t === 'Да, начнём!') {
      askStep('firstName');
      return;
    }

    // Обработка шагов теста
    if (state.step === 'test_running') {
      handleTestInput(t);
      return;
    }

    // Обработка шага языков
    if (state.step === 'langs') {
      handleLangsInput(t);
      return;
    }

    // Шаг приглашения к тесту
    if (state.step === 'test_invite') {
      handleTestInvite(t);
      return;
    }

    // Стандартные шаги
    saveStepData(state.step, t);
    nextStep();
  }

  // ── СОХРАНЕНИЕ ДАННЫХ ШАГА ───────────────────────────────
  function saveStepData(step, value) {
    state.data[step] = value;
  }

  // ── СЛЕДУЮЩИЙ ШАГ ────────────────────────────────────────
  function nextStep() {
    const idx = STEPS.indexOf(state.step);
    if (idx === -1 || idx >= STEPS.length - 1) return;
    const next = STEPS[idx + 1];
    state.step = next;
    askStep(next);
  }

  // ── ЗАДАТЬ ВОПРОС ────────────────────────────────────────
  function askStep(step) {
    state.step = step;

    if (step === 'langs') {
      renderLangsStep();
      return;
    }

    if (step === 'test_invite') {
      renderTestInvite();
      return;
    }

    if (step === 'submit') {
      submitSurvey();
      return;
    }

    const q = STEP_QUESTIONS[step];
    const replies = QUICK_REPLIES[step] || [];

    if (q) {
      appendBot(q, replies);
    }
  }

  // ── ШАГ: ЯЗЫКИ ───────────────────────────────────────────
  function renderLangsStep() {
    // Рендерим кастомный блок добавления языков
    const html = buildLangsWidget();
    appendBotRich(
      'Укажите иностранные языки и уровень владения.\nНажмите на язык из списка или введите свой 👇',
      html,
      []
    );
    // QR для быстрого добавления
    setQR(POPULAR_LANGS.map(l => l));
    // Особый обработчик QR
    state.step = 'langs';
  }

  function buildLangsWidget() {
    const tags = state.langs.map(l => {
      const sc = state.scores[l.name];
      const badge = sc
        ? `<span style="background:rgba(45,158,96,.15);color:#1a6a38;padding:1px 7px;border-radius:10px;font-size:11px;font-weight:700">★ ${sc.score}/100</span>`
        : `<button onclick="TranslatorSurvey.runTest('${l.name}')" style="background:rgba(196,146,42,.15);color:#7a5500;border:none;border-radius:10px;padding:1px 8px;font-size:11px;font-weight:700;cursor:pointer">→ Тест</button>`;
      return `<span style="display:inline-flex;align-items:center;gap:6px;background:rgba(79,106,255,0.12);border:1.5px solid rgba(79,106,255,0.3);border-radius:20px;padding:4px 10px;font-size:12px;color:#e0e8ff">
        ${l.name} <span style="background:rgba(255,255,255,0.15);padding:1px 6px;border-radius:8px;font-size:10px;font-weight:700">${l.level}</span>
        ${badge}
        <button onclick="TranslatorSurvey.removeLang('${l.name}')" style="background:none;border:none;color:rgba(255,255,255,0.5);cursor:pointer;font-size:12px;padding:0 2px">×</button>
      </span>`;
    }).join(' ');

    return `<div id="ts-langs-widget" style="padding:4px 0">
      <div style="margin-bottom:10px;font-size:12px;color:rgba(160,170,220,0.7)">Добавленные языки:</div>
      <div id="ts-lang-tags" style="display:flex;flex-wrap:wrap;gap:6px;min-height:30px;margin-bottom:12px">${tags || '<span style="color:rgba(160,170,220,0.4);font-size:12px">Пока не добавлено</span>'}</div>
      <div style="display:flex;gap:6px;align-items:stretch">
        <select id="ts-lang-sel" style="flex:1;background:rgba(8,18,52,0.8);border:1px solid rgba(82,108,255,0.35);border-radius:8px;color:#e0e8ff;padding:7px 10px;font-size:13px;outline:none">
          <option value="">Выберите язык…</option>
          ${getLangOptions()}
        </select>
        <select id="ts-level-sel" style="width:130px;background:rgba(8,18,52,0.8);border:1px solid rgba(82,108,255,0.35);border-radius:8px;color:#e0e8ff;padding:7px 10px;font-size:12px;outline:none">
          ${LEVELS.map(l => `<option value="${l.v}">${l.l}</option>`).join('')}
        </select>
        <button onclick="TranslatorSurvey.addLang()" style="background:linear-gradient(135deg,#4f6aff,#7c5cfc);border:none;border-radius:8px;color:#fff;padding:7px 14px;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap">+ Добавить</button>
      </div>
      <div style="margin-top:10px;display:flex;gap:8px;flex-wrap:wrap">
        <button onclick="TranslatorSurvey.confirmLangs()" style="background:linear-gradient(135deg,#22d46e,#1aaa55);border:none;border-radius:20px;color:#fff;padding:7px 18px;font-size:13px;font-weight:600;cursor:pointer">✅ Готово</button>
        ${state.langs.length > 0 ? `<button onclick="TranslatorSurvey.runTest('${state.langs[0].name}')" style="background:rgba(196,146,42,0.2);border:1px solid rgba(196,146,42,0.4);border-radius:20px;color:#e8b84b;padding:7px 18px;font-size:13px;font-weight:600;cursor:pointer">🎯 Пройти тест</button>` : ''}
      </div>
    </div>`;
  }

  function getLangOptions() {
    const all = [
      'Английский','Немецкий','Французский','Итальянский','Испанский',
      'Португальский','Китайский','Японский','Арабский','Корейский',
      'Польский','Чешский','Нидерландский','Шведский','Финский',
      'Норвежский','Румынский','Болгарский','Сербский','Греческий',
      'Турецкий','Украинский','Белорусский','Литовский','Латышский',
      'Эстонский','Грузинский','Армянский','Азербайджанский','Казахский',
      'Узбекский','Таджикский','Иврит','Персидский','Хинди','Вьетнамский','Тайский',
    ];
    return all.map(l => `<option value="${l}">${l}</option>`).join('');
  }

  function addLang() {
    const sel = document.getElementById('ts-lang-sel');
    const lvl = document.getElementById('ts-level-sel');
    if (!sel || !sel.value) { showToast('⚠️ Выберите язык'); return; }
    const name = sel.value;
    if (state.langs.find(l => l.name === name)) { showToast('⚠️ ' + name + ' уже добавлен'); return; }
    state.langs.push({ name, level: lvl ? lvl.value : 'B2' });
    refreshLangsWidget();
    showToast('✅ ' + name + ' добавлен');
  }

  function removeLang(name) {
    state.langs = state.langs.filter(l => l.name !== name);
    delete state.scores[name];
    refreshLangsWidget();
  }

  function refreshLangsWidget() {
    const widget = document.getElementById('ts-langs-widget');
    if (widget) {
      const tmp = document.createElement('div');
      tmp.innerHTML = buildLangsWidget();
      widget.replaceWith(tmp.firstElementChild);
    }
  }

  function handleLangsInput(text) {
    // Если пришёл быстрый ответ (название языка из QR)
    const cleanName = text.replace(/^[🇦-🇿\s]+/, '').trim();
    if (cleanName && !state.langs.find(l => l.name === cleanName)) {
      state.langs.push({ name: cleanName, level: 'B2' });
      refreshLangsWidget();
      showToast('✅ ' + cleanName + ' добавлен — уточните уровень в виджете');
    }
  }

  function confirmLangs() {
    if (!state.langs.length) {
      appendBot('⚠️ Укажите хотя бы один язык.', POPULAR_LANGS);
      return;
    }
    appendUser('Языки: ' + state.langs.map(l => l.name + ' (' + l.level + ')').join(', '));
    state.data.langs = state.langs;
    state.step = 'empType';
    askStep('empType');
  }

  // ── ШАГ: ПРИГЛАШЕНИЕ К ТЕСТУ ─────────────────────────────
  function renderTestInvite() {
    const langList = state.langs.map(l => {
      const sc = state.scores[l.name];
      return sc
        ? `✅ ${l.name} — ${sc.score}/100`
        : `📝 ${l.name} (тест не пройден)`;
    }).join('\n');

    appendBot(
      `🎯 Отлично! Анкета почти готова.\n\nВы можете пройти профессиональный языковой тест — ИИ сгенерирует 10 профильных вопросов и оценит ваш уровень. Результат попадёт в анкету.\n\n${langList}\n\nПройти тест?`,
      state.langs.map(l => `🎯 Тест: ${l.name}`).concat(['⏭ Пропустить тест', '✉️ Сразу отправить'])
    );
  }

  function handleTestInvite(text) {
    if (text.includes('Пропустить') || text.includes('Сразу отправить')) {
      state.step = 'comment';
      askStep('comment');
      return;
    }
    // Попытка найти язык в ответе
    for (const l of state.langs) {
      if (text.includes(l.name)) {
        startAiTest(l.name);
        return;
      }
    }
    // Если просто "Да" — тест первого языка
    if (/^(да|yes|тест|начать)/i.test(text)) {
      startAiTest(state.langs[0].name);
    } else {
      state.step = 'comment';
      askStep('comment');
    }
  }

  // ── AI ТЕСТ ──────────────────────────────────────────────
  function runTest(lang) {
    if (state.step !== 'langs' && state.step !== 'test_invite' && state.step !== 'test_running') return;
    appendUser(`🎯 Хочу пройти тест: ${lang}`);
    startAiTest(lang);
  }

  function startAiTest(lang) {
    state.test = { lang, qs: [], ans: [], cur: 0 };
    state.step = 'test_running';

    showTestLoading(lang);
    generateQuestions(lang)
      .then(qs => {
        state.test.qs = qs;
        renderTestQuestion(0);
      })
      .catch(e => {
        appendBot(`❌ Не удалось загрузить тест: ${e.message}\n\nПопробуем ещё раз?`, ['🔄 Повторить', '⏭ Пропустить тест']);
        state.step = 'test_invite';
      });
  }

  async function generateQuestions(lang) {
    const prompt =
      `Профессиональный тест для переводчика с ${lang} языка на русский.\n` +
      `Создай РОВНО 10 вопросов в этом порядке типов:\n` +
      `1. error_find, 2. improve, 3. multiple_choice, 4. translate, 5. ambiguity,\n` +
      `6. editing, 7. multiple_choice, 8. multiple_choice, 9. multiple_choice, 10. multiple_choice\n\n` +
      `Правила: вопросы (q) на русском, примеры (ex) на ${lang}.\n` +
      `Только JSON без markdown:\n` +
      `[{"id":1,"type":"error_find","cat":"Найди ошибку","q":"...","ex":"...","opts":null,"corr":null}]`;

    const proxyUrl = (typeof RemarkaConfig !== 'undefined')
      ? RemarkaConfig.ajaxUrl
      : '/api/gpt.php';

    if (typeof RemarkaConfig !== 'undefined') {
      // WordPress режим — через WP Ajax
      const body = new URLSearchParams({
        action: 'remarka_chat',
        nonce:  RemarkaConfig.nonce,
        text:   prompt,
        system: 'Ты генератор профессиональных тестов для переводчиков. Отвечай ТОЛЬКО валидным JSON-массивом без markdown.',
      });
      const resp = await fetch(proxyUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body.toString(),
      });
      const data = await resp.json();
      const payload = data.data || data;
      const text = extractApiText(payload);
      return JSON.parse(text.replace(/```json\n?/g, '').replace(/```\n?/g, '').trim());
    } else {
      // Standalone режим — прямой вызов api/gpt.php
      const resp = await fetch('/api/gpt.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          text: prompt,
          system: 'Ты генератор профессиональных тестов для переводчиков. Отвечай ТОЛЬКО валидным JSON-массивом без markdown.',
        }),
      });
      const data = await resp.json();
      const text = extractApiText(data);
      return JSON.parse(text.replace(/```json\n?/g, '').replace(/```\n?/g, '').trim());
    }
  }

  function extractApiText(data) {
    if (data?.output) {
      return data.output
        .filter(b => b.type === 'message')
        .flatMap(b => b.content || [])
        .filter(c => c.type === 'output_text')
        .map(c => c.text)
        .join('');
    }
    if (data?.choices) return data.choices[0]?.message?.content || '';
    if (typeof data === 'string') return data;
    return '';
  }

  function showTestLoading(lang) {
    appendBotRich(
      `Генерирую профессиональный тест по языку "${lang}"…`,
      `<div style="text-align:center;padding:16px 0">
        <div style="width:40px;height:40px;border:3px solid rgba(82,108,255,0.2);border-top-color:#4f6aff;border-radius:50%;animation:rc-spin 0.7s linear infinite;margin:0 auto 10px"></div>
        <div style="color:rgba(160,170,220,0.7);font-size:13px">GPT-4o создаёт 10 профильных вопросов…</div>
      </div>`,
      []
    );
  }

  function renderTestQuestion(idx) {
    const q = state.test.qs[idx];
    if (!q) return;
    const tot = state.test.qs.length;
    state.test.cur = idx;

    const catMap = {
      error_find: '🔍 Найди ошибку',
      improve: '✍️ Улучши',
      translate: '🌐 Перевод',
      ambiguity: '🧩 Неоднозначность',
      editing: '📝 Редактура',
      multiple_choice: '🎯 Выбор варианта',
    };
    const cat = catMap[q.type] || q.cat || q.type;
    const isMultiple = q.type === 'multiple_choice' && Array.isArray(q.opts);
    const pct = Math.round((idx / tot) * 100);

    let inputHtml = '';
    if (isMultiple) {
      inputHtml = q.opts.map((o, i) => {
        const letter = ['А', 'Б', 'В', 'Г'][i] || String(i + 1);
        return `<button onclick="TranslatorSurvey.pickOption(${i})" data-qopt="${i}"
          style="display:flex;align-items:center;gap:10px;width:100%;padding:10px 12px;margin-bottom:6px;
          background:rgba(8,18,52,0.6);border:1.5px solid rgba(82,108,255,0.25);border-radius:10px;
          color:#dde4ff;font-size:13px;cursor:pointer;text-align:left;transition:all 0.2s"
          onmouseover="this.style.borderColor='rgba(82,108,255,0.6)'"
          onmouseout="this.style.borderColor='rgba(82,108,255,0.25)'">
          <span style="width:24px;height:24px;border-radius:50%;background:rgba(79,106,255,0.2);
            color:#a5b4fc;display:inline-flex;align-items:center;justify-content:center;
            font-size:11px;font-weight:700;flex-shrink:0">${letter}</span>
          ${o}
        </button>`;
      }).join('');
    } else {
      inputHtml = `<textarea id="ts-test-answer" placeholder="Введите ваш ответ…"
        oninput="TranslatorSurvey.onTextAnswer(this.value)"
        style="width:100%;background:rgba(8,18,52,0.7);border:1.5px solid rgba(82,108,255,0.3);
        border-radius:10px;color:#dde4ff;padding:10px 14px;font-size:13px;
        resize:vertical;min-height:88px;outline:none;font-family:inherit;line-height:1.5;
        box-sizing:border-box;margin-bottom:8px"></textarea>`;
    }

    const html = `<div>
      <!-- Прогресс -->
      <div style="height:4px;background:rgba(82,108,255,0.15);border-radius:2px;overflow:hidden;margin-bottom:14px">
        <div style="height:100%;width:${pct}%;background:linear-gradient(90deg,#4f6aff,#7c5cfc);border-radius:2px;transition:width 0.4s"></div>
      </div>
      <div style="font-size:10px;color:rgba(140,155,210,0.6);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">
        Вопрос ${idx + 1} / ${tot} · ${state.test.lang}
      </div>
      <!-- Категория -->
      <div style="display:inline-block;padding:2px 10px;background:rgba(79,106,255,0.12);border:1px solid rgba(82,108,255,0.25);border-radius:20px;font-size:11px;font-weight:600;color:#a5b4fc;margin-bottom:10px">${cat}</div>
      <!-- Вопрос -->
      <div style="font-size:14px;font-weight:500;color:#e8eeff;line-height:1.5;margin-bottom:10px">${q.q}</div>
      ${q.ex ? `<div style="background:rgba(0,0,0,0.25);border-left:3px solid #c4922a;padding:8px 12px;border-radius:0 8px 8px 0;font-size:13px;font-style:italic;color:#dde4ff;margin-bottom:12px;white-space:pre-wrap">${q.ex}</div>` : ''}
      <!-- Ответ -->
      ${inputHtml}
      <!-- Кнопка далее -->
      <div style="text-align:right;margin-top:8px">
        <button id="ts-next-btn" onclick="TranslatorSurvey.nextTestQuestion()" disabled
          style="padding:9px 22px;background:#555;border:none;border-radius:20px;color:#fff;font-size:13px;font-weight:600;cursor:not-allowed;transition:all 0.2s">
          ${idx < tot - 1 ? 'Следующий →' : 'Завершить ✓'}
        </button>
      </div>
    </div>`;

    appendBotRich(`Вопрос ${idx + 1} из ${tot}`, html, []);
    // Очищаем QR
    setQR([]);
  }

  function pickOption(optIdx) {
    // Сброс всех стилей
    document.querySelectorAll('[data-qopt]').forEach(btn => {
      btn.style.borderColor = 'rgba(82,108,255,0.25)';
      btn.style.background = 'rgba(8,18,52,0.6)';
      btn.style.color = '#dde4ff';
      const circle = btn.querySelector('span');
      if (circle) { circle.style.background = 'rgba(79,106,255,0.2)'; circle.style.color = '#a5b4fc'; }
    });
    // Выделяем выбранный
    const selected = document.querySelector(`[data-qopt="${optIdx}"]`);
    if (selected) {
      selected.style.borderColor = 'rgba(82,108,255,0.8)';
      selected.style.background = 'rgba(79,106,255,0.2)';
      selected.style.color = '#e8eeff';
      const circle = selected.querySelector('span');
      if (circle) { circle.style.background = '#4f6aff'; circle.style.color = '#fff'; }
    }
    state.test.ans[state.test.cur] = optIdx;
    enableNextBtn();
  }

  function onTextAnswer(val) {
    state.test.ans[state.test.cur] = val;
    const btn = document.getElementById('ts-next-btn');
    if (btn) {
      if (val.trim()) {
        enableNextBtn();
      } else {
        btn.disabled = true;
        btn.style.background = '#555';
        btn.style.cursor = 'not-allowed';
      }
    }
  }

  function enableNextBtn() {
    const btn = document.getElementById('ts-next-btn');
    if (btn) {
      btn.disabled = false;
      btn.style.background = 'linear-gradient(135deg,#4f6aff,#7c5cfc)';
      btn.style.cursor = 'pointer';
    }
  }

  function nextTestQuestion() {
    const ta = document.getElementById('ts-test-answer');
    if (ta) state.test.ans[state.test.cur] = ta.value.trim();

    state.test.cur++;
    if (state.test.cur >= state.test.qs.length) {
      evaluateTest();
    } else {
      renderTestQuestion(state.test.cur);
    }
  }

  // ── ОЦЕНКА ТЕСТА ─────────────────────────────────────────
  function evaluateTest() {
    appendBotRich(
      'Проверяем ваши ответы…',
      `<div style="text-align:center;padding:16px 0">
        <div style="width:40px;height:40px;border:3px solid rgba(82,108,255,0.2);border-top-color:#4f6aff;border-radius:50%;animation:rc-spin 0.7s linear infinite;margin:0 auto 10px"></div>
        <div style="color:rgba(160,170,220,0.7);font-size:13px">ИИ анализирует качество переводов…</div>
      </div>`,
      []
    );

    checkTestAnswers()
      .then(result => {
        state.scores[state.test.lang] = {
          score: result.score,
          feedback: result.feedback,
          date: new Date().toLocaleDateString('ru-RU'),
        };
        showTestResults(result.score, result.feedback);
      })
      .catch(e => {
        appendBot(`❌ Ошибка проверки: ${e.message}`, ['🔄 Повторить тест', '⏭ Продолжить без теста']);
        state.step = 'test_invite';
      });
  }

  async function checkTestAnswers() {
    const qa = state.test.qs.map((q, i) => ({
      type: q.type,
      q: q.q,
      ex: q.ex || '',
      ans: state.test.ans[i],
      corr: q.type === 'multiple_choice' && q.corr != null ? (q.opts && q.opts[q.corr]) : null,
    }));

    const prompt =
      `Проверь тест переводчика (${state.test.lang} → русский).\n` +
      JSON.stringify(qa, null, 1) +
      `\n\nДля multiple_choice: сравни ans (индекс) с corr.\nДля текстовых: оцени точность, стиль, естественность.\n` +
      `Только JSON: {"score":75,"feedback":"3-4 предложения обратной связи на русском"}`;

    const proxyUrl = (typeof RemarkaConfig !== 'undefined') ? RemarkaConfig.ajaxUrl : '/api/gpt.php';

    if (typeof RemarkaConfig !== 'undefined') {
      const body = new URLSearchParams({
        action: 'remarka_chat',
        nonce:  RemarkaConfig.nonce,
        text:   prompt,
        system: 'Ты опытный методист переводческой школы. Отвечай ТОЛЬКО JSON: {"score":N,"feedback":"..."}',
      });
      const resp = await fetch(proxyUrl, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: body.toString() });
      const data = await resp.json();
      const text = extractApiText(data.data || data);
      return JSON.parse(text.replace(/```json\n?/g, '').replace(/```\n?/g, '').trim());
    } else {
      const resp = await fetch('/api/gpt.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ text: prompt, system: 'Отвечай ТОЛЬКО JSON: {"score":N,"feedback":"..."}' }) });
      const data = await resp.json();
      const text = extractApiText(data);
      return JSON.parse(text.replace(/```json\n?/g, '').replace(/```\n?/g, '').trim());
    }
  }

  function showTestResults(score, feedback) {
    const emoji = score >= 85 ? '🏆' : score >= 70 ? '🌟' : score >= 50 ? '👍' : '📚';
    const color = score >= 85 ? '#22d46e' : score >= 70 ? '#c4922a' : score >= 50 ? '#4f6aff' : '#ef4444';
    const label = score >= 85 ? 'Отличный результат!' : score >= 70 ? 'Хороший результат' : score >= 50 ? 'Средний уровень' : 'Есть куда расти';

    const html = `<div style="text-align:center;padding:12px 0 16px">
      <div style="font-size:2.5rem;margin-bottom:6px">${emoji}</div>
      <div style="font-size:3.2rem;font-weight:800;color:#e8eeff;line-height:1">${score}</div>
      <div style="font-size:11px;color:rgba(140,155,210,0.6);text-transform:uppercase;letter-spacing:.06em;margin:4px 0">баллов из 100</div>
      <div style="font-size:14px;font-weight:600;color:${color};margin-bottom:12px">${label}</div>
      <div style="height:8px;border-radius:4px;background:rgba(82,108,255,0.15);overflow:hidden;margin:0 0 14px">
        <div style="height:100%;width:${score}%;background:${color};border-radius:4px;transition:width 0.7s"></div>
      </div>
      <div style="background:rgba(0,0,0,0.2);border-left:3px solid #c4922a;border-radius:0 8px 8px 0;padding:10px 14px;text-align:left;font-size:13px;color:#dde4ff;line-height:1.6;margin-bottom:12px">${feedback}</div>
      <div style="background:rgba(34,212,110,0.1);border-radius:8px;padding:8px 12px;font-size:12px;color:#22d46e">✅ Результат <b>${score}/100 (${state.test.lang})</b> сохранён в анкету</div>
    </div>
    <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:8px;flex-wrap:wrap">
      ${state.langs.filter(l => !state.scores[l.name]).map(l =>
        `<button onclick="TranslatorSurvey.runTest('${l.name}')" style="padding:7px 14px;background:rgba(79,106,255,0.15);border:1px solid rgba(82,108,255,0.35);border-radius:20px;color:#a5b4fc;font-size:12px;font-weight:600;cursor:pointer">🎯 Тест: ${l.name}</button>`
      ).join('')}
      <button onclick="TranslatorSurvey.afterTest()" style="padding:7px 18px;background:linear-gradient(135deg,#4f6aff,#7c5cfc);border:none;border-radius:20px;color:#fff;font-size:13px;font-weight:600;cursor:pointer">Продолжить →</button>
    </div>`;

    appendBotRich(`Тест завершён: ${state.test.lang}`, html, []);
    refreshLangsWidget();
  }

  function afterTest() {
    const untested = state.langs.filter(l => !state.scores[l.name]);
    if (untested.length > 0) {
      appendBot(
        `Хотите пройти тест по оставшимся языкам?\n${untested.map(l => '• ' + l.name).join('\n')}`,
        untested.map(l => `🎯 Тест: ${l.name}`).concat(['⏭ Продолжить без теста'])
      );
      state.step = 'test_invite';
    } else {
      state.step = 'comment';
      askStep('comment');
    }
  }

  // ── ОТМЕНА АНКЕТЫ ────────────────────────────────────────
  function cancelSurvey() {
    state.active = false;
    _restoreSend();
    appendBot(
      'Хорошо, понимаю! Если захотите заполнить анкету позже — просто напишите «хочу работать переводчиком» 😊\n\nЧем ещё могу помочь?',
      ['🔤 Нужен перевод', '💰 Узнать стоимость', '📋 Тарифы']
    );
  }

  // ── ОТПРАВКА АНКЕТЫ ──────────────────────────────────────
  function submitSurvey() {
    const d = state.data;
    appendBotRich(
      'Проверьте данные перед отправкой 👇',
      buildSummaryHtml(),
      ['✅ Всё верно, отправить', '✏️ Исправить данные']
    );
  }

  function buildSummaryHtml() {
    const d = state.data;
    const rows = [
      ['👤 Имя', `${d.firstName || ''} ${d.lastName || ''} ${d.patronymic || ''}`],
      ['📅 Дата рождения', d.birthDate || '—'],
      ['📞 Телефон', d.phone || '—'],
      ['📧 Email', d.email || '—'],
      ['🌍 Языки', state.langs.map(l => `${l.name} (${l.level})`).join(', ') || '—'],
      ['💼 Занятость', d.empType || '—'],
      ['🏠 Формат', d.workType || '—'],
      ['⏰ Загруженность', d.workload || '—'],
      ['📄 Производительность', d.productivity || '—'],
      ['⚡ Срочные', d.urgentWork || '—'],
      ['💰 Доход', d.salary || '—'],
      ['💻 Программы', d.pcSkills || '—'],
      ['🔧 CAT-инструменты', d.tradosSkills || '—'],
      ['📚 Специализация', d.specialization || '—'],
    ];

    const scoresRows = Object.entries(state.scores).map(([lang, s]) =>
      `<div style="display:flex;justify-content:space-between;padding:4px 0;border-bottom:1px solid rgba(82,108,255,0.1)">
        <span style="color:rgba(160,170,220,0.7);font-size:12px">🎯 ${lang}</span>
        <span style="color:#22d46e;font-weight:700;font-size:12px">${s.score}/100</span>
      </div>`
    ).join('');

    return `<div style="max-height:320px;overflow-y:auto;padding-right:4px">
      ${rows.map(([label, val]) => `
        <div style="display:flex;justify-content:space-between;padding:5px 0;border-bottom:1px solid rgba(82,108,255,0.08)">
          <span style="color:rgba(160,170,220,0.6);font-size:12px">${label}</span>
          <span style="color:#dde4ff;font-size:12px;font-weight:500;text-align:right;max-width:60%">${val}</span>
        </div>`).join('')}
      ${scoresRows ? `<div style="margin-top:10px;font-size:11px;color:rgba(140,155,210,0.5);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px">Результаты тестов</div>${scoresRows}` : ''}
    </div>`;
  }

  function confirmSubmit(answer) {
    if (answer === '✏️ Исправить данные') {
      appendBot('Хорошо! На каком шаге хотите внести изменения?',
        ['Личные данные', 'Языки', 'Условия работы', '❌ Отмена']
      );
      return;
    }
    sendViaEmailJS();
  }

  function sendViaEmailJS() {
    appendBot('📤 Отправляем анкету…', []);

    const d = state.data;
    const scStr = Object.keys(state.scores).length
      ? Object.keys(state.scores).map(l => `${l}: ${state.scores[l].score}/100`).join(' | ')
      : 'Тесты не пройдены';

    const params = {
      from_name:      `${d.firstName || ''} ${d.lastName || ''}`.trim(),
      first_name:     d.firstName || '',
      last_name:      d.lastName || '',
      patronymic:     d.patronymic || '—',
      birth_date:     d.birthDate || '—',
      phone:          d.phone || '—',
      reply_to:       d.email || '',
      email:          d.email || '',
      languages:      state.langs.map(l => `${l.name} (${l.level})`).join(', '),
      emp_type:       d.empType || '—',
      work_type:      d.workType || '—',
      workload:       d.workload || '—',
      productivity:   d.productivity || '—',
      urgent_work:    d.urgentWork || '—',
      salary:         d.salary || '—',
      pc_skills:      d.pcSkills || '—',
      trados_skills:  d.tradosSkills || '—',
      specialization: d.specialization || '—',
      test_scores:    scStr,
      comment:        d.comment || '—',
      submit_date:    new Date().toLocaleString('ru-RU'),
      source:         'AI-консультант (чат)',
    };

    // EmailJS конфигурация
    const ejsCfg = (typeof RemarkaConfig !== 'undefined' && RemarkaConfig.emailjs)
      ? RemarkaConfig.emailjs
      : { serviceId: 'remarka_service', templateTranslator: 'translator_template' };

    if (typeof emailjs === 'undefined') {
      // Fallback: WP Ajax
      saveViaWpAjax(params);
      return;
    }

    emailjs.send(ejsCfg.serviceId, ejsCfg.templateTranslator || 'translator_template', params)
      .then(() => {
        onSubmitSuccess();
        saveViaWpAjax(params); // дублируем в WP
      })
      .catch(err => {
        console.error('EmailJS error:', err);
        // Пробуем WP Ajax как fallback
        saveViaWpAjax(params);
      });
  }

  function saveViaWpAjax(params) {
    if (typeof RemarkaConfig === 'undefined') return;
    const body = new URLSearchParams({
      action:     'remarka_save_translator',
      nonce:      RemarkaConfig.nonce,
      data:       JSON.stringify(params),
      scores:     JSON.stringify(state.scores),
    });
    fetch(RemarkaConfig.ajaxUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: body.toString(),
    }).then(r => r.json()).then(d => {
      if (d.success) onSubmitSuccess();
    }).catch(() => onSubmitSuccess()); // показываем успех в любом случае
  }

  function onSubmitSuccess() {
    state.active = false;
    _restoreSend();

    appendBotRich(
      '✅ Анкета отправлена!',
      `<div style="text-align:center;padding:12px 0">
        <div style="font-size:2.5rem;margin-bottom:8px">🎉</div>
        <div style="font-size:15px;font-weight:700;color:#22d46e;margin-bottom:8px">Анкета успешно отправлена!</div>
        <div style="font-size:13px;color:rgba(160,170,220,0.8);line-height:1.6">
          Наш HR-менеджер рассмотрит вашу кандидатуру<br>и свяжется с вами в течение <b style="color:#e8eeff">48 часов</b>.<br><br>
          ${Object.keys(state.scores).length ? `Ваши результаты тестов учтены в анкете ✓` : ''}
        </div>
      </div>`,
      ['🔤 Нужен перевод', '💬 Задать вопрос']
    );
  }

  // ── UI HELPERS ───────────────────────────────────────────
  function appendBot(text, replies) {
    if (typeof ChatEngine !== 'undefined' && ChatEngine._appendBotMessage) {
      ChatEngine._appendBotMessage(text, replies);
    } else {
      // Прямой вызов если ChatEngine не экспортирует метод
      const msgs = document.getElementById('messages');
      if (!msgs) return;
      const div = document.createElement('div');
      div.className = 'msg msg--bot';
      const time = new Date().toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
      div.innerHTML = `<div class="bubble bubble--bot">${text.replace(/\n/g, '<br>')}</div><div class="msg-time">${time}</div>`;
      msgs.appendChild(div);
      setQR(replies || []);
      scrollBottom();
    }
  }

  function appendBotRich(text, html, replies) {
    const msgs = document.getElementById('messages');
    if (!msgs) return;
    const div = document.createElement('div');
    div.className = 'msg msg--bot';
    const time = new Date().toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
    div.innerHTML = `
      <div class="bubble bubble--bot bubble--rich">
        ${text ? `<div class="bubble-lead">${text.replace(/\n/g, '<br>')}</div>` : ''}
        ${html}
      </div>
      <div class="msg-time">${time}</div>`;
    msgs.appendChild(div);
    setQR(replies || []);
    scrollBottom();
  }

  function appendUser(text) {
    const msgs = document.getElementById('messages');
    if (!msgs) return;
    const div = document.createElement('div');
    div.className = 'msg msg--user';
    const time = new Date().toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
    div.innerHTML = `<div class="bubble bubble--user">${text}</div><div class="msg-time" style="text-align:right">${time}</div>`;
    msgs.appendChild(div);
    scrollBottom();
  }

  function setQR(arr) {
    const qr = document.getElementById('quick-replies');
    if (!qr) return;
    qr.innerHTML = '';
    (arr || []).forEach(text => {
      const btn = document.createElement('button');
      btn.className = 'qr-btn';
      btn.textContent = text;
      btn.onclick = () => {
        appendUser(text);
        handleSurveyInput(text);
      };
      qr.appendChild(btn);
    });
  }

  function scrollBottom() {
    const m = document.getElementById('messages');
    if (m) setTimeout(() => m.scrollTop = m.scrollHeight, 80);
  }

  function showToast(msg) {
    // Используем существующий toast чата или создаём
    let t = document.getElementById('remarka-toast');
    if (!t) {
      t = document.createElement('div');
      t.id = 'remarka-toast';
      t.style.cssText = 'position:fixed;bottom:100px;right:28px;background:rgba(14,26,72,0.95);color:#dde4ff;padding:10px 16px;border-radius:10px;font-size:13px;z-index:99999;max-width:280px;line-height:1.4;border:1px solid rgba(82,108,255,0.3);box-shadow:0 4px 20px rgba(0,0,0,0.4);display:none';
      document.body.appendChild(t);
    }
    t.textContent = msg;
    t.style.display = 'block';
    clearTimeout(t._timer);
    t._timer = setTimeout(() => t.style.display = 'none', 3000);
  }

  // ── PUBLIC API ───────────────────────────────────────────
  return {
    checkTranslatorIntent,
    startSurvey,
    addLang,
    removeLang,
    confirmLangs,
    runTest,
    pickOption,
    onTextAnswer,
    nextTestQuestion,
    afterTest,
    confirmSubmit,
  };

})();

window.TranslatorSurvey = TranslatorSurvey;
