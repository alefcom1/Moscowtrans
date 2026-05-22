<?php
/**
 * Template Name: Стоимость перевода
 */
get_header();

get_template_part('template-parts/hero-chat-window', null, [
    'greeting_1' => 'Здравствуйте! 👋 Хотите узнать стоимость перевода?',
    'greeting_2' => 'Вышлите файл — рассчитаю точную цену за 15 минут',
    'greeting_3' => 'Стоимость фиксируется до начала работы и не меняется 📋',
    'breadcrumb' => 'Стоимость перевода',
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
  <section class="sec sec-pricing-tiers" id="pricing">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Форматы перевода</h2>
        <p class="sec-sub">Три уровня качества для разных задач — выберите подходящий формат</p>
      </div>
      <div class="pricing-tiers">

        <div class="pricing-tier">
          <h3 class="tier-name">Постредактирование ИИ</h3>
          <div class="tier-price">от 250 ₽<span> / стр. (1800 зн.)</span></div>
          <p class="tier-desc">Машинный перевод с редактурой специалиста. Подходит для внутренней документации, черновиков и текстов с простой структурой.</p>
          <ul class="tier-features">
            <li>Машинный перевод (GPT / DeepL)</li>
            <li>Редактура переводчиком</li>
            <li>Исправление терминологии</li>
            <li>Быстрый срок сдачи</li>
          </ul>
          <a href="#calc-section" class="tier-cta tier-cta--outline">Заказать</a>
        </div>

        <div class="pricing-tier pricing-tier--featured">
          <div class="tier-badge">Популярный</div>
          <h3 class="tier-name">Профессиональный</h3>
          <div class="tier-price">от 500 ₽<span> / стр.</span></div>
          <p class="tier-desc">Перевод с нуля профильным специалистом. Оптимальный выбор для договоров, технической документации и деловой переписки.</p>
          <ul class="tier-features">
            <li>Перевод с нуля специалистом</li>
            <li>Профильное образование исполнителя</li>
            <li>Работа с терминологической базой</li>
            <li>Двухэтапная редакторская проверка</li>
            <li>Сохранение структуры документа</li>
          </ul>
          <a href="#calc-section" class="tier-cta tier-cta--primary">Заказать</a>
        </div>

        <div class="pricing-tier">
          <h3 class="tier-name">Премиум</h3>
          <div class="tier-price">от 800 ₽<span> / стр.</span></div>
          <p class="tier-desc">Перевод + редактура + вычитка носителем языка. Для публикаций, тендерной документации и материалов высшего уровня точности.</p>
          <ul class="tier-features">
            <li>Перевод профильным экспертом</li>
            <li>Редактура второго специалиста</li>
            <li>Вычитка носителем языка</li>
            <li>Сертификат качества по запросу</li>
            <li>Приоритетная поддержка</li>
          </ul>
          <a href="#calc-section" class="tier-cta tier-cta--outline">Заказать</a>
        </div>

      </div>
    </div>
  </section>

  <!-- Стоимость по типам документов -->
  <section class="sec sec--alt sec-doc-types">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Стоимость по типам документов</h2>
        <p class="sec-sub">Базовые тарифы для профессионального перевода с русского и на русский язык</p>
      </div>
      <div class="doc-type-table-wrap">
        <table class="doc-type-table">
          <thead>
            <tr><th>Тип документа</th><th>Стоимость от</th></tr>
          </thead>
          <tbody>
            <tr><td><strong>Технический перевод</strong> — инструкции, руководства по эксплуатации, спецификации, КД</td><td>400 ₽/стр.</td></tr>
            <tr><td><strong>Юридический перевод</strong> — договоры, контракты, уставы, корпоративные документы</td><td>500 ₽/стр.</td></tr>
            <tr><td><strong>Медицинский перевод</strong> — клинические протоколы, инструкции к препаратам, медицинская документация</td><td>500 ₽/стр.</td></tr>
            <tr><td><strong>IT-перевод</strong> — документация ПО, интерфейсы, локализация, технические спецификации</td><td>450 ₽/стр.</td></tr>
            <tr><td><strong>Финансовый перевод</strong> — отчётность МСФО/GAAP, аудиторские заключения, проспекты</td><td>550 ₽/стр.</td></tr>
            <tr><td><strong>Маркетинговый перевод</strong> — сайты, рекламные материалы, пресс-релизы</td><td>400 ₽/стр.</td></tr>
            <tr><td><strong>Патентный перевод</strong> — описания изобретений, формулы, патентная документация</td><td>600 ₽/стр.</td></tr>
            <tr><td><strong>Деловая переписка</strong> — письма, коммерческие предложения, запросы</td><td>350 ₽/стр.</td></tr>
          </tbody>
        </table>
      </div>
      <p style="font-size:13px;color:var(--text-muted);margin-top:16px;text-align:center">* Цены указаны для языковой пары русский ↔ английский при стандартных сроках. Стоимость для других языковых пар уточняйте у менеджера.</p>
    </div>
  </section>

  <!-- Примеры стоимости -->
  <section class="sec sec-price-examples">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Примеры стоимости</h2>
        <p class="sec-sub">Типичные заказы московских B2B-клиентов — ориентировочные цены при стандартных сроках</p>
      </div>
      <div class="price-examples-grid">
        <div class="price-example-card">
          <div class="pe-emoji">📄</div>
          <p class="pe-title">Договор поставки<br>5 стр. (рус. → англ.)</p>
          <div class="pe-price">от 2 500 ₽</div>
          <div class="pe-meta">Срок: 2–3 рабочих дня</div>
        </div>
        <div class="price-example-card">
          <div class="pe-emoji">📋</div>
          <p class="pe-title">Техническая инструкция<br>15 стр. (нем. → рус.)</p>
          <div class="pe-price">от 6 000 ₽</div>
          <div class="pe-meta">Срок: 3–5 рабочих дней</div>
        </div>
        <div class="price-example-card">
          <div class="pe-emoji">🌐</div>
          <p class="pe-title">Веб-сайт компании<br>25 стр. (рус. → англ.)</p>
          <div class="pe-price">от 10 000 ₽</div>
          <div class="pe-meta">Срок: 5–7 рабочих дней</div>
        </div>
        <div class="price-example-card">
          <div class="pe-emoji">💊</div>
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
