<?php
/**
 * Homepage template (front-page.php).
 * Displayed when "Front page displays → A static page" is set in WP settings.
 */
get_header();
?>

  <?php get_template_part('template-parts/hero-chat-window', null, [
      'home_dots'  => true,
      'greeting_1' => 'Здравствуйте! 👋 Я Ольга, менеджер бюро переводов «Ремарка». Чем могу вам помочь сегодня?',
      'greeting_2' => 'Вы можете общаться со мной голосовыми сообщениями.',
      'greeting_3' => 'Кроме русского я понимаю английский и итальянский. 🇷🇺&nbsp;🇬🇧&nbsp;🇮🇹',
  ]); ?>

  <!-- ════════════════ SERVICES ════════════════ -->
  <section class="services-section" id="services">
    <div class="services-row">
      <a href="/tekhnicheskiy-perevod/" class="srv-card">
        <div class="srv-icon-wrap srv-col-violet"><svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg></div>
        <div class="srv-text"><h3>Технический перевод</h3><p>Инструкции, руководства, спецификации, чертежи</p></div>
        <div class="srv-bottom"><span class="srv-price">От 400 р/стр.</span><span class="srv-arrow srv-arr-violet"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg></span></div>
      </a>
      <a href="/yuridicheskiy-perevod/" class="srv-card">
        <div class="srv-icon-wrap srv-col-violet"><svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8L14 2z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="11" x2="12" y2="18"/></svg></div>
        <div class="srv-text"><h3>Юридический перевод</h3><p>Договоры, контракты, учредительные документы</p></div>
        <div class="srv-bottom"><span class="srv-price">От 400 р/стр.</span><span class="srv-arrow srv-arr-violet"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg></span></div>
      </a>
      <a href="/meditsinskiy-perevod/" class="srv-card">
        <div class="srv-icon-wrap srv-col-teal"><svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg></div>
        <div class="srv-text"><h3>Медицинский перевод</h3><p>Исследования, протоколы, заключения, инструкции</p></div>
        <div class="srv-bottom"><span class="srv-price">От 400 р/стр.</span><span class="srv-arrow srv-arr-teal"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg></span></div>
      </a>
      <a href="/it-perevod/" class="srv-card">
        <div class="srv-icon-wrap srv-col-blue"><svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg></div>
        <div class="srv-text"><h3>IT перевод</h3><p>Интерфейсы, документация, локализация ПО</p></div>
        <div class="srv-bottom"><span class="srv-price">От 300 р/стр.</span><span class="srv-arrow srv-arr-blue"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg></span></div>
      </a>
      <a href="/perevod-saytov/" class="srv-card">
        <div class="srv-icon-wrap srv-col-blue"><svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></div>
        <div class="srv-text"><h3>Перевод сайтов</h3><p>Локализация, SEO адаптация, международный рынок</p></div>
        <div class="srv-bottom"><span class="srv-price">От 300 р/стр.</span><span class="srv-arrow srv-arr-blue"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg></span></div>
      </a>
    </div>
  </section>

  </div><!-- /hero-bg-block -->

  <!-- ════════════════ LANGUAGES ════════════════ -->
  <section class="sec sec-langs" id="languages">
    <div class="container">
      <div class="sec-head sec-head--center">
        <div class="langs-stat">
          <div class="langs-stat-num">60+</div>
          <span class="langs-stat-label">языков перевода — специалисты с профильным образованием для каждого направления</span>
        </div>
      </div>
      <div class="langs-cloud">
        <span class="lang-pill">🇬🇧 Английский</span><span class="lang-pill">🇩🇪 Немецкий</span><span class="lang-pill">🇫🇷 Французский</span><span class="lang-pill">🇪🇸 Испанский</span><span class="lang-pill">🇮🇹 Итальянский</span><span class="lang-pill">🇵🇹 Португальский</span><span class="lang-pill">🇨🇳 Китайский</span><span class="lang-pill">🇯🇵 Японский</span><span class="lang-pill">🇸🇦 Арабский</span><span class="lang-pill">🇰🇷 Корейский</span><span class="lang-pill">🇹🇷 Турецкий</span><span class="lang-pill">🇮🇷 Персидский</span><span class="lang-pill">🇮🇳 Хинди</span><span class="lang-pill">🇻🇳 Вьетнамский</span><span class="lang-pill">🇹🇭 Тайский</span><span class="lang-pill">🇮🇩 Индонезийский</span><span class="lang-pill">🇵🇱 Польский</span><span class="lang-pill">🇳🇱 Нидерландский</span><span class="lang-pill">🇸🇪 Шведский</span><span class="lang-pill">🇳🇴 Норвежский</span><span class="lang-pill">🇩🇰 Датский</span><span class="lang-pill">🇫🇮 Финский</span><span class="lang-pill">🇨🇿 Чешский</span><span class="lang-pill">🇷🇴 Румынский</span><span class="lang-pill">🇭🇺 Венгерский</span><span class="lang-pill">🇧🇬 Болгарский</span><span class="lang-pill">🇬🇷 Греческий</span><span class="lang-pill">🇺🇦 Украинский</span><span class="lang-pill">🇧🇾 Белорусский</span><span class="lang-pill">🇰🇿 Казахский</span><span class="lang-pill">🇦🇲 Армянский</span><span class="lang-pill">🇬🇪 Грузинский</span><span class="lang-pill">🇦🇿 Азербайджанский</span><span class="lang-pill">🇮🇱 Иврит</span>
        <span class="lang-pill lang-pill--more">и другие…</span>
      </div>
    </div>
  </section>

  <!-- ════════════════ TOPICS ════════════════ -->
  <section class="sec sec--alt sec-topics" id="industries">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h1 class="sec-title">Бюро переводов в Москве</h1>
        <p class="sec-sub">Специализированные переводчики для каждой отрасли — точная терминология и глубокое понимание предмета</p>
      </div>
      <div class="topics-grid">
        <a href="/yuridicheskiy-perevod/" class="topic-card"><div class="topic-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div><h3>Юридические документы</h3><p>Договоры, иски, доверенности, уставы, лицензии</p></a>
        <a href="/tekhnicheskiy-perevod/" class="topic-card"><div class="topic-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg></div><h3>Технические тексты</h3><p>Конструкторская документация, инструкции, патенты, стандарты</p></a>
        <a href="/meditsinskiy-perevod/" class="topic-card"><div class="topic-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg></div><h3>Медицина и фармацея</h3><p>Медкарты, инструкции к препаратам, клинические исследования</p></a>
        <a href="/it-perevod/" class="topic-card"><div class="topic-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg></div><h3>IT и разработка</h3><p>Интерфейсы, документация API, игры, мобильные приложения</p></a>
        <a href="/marketingovyy-perevod/" class="topic-card"><div class="topic-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l19-9-9 19-2-8-8-2z"/></svg></div><h3>Маркетинг и реклама</h3><p>Сайты, презентации, SMM, рекламные материалы</p></a>
        <a href="/finansovyy-perevod/" class="topic-card"><div class="topic-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div><h3>Финансы и банки</h3><p>Отчётность, аудит, проспекты эмиссии, банковские документы</p></a>
        <a href="/ved-perevod/" class="topic-card"><div class="topic-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg></div><h3>Таможенные и ВЭД</h3><p>Декларации, сертификаты происхождения, контракты, инвойсы</p></a>
        <a href="/nauchnyy-perevod/" class="topic-card"><div class="topic-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg></div><h3>Академические работы</h3><p>Научные статьи, диссертации, рефераты, рецензии</p></a>
        <a href="/patentnye-perevody/" class="topic-card"><div class="topic-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/></svg></div><h3>Патенты и интеллектуальная собственность</h3><p>Заявки ВОИС, описания изобретений, товарные знаки</p></a>
        <a href="/perevod-saytov/" class="topic-card"><div class="topic-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></div><h3>Локализация сайтов</h3><p>SEO-адаптация, CMS, интернет-магазины, мобильные приложения</p></a>
        <a href="/delovaya-perepiska/" class="topic-card"><div class="topic-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></div><h3>Деловая переписка</h3><p>Официальные письма, протоколы, корпоративные обращения</p></a>
        <a href="/pismennyy-perevod/" class="topic-card"><div class="topic-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"/><line x1="7" y1="2" x2="7" y2="22"/><line x1="17" y1="2" x2="17" y2="22"/><line x1="2" y1="12" x2="22" y2="12"/></svg></div><h3>Письменный перевод</h3><p>Документы, тексты, публикации — точно и в срок</p></a>
      </div>
    </div>
  </section>

  <!-- ════════════════ HOW WE WORK ════════════════ -->
  <section class="sec sec-how">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Как мы работаем</h2>
        <p class="sec-sub">Шесть шагов от файла до готового перевода — с контролем качества на каждом этапе</p>
      </div>
      <div class="steps-row steps-row--6">
        <div class="step-item"><div class="step-num">01</div><h3>Отправьте файл</h3><p>Загрузите документ через сайт, мессенджер, email или принесите в офис</p></div>
        <div class="step-item"><div class="step-num">02</div><h3>Анализ и расчёт</h3><p>Оцениваем объём, тематику и сложность — сообщаем стоимость и срок за несколько минут</p></div>
        <div class="step-item"><div class="step-num">03</div><h3>Подбор специалиста</h3><p>Выбираем переводчика с профильным образованием именно в вашей области</p></div>
        <div class="step-item"><div class="step-num">04</div><h3>Перевод</h3><p>Специалист работает с соблюдением отраслевой терминологии и стиля оригинала</p></div>
        <div class="step-item"><div class="step-num">05</div><h3>Корректура</h3><p>Независимый редактор проверяет точность, стиль и соответствие заданию</p></div>
        <div class="step-item"><div class="step-num">06</div><h3>Готовый перевод</h3><p>Отправляем файл или вы забираете из офиса — точно в оговорённый срок</p></div>
      </div>
    </div>
  </section>

  <!-- ════════════════ PRICING ════════════════ -->
  <section class="sec sec--alt sec-pricing" id="pricing">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Выберите формат перевода</h2>
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

  <!-- ════════════════ STATS ════════════════ -->
  <section class="sec sec-stats">
    <div class="container">
      <div class="stats-row">
        <div class="stat-item"><span class="stat-num">25<span class="stat-suffix">+</span></span><span class="stat-label">лет на рынке</span></div>
        <div class="stat-item"><span class="stat-num">2 400<span class="stat-suffix">+</span></span><span class="stat-label">выполненных заказов</span></div>
        <div class="stat-item"><span class="stat-num">60<span class="stat-suffix">+</span></span><span class="stat-label">языков перевода</span></div>
        <div class="stat-item"><span class="stat-num">4.98<span class="stat-suffix">★</span></span><span class="stat-label">средний рейтинг</span></div>
      </div>
    </div>
  </section>

  <!-- ════════════════ TEAM ════════════════ -->
  <section class="sec sec-team" id="about">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Наши сотрудники</h2>
        <p class="sec-sub">Профессионалы с многолетним опытом — каждый специализируется в своей области</p>
      </div>
      <div class="team-grid">
        <div class="team-card">
          <div class="team-photo-wrap"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/olga.jpg" alt="Капина Ольга Борисовна"></div>
          <div class="team-name">Капина Ольга Борисовна</div>
          <div class="team-role">Директор</div>
        </div>
        <div class="team-card"><div class="team-photo-wrap" style="background:linear-gradient(135deg,#3b5bdb,#7048e8)"><span class="team-initials">М</span></div><div class="team-name">Грищенко Максим</div><div class="team-role">Менеджер</div></div>
        <div class="team-card"><div class="team-photo-wrap" style="background:linear-gradient(135deg,#0ea5e9,#6366f1)"><span class="team-initials">Е</span></div><div class="team-name">Елена Скибенко</div><div class="team-role">Менеджер</div></div>
        <div class="team-card"><div class="team-photo-wrap" style="background:linear-gradient(135deg,#8b5cf6,#ec4899)"><span class="team-initials">Е</span></div><div class="team-name">Грищенко Елизавета</div><div class="team-role">Менеджер</div></div>
        <div class="team-card"><div class="team-photo-wrap" style="background:linear-gradient(135deg,#059669,#0ea5e9)"><span class="team-initials">Н</span></div><div class="team-name">Сырбу Наталья</div><div class="team-role">Переводчик</div></div>
        <div class="team-card"><div class="team-photo-wrap" style="background:linear-gradient(135deg,#f59e0b,#ef4444)"><span class="team-initials">П</span></div><div class="team-name">Сушко Пётр Петрович</div><div class="team-role">Менеджер</div></div>
        <div class="team-card"><div class="team-photo-wrap" style="background:linear-gradient(135deg,#6366f1,#14b8a6)"><span class="team-initials">Ш</span></div><div class="team-name">Акопян Шаке</div><div class="team-role">Менеджер</div></div>
      </div>
    </div>
  </section>

  <!-- ════════════════ B2B WORKFLOW ════════════════ -->
  <section class="sec sec--alt sec-b2b-flow" id="b2b">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Работаем с юридическими лицами по договору</h2>
        <p class="sec-sub">Полный пакет закрывающих документов — без дополнительных запросов в бухгалтерию</p>
      </div>
      <div class="b2b-flow-row">
        <div class="b2b-step"><div class="b2b-num">1</div><div class="b2b-label">Договор</div><div class="b2b-desc">Подписываем типовой договор на оказание переводческих услуг или работаем по вашей форме</div></div>
        <div class="b2b-arrow">→</div>
        <div class="b2b-step"><div class="b2b-num">2</div><div class="b2b-label">Счёт</div><div class="b2b-desc">Выставляем счёт с реквизитами ИП после согласования объёма и сроков</div></div>
        <div class="b2b-arrow">→</div>
        <div class="b2b-step"><div class="b2b-num">3</div><div class="b2b-label">Перевод</div><div class="b2b-desc">Выполняем работу в согласованные сроки, при необходимости подписываем NDA</div></div>
        <div class="b2b-arrow">→</div>
        <div class="b2b-step"><div class="b2b-num">4</div><div class="b2b-label">Акт + документы</div><div class="b2b-desc">Закрывающие документы: акт выполненных работ, УПД или счёт-фактура</div></div>
      </div>
      <div class="b2b-extras">
        <div class="b2b-extra-item"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg><span>NDA по вашей форме или нашей стандартной</span></div>
        <div class="b2b-extra-item"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg><span>Работаем онлайн — документы и переводы по e-mail</span></div>
        <div class="b2b-extra-item"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><span>Ответ менеджера в течение 30 минут в рабочее время</span></div>
        <div class="b2b-extra-item"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><span>ИП Климанова Ю.А. · ИНН 233406925261 · ОГРНИП 312236329700014</span></div>
      </div>
    </div>
  </section>

  <!-- ════════════════ REVIEWS ════════════════ -->
  <section class="sec sec-reviews">
    <div class="container">
      <div class="reviews-head">
        <div><h2 class="sec-title">Что говорят клиенты</h2><p class="sec-sub" style="margin-top:6px">Более 500 отзывов на ведущих платформах</p></div>
        <div class="review-rating-block">
          <div class="review-rating-score">4.98</div>
          <div class="review-stars-row">
            <?php for ($i = 0; $i < 5; $i++): ?><svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><?php endfor; ?>
          </div>
          <div class="review-rating-label">средний рейтинг</div>
        </div>
        <div class="review-platform-links">
          <a href="https://yandex.ru/maps/org/remarka/51867347382/reviews/" class="review-platform-link" target="_blank" rel="noopener">Яндекс</a>
          <a href="https://maps.app.goo.gl/d8BKJYw81PqBHXvz7" class="review-platform-link" target="_blank" rel="noopener">Google</a>
        </div>
      </div>
      <div class="reviews-grid">
        <?php
        $reviews = [
            ['name'=>'Елена Соколова',  'src'=>'Яндекс · февраль 2026',   'text'=>'Работаем с бюро второй год: переводим договоры с европейскими поставщиками. Юридическая терминология точная, глоссарий сохраняется от заказа к заказу. Документооборот удобный — договор, счёт, акт без лишних вопросов в бухгалтерию.',     'role'=>'Менеджер по закупкам, ООО «ТехноСнаб»'],
            ['name'=>'Дмитрий Попов',   'src'=>'Google · январь 2026',    'text'=>'Переводили техническую документацию на немецкий для международного тендера — 120 страниц за 4 дня. Терминология соответствует отраслевым стандартам, замечаний от зарубежного партнёра не было. Сроки соблюдены точно.',            'role'=>'Руководитель проекта, АО «Инжиниринг-Групп»'],
            ['name'=>'Ирина Матвеева',  'src'=>'Яндекс · ноябрь 2025',   'text'=>'Регулярно заказываем юридические переводы контрактов на английский и французский. Сроки всегда соблюдаются, менеджер на связи. Работаем по договору — закрывающие документы приходят вовремя, бухгалтерия довольна.',               'role'=>'Офис-менеджер, ЗАО «ЭкспортЛайн»'],
            ['name'=>'Михаил Захаров',  'src'=>'Google · сентябрь 2025',  'text'=>'Заказывали перевод медицинской документации для регистрации препарата в Росздравнадзоре. Переводчики знают фармацевтическую специфику, регуляторная терминология без ошибок. Первый раз без замечаний — редкость.',             'role'=>'Директор по регуляторным вопросам, ООО «ФармаМед»'],
            ['name'=>'Наталья Ефимова', 'src'=>'Яндекс · август 2025',   'text'=>'Перевели пакет ВЭД-документов: инвойсы, спецификации, сертификаты происхождения с китайского. Сроки сжатые — справились. Ценю то, что сразу понимают задачу и не задают лишних вопросов.',                                  'role'=>'Специалист ВЭД, ООО «ИмпортТорг»'],
            ['name'=>'Алексей Крылов',  'src'=>'Google · апрель 2025',   'text'=>'Переводим корпоративные IT-документы: технические задания, SLA, политики безопасности. Команда понимает IT-специфику, предлагает правки по смыслу, не только по форме. Сотрудничаем несколько лет стабильно.',                    'role'=>'CTO, ООО «ДиджиталСолюшнс»'],
        ];
        foreach ($reviews as $r):
            $initial = mb_substr($r['name'], 0, 1, 'UTF-8');
        ?>
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar"><?php echo esc_html($initial); ?></div>
            <div class="review-meta"><div class="review-name"><?php echo esc_html($r['name']); ?></div><div class="review-src"><?php echo esc_html($r['src']); ?></div></div>
          </div>
          <div class="review-stars"><?php for ($i = 0; $i < 5; $i++): ?><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><?php endfor; ?></div>
          <p class="review-text"><?php echo esc_html($r['text']); ?></p>
          <div class="review-role"><?php echo esc_html($r['role']); ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ════════════════ BLOG ════════════════ -->
  <section class="sec sec--alt sec-blog">
    <div class="container">
      <div class="sec-head">
        <div><h2 class="sec-title">Полезное о переводах</h2><p class="sec-sub" style="margin-top:6px">Советы, разборы, экспертные статьи</p></div>
        <a href="<?php echo esc_url(get_permalink(get_page_by_path('blog'))); ?>" class="sec-link">Все статьи →</a>
      </div>
      <div class="blog-grid">
        <?php
        $blog_q = remarka_latest_posts();
        if ($blog_q->have_posts()):
            $thumb_classes = ['blog-thumb--1', 'blog-thumb--2', 'blog-thumb--3'];
            $i = 0;
            while ($blog_q->have_posts()):
                $blog_q->the_post();
                $cats = get_the_category();
                $cat  = $cats ? esc_html($cats[0]->name) : 'Блог';
        ?>
        <article class="blog-card">
          <div class="blog-thumb <?php echo $thumb_classes[$i % 3]; ?>">
            <?php if (has_post_thumbnail()): ?>
              <?php the_post_thumbnail('blog-thumb'); ?>
            <?php endif; ?>
            <span class="blog-cat"><?php echo $cat; ?></span>
          </div>
          <div class="blog-body">
            <h3 class="blog-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <span class="blog-date"><?php echo get_the_date('j F Y'); ?></span>
            <a href="<?php the_permalink(); ?>" class="blog-link">Читать статью →</a>
          </div>
        </article>
        <?php $i++; endwhile; wp_reset_postdata();
        else: ?>
        <article class="blog-card"><div class="blog-thumb blog-thumb--1"><span class="blog-cat">Советы</span></div><div class="blog-body"><h3 class="blog-title">Постредактирование перевода ИИ: когда это оправданный выбор</h3><span class="blog-date">15 апреля 2026</span><a href="/blog/" class="blog-link">Читать статью →</a></div></article>
        <article class="blog-card"><div class="blog-thumb blog-thumb--2"><span class="blog-cat">Гид</span></div><div class="blog-body"><h3 class="blog-title">Как правильно подготовить документы к переводу: 6 простых правил</h3><span class="blog-date">2 марта 2026</span><a href="/blog/" class="blog-link">Читать статью →</a></div></article>
        <article class="blog-card"><div class="blog-thumb blog-thumb--3"><span class="blog-cat">Экспертиза</span></div><div class="blog-body"><h3 class="blog-title">Технический перевод: почему переводчик должен знать отрасль</h3><span class="blog-date">18 февраля 2026</span><a href="/blog/" class="blog-link">Читать статью →</a></div></article>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <?php get_template_part('template-parts/section-calc'); ?>

  <!-- ════════════════ CTA ════════════════ -->
  <section class="sec sec-cta">
    <div class="container">
      <h2 class="cta-title">Нужен перевод в Москве?</h2>
      <p class="cta-sub">Загрузите файл — и через несколько минут получите точный расчёт стоимости и сроков</p>
      <div class="cta-btns">
        <a href="#calc-section" class="btn btn-primary">Загрузить файл</a>
        <a href="https://wa.me/79859704413" class="btn btn-outline" target="_blank" rel="noopener">Написать менеджеру</a>
      </div>
    </div>
  </section>

<?php get_footer(); ?>
