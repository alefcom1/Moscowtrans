<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

  <!-- ════════════════ HEADER ════════════════ -->
  <header class="site-header">
    <div class="container header-inner">

      <a href="<?php echo esc_url(home_url('/')); ?>" class="logo" aria-label="Ремарка">
        <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/logo-dark.png" alt="Remarka" id="logo-img">
      </a>

      <nav class="main-nav" aria-label="Основное меню">

        <!-- ── Услуги ── -->
        <div class="nav-item" data-dropdown="services">
          <button class="nav-trigger" aria-expanded="false" aria-haspopup="true">Услуги
            <svg class="dd-chevron" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
          <div class="mega-menu mega-menu--services" role="region">
            <div class="dd-grid-2">
              <div>
                <div class="dd-section-label">Основные виды</div>
                <a href="/pismennyy-perevod/" class="dd-item"><span class="dd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></span><span class="dd-item-text"><span class="dd-title">Письменный перевод</span><span class="dd-sub">Все типы документов</span></span></a>
                <a href="/yuridicheskiy-perevod/" class="dd-item"><span class="dd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></span><span class="dd-item-text"><span class="dd-title">Юридический перевод</span><span class="dd-sub">Договоры, уставы, суд</span></span></a>
                <a href="/tekhnicheskiy-perevod/" class="dd-item"><span class="dd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.41 1.41M6.34 17.66l-1.41 1.41M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2m16 0h2M12 2v2m0 16v2"/></svg></span><span class="dd-item-text"><span class="dd-title">Технический перевод</span><span class="dd-sub">Инструкции, чертежи, ТЗ</span></span></a>
                <a href="/meditsinskiy-perevod/" class="dd-item"><span class="dd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg></span><span class="dd-item-text"><span class="dd-title">Медицинский перевод</span><span class="dd-sub">Справки, выписки, исследования</span></span></a>
                <a href="/it-perevod/" class="dd-item"><span class="dd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg></span><span class="dd-item-text"><span class="dd-title">IT и локализация ПО</span><span class="dd-sub">Интерфейсы, API-документация</span></span></a>
                <a href="/perevod-saytov/" class="dd-item"><span class="dd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></span><span class="dd-item-text"><span class="dd-title">Перевод сайтов</span><span class="dd-sub">CMS, лендинги, интернет-магазины</span></span></a>
              </div>
              <div>
                <div class="dd-section-label">Специализации</div>
                <a href="/finansovyy-perevod/" class="dd-item"><span class="dd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></span><span class="dd-item-text"><span class="dd-title">Финансовый перевод</span><span class="dd-sub">Отчёты, аудит, банки</span></span></a>
                <a href="/marketingovyy-perevod/" class="dd-item"><span class="dd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/></svg></span><span class="dd-item-text"><span class="dd-title">Маркетинговый перевод</span><span class="dd-sub">Реклама, контент, бренды</span></span></a>
                <a href="/ved-perevod/" class="dd-item"><span class="dd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></span><span class="dd-item-text"><span class="dd-title">ВЭД и таможня</span><span class="dd-sub">Декларации, сертификаты</span></span></a>
                <a href="/patentnye-perevody/" class="dd-item"><span class="dd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg></span><span class="dd-item-text"><span class="dd-title">Патентные переводы</span><span class="dd-sub">Изобретения, ИС</span></span></a>
                <a href="/nauchnyy-perevod/" class="dd-item"><span class="dd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3h6"/><path d="M6 20h12"/><path d="M9 3v8l-3 9"/><path d="M15 3v8l3 9"/></svg></span><span class="dd-item-text"><span class="dd-title">Научный перевод</span><span class="dd-sub">Статьи, диссертации</span></span></a>
                <a href="/delovaya-perepiska/" class="dd-item"><span class="dd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span><span class="dd-item-text"><span class="dd-title">Деловая переписка</span><span class="dd-sub">Письма, email, протоколы</span></span></a>
              </div>
            </div>
            <div class="dd-featured">
              <a href="/srochnyy-perevod/" class="dd-feat-item"><span class="dd-feat-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></span>Срочный перевод</a>
              <a href="/khudozhestvennyy-perevod/" class="dd-feat-item"><span class="dd-feat-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19l7-7 3 3-7 7-3-3z"/><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/></svg></span>Художественный перевод</a>
            </div>
          </div>
        </div>

        <!-- ── Отрасли ── -->
        <div class="nav-item" data-dropdown="industries">
          <button class="nav-trigger" aria-expanded="false" aria-haspopup="true">Отрасли
            <svg class="dd-chevron" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
          <div class="mega-menu mega-menu--industries" role="region">
            <div class="dd-section-label">Отраслевая специализация</div>
            <div class="dd-grid-2">
              <a href="/yuridicheskiy-perevod/" class="dd-item"><span class="dd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg></span><span class="dd-item-text"><span class="dd-title">Юриспруденция</span><span class="dd-sub">Суды, контракты, нотариат</span></span></a>
              <a href="/meditsinskiy-perevod/" class="dd-item"><span class="dd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg></span><span class="dd-item-text"><span class="dd-title">Медицина и фармация</span><span class="dd-sub">Клиники, препараты, КИ</span></span></a>
              <a href="/it-perevod/" class="dd-item"><span class="dd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg></span><span class="dd-item-text"><span class="dd-title">IT и технологии</span><span class="dd-sub">ПО, SaaS, кибербезопасность</span></span></a>
              <a href="/finansovyy-perevod/" class="dd-item"><span class="dd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span><span class="dd-item-text"><span class="dd-title">Финансы и банки</span><span class="dd-sub">Отчётность, аудит, биржа</span></span></a>
              <a href="/tekhnicheskiy-perevod/" class="dd-item"><span class="dd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg></span><span class="dd-item-text"><span class="dd-title">Промышленность</span><span class="dd-sub">Машиностроение, нефтегаз</span></span></a>
              <a href="/marketingovyy-perevod/" class="dd-item"><span class="dd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/></svg></span><span class="dd-item-text"><span class="dd-title">Маркетинг и реклама</span><span class="dd-sub">Бренды, контент, PR</span></span></a>
              <a href="/nauchnyy-perevod/" class="dd-item"><span class="dd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3h6"/><path d="M6 20h12"/><path d="M9 3v8l-3 9"/><path d="M15 3v8l3 9"/></svg></span><span class="dd-item-text"><span class="dd-title">Наука и образование</span><span class="dd-sub">Статьи, патенты, диссертации</span></span></a>
              <a href="/ved-perevod/" class="dd-item"><span class="dd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg></span><span class="dd-item-text"><span class="dd-title">ВЭД и таможня</span><span class="dd-sub">Экспорт, импорт, декларации</span></span></a>
            </div>
            <div class="dd-see-all-row"><a href="/#industries" class="dd-see-all">Все отрасли <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg></a></div>
          </div>
        </div>

        <!-- ── Языки ── -->
        <div class="nav-item" data-dropdown="languages">
          <button class="nav-trigger" aria-expanded="false" aria-haspopup="true">Языки
            <svg class="dd-chevron" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
          <div class="mega-menu mega-menu--languages" role="region">
            <div class="dd-lang-box">
              <div class="dd-lang-box-label">Топ языков</div>
              <div class="dd-lang-grid">
                <a href="/yazyki-perevoda/" class="dd-lang-item">🇬🇧 Английский</a>
                <a href="/yazyki-perevoda/" class="dd-lang-item">🇩🇪 Немецкий</a>
                <a href="/yazyki-perevoda/" class="dd-lang-item">🇫🇷 Французский</a>
                <a href="/yazyki-perevoda/" class="dd-lang-item">🇨🇳 Китайский</a>
                <a href="/yazyki-perevoda/" class="dd-lang-item">🇯🇵 Японский</a>
                <a href="/yazyki-perevoda/" class="dd-lang-item">🇰🇷 Корейский</a>
                <a href="/yazyki-perevoda/" class="dd-lang-item">🇸🇦 Арабский</a>
                <a href="/yazyki-perevoda/" class="dd-lang-item">🇪🇸 Испанский</a>
                <a href="/yazyki-perevoda/" class="dd-lang-item">🇮🇹 Итальянский</a>
              </div>
            </div>
            <div class="dd-lang-more">
              <a href="/yazyki-perevoda/" class="dd-lang-chip">🇦🇲 Армянский</a>
              <a href="/yazyki-perevoda/" class="dd-lang-chip">🇦🇿 Азербайджанский</a>
              <a href="/yazyki-perevoda/" class="dd-lang-chip">🇬🇪 Грузинский</a>
              <a href="/yazyki-perevoda/" class="dd-lang-chip">🇺🇦 Украинский</a>
              <a href="/yazyki-perevoda/" class="dd-lang-chip">🇧🇾 Белорусский</a>
              <a href="/yazyki-perevoda/" class="dd-lang-chip">🇲🇩 Молдавский</a>
              <a href="/yazyki-perevoda/" class="dd-lang-chip">🇺🇿 Узбекский</a>
              <a href="/yazyki-perevoda/" class="dd-lang-chip">🇰🇿 Казахский</a>
              <a href="/yazyki-perevoda/" class="dd-lang-chip">🇹🇯 Таджикский</a>
              <a href="/yazyki-perevoda/" class="dd-lang-chip">🇰🇬 Киргизский</a>
            </div>
            <div class="dd-see-all-row"><a href="/yazyki-perevoda/" class="dd-see-all">Все языки (60+) <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg></a></div>
          </div>
        </div>

        <!-- ── Цены ── -->
        <div class="nav-item" data-dropdown="pricing">
          <button class="nav-trigger" aria-expanded="false" aria-haspopup="true">Цены
            <svg class="dd-chevron" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
          <div class="mega-menu mega-menu--pricing" role="region">
            <div class="dd-pricing-layout">
              <div>
                <a href="/stoimost-perevoda/" class="dd-item"><span class="dd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg></span><span class="dd-item-text"><span class="dd-title">Тарифы и цены</span><span class="dd-sub">Прайс по всем услугам</span></span></a>
                <a href="#calc-section" class="dd-item"><span class="dd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="10" x2="16" y2="10"/><line x1="8" y1="14" x2="12" y2="14"/></svg></span><span class="dd-item-text"><span class="dd-title">Онлайн-калькулятор</span><span class="dd-sub">Расчёт за 1 минуту</span></span></a>
                <a href="/srochnyy-perevod/" class="dd-item"><span class="dd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></span><span class="dd-item-text"><span class="dd-title">Срочный перевод</span><span class="dd-sub">Доплата 50%, от 3 часов</span></span></a>
              </div>
              <div class="dd-pricing-desc">
                <h4>Прозрачное ценообразование</h4>
                <p>Мы предлагаем честные цены без скрытых комиссий. Стоимость зависит от объёма, языковой пары и сложности текста. Загрузите файл — получите точный расчёт за минуту.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- ── О нас ── -->
        <div class="nav-item" data-dropdown="about">
          <button class="nav-trigger" aria-expanded="false" aria-haspopup="true">О нас
            <svg class="dd-chevron" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
          <div class="mega-menu mega-menu--about" role="region">
            <div class="dd-section-label">Компания</div>
            <div class="dd-grid-2">
              <a href="/#about" class="dd-item"><span class="dd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span><span class="dd-item-text"><span class="dd-title">О компании</span><span class="dd-sub">С 2001 года, 5000+ заказов</span></span></a>
              <a href="/blog/" class="dd-item"><span class="dd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></span><span class="dd-item-text"><span class="dd-title">Блог</span><span class="dd-sub">Статьи о переводе</span></span></a>
              <a href="/kontakty/" class="dd-item"><span class="dd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.96a16 16 0 0 0 6.13 6.13l.96-.96a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg></span><span class="dd-item-text"><span class="dd-title">Контакты</span><span class="dd-sub">Москва, Глинищевский пер., 6</span></span></a>
              <a href="/keisy/" class="dd-item"><span class="dd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></span><span class="dd-item-text"><span class="dd-title">Кейсы</span><span class="dd-sub">Примеры выполненных работ</span></span></a>
            </div>
            <div class="dd-divider"></div>
            <a href="/politika-konfidenczialnosti/" class="dd-item" style="margin-top:0"><span class="dd-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span><span class="dd-item-text"><span class="dd-title">Конфиденциальность</span><span class="dd-sub">Политика обработки данных</span></span></a>
          </div>
        </div>

      </nav>

      <div class="header-actions">
        <a href="tel:+74959704413" class="header-phone" aria-label="Позвонить" title="+7 (495) 970-44-13">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.96a16 16 0 0 0 6.13 6.13l.96-.96a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        </a>
        <a href="https://wa.me/79859704413" class="header-wa" target="_blank" rel="noopener" aria-label="WhatsApp">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
        </a>
        <a href="https://tms.perevod4.ru/" target="_blank" rel="noopener" class="btn-link">Войти</a>
        <a href="#calc-section" class="btn-primary">
          Получить расчёт
          <svg class="arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
        </a>
        <div class="lang-switcher">
          <button type="button" class="lang-switcher-btn" aria-haspopup="true" aria-expanded="false">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            <span>RU</span>
            <svg class="chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
          <div class="lang-dropdown" role="menu">
            <a href="#" class="is-active" role="menuitem">🇷🇺 Русский</a>
            <a href="https://1russian.com" role="menuitem">🇬🇧 English</a>
            <a href="https://traduzione.tech" role="menuitem">🇮🇹 Italiano</a>
          </div>
        </div>
        <button class="theme-toggle" type="button" aria-label="Переключить тему">
          <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
          <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
        </button>
        <button class="mobile-toggle" type="button" aria-label="Открыть меню">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
      </div>

    </div>
  </header>

  <!-- Мобильное выдвижное меню -->
  <div class="mobile-drawer" aria-hidden="true">
    <nav aria-label="Мобильное меню">
      <a href="/#services">Услуги</a>
      <a href="/#industries">Отрасли</a>
      <a href="/yazyki-perevoda/">Языки</a>
      <a href="/stoimost-perevoda/">Цены</a>
      <a href="/#about">О нас</a>
    </nav>
    <div class="drawer-actions">
      <a href="tel:+74959704413" class="btn-link">+7 (495) 970-44-13</a>
      <a href="https://tms.perevod4.ru/" target="_blank" rel="noopener" class="btn-link">Войти</a>
      <a href="#calc-section" class="btn-primary">Получить расчёт <svg class="arrow" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg></a>
    </div>
  </div>
