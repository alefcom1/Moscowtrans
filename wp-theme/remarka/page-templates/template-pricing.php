<?php
/**
 * Template Name: Стоимость перевода
 */
get_header();
?>

<div class="hero-bg-block">
  <section class="pricing-calc-hero">
    <div class="container">
      <div class="pch-layout">

        <!-- Left: marketing copy -->
        <div class="pch-left">
          <nav class="cw-breadcrumbs" aria-label="Хлебные крошки">
            <a href="<?php echo esc_url(home_url('/')); ?>">Главная</a>
            <span class="cw-bc-sep" aria-hidden="true">›</span>
            <span class="cw-bc-current" aria-current="page">Стоимость перевода</span>
          </nav>
          <h1 class="pch-h1">Рассчитайте стоимость перевода онлайн</h1>
          <p class="pch-tagline">Укажите языки, тип документа и объём — получите ориентировочную цену прямо сейчас. Точный расчёт — за 15 минут после отправки файла менеджеру.</p>
          <ul class="pch-bullets">
            <li class="pch-bullet"><span class="pch-bullet-dot"></span>Фиксированная цена до начала работы</li>
            <li class="pch-bullet"><span class="pch-bullet-dot"></span>Скидки от 10 страниц</li>
            <li class="pch-bullet"><span class="pch-bullet-dot"></span>Профильные переводчики по тематике</li>
            <li class="pch-bullet"><span class="pch-bullet-dot"></span>Работа с юрлицами по договору</li>
          </ul>
        </div>

        <!-- Right: inline pricing calculator -->
        <div class="pch-right">
          <div class="pch-card">
            <div class="pch-card-label">Онлайн-калькулятор</div>
            <p class="pch-card-title">Узнайте цену за секунды</p>

            <div class="pch-fields">

              <!-- Language pair -->
              <div class="pch-lang-row">
                <div class="pch-field">
                  <label for="pch-from">Язык оригинала</label>
                  <select id="pch-from"></select>
                </div>
                <button class="pch-swap-btn" id="pch-swap" type="button" title="Поменять языки">⇄</button>
                <div class="pch-field">
                  <label for="pch-to">Язык перевода</label>
                  <select id="pch-to"></select>
                </div>
              </div>

              <!-- Document type -->
              <div class="pch-field">
                <label for="pch-doctype">Тип документа</label>
                <select id="pch-doctype">
                  <option value="400">Технический — инструкции, руководства, КД</option>
                  <option value="500">Юридический — договоры, контракты, уставы</option>
                  <option value="500">Медицинский — протоколы, инструкции к препаратам</option>
                  <option value="450">IT-документация — ПО, интерфейсы, локализация</option>
                  <option value="550">Финансовый — МСФО/GAAP, аудиторские заключения</option>
                  <option value="400">Маркетинговый — сайты, реклама, пресс-релизы</option>
                  <option value="600">Патентный — описания изобретений, формулы</option>
                  <option value="350">Деловая переписка — письма, коммерческие предложения</option>
                  <option value="400" selected>Общий перевод</option>
                </select>
              </div>

              <!-- Page count -->
              <div class="pch-field">
                <label for="pch-pages">Количество страниц&nbsp;<span style="font-weight:400;text-transform:none;letter-spacing:0;font-size:10px">(1 стр. = 1 800 знаков)</span></label>
                <input type="number" id="pch-pages" value="5" min="1" max="9999" step="1">
              </div>

              <!-- Urgency -->
              <div class="pch-field">
                <label>Срочность</label>
                <div class="pch-urgency">
                  <button class="pch-urg-btn active" data-urgency="1" type="button">Стандарт</button>
                  <button class="pch-urg-btn" data-urgency="1.5" type="button">Срочно ×1.5</button>
                </div>
              </div>

            </div><!-- /pch-fields -->

            <!-- Price results -->
            <div class="pch-results">
              <div class="pch-tier">
                <div class="pch-tier-name">Постред. ИИ</div>
                <div class="pch-tier-price" id="pch-price-ai">—</div>
                <div class="pch-tier-days"  id="pch-days-ai">—</div>
              </div>
              <div class="pch-tier pch-tier--featured">
                <div class="pch-tier-badge">Популярный</div>
                <div class="pch-tier-name">Профессиональный</div>
                <div class="pch-tier-price" id="pch-price-pro">—</div>
                <div class="pch-tier-days"  id="pch-days-pro">—</div>
              </div>
              <div class="pch-tier">
                <div class="pch-tier-name">Премиум</div>
                <div class="pch-tier-price" id="pch-price-prem">—</div>
                <div class="pch-tier-days"  id="pch-days-prem">—</div>
              </div>
            </div>

            <a href="#calc-section" class="pch-cta-btn">Получить точный расчёт бесплатно →</a>
            <p class="pch-disclaimer">* Ориентировочная стоимость. Точная цена — после оценки файла менеджером</p>
          </div>
        </div><!-- /pch-right -->

      </div><!-- /pch-layout -->
    </div><!-- /container -->
  </section>
