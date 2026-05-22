<?php
/**
 * Template Name: Языки перевода
 */
get_header();

get_template_part('template-parts/hero-chat-window', null, [
    'greeting_1' => 'Здравствуйте! 👋 Переводим на 60+ языков мира.',
    'greeting_2' => 'Напишите нужную языковую пару — найдём профильного переводчика.',
    'greeting_3' => 'Специалисты с отраслевым образованием для технических, юридических и медицинских текстов.',
    'breadcrumb' => 'Языки перевода',
    'home_dots'  => false,
]);
?>
  </div><!-- /hero-bg-block -->

  <!-- Введение -->
  <section class="sec sec-intro">
    <div class="container">
      <div class="intro-grid">
        <div class="intro-text">
          <p class="intro-label">Бюро переводов «Ремарка», Москва</p>
          <h1 class="intro-title">Языки перевода</h1>
          <p class="intro-tagline">Более 60 языков — с профильными специалистами</p>
          <p class="intro-body">Бюро переводов «Ремарка» предлагает профессиональный перевод на более чем 60 языков. Наши переводчики сочетают лингвистическую подготовку с отраслевой экспертизой: право, медицина, IT, инженерия, финансы.</p>
          <p class="intro-body">Для каждой языковой пары мы подбираем специалиста с профильными знаниями правовой системы и терминологической базы страны. Это гарантирует точность перевода как на уровне текста, так и на уровне смысла.</p>
        </div>
        <div class="intro-visual">
          <div class="lang-pills-wrap">
            <?php
            $langs = [
              ['🇬🇧', 'Английский'], ['🇩🇪', 'Немецкий'], ['🇫🇷', 'Французский'],
              ['🇮🇹', 'Итальянский'], ['🇪🇸', 'Испанский'], ['🇵🇹', 'Португальский'],
              ['🇨🇳', 'Китайский'], ['🇯🇵', 'Японский'], ['🇸🇦', 'Арабский'],
              ['🇰🇷', 'Корейский'], ['🇳🇱', 'Нидерландский'], ['🇵🇱', 'Польский'],
              ['🇨🇿', 'Чешский'], ['🇸🇰', 'Словацкий'], ['🇭🇺', 'Венгерский'],
              ['🇷🇴', 'Румынский'], ['🇧🇬', 'Болгарский'], ['🇷🇸', 'Сербский'],
              ['🇭🇷', 'Хорватский'], ['🇸🇮', 'Словенский'], ['🇬🇷', 'Греческий'],
              ['🇫🇮', 'Финский'], ['🇸🇪', 'Шведский'], ['🇳🇴', 'Норвежский'],
              ['🇩🇰', 'Датский'], ['🇪🇪', 'Эстонский'], ['🇱🇻', 'Латышский'],
              ['🇱🇹', 'Литовский'], ['🇺🇦', 'Украинский'], ['🇧🇾', 'Белорусский'],
              ['🇰🇿', 'Казахский'], ['🇦🇿', 'Азербайджанский'], ['🇦🇲', 'Армянский'],
              ['🇬🇪', 'Грузинский'],
            ];
            foreach ($langs as $l) {
              echo '<span class="lang-pill">' . $l[0] . ' ' . esc_html($l[1]) . '</span>';
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Популярные языковые пары -->
  <section class="sec sec--alt sec-lang-featured">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Популярные языковые пары</h2>
        <p class="sec-sub">Переводчики знают правовую систему страны оригинала — не только язык</p>
      </div>
      <div class="fl-grid">

        <div class="doc-ref-card">
          <span class="lang-flag-big">🇬🇧</span>
          <strong>Английский</strong>
          <ul class="lang-specs">
            <li>Юридический (Common Law, LCIA, ICC)</li>
            <li>Технический — любые отрасли</li>
            <li>IT-документация и локализация</li>
            <li>Финансовая отчётность МСФО</li>
          </ul>
          <span class="lang-price">от 400 ₽/стр.</span>
        </div>

        <div class="doc-ref-card">
          <span class="lang-flag-big">🇩🇪</span>
          <strong>Немецкий</strong>
          <ul class="lang-specs">
            <li>Технический (машиностроение, авто)</li>
            <li>Юридический (GmbH, AG, DIS)</li>
            <li>Медицинская документация</li>
            <li>Стандарты DIN/VDE</li>
          </ul>
          <span class="lang-price">от 600 ₽/стр.</span>
        </div>

        <div class="doc-ref-card">
          <span class="lang-flag-big">🇫🇷</span>
          <strong>Французский</strong>
          <ul class="lang-specs">
            <li>Юридический (международное право)</li>
            <li>Технический перевод</li>
            <li>Медицинская документация</li>
            <li>Корпоративная переписка</li>
          </ul>
          <span class="lang-price">от 600 ₽/стр.</span>
        </div>

        <div class="doc-ref-card">
          <span class="lang-flag-big">🇮🇹</span>
          <strong>Итальянский</strong>
          <ul class="lang-specs">
            <li>Технический перевод</li>
            <li>Юридические контракты</li>
            <li>Мода и дизайн</li>
            <li>Пищевая промышленность</li>
          </ul>
          <span class="lang-price">от 600 ₽/стр.</span>
        </div>

        <div class="doc-ref-card">
          <span class="lang-flag-big">🇨🇳</span>
          <strong>Китайский</strong>
          <ul class="lang-specs">
            <li>Технический (электроника, машиностроение)</li>
            <li>Таможенная документация</li>
            <li>Коммерческие контракты</li>
            <li>Сертификаты соответствия</li>
          </ul>
          <span class="lang-price">от 1 000 ₽/стр.</span>
        </div>

        <div class="doc-ref-card">
          <span class="lang-flag-big">🇸🇦</span>
          <strong>Арабский</strong>
          <ul class="lang-specs">
            <li>Юридические контракты</li>
            <li>Нефтегазовая документация</li>
            <li>Технический перевод</li>
            <li>Корпоративная отчётность</li>
          </ul>
          <span class="lang-price">от 800 ₽/стр.</span>
        </div>

      </div>
    </div>
  </section>

  <!-- Полный перечень языков -->
  <section class="sec sec-full-list">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Полный перечень языков</h2>
        <p class="sec-sub">Раскройте нужную группу — посмотрите цену за страницу профессионального перевода</p>
      </div>
      <div class="lang-accordion">

        <details class="lang-item" open>
          <summary class="lang-summary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            <span>Европейские языки</span>
            <span class="ls-count">24 языка</span>
            <svg class="ls-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
          </summary>
          <div class="lang-body">
            <table class="lang-table">
              <tbody>
                <tr><td>Английский</td><td>от 400 ₽/стр.</td></tr>
                <tr><td>Болгарский</td><td>от 800 ₽/стр.</td></tr>
                <tr><td>Венгерский</td><td>от 900 ₽/стр.</td></tr>
                <tr><td>Греческий</td><td>от 800 ₽/стр.</td></tr>
                <tr><td>Датский</td><td>от 1 000 ₽/стр.</td></tr>
                <tr><td>Испанский</td><td>от 600 ₽/стр.</td></tr>
                <tr><td>Итальянский</td><td>от 600 ₽/стр.</td></tr>
                <tr><td>Латышский</td><td>от 800 ₽/стр.</td></tr>
                <tr><td>Литовский</td><td>от 800 ₽/стр.</td></tr>
                <tr><td>Немецкий</td><td>от 600 ₽/стр.</td></tr>
                <tr><td>Нидерландский</td><td>от 800 ₽/стр.</td></tr>
                <tr><td>Норвежский</td><td>от 1 000 ₽/стр.</td></tr>
                <tr><td>Польский</td><td>от 600 ₽/стр.</td></tr>
                <tr><td>Португальский</td><td>от 600 ₽/стр.</td></tr>
                <tr><td>Румынский</td><td>от 600 ₽/стр.</td></tr>
                <tr><td>Сербский</td><td>от 600 ₽/стр.</td></tr>
                <tr><td>Словацкий</td><td>от 600 ₽/стр.</td></tr>
                <tr><td>Словенский</td><td>от 800 ₽/стр.</td></tr>
                <tr><td>Финский</td><td>от 1 000 ₽/стр.</td></tr>
                <tr><td>Французский</td><td>от 600 ₽/стр.</td></tr>
                <tr><td>Хорватский</td><td>от 600 ₽/стр.</td></tr>
                <tr><td>Чешский</td><td>от 600 ₽/стр.</td></tr>
                <tr><td>Шведский</td><td>от 1 000 ₽/стр.</td></tr>
                <tr><td>Эстонский</td><td>от 800 ₽/стр.</td></tr>
              </tbody>
            </table>
          </div>
        </details>

        <details class="lang-item">
          <summary class="lang-summary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <span>Языки СНГ и постсоветские</span>
            <span class="ls-count">12 языков</span>
            <svg class="ls-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
          </summary>
          <div class="lang-body">
            <table class="lang-table">
              <tbody>
                <tr><td>Абхазский</td><td>от 600 ₽/стр.</td></tr>
                <tr><td>Азербайджанский</td><td>от 500 ₽/стр.</td></tr>
                <tr><td>Армянский</td><td>от 600 ₽/стр.</td></tr>
                <tr><td>Белорусский</td><td>от 600 ₽/стр.</td></tr>
                <tr><td>Грузинский</td><td>от 600 ₽/стр.</td></tr>
                <tr><td>Казахский</td><td>от 600 ₽/стр.</td></tr>
                <tr><td>Киргизский</td><td>от 600 ₽/стр.</td></tr>
                <tr><td>Молдавский</td><td>от 600 ₽/стр.</td></tr>
                <tr><td>Таджикский</td><td>от 600 ₽/стр.</td></tr>
                <tr><td>Туркменский</td><td>от 600 ₽/стр.</td></tr>
                <tr><td>Узбекский</td><td>от 600 ₽/стр.</td></tr>
                <tr><td>Украинский</td><td>от 400 ₽/стр.</td></tr>
              </tbody>
            </table>
          </div>
        </details>

        <details class="lang-item">
          <summary class="lang-summary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 22 8.5 22 15.5 12 22 2 15.5 2 8.5 12 2"/></svg>
            <span>Ближний Восток и Африка</span>
            <span class="ls-count">4 языка</span>
            <svg class="ls-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
          </summary>
          <div class="lang-body">
            <table class="lang-table">
              <tbody>
                <tr><td>Арабский</td><td>от 800 ₽/стр.</td></tr>
                <tr><td>Иврит</td><td>от 1 000 ₽/стр.</td></tr>
                <tr><td>Персидский / Фарси</td><td>от 800 ₽/стр.</td></tr>
                <tr><td>Турецкий</td><td>от 600 ₽/стр.</td></tr>
              </tbody>
            </table>
          </div>
        </details>

        <details class="lang-item">
          <summary class="lang-summary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10"/><path d="M12 2a15.3 15.3 0 0 1 4 10"/><path d="M22 2l-10 10"/></svg>
            <span>Азия</span>
            <span class="ls-count">7 языков</span>
            <svg class="ls-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
          </summary>
          <div class="lang-body">
            <table class="lang-table">
              <tbody>
                <tr><td>Вьетнамский</td><td>от 1 200 ₽/стр.</td></tr>
                <tr><td>Индонезийский</td><td>от 1 000 ₽/стр.</td></tr>
                <tr><td>Китайский</td><td>от 1 000 ₽/стр.</td></tr>
                <tr><td>Корейский</td><td>от 1 000 ₽/стр.</td></tr>
                <tr><td>Тайский</td><td>от 1 200 ₽/стр.</td></tr>
                <tr><td>Хинди</td><td>от 1 000 ₽/стр.</td></tr>
                <tr><td>Японский</td><td>от 1 200 ₽/стр.</td></tr>
              </tbody>
            </table>
          </div>
        </details>

      </div>
    </div>
  </section>

<?php
get_template_part('template-parts/section-calc', null, [
    'heading' => 'Уточните стоимость для вашей языковой пары',
    'sub'     => 'Загрузите документ и укажите языковую пару — рассчитаем стоимость бесплатно.',
]);

get_footer();
