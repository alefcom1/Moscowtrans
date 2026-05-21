(function () {
  'use strict';

  /* ── Контент по страницам ────────────────────────── */
  var CONTENT = {
    'yuridicheskiy-perevod': {
      badge: 'ЮРИДИЧЕСКИЙ ПЕРЕВОД',
      h: 'Рассчитайте стоимость юридического перевода',
      sub: 'Загрузите договор, устав или судебный документ — автоматически определим объём и дадим точный расчёт с учётом нотариального заверения.',
      features: [
        { icon: 'scales', title: 'Юридическая точность', text: 'Переводчики с юридическим образованием. Знание российского и международного права.' },
        { icon: 'shield', title: 'Нотариальное заверение', text: 'Полный цикл: перевод + заверение нотариуса-партнёра. От 250 ₽/стр.' },
        { icon: 'lock', title: 'NDA и конфиденциальность', text: 'Подписываем соглашение о неразглашении по запросу клиента.' },
        { icon: 'check', title: 'Принимают суды и госорганы', text: 'Переводы принимаются в судах, ФНС, нотариусах и посольствах.' }
      ]
    },
    'tekhnicheskiy-perevod': {
      badge: 'ТЕХНИЧЕСКИЙ ПЕРЕВОД',
      h: 'Рассчитайте стоимость технического перевода',
      sub: 'Загрузите инструкцию, чертёж или технический регламент — подберём профильного переводчика и рассчитаем стоимость за минуту.',
      features: [
        { icon: 'gear', title: 'Инженерная экспертиза', text: 'Переводчики с техническим профильным образованием в нужной отрасли.' },
        { icon: 'file', title: 'Все форматы документов', text: 'PDF, DOCX, CAD-файлы, чертежи, XML-документация.' },
        { icon: 'flash', title: 'Отраслевой глоссарий', text: 'Единая терминологическая база для согласованности всех документов.' },
        { icon: 'check', title: 'ГОСТ и ISO', text: 'Знакомы со стандартами и требованиями к технической документации.' }
      ]
    },
    'meditsinskiy-perevod': {
      badge: 'МЕДИЦИНСКИЙ ПЕРЕВОД',
      h: 'Рассчитайте стоимость медицинского перевода',
      sub: 'Загрузите историю болезни, справку или клиническое исследование — переводчик с медицинским образованием выполнит заказ точно и в срок.',
      features: [
        { icon: 'heart', title: 'Медицинское образование', text: 'Переводчики с дипломами врачей и медицинских специалистов.' },
        { icon: 'shield', title: 'Строгая конфиденциальность', text: 'Медицинские данные обрабатываются по самым жёстким стандартам NDA.' },
        { icon: 'check', title: 'Принимают клиники за рубежом', text: 'Переводы признаются иностранными медицинскими учреждениями.' },
        { icon: 'flash', title: 'Срочные переводы', text: 'Срочный медицинский перевод готов за 3–6 часов.' }
      ]
    },
    'it-perevod': {
      badge: 'IT-ЛОКАЛИЗАЦИЯ',
      h: 'Рассчитайте стоимость IT-локализации',
      sub: 'Загрузите строки интерфейса, XLIFF/JSON файл или документацию API — рассчитаем стоимость и предложим TMS-интеграцию.',
      features: [
        { icon: 'code', title: 'TMS-интеграция', text: 'Работаем с Lokalise, Phrase, Crowdin. CI/CD webhooks по запросу.' },
        { icon: 'file', title: 'Все форматы', text: 'XLIFF, JSON i18n, PO/POT, Android XML, iOS Strings, Excel.' },
        { icon: 'gear', title: 'Единый глоссарий', text: 'Строим и поддерживаем отраслевую терминологическую базу.' },
        { icon: 'check', title: 'Нативные переводчики', text: 'Только носители языка для каждой локали. Контекст в интерфейсе.' }
      ]
    },
    'finansovyy-perevod': {
      badge: 'ФИНАНСОВЫЙ ПЕРЕВОД',
      h: 'Рассчитайте стоимость финансового перевода',
      sub: 'Загрузите годовой отчёт, аудиторское заключение или банковский договор — переводчик с экономическим образованием выполнит заказ точно.',
      features: [
        { icon: 'dollar', title: 'Экономическое образование', text: 'Переводчики с дипломами финансистов, аудиторов, банкиров.' },
        { icon: 'shield', title: 'Строгий NDA', text: 'Финансовые данные обрабатываются под полной конфиденциальностью.' },
        { icon: 'check', title: 'МСФО и GAAP', text: 'Знакомы с международными стандартами финансовой отчётности.' },
        { icon: 'flash', title: 'Сжатые дедлайны', text: 'Работаем с отчётами в высокий сезон и при срочных проверках.' }
      ]
    },
    'marketingovyy-perevod': {
      badge: 'МАРКЕТИНГОВЫЙ ПЕРЕВОД',
      h: 'Рассчитайте стоимость маркетингового перевода',
      sub: 'Загрузите рекламный текст, презентацию или контент для сайта — адаптируем под культуру целевого рынка, сохраним посыл и tone of voice.',
      features: [
        { icon: 'mega', title: 'Транскреация', text: 'Не просто перевод — культурная адаптация слоганов и рекламных текстов.' },
        { icon: 'gear', title: 'Нативные редакторы', text: 'Каждый текст вычитывает носитель языка с маркетинговым опытом.' },
        { icon: 'file', title: 'Все форматы', text: 'PPTX, InDesign, PDF, HTML, email-шаблоны, посты для соцсетей.' },
        { icon: 'check', title: 'Сохраняем бренд', text: 'Следуем вашему brand book и руководству по стилю.' }
      ]
    },
    'ved-perevod': {
      badge: 'ВЭД И ТАМОЖНЯ',
      h: 'Рассчитайте стоимость перевода документов ВЭД',
      sub: 'Загрузите таможенную декларацию, сертификат или контракт поставки — рассчитаем стоимость с учётом официального заверения.',
      features: [
        { icon: 'truck', title: 'Опыт ВЭД-документации', text: 'Знание требований ФТС, ЕврАзЭС, сертификации и стандартов.' },
        { icon: 'shield', title: 'Заверение по запросу', text: 'Нотариальное заверение и апостиль для международного использования.' },
        { icon: 'flash', title: 'Срочный режим', text: 'Таможенные документы — максимальный приоритет, выполняем за 1 день.' },
        { icon: 'check', title: 'Все языки ВЭД', text: 'Китайский, английский, немецкий, французский, японский и другие.' }
      ]
    },
    'patentnye-perevody': {
      badge: 'ПАТЕНТНЫЙ ПЕРЕВОД',
      h: 'Рассчитайте стоимость патентного перевода',
      sub: 'Загрузите патентную заявку, описание изобретения или формулу — специалист с профильным образованием обеспечит терминологическую точность.',
      features: [
        { icon: 'award', title: 'Патентная экспертиза', text: 'Переводчики, работающие с патентными поверенными и ФИПС.' },
        { icon: 'shield', title: 'Юридическая точность', text: 'Формула изобретения и реферат переводятся с максимальной точностью.' },
        { icon: 'file', title: 'Для Роспатента и EPO', text: 'Переводы принимаются российскими и зарубежными патентными ведомствами.' },
        { icon: 'lock', title: 'Строгий NDA', text: 'Изобретения охраняются полной конфиденциальностью до подачи заявки.' }
      ]
    },
    'nauchnyy-perevod': {
      badge: 'НАУЧНЫЙ ПЕРЕВОД',
      h: 'Рассчитайте стоимость научного перевода',
      sub: 'Загрузите статью, диссертацию или монографию — переводчик с учёной степенью в нужной области выполнит перевод с сохранением академического стиля.',
      features: [
        { icon: 'flask', title: 'Учёные степени', text: 'Переводчики с PhD и кандидатскими степенями в профильных науках.' },
        { icon: 'check', title: 'Scopus и WoS', text: 'Переведённые статьи принимаются в международных журналах.' },
        { icon: 'file', title: 'Академический стиль', text: 'Сохраняем научный стиль изложения и требования издательства.' },
        { icon: 'gear', title: 'Предметная терминология', text: 'Единая терминологическая база для каждой дисциплины.' }
      ]
    },
    'delovaya-perepiska': {
      badge: 'ДЕЛОВАЯ ПЕРЕПИСКА',
      h: 'Рассчитайте стоимость перевода деловой переписки',
      sub: 'Загрузите письмо, email или протокол переговоров — переведём с учётом делового этикета страны-адресата, быстро и профессионально.',
      features: [
        { icon: 'mail', title: 'Деловой этикет', text: 'Знаем нормы деловой переписки в разных культурах и странах.' },
        { icon: 'flash', title: 'Быстро', text: 'Короткое письмо переводим за 1–3 часа. Срочно — за 30 минут.' },
        { icon: 'check', title: 'Профессиональный тон', text: 'Письмо звучит как написанное носителем языка, а не переведённое.' },
        { icon: 'dollar', title: 'Выгодная цена', text: 'От 150 ₽ за короткое письмо. Без минимального заказа.' }
      ]
    }
  };

  var DEFAULT = {
    badge: 'БЕСПЛАТНЫЙ РАСЧЁТ',
    h: 'Рассчитайте стоимость перевода за 30 минут',
    sub: 'Загрузите документ, укажите языковую пару — получите прозрачный расчёт без скрытых комиссий.',
    features: [
      { icon: 'flash', title: 'Быстрый ответ', text: 'Расчёт за 30 минут. Срочное выполнение в тот же день.' },
      { icon: 'dollar', title: 'Прозрачные цены', text: 'Оплата за страницу или за слово. Никаких скрытых доплат.' },
      { icon: 'lock', title: '100% конфиденциально', text: 'Файлы в защищённом хранилище. NDA по запросу.' },
      { icon: 'check', title: 'Гарантия качества', text: 'Двухэтапная редактура. Бесплатные правки 30 дней.' }
    ]
  };

  var ICONS = {
    flash:  '<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>',
    dollar: '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>',
    lock:   '<rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
    check:  '<polyline points="20 6 9 17 4 12"/>',
    gear:   '<circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.41 1.41M6.34 17.66l-1.41 1.41M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2m16 0h2M12 2v2m0 16v2"/>',
    shield: '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>',
    file:   '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
    code:   '<polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>',
    heart:  '<path d="M22 12h-4l-3 9L9 3l-3 9H2"/>',
    award:  '<circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/>',
    flask:  '<path d="M9 3h6"/><path d="M6 20h12"/><path d="M9 3v8l-3 9"/><path d="M15 3v8l3 9"/>',
    mail:   '<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>',
    mega:   '<polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/>',
    truck:  '<rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
    scales: '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>',
    globe:  '<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>'
  };

  function svgIcon(name) {
    var d = ICONS[name] || ICONS.check;
    return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' + d + '</svg>';
  }

  var page = location.pathname.replace(/^.*\//, '').replace('.html', '');
  var data = CONTENT[page] || DEFAULT;

  /* ── Случай 1: calc-hero-layout уже есть (index.html) ── */
  var hEl    = document.getElementById('calc-hero-heading');
  var subEl  = document.getElementById('calc-hero-sub');
  var badge  = document.querySelector('.calc-badge');
  var featEl = document.querySelector('.calc-features');

  if (hEl) {
    hEl.innerHTML = data.h;
    if (subEl)   subEl.textContent = data.sub;
    if (badge)   badge.textContent = data.badge;
    if (featEl && data.features) {
      featEl.innerHTML = data.features.map(function (f) {
        return '<li class="cf-item">' +
          '<span class="cf-icon">' + svgIcon(f.icon) + '</span>' +
          '<div><strong>' + f.title + '</strong><span>' + f.text + '</span></div>' +
          '</li>';
      }).join('');
    }
    return;
  }

  /* ── Случай 2: старая структура — перестраиваем динамически ── */
  var section = document.getElementById('calc-section');
  if (!section) return;

  var container = section.querySelector('.container');
  if (!container) return;

  var widget = document.getElementById('calc-docs');
  if (!widget) return;

  /* Меняем классы секции */
  section.className = section.className.replace('sec--alt', '').replace('sec-calculator', 'sec-calculator-hero');

  /* Строим левую колонку */
  var featuresHTML = (data.features || DEFAULT.features).map(function (f) {
    return '<li class="cf-item">' +
      '<span class="cf-icon">' + svgIcon(f.icon) + '</span>' +
      '<div><strong>' + f.title + '</strong><span>' + f.text + '</span></div>' +
      '</li>';
  }).join('');

  var leftHTML =
    '<div class="calc-hero-left">' +
      '<span class="calc-badge">' + data.badge + '</span>' +
      '<h2 class="calc-hero-h">' + data.h + '</h2>' +
      '<p class="calc-hero-sub">' + data.sub + '</p>' +
      '<ul class="calc-features">' + featuresHTML + '</ul>' +
      '<div class="calc-trust-row">' +
        '<span>✓ Без предоплаты</span>' +
        '<span>✓ Дипломированные переводчики</span>' +
        '<span>✓ 4.98/5 рейтинг</span>' +
        '<span>✓ 2 400+ заказов</span>' +
      '</div>' +
    '</div>';

  /* Обёртка правой колонки */
  var rightDiv = document.createElement('div');
  rightDiv.className = 'calc-hero-right';
  widget.parentNode.insertBefore(rightDiv, widget);
  rightDiv.appendChild(widget);

  /* Создаём layout-обёртку */
  var layout = document.createElement('div');
  layout.className = 'calc-hero-layout';
  layout.innerHTML = leftHTML;
  layout.appendChild(rightDiv);

  /* Убираем старый sec-head и вставляем layout */
  var oldHead = container.querySelector('.sec-head');
  if (oldHead) oldHead.remove();
  container.insertBefore(layout, container.firstChild);

}());