</div><!-- /hero-bg-block -->

<script>
(function () {
  var LANGS = [
    { name: 'Русский',         mult: 1.0  },
    { name: 'Английский',      mult: 1.0  },
    { name: 'Немецкий',        mult: 1.0  },
    { name: 'Французский',     mult: 1.0  },
    { name: 'Испанский',       mult: 1.0  },
    { name: 'Итальянский',     mult: 1.0  },
    { name: 'Нидерландский',   mult: 1.0  },
    { name: 'Португальский',   mult: 1.0  },
    { name: 'Польский',        mult: 1.05 },
    { name: 'Чешский',         mult: 1.05 },
    { name: 'Словацкий',       mult: 1.05 },
    { name: 'Болгарский',      mult: 1.1  },
    { name: 'Румынский',       mult: 1.1  },
    { name: 'Венгерский',      mult: 1.1  },
    { name: 'Украинский',      mult: 1.1  },
    { name: 'Белорусский',     mult: 1.1  },
    { name: 'Шведский',        mult: 1.15 },
    { name: 'Норвежский',      mult: 1.15 },
    { name: 'Датский',         mult: 1.15 },
    { name: 'Финский',         mult: 1.2  },
    { name: 'Казахский',       mult: 1.2  },
    { name: 'Турецкий',        mult: 1.2  },
    { name: 'Греческий',       mult: 1.2  },
    { name: 'Грузинский',      mult: 1.3  },
    { name: 'Армянский',       mult: 1.3  },
    { name: 'Азербайджанский', mult: 1.3  },
    { name: 'Хинди',           mult: 1.5  },
    { name: 'Арабский',        mult: 1.6  },
    { name: 'Иврит',           mult: 1.6  },
    { name: 'Персидский (фарси)', mult: 1.6 },
    { name: 'Корейский',       mult: 1.7  },
    { name: 'Японский',        mult: 1.8  },
    { name: 'Китайский',       mult: 1.8  },
  ];

  var fromSel = document.getElementById('pch-from');
  var toSel   = document.getElementById('pch-to');
  if (!fromSel || !toSel) return;

  LANGS.forEach(function (lang, i) {
    fromSel.add(new Option(lang.name, i));
    toSel.add(new Option(lang.name, i));
  });
  fromSel.value = '0'; // Русский
  toSel.value   = '1'; // Английский

  document.getElementById('pch-swap').addEventListener('click', function () {
    var tmp = fromSel.value;
    fromSel.value = toSel.value;
    toSel.value   = tmp;
    recalc();
  });

  var urgency = 1;
  document.querySelectorAll('.pch-urg-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.pch-urg-btn').forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      urgency = parseFloat(btn.dataset.urgency);
      recalc();
    });
  });

  function fmtPrice(p) {
    return 'от ' + (Math.round(p / 50) * 50).toLocaleString('ru-RU') + ' ₽';
  }
  function fmtDays(d) {
    if (d === 1) return '1 день';
    if (d >= 2 && d <= 4) return d + ' дня';
    return d + ' дней';
  }

  function recalc() {
    var fromMult  = LANGS[parseInt(fromSel.value)].mult;
    var toMult    = LANGS[parseInt(toSel.value)].mult;
    var langMult  = Math.max(fromMult, toMult);
    var baseRate  = parseInt(document.getElementById('pch-doctype').value);
    var pages     = Math.max(1, parseInt(document.getElementById('pch-pages').value) || 1);
    var urg       = urgency;

    var volDiscount = pages >= 50 ? 0.85 : pages >= 20 ? 0.92 : pages >= 10 ? 0.96 : 1;

    var tiers = [
      { id: 'ai',   mult: 0.55, pagesPerDay: 25, minDays: 1 },
      { id: 'pro',  mult: 1.0,  pagesPerDay: 10, minDays: 2 },
      { id: 'prem', mult: 1.6,  pagesPerDay: 7,  minDays: 3 },
    ];

    tiers.forEach(function (t) {
      var price = pages * baseRate * langMult * urg * t.mult * volDiscount;
      var days  = Math.max(t.minDays, Math.ceil(pages / t.pagesPerDay));
      if (urg > 1) days = Math.max(1, Math.ceil(days / 1.5));

      document.getElementById('pch-price-' + t.id).textContent = fmtPrice(price);
      document.getElementById('pch-days-'  + t.id).textContent = fmtDays(days);
    });
  }

  fromSel.addEventListener('change', recalc);
  toSel.addEventListener('change', recalc);
  document.getElementById('pch-doctype').addEventListener('change', recalc);
  document.getElementById('pch-pages').addEventListener('input', recalc);

  recalc();
}());
</script>

  <!-- Введение -->
  <section class="sec sec-intro">
    <div class="container">
      <div class="intro-grid">
        <div class="intro-text">
          <p class="intro-label">Бюро переводов «Ремарка», Москва</p>
          <h1 class="intro-title">Стоимость профессионального перевода</h1>
          <p class="intro-tagline">Точная цена — за 15 минут, без предоплаты</p>
          <p class="intro-body">Стоимость перевода складывается из нескольких переменных: языковая пара определяет базовый тариф — европейские языки дешевле, редкие восточные и азиатские дороже из-за узкого рынка специалистов. Тематика документа напрямую влияет на цену: стандартный деловой текст оценивается ниже, чем нефтегазовая документация, фармацевтические инструкции или патентные заявки.</p>
          <p class="intro-body">Мы работаем с корпоративными клиентами: договорами, техническими регламентами, медицинскими протоколами, IT-документацией и финансовой отчётностью. Наши цены рассчитаны на B2B-сегмент — компании, которым важны точность терминологии и соблюдение отраслевых стандартов.</p>
          <p class="intro-body">Чтобы получить точный расчёт, достаточно прислать файл Ольге в чат. За 15 минут мы определим объём в страницах (1 стр. = 1800 знаков без пробелов), укажем точную стоимость и срок выполнения. Цена фиксируется до начала работы.</p>
        </div>
        <div class="intro-visual">
          <div class="intro-features">
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              </div>
              <div><strong>Точный расчёт за 15 мин</strong><span>Вышлите файл — назовём цену без промедления</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              </div>
              <div><strong>Фиксированная цена</strong><span>Стоимость не меняется в процессе работы</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
              </div>
              <div><strong>Скидки от объёма</strong><span>От 10 страниц цена снижается автоматически</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
              </div>
              <div><strong>Срочность по запросу</strong><span>Экспресс-перевод с прозрачной наценкой</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9c1.3 0 2.54.28 3.65.77"/></svg>
              </div>
              <div><strong>Без предоплаты</strong><span>Начинаем работу после согласования цены</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 9h.01M15 9h.01M9 15h6"/></svg>
              </div>
              <div><strong>NDA по запросу</strong><span>Конфиденциальность всех переданных материалов</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Цифры -->
  <section class="sec-stats">
    <div class="container">
      <div class="stats-row">
        <div class="stat-item"><span class="stat-num">25<span class="stat-suffix">+</span></span><span class="stat-label">лет на рынке</span></div>
        <div class="stat-item"><span class="stat-num">60<span class="stat-suffix">+</span></span><span class="stat-label">языков перевода</span></div>
        <div class="stat-item"><span class="stat-num">2 400<span class="stat-suffix">+</span></span><span class="stat-label">выполненных заказов</span></div>
        <div class="stat-item"><span class="stat-num">4.98<span class="stat-suffix">★</span></span><span class="stat-label">средний рейтинг</span></div>
      </div>
    </div>
  </section>

  <!-- Форматы перевода -->
  <section class="sec sec--alt sec-pricing" id="pricing">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Форматы перевода</h2>
        <p class="sec-sub">Три уровня сервиса — от бюджетного редактирования ИИ-перевода до премиального с экспертной проверкой</p>
      </div>
      <div class="pricing-row">
        <div class="price-card">
          <div class="price-icon"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg></div>
          <div class="price-name">Постредактирование ИИ</div>
          <div class="price-desc">Правка машинного перевода профессиональным переводчиком</div>
          <div class="price-amount">от 250 <span style="font-size:20px;font-weight:600">₽</span></div>
          <span class="price-unit">за страницу (1800 зн.)</span>
          <ul class="price-features"><li>Редактура машинного перевода</li><li>Исправление ошибок и смысловых несоответствий</li><li>Срок: от 1 рабочего дня</li><li>Форматы: DOC, PDF, TXT, XLSX</li></ul>
          <a href="#calc-section" class="price-btn price-btn--outline">Заказать</a>
        </div>
        <div class="price-card price-card--featured">
          <div class="price-badge">Популярный выбор</div>
          <div class="price-icon"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg></div>
          <div class="price-name">Профессиональный</div>
          <div class="price-desc">Перевод с нуля специалистом с профильным образованием</div>
          <div class="price-amount">от 500 <span style="font-size:20px;font-weight:600">₽</span></div>
          <span class="price-unit">за страницу (1800 зн.)</span>
          <ul class="price-features"><li>Перевод профильным специалистом</li><li>Корректура и вычитка</li><li>Срок: 1–3 рабочих дня</li><li>Сохранение форматирования</li></ul>
          <a href="#calc-section" class="price-btn price-btn--primary">Заказать</a>
        </div>
        <div class="price-card">
          <div class="price-icon"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <div class="price-name">Премиум</div>
          <div class="price-desc">Перевод с дополнительной редактурой или вычиткой носителем</div>
          <div class="price-amount">от 800 <span style="font-size:20px;font-weight:600">₽</span></div>
          <span class="price-unit">за страницу (1800 зн.)</span>
          <ul class="price-features"><li>Перевод + редактура профильным специалистом</li><li>Вычитка носителем языка</li><li>Срок: 2–5 рабочих дней</li><li>Идеально для публикаций и тендеров</li></ul>
          <a href="#calc-section" class="price-btn price-btn--outline">Заказать</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Стоимость по типам документов -->
  <section class="sec sec-doc-types">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Стоимость по типам документов</h2>
        <p class="sec-sub">Базовые тарифы для профессионального перевода с русского и на русский язык</p>
      </div>
      <div class="doc-types-grid">

        <div class="doc-type-card">
          <div class="dtc-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg></div>
          <div class="dtc-name">Технический перевод</div>
          <div class="dtc-desc">Инструкции, руководства, спецификации, КД</div>
          <div class="dtc-price">от 400 ₽/стр.</div>
        </div>

        <div class="doc-type-card">
          <div class="dtc-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/><circle cx="12" cy="12" r="4"/></svg></div>
          <div class="dtc-name">Юридический перевод</div>
          <div class="dtc-desc">Договоры, контракты, уставы, корпоративные документы</div>
          <div class="dtc-price">от 500 ₽/стр.</div>
        </div>

        <div class="doc-type-card">
          <div class="dtc-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2h-2"/><rect x="9" y="2" width="6" height="4" rx="1"/><line x1="12" y1="11" x2="12" y2="17"/><line x1="9" y1="14" x2="15" y2="14"/></svg></div>
          <div class="dtc-name">Медицинский перевод</div>
          <div class="dtc-desc">Клинические протоколы, инструкции к препаратам</div>
          <div class="dtc-price">от 500 ₽/стр.</div>
        </div>

        <div class="doc-type-card">
          <div class="dtc-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg></div>
          <div class="dtc-name">IT-перевод</div>
          <div class="dtc-desc">Документация ПО, интерфейсы, локализация</div>
          <div class="dtc-price">от 450 ₽/стр.</div>
        </div>

        <div class="doc-type-card">
          <div class="dtc-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
          <div class="dtc-name">Финансовый перевод</div>
          <div class="dtc-desc">Отчётность МСФО/GAAP, аудиторские заключения</div>
          <div class="dtc-price">от 550 ₽/стр.</div>
        </div>

        <div class="doc-type-card">
          <div class="dtc-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg></div>
          <div class="dtc-name">Маркетинговый перевод</div>
          <div class="dtc-desc">Сайты, рекламные материалы, пресс-релизы</div>
          <div class="dtc-price">от 400 ₽/стр.</div>
        </div>

        <div class="doc-type-card">
          <div class="dtc-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="9" y1="18" x2="15" y2="18"/><line x1="10" y1="22" x2="14" y2="22"/><path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0 0 18 8 6 6 0 0 0 6 8c0 1 .23 2.23 1.5 3.5A4.61 4.61 0 0 1 8.91 14"/></svg></div>
          <div class="dtc-name">Патентный перевод</div>
          <div class="dtc-desc">Описания изобретений, формулы, патентная документация</div>
          <div class="dtc-price">от 600 ₽/стр.</div>
        </div>

        <div class="doc-type-card">
          <div class="dtc-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></div>
          <div class="dtc-name">Деловая переписка</div>
          <div class="dtc-desc">Письма, коммерческие предложения, запросы</div>
          <div class="dtc-price">от 350 ₽/стр.</div>
        </div>

      </div>
      <p class="doc-types-note">* Цены для языковой пары русский ↔ английский при стандартных сроках. Для других языковых пар уточняйте у менеджера.</p>
    </div>
  </section>

  <!-- Примеры стоимости -->
  <section class="sec sec--alt sec-price-examples">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Примеры стоимости</h2>
        <p class="sec-sub">Типичные заказы московских B2B-клиентов — ориентировочные цены при стандартных сроках</p>
      </div>
      <div class="price-examples-grid">

        <div class="price-example-card">
          <div class="pe-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/></svg></div>
          <p class="pe-title">Договор поставки<br>5 стр. (рус. → англ.)</p>
          <div class="pe-price">от 2 500 ₽</div>
          <div class="pe-meta">Срок: 2–3 рабочих дня</div>
        </div>

        <div class="price-example-card">
          <div class="pe-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg></div>
          <p class="pe-title">Техническая инструкция<br>15 стр. (нем. → рус.)</p>
          <div class="pe-price">от 6 000 ₽</div>
          <div class="pe-meta">Срок: 3–5 рабочих дней</div>
        </div>

        <div class="price-example-card">
          <div class="pe-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></div>
          <p class="pe-title">Веб-сайт компании<br>25 стр. (рус. → англ.)</p>
          <div class="pe-price">от 10 000 ₽</div>
          <div class="pe-meta">Срок: 5–7 рабочих дней</div>
        </div>

        <div class="price-example-card">
          <div class="pe-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2h-2"/><rect x="9" y="2" width="6" height="4" rx="1"/><line x1="12" y1="11" x2="12" y2="17"/><line x1="9" y1="14" x2="15" y2="14"/></svg></div>
          <p class="pe-title">Медицинский протокол<br>10 стр. (англ. → рус.)</p>
          <div class="pe-price">от 5 000 ₽</div>
          <div class="pe-meta">Срок: 3–4 рабочих дня</div>
        </div>

      </div>
    </div>
  </section>

<?php
get_template_part('template-parts/section-calc', null, [
    'heading' => 'Рассчитайте стоимость за&nbsp;15&nbsp;минут',
    'sub'     => 'Загрузите документ — автоматически определим объём и рассчитаем цену.',
]);

get_footer();
