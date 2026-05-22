(function () {
  'use strict';

  const WORKER_URL = 'https://olga.alefcom1.workers.dev';

  const EJS_PUBLIC_KEY  = 'qIHC--GaJ6MMVCOg5';
  const EJS_SERVICE_ID  = 'service_htuz6bm';
  const EJS_TEMPLATE_ID = 'template_zl1knyb';
  let _ejsReady = false;
  function ensureEjs() {
    if (typeof emailjs === 'undefined') return false;
    if (!_ejsReady) { emailjs.init({ publicKey: EJS_PUBLIC_KEY }); _ejsReady = true; }
    return true;
  }

  const msgsCol  = document.querySelector('.cw-msgs-col');
  const msgsEl   = document.querySelector('.cw-msgs');
  const inputEl  = document.getElementById('cwInput');
  const sendBtn  = document.querySelector('.cw-btn-send');

  if (!msgsEl || !inputEl || !sendBtn) return;

  const history = [];

  /* ── Определение языка по тексту ── */
  function detectLang(text) {
    if (!text || text.trim().length < 3) return null;
    const t = ' ' + text.toLowerCase() + ' ';

    const cyrillic = (t.match(/[а-яё]/g) || []).length;
    const total    = t.replace(/\s/g, '').length;
    if (total > 0 && cyrillic / total > 0.3) return 'ru-RU';

    const itWords = [
      'ciao', 'salve', 'buongiorno', 'grazie', 'prego', 'voglio', 'bisogno',
      'traduz', 'documento', 'per favore', 'italiano', 'tradurre', 'parla',
      'parlo', 'parlate', 'vorrei', 'capisce', ' il ', ' la ', ' lo ',
      ' un ', ' una ', ' mi ', ' si ', ' di ', ' per ', ' con ', ' ho ', ' che '
    ];
    const itScore = itWords.filter(function (w) { return t.includes(w); }).length;

    const enWords = [
      'hello', ' hi ', 'thank', 'please', 'need', 'want', 'translat',
      'document', 'english', 'help', 'quote', 'price', 'speak',
      ' the ', ' is ', ' are ', ' you ', ' this ', ' that ',
      ' have ', ' can ', ' my ', ' for ', ' with ', ' of ', ' i ', ' do '
    ];
    const enScore = enWords.filter(function (w) { return t.includes(w); }).length;

    if (itScore === 0 && enScore === 0) return null;
    return itScore >= enScore ? 'it-IT' : 'en-US';
  }

  function getCurrentLang() {
    const active = document.querySelector('.lang-flag--active');
    return active ? active.dataset.lang : 'ru-RU';
  }

  const LANG_PROMPTS = {
    'en-US': "I can see you're writing in English 🇬🇧 To chat with me in English, please tap the 🇬🇧 flag in my panel.",
    'it-IT': "Vedo che scrivi in italiano 🇮🇹 Per chattare con me in italiano, clicca sulla bandiera 🇮🇹 nel mio pannello."
  };

  /* ── Приветственные сообщения ── */
  (function showGreetings() {
    const greetings = Array.from(msgsEl.querySelectorAll('.msg-bub[data-greeting]'));
    if (!greetings.length) return;

    greetings.forEach(function (el) { el.style.visibility = 'hidden'; el.style.position = 'absolute'; });

    let idx = 0;

    function next() {
      if (idx >= greetings.length) return;
      const el = greetings[idx];
      const charCount = (el.textContent || '').trim().length;
      const typingMs  = Math.max(700, Math.min(charCount * 18, 2200));
      const dot = appendTyping();
      setTimeout(function () {
        dot.remove();
        el.style.position   = '';
        el.style.visibility = '';
        el.removeAttribute('data-greeting');
        el.classList.add('msg-bub--entering');
        scrollBottom();
        idx++;
        if (idx < greetings.length) setTimeout(next, 380);
      }, typingMs);
    }

    setTimeout(next, 500);
  }());

  /* ── Helpers ── */
  function ts() {
    return new Date().toLocaleTimeString('ru', { hour: '2-digit', minute: '2-digit' });
  }

  function esc(s) {
    return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  }

  function renderBubText(s) {
    let t = s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    t = t.replace(/\[([^\]]+)\]\((https?:\/\/[^)]+)\)/g,
      '<a href="$2" target="_blank" rel="noopener" class="msg-link">$1 →</a>');
    t = t.replace(/\n/g, '<br>');
    return t;
  }

  function appendBub(text, role) {
    const div = document.createElement('div');
    div.className = 'msg-bub' + (role === 'user' ? ' msg-bub--user' : '');
    const content = role === 'user'
      ? esc(text).replace(/\n/g, '<br>')
      : renderBubText(text);
    div.innerHTML = '<p>' + content + '</p><span class="msg-ts">' + ts() + '</span>';
    msgsEl.appendChild(div);
    scrollBottom();
    return div;
  }

  function appendTyping() {
    const div = document.createElement('div');
    div.className = 'typing-dots';
    div.innerHTML = '<span></span><span></span><span></span>';
    msgsEl.appendChild(div);
    scrollBottom();
    return div;
  }

  function scrollBottom() {
    if (msgsCol) msgsCol.scrollTop = msgsCol.scrollHeight;
  }

  function getActiveLang() {
    var active = document.querySelector('.lang-flag--active');
    return active ? active.dataset.lang : 'ru-RU';
  }

  function sayOlga(text) {
    if (typeof window.speakOlga === 'function') window.speakOlga(text, getActiveLang());
  }

  function pulseLangFlags() {
    const flagsEl = document.querySelector('.agent-langs');
    if (!flagsEl) return;
    flagsEl.classList.add('lang-flags--pulse');
    setTimeout(function () { flagsEl.classList.remove('lang-flags--pulse'); }, 2400);
  }

  /* ══════════════════════════════════════════════════════════
     Локальный ответчик (fallback если Worker недоступен)
  ══════════════════════════════════════════════════════════ */
  var SMART_RULES = [
    {
      re: /цен|стоимост|сколько стоит|сколько будет|стоить|тариф|расценк|прайс|оплат|почём|по чём|расчёт|расчет/,
      answers: [
        'Три уровня перевода: постредактирование ИИ — от 300 ₽/стр, профессиональный (юридический, технический, медицинский, IT) — от 490 ₽/стр, премиум с вычиткой редактора или носителя языка — от 790 ₽/стр. Для точного расчёта загрузите файл в калькулятор.',
        'Цены зависят от типа и сложности: от 300 ₽/стр (постредактирование ИИ) до 790 ₽/стр (премиум). Срочный заказ — доплата 25–50%. Назовите язык и объём — скажу точнее.'
      ]
    },
    {
      re: /срок|как быстро|когда|срочн|сколько дней|сколько времени|за сколько|успеет/,
      answers: [
        'Стандартные сроки: 1–2 страницы — за 1 рабочий день, 10–15 стр. — 3–5 дней. Срочный перевод готов за 3–6 часов, доплата 25–50%. Скажите объём — дам точные сроки.',
        'Зависит от объёма: небольшой документ (1–3 стр.) — за 1 день, пакет договоров — 3–7 дней. Срочный режим — от 3 часов, доплата 25–50%. Что нужно перевести?'
      ]
    },
    {
      re: /язык|с какого|на какой|английск|немецк|китайск|японск|французск|испанск|итальянск|арабск|турецк/,
      answers: [
        'Работаем с 60+ языками: все европейские, арабский, китайский, японский, корейский, турецкий, хинди и другие. Какая языковая пара вас интересует?',
        'У нас переводчики по всем основным языкам мира. Редкие языки тоже доступны — уточните, с/на какой язык нужен перевод.'
      ]
    },
    {
      re: /нотариус|нотариальн|заверен|апостил|легализ|консульств/,
      answers: [
        'Нотариальным заверением и апостилированием мы не занимаемся. Могу помочь с письменным переводом документа — уточните язык и тип документа.',
        'Заверение у нотариуса и апостиль — не наше направление. Но если нужен сам перевод документа, с удовольствием поможем. Что за документ?'
      ]
    },
    {
      re: /постредактир|мтпе|mtpe|машинн|ии.перевод|ии-перевод|нейро|гпт|chatgpt|deepl/,
      answers: [
        'Постредактирование машинного перевода (MTPE) — от 300 ₽/стр. Редактор исправляет ошибки ИИ, выравнивает стиль и терминологию. Подходит для больших объёмов с ограниченным бюджетом.',
        'MTPE: берём машинный перевод (DeepL, GPT) и дорабатываем его до профессионального уровня. От 300 ₽/стр — экономия до 40% по сравнению с полным переводом.'
      ]
    },
    {
      re: /премиум|носитель|вычитк|редактор|нативн/,
      answers: [
        'Премиум-перевод — от 790 ₽/стр. Включает перевод, редактуру профильным специалистом и дополнительную вычитку носителем языка или отраслевым экспертом. Для ответственных документов.',
        'Премиум-уровень: перевод + два этапа вычитки (профильный редактор и/или носитель языка). От 790 ₽/стр. Идеально для публикаций, тендерной документации, международных контрактов.'
      ]
    },
    {
      re: /юридич|договор|контракт|устав|доверенност|суд|иск|протокол|соглашен/,
      answers: [
        'Юридический перевод — одно из ключевых направлений. Переводим договоры, уставы, доверенности, судебные документы. От 490 ₽/стр, переводчики с юридическим образованием.',
        'Специализируемся на юридических текстах: контракты, корпоративные документы, судебные материалы. Переводчики с юридическим образованием. Пришлите документ для расчёта.'
      ]
    },
    {
      re: /медицин|история болезни|справк|анализ|выписк|клиник|врач|диагноз|рецепт/,
      answers: [
        'Медицинские переводы выполняем с врачами-переводчиками: истории болезней, справки, инструкции к препаратам, клинические исследования. От 490 ₽/стр.',
        'Медицинский перевод — специализированное направление. Переводим для визовых центров, иностранных клиник, страховых компаний. От 490 ₽/стр, срок — от 1 дня.'
      ]
    },
    {
      re: /технич|инструкц|документац|чертёж|чертеж|руководств|spec|гост|iso/,
      answers: [
        'Технический перевод: инструкции, ТЗ, регламенты, чертежи, стандарты (ГОСТ, ISO). Переводчики с инженерным профилем. От 490 ₽/стр. Какая отрасль?',
        'Выполняем технические переводы в нефтегазовой, машиностроительной, строительной, энергетической сферах. С использованием отраслевых глоссариев. От 490 ₽/стр.'
      ]
    },
    {
      re: /it\b|программ|сайт|локализац|интерфейс|по\b|software|приложен|мобильн|игр/,
      answers: [
        'IT-локализация: интерфейсы ПО, мобильные приложения, игры, документация API, XLIFF/JSON файлы. Работаем с TMS-системами. От 490 ₽/стр. Какой проект?',
        'Локализуем ПО, сайты и мобильные приложения: единый глоссарий, консистентность терминологии. Форматы: XLIFF, JSON i18n, PO/POT, Android XML. От 490 ₽/стр.'
      ]
    },
    {
      re: /финанс|банк|отчёт|отчет|бухгалтер|баланс|аудит|бирж|ценные бумаги/,
      answers: [
        'Финансовый перевод: годовые отчёты, банковские документы, аудиторские заключения, проспекты эмиссии. Переводчики с экономическим образованием. От 490 ₽/стр.',
        'Переводим финансовую документацию для банков, инвестфондов, аудиторских компаний. Строгая конфиденциальность, NDA по запросу.'
      ]
    },
    {
      re: /маркетинг|реклам|слоган|бренд|контент|smm|копирайт/,
      answers: [
        'Маркетинговый перевод — это адаптация под культуру рынка, а не дословность. Переводим рекламные тексты, слоганы, контент для соцсетей, презентации. От 490 ₽/стр.',
        'Работаем с маркетинговыми материалами: брошюры, лендинги, email-рассылки, PR-тексты. Нативные редакторы для каждого языка.'
      ]
    },
    {
      re: /патент|изобретен|полезная модель|товарный знак|интеллектуальн/,
      answers: [
        'Патентный перевод — точность критична. Переводим патентные заявки, описания изобретений, формулы изобретений. Сотрудничаем с патентными поверенными. От 490 ₽/стр.',
        'Выполняем патентные переводы для Роспатента и иностранных патентных ведомств. Специализированные терминологические базы.'
      ]
    },
    {
      re: /научн|диссертац|статья|журнал|конференц|академич|исследован/,
      answers: [
        'Научные переводы: статьи для международных журналов, диссертации, монографии. Переводчики с учёной степенью в нужной области. От 490 ₽/стр.',
        'Переводим академические тексты с сохранением стиля и терминологии. Опыт публикаций в Scopus/Web of Science-журналах.'
      ]
    },
    {
      re: /таможн|вэд|внешнеэкономич|экспорт|импорт|декларац|сертификат|грузо/,
      answers: [
        'Переводы для ВЭД: декларации, сертификаты соответствия, паспорта качества, грузовые документы, договоры поставки. От 490 ₽/стр.',
        'Для внешнеэкономической деятельности переводим коммерческие предложения, договоры поставки, техническую документацию на товары.'
      ]
    },
    {
      re: /деловая переписк|письмо|email|корреспондент|переговор/,
      answers: [
        'Деловую переписку переводим в течение 1–4 часов. Email, официальные письма, коммерческие предложения. Уточните язык и объём — рассчитаю стоимость.',
        'Переводим деловые письма и email с учётом делового этикета страны-получателя. Быстро, корректно, по-деловому.'
      ]
    },
    {
      re: /художествен|книг|роман|рассказ|поэзия|сценарий|субтитр/,
      answers: [
        'Художественный перевод — творческая работа. Переводим книги, рассказы, сценарии, субтитры к фильмам. Каждый проект обсуждается индивидуально.',
        'Художественные переводы выполняют опытные литературные переводчики. Работаем с издательствами и кинокомпаниями.'
      ]
    },
    {
      re: /о вас|о компании|кто вы|когда основан|сколько лет работает|портфолио|отзыв|репутац/,
      answers: [
        'Бюро переводов «Ремарка» — работаем с 2001 года. Более 2400 выполненных заказов, рейтинг 4.98★. Специализация — B2B: юридические, технические, медицинские переводы для корпоративных клиентов.',
        '«Ремарка» работает с 2001 года. Сертифицированные переводчики с профильным образованием, более 200 постоянных корпоративных клиентов. Офис: Москва, Глинищевский пер., 6.'
      ]
    },
    {
      re: /гарантия|качество|проверк|редактур|корректур/,
      answers: [
        'Гарантируем качество: двухэтапная проверка (перевод + редактура), единый глоссарий, бесплатная правка в течение 30 дней. Работаем по договору, подписываем NDA.',
        'Каждый перевод проходит проверку редактором. При неудовлетворённости — бесплатные правки или возврат. NDA по запросу.'
      ]
    },
    {
      re: /договор|ндс|инн|реквизит|юрлицо|счёт|счет|акт|закрывающ/,
      answers: [
        'Работаем с юридическими лицами по договору. Выставляем полный пакет: договор, счёт, акт выполненных работ. ИНН 233406925261, ОГРНИП 312236329700014. Без НДС.',
        'Да, выставляем все закрывающие документы: договор, счёт, акт. Работаем с ИП и ООО. Для реквизитов — напишите на alefcom1@gmail.com.'
      ]
    },
    {
      re: /привет|здравствуй|добрый|хеллоу|hi\b|hello/,
      answers: [
        'Здравствуйте! Рада вас видеть. Чем могу помочь? Если нужен перевод — расскажите о задаче: язык, тип документа, объём и срочность.',
        'Добрый день! Помогу подобрать переводчика и рассчитать стоимость. Опишите вашу задачу.'
      ]
    },
    {
      re: /спасибо|благодарю|отлично|хорошо|понятно|ясно|окей|ок\b/,
      answers: [
        'Всегда пожалуйста! Если появятся вопросы — пишите. Для заказа: +7 (495) 970-44-13 или загрузите файл в калькулятор.',
        'Рада помочь! Звоните: +7 (495) 970-44-13, или пишите в WhatsApp: +7 (985) 970-44-13.'
      ]
    },
    {
      re: /замужем|женат|возраст|сколько.*лет|сколько вам|личн|семь|дети|парен|девушк|влюбл|свидани|отношен|красив|нравишься/,
      answers: [
        'Я виртуальный ассистент бюро переводов — личные вопросы не по адресу 😊 По переводу документов — спрашивайте всё что угодно!',
        'Это за пределами моей компетенции — я здесь по делу 😊 Если нужен перевод: расскажите язык, тип документа и объём.'
      ]
    }
  ];

  var _answerIdx = {};

  function smartReply(messages) {
    var lastUser = null;
    for (var i = messages.length - 1; i >= 0; i--) {
      if (messages[i].role === 'user') { lastUser = messages[i]; break; }
    }
    var text = (lastUser ? lastUser.content : '').toLowerCase();

    for (var r = 0; r < SMART_RULES.length; r++) {
      var rule = SMART_RULES[r];
      if (rule.re.test(text)) {
        var key = r;
        _answerIdx[key] = ((_answerIdx[key] || 0) + 1) % rule.answers.length;
        return rule.answers[_answerIdx[key]];
      }
    }

    if (/перевод|перевест|translate/.test(text)) {
      return 'Расскажите подробнее: с какого и на какой язык нужен перевод, что за документ и какой объём? Это поможет дать точные сроки и цену.';
    }

    if (/\[файл:/i.test(text) || /\[изображение:/i.test(text)) {
      return 'Получила ваш файл! Уточните, пожалуйста, с какого языка нужен перевод и когда нужна готовая работа.';
    }

    return 'Расскажите о задаче: какой документ нужно перевести, с какого языка и в какие сроки? Дам точный расчёт или передам специалисту.';
  }

  /* ── Send ── */
  async function send() {
    const text = inputEl.value.trim();
    if (!text) return;

    inputEl.value    = '';
    inputEl.disabled = true;
    sendBtn.disabled = true;

    appendBub(text, 'user');
    history.push({ role: 'user', content: text });

    const detected    = detectLang(text);
    const currentLang = getCurrentLang();
    const langMismatch =
      detected &&
      detected.split('-')[0] !== currentLang.split('-')[0]
        ? detected
        : (window._chatLangMismatch || null);
    window._chatLangMismatch = null;

    if (langMismatch) {
      const reply = LANG_PROMPTS[langMismatch] || LANG_PROMPTS['en-US'];
      const dot   = appendTyping();
      setTimeout(function () {
        dot.remove();
        appendBub(reply, 'assistant');
        history.push({ role: 'assistant', content: reply });
        if (typeof window.speakOlga === 'function') window.speakOlga(reply, langMismatch);
        pulseLangFlags();
        inputEl.disabled = false;
        sendBtn.disabled = false;
        inputEl.focus();
      }, 900);
      return;
    }

    const dot = appendTyping();
    let reply = null;

    if (WORKER_URL) {
      try {
        const res = await fetch(WORKER_URL, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ messages: history })
        });
        if (res.ok) {
          const data = await res.json();
          const candidate = (data.text || data.reply || '').trim();
          if (candidate && candidate.toLowerCase() !== 'ответ недоступен' && candidate.length > 5) {
            reply = candidate;
          }
        }
      } catch (e) {
        console.warn('Worker fetch error:', e);
      }
    }

    if (!reply) reply = smartReply(history);

    dot.remove();

    if (reply) {
      appendBub(reply, 'assistant');
      history.push({ role: 'assistant', content: reply });
      sayOlga(reply);
    } else {
      showOfflineFallback(text);
    }

    inputEl.disabled = false;
    sendBtn.disabled = false;
    inputEl.focus();
  }

  sendBtn.addEventListener('click', send);
  inputEl.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); send(); }
  });

  /* ── Прикрепление файла ── */
  const attachBtn = document.querySelector('.cw-btn-icon[title="Прикрепить файл"]');
  if (attachBtn) {
    const fileInput = document.createElement('input');
    fileInput.type    = 'file';
    fileInput.accept  = '.pdf,.doc,.docx,.odt,.txt,.rtf,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.webp,.zip,.rar,.7z';
    fileInput.style.cssText = 'position:absolute;width:0;height:0;opacity:0;pointer-events:none';
    document.body.appendChild(fileInput);

    attachBtn.addEventListener('click', function () { fileInput.click(); });

    fileInput.addEventListener('change', function () {
      const file = fileInput.files[0];
      if (!file) return;
      fileInput.value = '';

      const sizeMB = (file.size / 1048576).toFixed(1);
      const isImage = file.type.startsWith('image/');

      if (isImage) {
        const reader = new FileReader();
        reader.onload = function (ev) {
          const div = document.createElement('div');
          div.className = 'msg-bub msg-bub--user';
          div.innerHTML =
            '<img src="' + ev.target.result + '" alt="' + esc(file.name) +
            '" class="msg-img"><span class="msg-ts">' + ts() + '</span>';
          msgsEl.appendChild(div);
          scrollBottom();
          history.push({ role: 'user', content: '[Изображение: ' + file.name + ']' });
          autoReply('[Изображение: ' + file.name + ']');
        };
        reader.readAsDataURL(file);
      } else {
        const ext = file.name.split('.').pop().toUpperCase();
        const div = document.createElement('div');
        div.className = 'msg-bub msg-bub--user';
        div.innerHTML =
          '<div class="msg-file"><div class="msg-file-icon">' + esc(ext) + '</div>' +
          '<div class="msg-file-info"><span class="msg-file-name">' + esc(file.name) +
          '</span><span class="msg-file-size">' + sizeMB + ' МБ</span></div></div>' +
          '<span class="msg-ts">' + ts() + '</span>';
        msgsEl.appendChild(div);
        scrollBottom();
        history.push({ role: 'user', content: '[Файл: ' + file.name + ', ' + sizeMB + ' МБ]' });
        autoReply('[Файл: ' + file.name + ']');
      }
    });
  }

  async function autoReply(userText) {
    const dot = appendTyping();
    await new Promise(function(resolve) { setTimeout(resolve, 900); });
    dot.remove();
    const reply = smartReply(history)
      || 'Получила ваш файл! Уточните, пожалуйста, с какого языка нужен перевод и когда нужна готовая работа.';
    appendBub(reply, 'assistant');
    history.push({ role: 'assistant', content: reply });
    sayOlga(reply);
  }

  /* ── Офлайн-фолбэк: собираем контакт и шлём через EmailJS ── */
  function showOfflineFallback(userMessage) {
    const intro = 'Обрабатываю ваш запрос — дайте мне секунду. Чтобы я могла написать вам напрямую, оставьте e-mail или телефон:';
    appendBub(intro, 'assistant');
    sayOlga(intro);

    const wrap = document.createElement('div');
    wrap.className = 'chat-lead-form';
    wrap.innerHTML =
      '<input type="text" class="clf-input" placeholder="E-mail или телефон" />' +
      '<button class="clf-btn">Отправить</button>' +
      '<a href="https://wa.me/79859704413" class="clf-wa" target="_blank" rel="noopener">или написать в WhatsApp</a>';
    msgsEl.appendChild(wrap);
    scrollBottom();

    const inp = wrap.querySelector('.clf-input');
    const btn = wrap.querySelector('.clf-btn');

    async function submitLead() {
      const contact = inp.value.trim();
      if (!contact) { inp.focus(); return; }
      btn.disabled = true;
      btn.textContent = '…';

      const msgHistory = history
        .filter(function(m){ return m.role === 'user'; })
        .map(function(m){ return m.content; })
        .join('\n');

      if (ensureEjs()) {
        try {
          await emailjs.send(EJS_SERVICE_ID, EJS_TEMPLATE_ID, {
            to_email:   'alefcom1@gmail.com',
            from_name:  contact,
            from_email: contact.includes('@') ? contact : 'chat@moscowtrans.ru',
            reply_to:   contact.includes('@') ? contact : 'chat@moscowtrans.ru',
            phone:      contact.includes('@') ? '—' : contact,
            company:    '—',
            calc_type:  'Чат-сообщение с сайта',
            price_est:  '—',
            details:    msgHistory || userMessage,
            comment:    'Контакт для связи: ' + contact
          });
        } catch(e) { console.warn('Chat EmailJS error:', e); }
      }

      wrap.innerHTML = '';
      const thanks = 'Спасибо! Свяжусь с вами в течение 30 минут в рабочее время.';
      appendBub(thanks, 'assistant');
      sayOlga(thanks);
      history.push({ role: 'assistant', content: thanks });
    }

    btn.addEventListener('click', submitLead);
    inp.addEventListener('keydown', function(e){
      if (e.key === 'Enter') { e.preventDefault(); submitLead(); }
    });
  }

}());
