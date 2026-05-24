<?php
/**
 * One-time page creation script.
 *
 * Trigger (logged-in admin only):
 *   https://your-site.com/?remarka_setup_pages=1
 *
 * Safe to run multiple times — skips pages that already exist by slug.
 */

add_action('init', function () {
    if (empty($_GET['remarka_setup_pages']) || !current_user_can('manage_options')) {
        return;
    }

    $results = remarka_create_all_pages();

    echo '<pre style="margin:40px auto;max-width:900px;font-family:monospace;font-size:13px">';
    echo '<strong>Remarka — page setup complete</strong>' . "\n\n";
    foreach ($results as $r) {
        $icon = $r['action'] === 'created' ? '✅' : ($r['action'] === 'updated' ? '🔄' : ($r['action'] === 'skipped' ? '⏭' : '❌'));
        echo $icon . '  ' . str_pad($r['action'], 10) . ' /' . $r['slug'] . ' — ' . $r['title'] . "\n";
    }
    echo '</pre>';
    exit;
});

function remarka_create_all_pages() {
    $results = [];

    /* ── 1. Language pages ─────────────────────────────────────────── */
    $lang_pages = [
        ['perevod-na-angliyskiy',       'Перевод на английский язык',    'en'],
        ['perevod-na-nemetskiy',        'Перевод на немецкий язык',      'de'],
        ['perevod-na-frantsuzskiy',     'Перевод на французский язык',   'fr'],
        ['perevod-na-italyanskiy',      'Перевод на итальянский язык',   'it'],
        ['perevod-na-ispanskiy',        'Перевод на испанский язык',     'es'],
        ['perevod-na-portugalskiy',     'Перевод на португальский язык', 'pt'],
        ['perevod-na-kitayskiy',        'Перевод на китайский язык',     'zh'],
        ['perevod-na-yaponskiy',        'Перевод на японский язык',      'ja'],
        ['perevod-na-arabskiy',         'Перевод на арабский язык',      'ar'],
        ['perevod-na-koreyskiy',        'Перевод на корейский язык',     'ko'],
        ['perevod-na-niderlandskiy',    'Перевод на нидерландский язык', 'nl'],
        ['perevod-na-polskiy',          'Перевод на польский язык',      'pl'],
        ['perevod-na-cheshskiy',        'Перевод на чешский язык',       'cs'],
        ['perevod-na-slovatskiy',       'Перевод на словацкий язык',     'sk'],
        ['perevod-na-vengerskiy',       'Перевод на венгерский язык',    'hu'],
        ['perevod-na-rumynskiy',        'Перевод на румынский язык',     'ro'],
        ['perevod-na-bolgarskiy',       'Перевод на болгарский язык',    'bg'],
        ['perevod-na-serbskiy',         'Перевод на сербский язык',      'sr'],
        ['perevod-na-khorvatskiy',      'Перевод на хорватский язык',    'hr'],
        ['perevod-na-slovenskiy',       'Перевод на словенский язык',    'sl'],
        ['perevod-na-grecheskiy',       'Перевод на греческий язык',     'el'],
        ['perevod-na-finskiy',          'Перевод на финский язык',       'fi'],
        ['perevod-na-shvedskiy',        'Перевод на шведский язык',      'sv'],
        ['perevod-na-norvezhskiy',      'Перевод на норвежский язык',    'no'],
        ['perevod-na-datskiy',          'Перевод на датский язык',       'da'],
        ['perevod-na-estonskiy',        'Перевод на эстонский язык',     'et'],
        ['perevod-na-latyshskiy',       'Перевод на латышский язык',     'lv'],
        ['perevod-na-litovskiy',        'Перевод на литовский язык',     'lt'],
        ['perevod-na-ukrainskiy',       'Перевод на украинский язык',    'uk'],
        ['perevod-na-belorusskiy',      'Перевод на белорусский язык',   'be'],
        ['perevod-na-kazakhskiy',       'Перевод на казахский язык',     'kk'],
        ['perevod-na-azerbaydzhanskiy', 'Перевод на азербайджанский язык', 'az'],
        ['perevod-na-armyanskiy',       'Перевод на армянский язык',     'hy'],
        ['perevod-na-gruzinskiy',       'Перевод на грузинский язык',    'ka'],
    ];

    foreach ($lang_pages as [$slug, $title]) {
        $results[] = remarka_insert_page([
            'slug'     => $slug,
            'title'    => $title,
            'template' => 'page-templates/template-language.php',
        ]);
    }

    /* ── 2. Service pages ──────────────────────────────────────────── */
    $service_pages = [
        [
            'slug'  => 'pismennyy-perevod',
            'title' => 'Письменный перевод',
            'g1'    => 'Здравствуйте! 👋 Принимаем любые документы на перевод.',
            'g2'    => 'Загрузите файл — рассчитаем стоимость и срок за 30 минут.',
            'g3'    => 'Конфиденциальность гарантирована. NDA подписывается до передачи файлов.',
        ],
        [
            'slug'  => 'yuridicheskiy-perevod',
            'title' => 'Юридический перевод',
            'g1'    => 'Здравствуйте! 👋 Специализируемся на юридических переводах.',
            'g2'    => 'Загрузите договор, устав или судебный документ — рассчитаем стоимость.',
            'g3'    => 'Переводчики с юридическим образованием. NDA до передачи файлов.',
        ],
        [
            'slug'  => 'tekhnicheskiy-perevod',
            'title' => 'Технический перевод',
            'g1'    => 'Здравствуйте! 👋 Переводим техническую документацию любой сложности.',
            'g2'    => 'Загрузите инструкцию, чертёж или регламент — подберём профильного переводчика.',
            'g3'    => 'Инженерная экспертиза + отраслевой глоссарий. NDA по запросу.',
        ],
        [
            'slug'  => 'meditsinskiy-perevod',
            'title' => 'Медицинский перевод',
            'g1'    => 'Здравствуйте! 👋 Переводим медицинские документы с точностью врача.',
            'g2'    => 'Загрузите историю болезни или справку — переводчик с медобразованием выполнит заказ.',
            'g3'    => 'Строгая конфиденциальность медицинских данных. NDA обязателен.',
        ],
        [
            'slug'  => 'it-perevod',
            'title' => 'IT-перевод и локализация',
            'g1'    => 'Здравствуйте! 👋 Локализуем ПО, интерфейсы и документацию.',
            'g2'    => 'Загрузите XLIFF/JSON или строки интерфейса — предложим TMS-интеграцию.',
            'g3'    => 'Поддержка Lokalise, Phrase, Crowdin. CI/CD webhooks по запросу.',
        ],
        [
            'slug'  => 'perevod-saytov',
            'title' => 'Перевод сайтов',
            'g1'    => 'Здравствуйте! 👋 Переводим и локализуем сайты под любой рынок.',
            'g2'    => 'Укажите URL или загрузите экспорт — рассчитаем стоимость локализации.',
            'g3'    => 'SEO-адаптация, транскреация, работа с CMS. Конфиденциально.',
        ],
        [
            'slug'  => 'finansovyy-perevod',
            'title' => 'Финансовый перевод',
            'g1'    => 'Здравствуйте! 👋 Переводим финансовую документацию и отчётность.',
            'g2'    => 'Загрузите годовой отчёт или банковский договор — рассчитаем стоимость.',
            'g3'    => 'Переводчики с экономическим образованием. Строгий NDA.',
        ],
        [
            'slug'  => 'marketingovyy-perevod',
            'title' => 'Маркетинговый перевод',
            'g1'    => 'Здравствуйте! 👋 Адаптируем маркетинговые материалы под любой рынок.',
            'g2'    => 'Загрузите текст или презентацию — нативный редактор сохранит tone of voice.',
            'g3'    => 'Транскреация слоганов, соответствие brand book. Конфиденциально.',
        ],
        [
            'slug'  => 'ved-perevod',
            'title' => 'ВЭД-перевод',
            'g1'    => 'Здравствуйте! 👋 Переводим документы для внешнеэкономической деятельности.',
            'g2'    => 'Загрузите таможенную декларацию или контракт — рассчитаем с учётом заверения.',
            'g3'    => 'Опыт ФТС и ЕврАзЭС. Срочные таможенные документы — приоритет.',
        ],
        [
            'slug'  => 'patentnye-perevody',
            'title' => 'Патентные переводы',
            'g1'    => 'Здравствуйте! 👋 Переводим патентные заявки и описания изобретений.',
            'g2'    => 'Загрузите патент или формулу — специалист обеспечит терминологическую точность.',
            'g3'    => 'Для Роспатента и EPO. Строгий NDA до подачи заявки.',
        ],
        [
            'slug'  => 'nauchnyy-perevod',
            'title' => 'Научный перевод',
            'g1'    => 'Здравствуйте! 👋 Переводим научные статьи, диссертации и монографии.',
            'g2'    => 'Загрузите рукопись — переводчик с учёной степенью сохранит академический стиль.',
            'g3'    => 'Переводы принимаются в Scopus и WoS. Конфиденциально.',
        ],
        [
            'slug'  => 'delovaya-perepiska',
            'title' => 'Деловая переписка',
            'g1'    => 'Здравствуйте! 👋 Переводим деловые письма и протоколы переговоров.',
            'g2'    => 'Загрузите письмо или email — переведём с учётом бизнес-этикета страны-адресата.',
            'g3'    => 'Короткое письмо — за 1–3 часа. Профессиональный тон, без следов перевода.',
        ],
        [
            'slug'  => 'srochnyy-perevod',
            'title' => 'Срочный перевод',
            'g1'    => 'Здравствуйте! 👋 Выполняем срочные переводы в тот же день.',
            'g2'    => 'Загрузите документ — рассчитаем срок и стоимость срочного выполнения.',
            'g3'    => 'Готовность через 1–4 часа. Ночные и выходные дни — без доплат.',
        ],
        [
            'slug'  => 'khudozhestvennyy-perevod',
            'title' => 'Художественный перевод',
            'g1'    => 'Здравствуйте! 👋 Переводим художественную литературу и сценарии.',
            'g2'    => 'Загрузите отрывок или полную рукопись — подберём переводчика с литературным опытом.',
            'g3'    => 'Сохраняем авторский стиль, ритм и культурные отсылки.',
        ],
    ];

    $service_content = remarka_service_pages_content();

    foreach ($service_pages as $p) {
        global $wpdb;

        $r = remarka_insert_page([
            'slug'     => $p['slug'],
            'title'    => $p['title'],
            'template' => 'page-templates/template-service.php',
        ]);

        // Always update meta (not only on first create)
        update_post_meta($r['id'], '_hero_greeting_1', $p['g1']);
        update_post_meta($r['id'], '_hero_greeting_2', $p['g2']);
        update_post_meta($r['id'], '_hero_greeting_3', $p['g3']);

        // Write content directly via $wpdb to bypass KSES filters
        if (!empty($service_content[$p['slug']])) {
            $wpdb->update(
                $wpdb->posts,
                ['post_content' => $service_content[$p['slug']]],
                ['ID' => $r['id']],
                ['%s'],
                ['%d']
            );
            clean_post_cache($r['id']);
        }

        $results[] = $r;
    }

    /* ── 3. Listing / utility pages ───────────────────────────────── */
    $utility_pages = [
        ['yazyki-perevoda',             'Языки перевода',                'page-templates/template-languages.php'],
        ['stoimost-perevoda',           'Стоимость перевода',            'page-templates/template-pricing.php'],
        ['kontakty',                    'Контакты',                      'page-templates/template-contact.php'],
        ['keisy',                       'Кейсы',                         'page-templates/template-cases.php'],
        ['politika-konfidenczialnosti', 'Политика конфиденциальности',   'page-templates/template-privacy.php'],
        ['blog',                        'Блог',                          'default'],
    ];

    foreach ($utility_pages as [$slug, $title, $tpl]) {
        $results[] = remarka_insert_page([
            'slug'     => $slug,
            'title'    => $title,
            'template' => $tpl === 'default' ? '' : $tpl,
        ]);
    }

    return $results;
}

function remarka_insert_page(array $args): array {
    $slug     = $args['slug'];
    $title    = $args['title'];
    $template = $args['template'] ?? '';

    $existing = get_page_by_path($slug, OBJECT, 'page');

    if ($existing) {
        /* Update existing page: clear old content, assign correct template */
        wp_update_post([
            'ID'           => $existing->ID,
            'post_content' => '',
            'post_status'  => 'publish',
        ]);
        if ($template) {
            update_post_meta($existing->ID, '_wp_page_template', $template);
        }
        return ['action' => 'updated', 'slug' => $slug, 'title' => $title, 'id' => $existing->ID];
    }

    $id = wp_insert_post([
        'post_title'   => $title,
        'post_name'    => $slug,
        'post_type'    => 'page',
        'post_status'  => 'publish',
        'post_author'  => 1,
        'post_content' => '',
    ], true);

    if (is_wp_error($id)) {
        return ['action' => 'error', 'slug' => $slug, 'title' => $title, 'id' => 0];
    }

    if ($template) {
        update_post_meta($id, '_wp_page_template', $template);
    }

    return ['action' => 'created', 'slug' => $slug, 'title' => $title, 'id' => $id];
}

function remarka_service_pages_content(): array {
    return [
        'pismennyy-perevod' => '<section class="sec sec-intro">
    <div class="container">
      <div class="intro-grid">
        <div class="intro-text">
          <p class="intro-label">Бюро переводов «Ремарка», Москва</p>
          <h1 class="intro-title">Письменный перевод в Москве</h1>
          <p class="intro-tagline">Любой документ — на нужный язык, точно и в срок</p>
          <p class="intro-body">Письменный перевод охватывает любой текстовый материал: деловые документы, договоры, технические инструкции, медицинские справки, сайты, статьи, переписку. Главное отличие от устного — итогом работы всегда является готовый письменный текст, который можно прочитать, проверить и подписать.</p>
          <p class="intro-body">Мы не работаем по принципу «один переводчик на всё». Письменный перевод в московском бюро «Ремарка» выполняет профильный специалист: юридические документы — юристы-лингвисты, технические тексты — инженеры, медицинские материалы — врачи. Это принципиальная разница в качестве терминологии.</p>
          <p class="intro-body">Заказать письменный перевод документа просто: пришлите файл через сайт, Telegram или email — ответим в течение нескольких минут. Работаем с 60+ языками, принимаем все распространённые форматы.</p>
        </div>
        <div class="intro-visual">
          <div class="intro-illus" aria-hidden="true">
            <svg viewBox="0 0 480 200" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect x="10" y="20" width="140" height="168" rx="12" fill="rgba(120,60,240,.10)" stroke="rgba(120,60,240,.35)" stroke-width="1.5"/>
              <rect x="10" y="20" width="140" height="38" rx="12" fill="rgba(120,60,240,.20)"/>
              <rect x="10" y="43" width="140" height="15" fill="rgba(120,60,240,.20)"/>
              <rect x="24" y="29" width="32" height="16" rx="8" fill="rgba(0,160,240,.40)"/>
              <rect x="28" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="42" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="24" y="76" width="112" height="5" rx="2.5" fill="rgba(167,139,250,.45)"/>
              <rect x="24" y="88" width="84" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="100" width="102" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="112" width="70" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="124" width="110" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="136" width="90" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="148" width="98" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="160" width="76" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="330" y="20" width="140" height="168" rx="12" fill="rgba(0,160,240,.10)" stroke="rgba(0,160,240,.35)" stroke-width="1.5"/>
              <rect x="330" y="20" width="140" height="38" rx="12" fill="rgba(0,160,240,.20)"/>
              <rect x="330" y="43" width="140" height="15" fill="rgba(0,160,240,.20)"/>
              <rect x="344" y="29" width="32" height="16" rx="8" fill="rgba(120,60,240,.40)"/>
              <rect x="348" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="362" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="344" y="76" width="112" height="5" rx="2.5" fill="rgba(0,160,240,.45)"/>
              <rect x="344" y="88" width="96" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="100" width="86" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="112" width="112" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="124" width="74" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="136" width="106" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="148" width="88" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="160" width="100" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <circle cx="240" cy="104" r="46" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
              <circle cx="240" cy="104" r="30" fill="rgba(120,60,240,.18)"/>
              <path d="M152 104h56M288 104h40" stroke="rgba(120,60,240,.40)" stroke-width="1.5" stroke-dasharray="4 3"/>
              <path d="M224 104h32M246 96l10 8-10 8" stroke="rgba(167,139,250,.95)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="intro-features">
            <div class="intro-feat">
              <div class="intro-feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg></div>
              <div><strong>Все тематики</strong><span>юридика, медицина, техника, IT, маркетинг</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></div>
              <div><strong>60+ языков</strong><span>европейские, азиатские, редкие</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></div>
              <div><strong>Все форматы</strong><span>DOC, PDF, XLSX, PPTX, HTML, XLIFF</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
              <div><strong>Онлайн</strong><span>принимаем файлы 24/7, отвечаем в рабочее время</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

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

  <!-- ════════ КАКИЕ ДОКУМЕНТЫ ════════ -->
  <section class="sec sec--alt sec-docs">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Какие документы мы переводим</h2>
        <p class="sec-sub">Раскройте нужную категорию — посмотрите конкретные виды документов</p>
      </div>
      <div class="docs-accordion">

        <details class="doc-item" open>
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            <span>Деловые документы</span>
          </summary>
          <div class="doc-body">
            <p>Письма, контракты, коммерческие предложения, протоколы совещаний, служебные записки. Перевод с сохранением делового стиля и структуры.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <span>Юридические тексты</span>
          </summary>
          <div class="doc-body">
            <p>Договоры, уставы, судебные документы, доверенности, лицензионные соглашения. Точная юридическая терминология обеих правовых систем.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
            <span>Технические материалы</span>
          </summary>
          <div class="doc-body">
            <p>Инструкции по эксплуатации, руководства, конструкторская документация, патентные заявки. Переводчик с профильным инженерным образованием.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
            <span>Медицинские документы</span>
          </summary>
          <div class="doc-body">
            <p>Справки, выписки, эпикризы, инструкции к препаратам. Медицинская терминология, МКБ-классификаторы.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l19-9-9 19-2-8-8-2z"/></svg>
            <span>Маркетинговые тексты</span>
          </summary>
          <div class="doc-body">
            <p>Сайты, презентации, пресс-релизы, рекламные материалы. Адаптация под культурный контекст целевой аудитории.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            <span>Академические работы</span>
          </summary>
          <div class="doc-body">
            <p>Статьи, диссертации, рецензии, тезисы конференций. Соответствие академическому стилю и цитированию.</p>
          </div>
        </details>

      </div>
    </div>
  </section>

  <!-- ════════ НАШИ ПЕРЕВОДЧИКИ ════════ -->
  <section class="sec sec-translators">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Письменные переводчики в Москве</h2>
        <p class="sec-sub">Переводчики московского бюро «Ремарка» работают только в своей предметной области. Мы не даём юридические документы техническому переводчику и наоборот.</p>
      </div>
      <div class="translators-grid">
        <div class="translator-card">
          <div class="translator-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg>
          </div>
          <h3>Профильная специализация</h3>
          <p>Для каждой тематики свой специалист с профильным образованием.</p>
        </div>
        <div class="translator-card">
          <div class="translator-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><path d="M21 12c0 4.97-4.03 9-9 9S3 16.97 3 12 7.03 3 12 3s9 4.03 9 9z"/></svg>
          </div>
          <h3>Двухэтапная проверка</h3>
          <p>Перевод + редакторская вычитка перед сдачей.</p>
        </div>
        <div class="translator-card">
          <div class="translator-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M8.56 2.75c4.37 6.03 6.02 9.42 8.03 17.72m2.54-15.38c-3.72 4.35-8.94 5.66-16.88 5.85m19.5 1.9c-3.5-.93-6.63-.82-8.94 0-2.58.92-5.01 2.86-7.44 6.32"/></svg>
          </div>
          <h3>Опыт от 3 лет</h3>
          <p>Только практикующие профессионалы, прошедшие тестовый отбор.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="sec sec-split">
    <div class="container">
      <div class="split-row split-row--rev">
        <div class="split-text">
          <p class="sec-label">Профильная специализация</p>
          <h2>Почему важно выбрать специалиста, а не «переводчика вообще»</h2>
          <p>Письменный перевод охватывает десятки тематических областей, и у каждой из них своя терминология, свой стиль, свои требования. Юридический текст — это не просто «деловой», медицинский — не просто «сложный».</p>
          <p>Мы не отдаём ваш документ первому свободному переводчику. Сначала определяем тематику, затем подбираем специалиста с профильным образованием и опытом именно в этой области. Это занимает несколько минут, но определяет качество результата.</p>
          <ul class="split-checklist">
            <li>Подбор специалиста по тематике вашего документа</li>
            <li>Переводчик с двойной квалификацией (лингвистика + специализация)</li>
            <li>Двухэтапная проверка: перевод + независимая редактура</li>
            <li>Все форматы файлов — DOC, PDF, XLSX, PPTX, HTML</li>
          </ul>
        </div>
        <div class="split-visual" aria-hidden="true">
          <svg viewBox="0 0 380 320" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <linearGradient id="sq-g1-p" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#783CF0"/><stop offset="100%" stop-color="#00A0F0"/></linearGradient>
              <linearGradient id="sq-g2-p" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#783CF0" stop-opacity=".5"/><stop offset="100%" stop-color="#00A0F0" stop-opacity=".5"/></linearGradient>
            </defs>
            <circle cx="190" cy="160" r="130" fill="rgba(120,60,240,.07)"/>
            <rect x="60" y="30" width="260" height="64" rx="16" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="62" r="20" fill="url(#sq-g1-p)" opacity=".85"/>
            <path d="M88 62l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="50" width="170" height="9" rx="4" fill="rgba(167,139,250,.55)"/>
            <rect x="126" y="66" width="120" height="7" rx="3" fill="rgba(167,139,250,.30)"/>
            <line x1="190" y1="94" x2="190" y2="118" stroke="url(#sq-g2-p)" stroke-width="2" stroke-dasharray="4 3"/>
            <rect x="60" y="118" width="260" height="64" rx="16" fill="rgba(0,160,240,.12)" stroke="rgba(0,160,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="150" r="20" fill="url(#sq-g1-p)" opacity=".85"/>
            <path d="M88 150l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="138" width="155" height="9" rx="4" fill="rgba(0,160,240,.55)"/>
            <rect x="126" y="154" width="110" height="7" rx="3" fill="rgba(0,160,240,.30)"/>
            <line x1="190" y1="182" x2="190" y2="206" stroke="url(#sq-g2-p)" stroke-width="2" stroke-dasharray="4 3"/>
            <rect x="60" y="206" width="260" height="64" rx="16" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="238" r="20" fill="url(#sq-g1-p)" opacity=".85"/>
            <path d="M88 238l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="226" width="175" height="9" rx="4" fill="rgba(167,139,250,.55)"/>
            <rect x="126" y="242" width="130" height="7" rx="3" fill="rgba(167,139,250,.30)"/>
            <circle cx="310" cy="290" r="26" fill="rgba(0,200,100,.12)" stroke="rgba(0,200,100,.35)" stroke-width="1.5"/>
            <path d="M300 290l8 8 14-16" stroke="rgba(0,200,100,.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ КАК ПРОХОДИТ РАБОТА (6 шагов) ════════ -->
  <section class="sec sec--alt sec-how-pro">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Как проходит работа</h2>
        <p class="sec-sub">Шесть шагов от файла до готового перевода — с контролем качества на каждом этапе</p>
      </div>
      <div class="steps-row steps-row--6">
        <div class="step-item">
          <div class="step-num">01</div>
          <h3>Загрузите файл</h3>
          <p>Через сайт, мессенджер, email или принесите в офис</p>
        </div>
        <div class="step-item">
          <div class="step-num">02</div>
          <h3>Анализ документа</h3>
          <p>Оцениваем объём, тематику и сложность — за несколько минут</p>
        </div>
        <div class="step-item">
          <div class="step-num">03</div>
          <h3>Подбор специалиста</h3>
          <p>Выбираем переводчика с профильным образованием в вашей области</p>
        </div>
        <div class="step-item">
          <div class="step-num">04</div>
          <h3>Перевод</h3>
          <p>Специалист работает с соблюдением терминологии и стиля оригинала</p>
        </div>
        <div class="step-item">
          <div class="step-num">05</div>
          <h3>Корректура</h3>
          <p>Редактор проверяет точность, стиль и соответствие заданию</p>
        </div>
        <div class="step-item">
          <div class="step-num">06</div>
          <h3>Готовый перевод</h3>
          <p>Отправляем файл или вы забираете из офиса — точно в срок</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ГАРАНТИИ КАЧЕСТВА ════════ -->
  <section class="sec sec-guarantees">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Наши гарантии</h2>
        <p class="sec-sub">Мы отвечаем за результат — не только словами</p>
      </div>
      <div class="guarantees-grid">
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.9"/></svg>
          </div>
          <h3>Бесплатная правка</h3>
          <p>Исправим бесплатно в течение 7 дней, если перевод не соответствует заданию или содержит неточности</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </div>
          <h3>Конфиденциальность</h3>
          <p>NDA по запросу. Ваши документы не передаются третьим лицам и не хранятся на внешних серверах</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </div>
          <h3>Соблюдение сроков</h3>
          <p>Если не уложились в оговорённый срок по нашей вине — возвращаем предоплату или делаем скидку на следующий заказ</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
          </div>
          <h3>База терминологии</h3>
          <p>Сохраняем глоссарий вашей компании — при повторных заказах терминология остаётся единой, перевод быстрее</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ СРОКИ И СТОИМОСТЬ ════════ -->
  <section class="sec sec--alt sec-vol-pricing">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Сроки и стоимость</h2>
        <p class="sec-sub">Цена зависит от объёма. Точный расчёт — бесплатно, за несколько минут</p>
      </div>
      <div class="vol-table-wrap">
        <table class="vol-table">
          <thead>
            <tr>
              <th>Объём</th>
              <th>Срок</th>
              <th>Стоимость</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>До 5 страниц</strong></td>
              <td>1 рабочий день</td>
              <td class="vol-price">от 1 250 ₽</td>
              <td><a href="/stoimost-perevoda/" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>5–20 страниц</strong></td>
              <td>2–3 рабочих дня</td>
              <td class="vol-price">от 250 ₽/стр.</td>
              <td><a href="/stoimost-perevoda/" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>20–50 страниц</strong></td>
              <td>3–5 рабочих дней</td>
              <td class="vol-price">от 230 ₽/стр.</td>
              <td><a href="/stoimost-perevoda/" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>Свыше 50 стр.</strong></td>
              <td>по договорённости</td>
              <td class="vol-price">от 200 ₽/стр.</td>
              <td><a href="/stoimost-perevoda/" class="vol-btn">Заказать</a></td>
            </tr>
          </tbody>
        </table>
      </div>
      <p class="vol-note">Срочный заказ — коэффициент ×1,5. Цена зависит от тематики и языковой пары.</p>
    </div>
  </section>

  <!-- ════════ СРАВНЕНИЕ ФОРМАТОВ ════════ -->
  <section class="sec sec-comparison">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Какой формат вам подходит</h2>
        <p class="sec-sub">Сравните три уровня сервиса и выберите оптимальный для вашей задачи</p>
      </div>
      <div class="cmp-table-wrap">
        <table class="cmp-table">
          <thead>
            <tr>
              <th></th>
              <th>Постредактирование ИИ</th>
              <th class="cmp-featured">Профессиональный перевод</th>
              <th>Премиум-перевод</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>Перевод с нуля специалистом</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Профильное образование переводчика</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Корректура и вычитка</td><td class="cmp-part">частичная</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Редактура профильным экспертом</td><td class="cmp-no">✗</td><td class="cmp-no cmp-featured">✗</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Вычитка носителем языка</td><td class="cmp-no">✗</td><td class="cmp-no cmp-featured">✗</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Подходит для публикаций и тендеров</td><td class="cmp-no">✗</td><td class="cmp-part cmp-featured">условно</td><td class="cmp-yes">✓</td></tr>
            <tr class="cmp-meta"><td>Срок</td><td>от 1 дня</td><td class="cmp-featured">1–3 дня</td><td>2–5 дней</td></tr>
            <tr class="cmp-meta cmp-price-row"><td>Стоимость</td><td>от 250 ₽/стр.</td><td class="cmp-featured">от 500 ₽/стр.</td><td>от 800 ₽/стр.</td></tr>
          </tbody>
          <tfoot>
            <tr>
              <td></td>
              <td><a href="/stoimost-perevoda/" class="price-btn price-btn--outline">Выбрать</a></td>
              <td class="cmp-featured"><a href="/stoimost-perevoda/" class="price-btn price-btn--primary">Выбрать</a></td>
              <td><a href="/stoimost-perevoda/" class="price-btn price-btn--outline">Выбрать</a></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </section>

  <!-- ════════ СПРАВОЧНИК ПО ДОКУМЕНТАМ ════════ -->
  <section class="sec sec-doc-reference">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Справочник по документам</h2>
        <p class="sec-sub">Выберите тип документа — узнайте особенности перевода</p>
      </div>
      <div class="doc-ref-grid">

        <div class="doc-ref-card">
          <div class="doc-ref-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          </div>
          <h3>Деловые письма</h3>
          <p>Официальная корреспонденция между компаниями: запросы, ответы, уведомления. Перевод сохраняет деловой тон и этикет.</p>
          <a href="#"><span class="doc-ref-more">Подробнее →</span></a>
        </div>

        <div class="doc-ref-card">
          <div class="doc-ref-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
          </div>
          <h3>Договоры и контракты</h3>
          <p>Купли-продажи, поставки, аренды, подряда. Юридически точный перевод с соблюдением терминологии обеих правовых систем.</p>
          <a href="#"><span class="doc-ref-more">Подробнее →</span></a>
        </div>

        <div class="doc-ref-card">
          <div class="doc-ref-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/></svg>
          </div>
          <h3>Коммерческие предложения</h3>
          <p>КП и тендерная документация для зарубежных партнёров. Адаптируем под стиль и ожидания целевой аудитории.</p>
          <a href="#"><span class="doc-ref-more">Подробнее →</span></a>
        </div>

        <div class="doc-ref-card">
          <div class="doc-ref-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          </div>
          <h3>Уставы и учредительные документы</h3>
          <p>Для регистрации компании за рубежом или привлечения иностранных инвесторов. Требуют точного юридического перевода.</p>
          <a href="#"><span class="doc-ref-more">Подробнее →</span></a>
        </div>

        <div class="doc-ref-card">
          <div class="doc-ref-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
          </div>
          <h3>Технические инструкции</h3>
          <p>Руководства по эксплуатации оборудования, технические описания, регламенты. Переводчик с инженерным образованием.</p>
          <a href="#"><span class="doc-ref-more">Подробнее →</span></a>
        </div>

        <div class="doc-ref-card">
          <div class="doc-ref-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
          </div>
          <h3>Медицинские справки</h3>
          <p>Выписки, эпикризы, рецепты для лечения за рубежом или подтверждения диагноза. Строгая конфиденциальность.</p>
          <a href="#"><span class="doc-ref-more">Подробнее →</span></a>
        </div>

        <div class="doc-ref-card">
          <div class="doc-ref-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          </div>
          <h3>Патенты и заявки</h3>
          <p>Патентная документация РОСПАТЕНТ/ВОИС, описания изобретений, формулы. Специализированная терминология.</p>
          <a href="#"><span class="doc-ref-more">Подробнее →</span></a>
        </div>

        <div class="doc-ref-card">
          <div class="doc-ref-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
          </div>
          <h3>Академические работы</h3>
          <p>Статьи, диссертации, рецензии. Соответствие академическому стилю, правильное цитирование источников.</p>
          <a href="#"><span class="doc-ref-more">Подробнее →</span></a>
        </div>

        <div class="doc-ref-card">
          <div class="doc-ref-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l19-9-9 19-2-8-8-2z"/></svg>
          </div>
          <h3>Рекламные и маркетинговые тексты</h3>
          <p>Сайты, брошюры, флаеры, слоганы. Не дословный перевод, а адаптация под культуру и язык аудитории.</p>
          <a href="#"><span class="doc-ref-more">Подробнее →</span></a>
        </div>

        <div class="doc-ref-card">
          <div class="doc-ref-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
          </div>
          <h3>Финансовые документы</h3>
          <p>Годовые отчёты, бухгалтерские балансы, аудиторские заключения. Точные финансовые термины.</p>
          <a href="#"><span class="doc-ref-more">Подробнее →</span></a>
        </div>

        <div class="doc-ref-card">
          <div class="doc-ref-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
          </div>
          <h3>Судебные документы</h3>
          <p>Решения судов, исковые заявления, апелляции, апостили. Юрист-переводчик с пониманием процессуального права.</p>
          <a href="#"><span class="doc-ref-more">Подробнее →</span></a>
        </div>

        <div class="doc-ref-card">
          <div class="doc-ref-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
          </div>
          <h3>Сертификаты и лицензии</h3>
          <p>Для работы за рубежом, участия в тендерах, получения разрешений. Заверение и апостиль при необходимости.</p>
          <a href="#"><span class="doc-ref-more">Подробнее →</span></a>
        </div>

      </div>
    </div>
  </section>

  <!-- ════════ ПЕРЕВОДЫ ПО ОТРАСЛЯМ ════════ -->
  <section class="sec sec--alt sec-industries">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Переводы по отраслям</h2>
        <p class="sec-sub">Специфика письменного перевода в разных сферах</p>
      </div>
      <div class="industries-grid">

        <div class="industry-card">
          <div class="industry-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
          </div>
          <h3>Бизнес и финансы</h3>
          <p>Переводы для международной торговли, инвестиций и корпоративного управления.</p>
          <ul class="industry-docs">
            <li>Годовые отчёты</li>
            <li>Инвестиционные меморандумы</li>
            <li>Корпоративные соглашения</li>
          </ul>
          <a href="#"><span class="industry-more">Подробнее →</span></a>
        </div>

        <div class="industry-card">
          <div class="industry-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
          </div>
          <h3>Юриспруденция</h3>
          <p>Правовые документы для международных сделок и судебных процессов.</p>
          <ul class="industry-docs">
            <li>Международные договоры</li>
            <li>Судебные решения</li>
            <li>Арбитражные соглашения</li>
          </ul>
          <a href="#"><span class="industry-more">Подробнее →</span></a>
        </div>

        <div class="industry-card">
          <div class="industry-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
          </div>
          <h3>Медицина и фармация</h3>
          <p>Медицинская документация для лечения, исследований, регуляторных органов.</p>
          <ul class="industry-docs">
            <li>Медкарты и выписки</li>
            <li>Регуляторные досье</li>
            <li>Инструкции к препаратам</li>
          </ul>
          <a href="#"><span class="industry-more">Подробнее →</span></a>
        </div>

        <div class="industry-card">
          <div class="industry-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
          </div>
          <h3>Промышленность</h3>
          <p>Техническая документация для производственных предприятий.</p>
          <ul class="industry-docs">
            <li>Конструкторская документация</li>
            <li>Технические регламенты</li>
            <li>Руководства по обслуживанию</li>
          </ul>
          <a href="#"><span class="industry-more">Подробнее →</span></a>
        </div>

        <div class="industry-card">
          <div class="industry-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
          </div>
          <h3>IT и технологии</h3>
          <p>Документация для разработки, поддержки и пользователей.</p>
          <ul class="industry-docs">
            <li>API-документация</li>
            <li>Пользовательские руководства</li>
            <li>Политики конфиденциальности</li>
          </ul>
          <a href="#"><span class="industry-more">Подробнее →</span></a>
        </div>

        <div class="industry-card">
          <div class="industry-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
          </div>
          <h3>Наука и образование</h3>
          <p>Академические тексты для публикаций и международного сотрудничества.</p>
          <ul class="industry-docs">
            <li>Научные статьи</li>
            <li>Диссертации</li>
            <li>Академические отчёты</li>
          </ul>
          <a href="#"><span class="industry-more">Подробнее →</span></a>
        </div>

        <div class="industry-card">
          <div class="industry-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l19-9-9 19-2-8-8-2z"/></svg>
          </div>
          <h3>Маркетинг и реклама</h3>
          <p>Рекламные материалы для выхода на зарубежные рынки.</p>
          <ul class="industry-docs">
            <li>Рекламные тексты и слоганы</li>
            <li>Сайты и лендинги</li>
            <li>PR-материалы</li>
          </ul>
          <a href="#"><span class="industry-more">Подробнее →</span></a>
        </div>

        <div class="industry-card">
          <div class="industry-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          </div>
          <h3>Государственный сектор</h3>
          <p>Официальные документы для госорганов и международных организаций.</p>
          <ul class="industry-docs">
            <li>Официальные соглашения</li>
            <li>Государственные контракты</li>
            <li>Нормативные акты</li>
          </ul>
          <a href="#"><span class="industry-more">Подробнее →</span></a>
        </div>

        <div class="industry-card">
          <div class="industry-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
          </div>
          <h3>Международная торговля</h3>
          <p>Документы для экспорта, импорта и таможенного оформления.</p>
          <ul class="industry-docs">
            <li>Контракты на поставку</li>
            <li>Таможенные декларации</li>
            <li>Сертификаты происхождения</li>
          </ul>
          <a href="#"><span class="industry-more">Подробнее →</span></a>
        </div>

      </div>
    </div>
  </section>

  <!-- ════════ ОТЗЫВЫ ════════ -->
  <section class="sec sec--alt sec-reviews">
    <div class="container">
      <div class="reviews-head">
        <div>
          <h2 class="sec-title">Что говорят клиенты</h2>
          <p class="sec-sub" style="margin-top:6px">Реальные отзывы о письменных переводах</p>
        </div>
        <div class="review-rating-block">
          <div class="review-rating-score">4.98</div>
          <div class="review-stars-row">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          </div>
          <div class="review-rating-label">средний рейтинг</div>
        </div>
        <div class="review-platform-links">
          <a href="https://yandex.ru/maps/org/remarka/51867347382/reviews/" class="review-platform-link" target="_blank" rel="noopener">Яндекс</a>
          <a href="https://maps.app.goo.gl/d8BKJYw81PqBHXvz7" class="review-platform-link" target="_blank" rel="noopener">Google</a>
          <a href="https://2gis.ru" class="review-platform-link" target="_blank" rel="noopener">2ГИС</a>
        </div>
      </div>
      <div class="reviews-grid" style="grid-template-columns:repeat(3,1fr)">
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">М</div>
            <div class="review-meta"><div class="review-name">Марина Соколова</div><div class="review-src">Яндекс · Менеджер по ВЭД, ООО «Экспортпром»</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Регулярно заказываем переводы контрактов с немецкими и китайскими партнёрами. Качество отличное, сроки соблюдают. Понравилось, что переводчик уточнял специфические термины по нашей отрасли — сразу видно, специалист.</p>
        </div>
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">А</div>
            <div class="review-meta"><div class="review-name">Алексей Воронов</div><div class="review-src">Google Maps · Директор по развитию</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Заказывал перевод финансовой отчётности для иностранных акционеров. Объём большой — 80 страниц. Справились за 4 рабочих дня, качество проверил наш аудитор — никаких замечаний.</p>
        </div>
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">Н</div>
            <div class="review-meta"><div class="review-name">Наталья Кузнецова</div><div class="review-src">2ГИС · HR-директор</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Используем бюро для перевода трудовых договоров и корпоративных документов на английский. Всегда точно, профессионально. Несколько раз обращались со срочными заказами — ни разу не подвели.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ FAQ ════════ -->
  <section class="sec sec-faq">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Часто задаваемые вопросы</h2>
        <p class="sec-sub">Не нашли ответа — напишите Ольге в чат, ответим за минуту</p>
      </div>
      <div class="faq-list">

        <details class="faq-item">
          <summary class="faq-summary">Чем письменный перевод отличается от устного?</summary>
          <div class="faq-body">
            <p>Письменный перевод — это работа с текстовым документом, результатом которой является готовый письменный текст. Устный перевод выполняется синхронно или последовательно в реальном времени: на переговорах, конференциях, в суде.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Как быстро вы переводите документы?</summary>
          <div class="faq-body">
            <p>Стандартный срок — 1–3 рабочих дня для документа до 5 страниц. При заказе до 12:00 небольшой документ готов в тот же день. Для больших объёмов составляем индивидуальный график.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Принимаете ли вы рукописные документы и сканы?</summary>
          <div class="faq-body">
            <p>Да, работаем со сканами и фотографиями документов. При низком качестве изображения предупредим заранее. Рукописные документы принимаем, если текст разборчив.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Переводите ли вы сайты и маркетинговые материалы?</summary>
          <div class="faq-body">
            <p>Да. Переводим сайты (HTML, CMS-экспорты), презентации, брошюры, рекламные тексты. Для маркетинговых материалов делаем адаптацию, а не дословный перевод.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Сколько стоит письменный перевод на редкий язык?</summary>
          <div class="faq-body">
            <p>Стоимость перевода на редкие языки (японский, корейский, арабский, хинди и др.) выше стандартной — уточняйте при заказе. Работаем с 60+ языками.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Где заказать письменный перевод в Москве?</summary>
          <div class="faq-body">
            <p>Закажите через форму на сайте, Telegram или WhatsApp. Наш офис находится в Москве, но работаем удалённо по всей России — присылайте документ онлайн.</p>
          </div>
        </details>

      </div>
    </div>
  </section>

  <!-- ════════ КАЛЬКУЛЯТОР СТОИМОСТИ ════════ -->',
        'yuridicheskiy-perevod' => '<section class="sec sec-intro">
    <div class="container">
      <div class="intro-grid">
        <div class="intro-text">
          <p class="intro-label">Бюро переводов «Ремарка», Москва</p>
          <h1 class="intro-title">Юридический перевод в Москве</h1>
          <p class="intro-tagline">Каждое слово имеет юридический вес</p>
          <p class="intro-body">Юридический документ — не просто текст: неточный перевод одного термина может изменить смысл целого договора или сделать судебное решение неисполнимым. Переводчик с только лингвистическим образованием не знает разницы между «существенным условием» и «обычным» — и именно здесь возникают проблемы.</p>
          <p class="intro-body">Юридический перевод в московском бюро «Ремарка» выполняют специалисты с двойной квалификацией. Каждый из них прошёл профессиональную подготовку как юрист и как лингвист — и понимает не только текст документа, но и правовую систему страны, с языка которой переводит.</p>
          <p class="intro-body">Заказать юридический перевод документов у нас означает получить результат, который не придётся переделывать перед подачей в суд, нотариусу или иностранному партнёру. Мы работаем с NDA и гарантируем полную конфиденциальность.</p>
        </div>
        <div class="intro-visual">
          <div class="intro-illus" aria-hidden="true">
            <svg viewBox="0 0 480 200" fill="none" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <linearGradient id="ip-g1" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#783CF0"/><stop offset="100%" stop-color="#00A0F0"/></linearGradient>
              </defs>
              <rect x="10" y="20" width="140" height="168" rx="12" fill="rgba(120,60,240,.10)" stroke="rgba(120,60,240,.35)" stroke-width="1.5"/>
              <rect x="10" y="20" width="140" height="38" rx="12" fill="rgba(120,60,240,.20)"/>
              <rect x="10" y="43" width="140" height="15" fill="rgba(120,60,240,.20)"/>
              <rect x="24" y="29" width="32" height="16" rx="8" fill="rgba(0,160,240,.40)"/>
              <rect x="28" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="42" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="24" y="76" width="112" height="5" rx="2.5" fill="rgba(167,139,250,.45)"/>
              <rect x="24" y="88" width="84" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="100" width="102" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="112" width="70" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="124" width="110" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="136" width="90" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="148" width="98" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="160" width="76" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="330" y="20" width="140" height="168" rx="12" fill="rgba(0,160,240,.10)" stroke="rgba(0,160,240,.35)" stroke-width="1.5"/>
              <rect x="330" y="20" width="140" height="38" rx="12" fill="rgba(0,160,240,.20)"/>
              <rect x="330" y="43" width="140" height="15" fill="rgba(0,160,240,.20)"/>
              <rect x="344" y="29" width="32" height="16" rx="8" fill="rgba(120,60,240,.40)"/>
              <rect x="348" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="362" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="344" y="76" width="112" height="5" rx="2.5" fill="rgba(0,160,240,.45)"/>
              <rect x="344" y="88" width="96" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="100" width="86" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="112" width="112" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="124" width="74" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="136" width="106" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="148" width="88" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="160" width="100" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <circle cx="240" cy="104" r="46" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
              <circle cx="240" cy="104" r="30" fill="rgba(120,60,240,.18)"/>
              <path d="M152 104h56M288 104h40" stroke="rgba(120,60,240,.40)" stroke-width="1.5" stroke-dasharray="4 3"/>
              <path d="M224 104h32M246 96l10 8-10 8" stroke="rgba(167,139,250,.95)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="intro-features">
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <!-- Gavel icon -->
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 6l-1-2H5v17h2v-7h5.5l1 2H20V6h-6z"/><line x1="3" y1="6" x2="5" y2="6"/><line x1="3" y1="10" x2="5" y2="10"/></svg>
              </div>
              <div><strong>Юридическое образование</strong><span>Переводчик понимает правовую систему, а не только язык</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <!-- Lock icon -->
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              </div>
              <div><strong>Конфиденциальность</strong><span>NDA по запросу, документы не передаются третьим лицам</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <!-- File-text icon -->
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
              </div>
              <div><strong>Сохранение структуры</strong><span>Нумерация статей, ссылки, форматирование сохраняются</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <!-- Book icon -->
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
              </div>
              <div><strong>Глоссарий клиента</strong><span>Единая терминология во всех документах компании</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ЦИФРЫ ════════ -->
  <section class="sec-stats">
    <div class="container">
      <div class="stats-row">
        <div class="stat-item">
          <span class="stat-num">25<span class="stat-suffix">+</span></span>
          <span class="stat-label">лет на рынке</span>
        </div>
        <div class="stat-item">
          <span class="stat-num">60<span class="stat-suffix">+</span></span>
          <span class="stat-label">языков перевода</span>
        </div>
        <div class="stat-item">
          <span class="stat-num">2 400<span class="stat-suffix">+</span></span>
          <span class="stat-label">выполненных заказов</span>
        </div>
        <div class="stat-item">
          <span class="stat-num">4.98<span class="stat-suffix">★</span></span>
          <span class="stat-label">средний рейтинг</span>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ЯЗЫКОВЫЕ ПАРЫ ════════ -->
  <section class="sec sec--alt sec-lang-pairs">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Языковые пары для юридических документов</h2>
        <p class="sec-sub">Переводчики знают правовую систему страны оригинала — не только язык</p>
      </div>
      <div class="fl-grid" style="grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:20px">
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">EN ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Английский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Common law (UK/US), договоры по английскому праву, LCIA, ICC, международные контракты</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">DE ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Немецкий</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Немецкое/австрийское/швейцарское право, GmbH, AG, DIS-арбитраж, документы SAP/DACH</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">FR ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Французский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Французское/бельгийское/швейцарское право, документы ICC, ВЭД, корпоративные документы</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">ZH ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Китайский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Контракты с КНР, СП-соглашения, JV-документация, нормы КНР о корпоративном и контрактном праве</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">IT ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Итальянский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Итальянское коммерческое право, договоры с итальянскими компаниями, лицензионные соглашения</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">AR ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Арабский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Контракты с ОАЭ, Саудовской Аравией, документы по законодательству стран Залива и шариатскому праву</p>
        </div>
      </div>
      <p style="text-align:center;font-size:13px;color:var(--text-secondary)">Другие языки — испанский, японский, португальский, нидерландский, польский и ещё 50+. <a href="#calc-section" style="color:var(--accent)">Уточните наличие переводчика</a></p>
    </div>
  </section>

  <!-- ════════ КАКИЕ ДОКУМЕНТЫ ════════ -->
  <section class="sec sec-docs">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Какие документы мы переводим</h2>
        <p class="sec-sub">Раскройте нужную категорию — посмотрите конкретные виды документов</p>
      </div>
      <div class="docs-accordion">

        <details class="doc-item" open>
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            <span>Договоры и контракты</span>
          </summary>
          <div class="doc-body">
            <p>Купли-продажи, аренды, подряда, поставки, лицензионные, NDA, агентские соглашения.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <span>Корпоративные документы</span>
          </summary>
          <div class="doc-body">
            <p>Уставы, учредительные договоры, протоколы собраний, доверенности.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="8" y1="8" x2="16" y2="8"/><line x1="8" y1="16" x2="12" y2="16"/></svg>
            <span>Судебные документы</span>
          </summary>
          <div class="doc-body">
            <p>Исковые заявления, решения судов, определения, апостили, нотариальные акты.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            <span>Международные соглашения</span>
          </summary>
          <div class="doc-body">
            <p>Двусторонние договоры, арбитражные соглашения, коносаменты, аккредитивы.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
            <span>Интеллектуальная собственность</span>
          </summary>
          <div class="doc-body">
            <p>Патентные лицензии, авторские договоры, регистрация торговых марок.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            <span>Разрешительная документация</span>
          </summary>
          <div class="doc-body">
            <p>Лицензии, сертификаты, разрешения на деятельность, декларации.</p>
          </div>
        </details>

      </div>
    </div>
  </section>

  <!-- ════════ НАШИ ПЕРЕВОДЧИКИ ════════ -->
  <section class="sec sec-translators">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Юристы-переводчики в Москве</h2>
        <p class="sec-sub">Юридические переводчики московского бюро «Ремарка» знают не только язык, но и правовые системы обеих стран</p>
      </div>
      <div class="translators-grid">
        <div class="translator-card">
          <div class="translator-icon">
            <!-- Degree/diploma icon -->
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
          </div>
          <h3>Двойная квалификация</h3>
          <p>Юридический диплом плюс лингвистическая подготовка — не одно или другое, а оба.</p>
        </div>
        <div class="translator-card">
          <div class="translator-icon">
            <!-- Globe icon -->
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
          </div>
          <h3>Знание правовых систем</h3>
          <p>Common law, континентальное право, МКАС — переводчик понимает контекст каждого документа.</p>
        </div>
        <div class="translator-card">
          <div class="translator-icon">
            <!-- Shield icon -->
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          </div>
          <h3>Строгий NDA</h3>
          <p>Каждый переводчик подписывает соглашение о неразглашении перед началом работы.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ КОНТРОЛЬ КАЧЕСТВА — SPLIT SECTION ════════ -->
  <section class="sec sec-split">
    <div class="container">
      <div class="split-row split-row--rev">
        <div class="split-text">
          <p class="sec-label">Юридическая точность</p>
          <h2>Почему юридический перевод требует особого подхода</h2>
          <p>Юридические документы содержат термины, значение которых строго определено законодательством конкретной страны. Слово «договор» в российском праве и «contract» в английском — это не просто перевод, а соотнесение двух разных правовых систем.</p>
          <p>Наши переводчики-юристы прошли подготовку по праву обеих стран. Они знают, как перевести понятие так, чтобы оно имело корректное правовое значение в целевой системе.</p>
          <ul class="split-checklist">
            <li>Переводчик с юридическим образованием и лингвистической подготовкой</li>
            <li>Знание common law, континентального права, МКАС</li>
            <li>Строгое NDA — каждый переводчик подписывает соглашение</li>
            <li>Независимая юридическая проверка точности терминологии</li>
          </ul>
        </div>
        <div class="split-visual" aria-hidden="true">
          <svg viewBox="0 0 380 320" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <linearGradient id="sq-g1-y" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#783CF0"/><stop offset="100%" stop-color="#00A0F0"/></linearGradient>
              <linearGradient id="sq-g2-y" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#783CF0" stop-opacity=".5"/><stop offset="100%" stop-color="#00A0F0" stop-opacity=".5"/></linearGradient>
            </defs>
            <circle cx="190" cy="160" r="130" fill="rgba(120,60,240,.07)"/>
            <rect x="60" y="30" width="260" height="64" rx="16" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="62" r="20" fill="url(#sq-g1-y)" opacity=".85"/>
            <path d="M88 62l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="50" width="170" height="9" rx="4" fill="rgba(167,139,250,.55)"/>
            <rect x="126" y="66" width="120" height="7" rx="3" fill="rgba(167,139,250,.30)"/>
            <line x1="190" y1="94" x2="190" y2="118" stroke="url(#sq-g2-y)" stroke-width="2" stroke-dasharray="4 3"/>
            <rect x="60" y="118" width="260" height="64" rx="16" fill="rgba(0,160,240,.12)" stroke="rgba(0,160,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="150" r="20" fill="url(#sq-g1-y)" opacity=".85"/>
            <path d="M88 150l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="138" width="155" height="9" rx="4" fill="rgba(0,160,240,.55)"/>
            <rect x="126" y="154" width="110" height="7" rx="3" fill="rgba(0,160,240,.30)"/>
            <line x1="190" y1="182" x2="190" y2="206" stroke="url(#sq-g2-y)" stroke-width="2" stroke-dasharray="4 3"/>
            <rect x="60" y="206" width="260" height="64" rx="16" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="238" r="20" fill="url(#sq-g1-y)" opacity=".85"/>
            <path d="M88 238l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="226" width="175" height="9" rx="4" fill="rgba(167,139,250,.55)"/>
            <rect x="126" y="242" width="130" height="7" rx="3" fill="rgba(167,139,250,.30)"/>
            <circle cx="310" cy="290" r="26" fill="rgba(0,200,100,.12)" stroke="rgba(0,200,100,.35)" stroke-width="1.5"/>
            <path d="M300 290l8 8 14-16" stroke="rgba(0,200,100,.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ КАК ПРОХОДИТ РАБОТА (6 шагов) ════════ -->
  <section class="sec sec--alt sec-how-pro">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Как проходит работа</h2>
        <p class="sec-sub">Шесть шагов от файла до готового перевода — с контролем качества на каждом этапе</p>
      </div>
      <div class="steps-row steps-row--6">
        <div class="step-item">
          <div class="step-num">01</div>
          <h3>Загрузите файл</h3>
          <p>Через сайт, мессенджер, email или принесите в офис</p>
        </div>
        <div class="step-item">
          <div class="step-num">02</div>
          <h3>Анализ документа</h3>
          <p>Оцениваем объём, тематику и сложность — за несколько минут</p>
        </div>
        <div class="step-item">
          <div class="step-num">03</div>
          <h3>Подбор специалиста</h3>
          <p>Выбираем переводчика с профильным образованием в вашей области</p>
        </div>
        <div class="step-item">
          <div class="step-num">04</div>
          <h3>Перевод</h3>
          <p>Специалист работает с соблюдением терминологии и стиля оригинала</p>
        </div>
        <div class="step-item">
          <div class="step-num">05</div>
          <h3>Корректура</h3>
          <p>Редактор проверяет точность, стиль и соответствие заданию</p>
        </div>
        <div class="step-item">
          <div class="step-num">06</div>
          <h3>Готовый перевод</h3>
          <p>Отправляем файл или вы забираете из офиса — точно в срок</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ГАРАНТИИ КАЧЕСТВА ════════ -->
  <section class="sec sec-guarantees">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Наши гарантии</h2>
        <p class="sec-sub">Мы отвечаем за результат — не только словами</p>
      </div>
      <div class="guarantees-grid">
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.9"/></svg>
          </div>
          <h3>Бесплатная правка</h3>
          <p>Исправим бесплатно в течение 7 дней, если перевод не соответствует заданию или содержит неточности</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </div>
          <h3>Конфиденциальность</h3>
          <p>NDA по запросу. Ваши документы не передаются третьим лицам и не хранятся на внешних серверах</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </div>
          <h3>Соблюдение сроков</h3>
          <p>Если не уложились в оговорённый срок по нашей вине — возвращаем предоплату или делаем скидку на следующий заказ</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
          </div>
          <h3>База терминологии</h3>
          <p>Сохраняем глоссарий вашей компании — при повторных заказах терминология остаётся единой, перевод быстрее</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ СРОКИ И СТОИМОСТЬ ════════ -->
  <section class="sec sec--alt sec-vol-pricing">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Сроки и стоимость</h2>
        <p class="sec-sub">Цена зависит от объёма. Точный расчёт — бесплатно, за несколько минут</p>
      </div>
      <div class="vol-table-wrap">
        <table class="vol-table">
          <thead>
            <tr>
              <th>Объём</th>
              <th>Срок</th>
              <th>Стоимость</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>До 5 страниц</strong></td>
              <td>1 рабочий день</td>
              <td class="vol-price">от 3 000 ₽</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>5–20 страниц</strong></td>
              <td>2–3 рабочих дня</td>
              <td class="vol-price">от 400 ₽/стр.</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>20–50 страниц</strong></td>
              <td>3–5 рабочих дней</td>
              <td class="vol-price">от 380 ₽/стр.</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>Свыше 50 страниц</strong></td>
              <td>по договорённости</td>
              <td class="vol-price">от 350 ₽/стр.</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
          </tbody>
        </table>
      </div>
      <p class="vol-note">Срочный перевод (менее 24 часов) — коэффициент ×1,5 к базовой стоимости. Точный расчёт сделаем после анализа вашего файла.</p>
    </div>
  </section>

  <!-- ════════ СРАВНЕНИЕ ФОРМАТОВ ════════ -->
  <section class="sec sec-comparison">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Какой формат вам подходит</h2>
        <p class="sec-sub">Сравните три уровня сервиса и выберите оптимальный для вашей задачи</p>
      </div>
      <div class="cmp-table-wrap">
        <table class="cmp-table">
          <thead>
            <tr>
              <th></th>
              <th>Постредактирование ИИ</th>
              <th class="cmp-featured">Профессиональный перевод</th>
              <th>Премиум-перевод</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>Переводчик с юридическим образованием</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Знание правовых систем (common law, ГК РФ, МКАС)</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>NDA переводчика (до передачи файлов)</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Глоссарий и TM клиента</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Оговорка переводчика (certification)</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Корректура юридическим редактором</td><td class="cmp-no">✗</td><td class="cmp-no cmp-featured">✗</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Вычитка носителем языка</td><td class="cmp-no">✗</td><td class="cmp-no cmp-featured">✗</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Подходит для суда / нотариуса / арбитража</td><td class="cmp-no">✗</td><td class="cmp-part cmp-featured">условно</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Нотариальное заверение</td><td class="cmp-no">✗</td><td class="cmp-part cmp-featured">по запросу</td><td class="cmp-part">по запросу</td></tr>
            <tr class="cmp-meta"><td>Срок</td><td>от 1 дня</td><td class="cmp-featured">1–3 дня</td><td>2–5 дней</td></tr>
            <tr class="cmp-meta cmp-price-row"><td>Стоимость</td><td>от 250 ₽/стр.</td><td class="cmp-featured">от 500 ₽/стр.</td><td>от 800 ₽/стр.</td></tr>
          </tbody>
          <tfoot>
            <tr>
              <td></td>
              <td><a href="#calc-section" class="price-btn price-btn--outline">Выбрать</a></td>
              <td class="cmp-featured"><a href="#calc-section" class="price-btn price-btn--primary">Выбрать</a></td>
              <td><a href="#calc-section" class="price-btn price-btn--outline">Выбрать</a></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </section>

  <!-- ════════ СПРАВОЧНИК ПО ДОКУМЕНТАМ ════════ -->
  <section class="sec sec--alt sec-doc-reference">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Справочник по документам</h2>
        <p class="sec-sub">Выберите тип документа — узнайте особенности юридического перевода</p>
      </div>
      <div class="doc-ref-grid">
        <a href="/yuridicheskiy-perevod/dogovory/" class="doc-ref-card">
          <strong>Перевод договоров</strong>
          <span>Любые договоры и соглашения для сделок, партнёрств и ВЭД</span>
        </a>
        <a href="/yuridicheskiy-perevod/kontrakty/" class="doc-ref-card">
          <strong>Перевод контрактов</strong>
          <span>Международные контракты с иностранными компаниями</span>
        </a>
        <a href="/yuridicheskiy-perevod/ustavy/" class="doc-ref-card">
          <strong>Перевод уставов</strong>
          <span>Учредительные документы компаний для регистрации и аккредитации</span>
        </a>
        <a href="/yuridicheskiy-perevod/doverennosti/" class="doc-ref-card">
          <strong>Перевод доверенностей</strong>
          <span>Генеральные и специальные доверенности для корпоративных целей</span>
        </a>
        <a href="/yuridicheskiy-perevod/sudebnye-resheniya/" class="doc-ref-card">
          <strong>Перевод судебных решений</strong>
          <span>Решения, определения, постановления для иностранных судов</span>
        </a>
        <a href="/yuridicheskiy-perevod/soglasheniya-nda/" class="doc-ref-card">
          <strong>Перевод соглашений NDA</strong>
          <span>Соглашения о неразглашении для международных проектов</span>
        </a>
        <a href="/yuridicheskiy-perevod/korporativnye-protokoly/" class="doc-ref-card">
          <strong>Перевод корпоративных протоколов</strong>
          <span>Протоколы собраний, решения участников, меморандумы</span>
        </a>
        <a href="/yuridicheskiy-perevod/bankovskie-garantii/" class="doc-ref-card">
          <strong>Перевод банковских гарантий</strong>
          <span>Гарантийные письма, аккредитивы, поручительства</span>
        </a>
        <a href="/yuridicheskiy-perevod/arbitrazhnye-materialy/" class="doc-ref-card">
          <strong>Перевод арбитражных материалов</strong>
          <span>Иски, ответы, решения МКАС, ICC, LCIA, SCC</span>
        </a>
        <a href="/yuridicheskiy-perevod/litsenzionnye-soglasheniya/" class="doc-ref-card">
          <strong>Перевод лицензионных соглашений</strong>
          <span>Лицензии на ПО, торговые марки, изобретения и франшизы</span>
        </a>
        <a href="/yuridicheskiy-perevod/sha-spa-term-sheets/" class="doc-ref-card">
          <strong>Перевод SHA / SPA / Term Sheets</strong>
          <span>Сделки M&amp;A, акционерные и инвестиционные соглашения</span>
        </a>
        <a href="/yuridicheskiy-perevod/sudoproizvodstvennye-dokumenty/" class="doc-ref-card">
          <strong>Судопроизводственные документы</strong>
          <span>Апелляции, ходатайства, экспертные заключения для суда</span>
        </a>
      </div>
    </div>
  </section>

  <!-- ════════ ПЕРЕВОДЫ ПО ОТРАСЛЯМ ════════ -->
  <section class="sec sec-industries-legal">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Переводы по отраслям</h2>
        <p class="sec-sub">Юридический перевод для разных сфер бизнеса</p>
      </div>
      <div class="industry-grid">
        <a href="/yuridicheskiy-perevod/mezhdunarodnaya-torgovlya/" class="industry-card">
          <strong>Международная торговля</strong>
          <span>Внешнеторговые контракты, инвойсы, условия Incoterms</span>
        </a>
        <a href="/yuridicheskiy-perevod/banki-finansy/" class="industry-card">
          <strong>Банки и финансы</strong>
          <span>Кредитные договоры, банковские гарантии, проспекты эмиссии</span>
        </a>
        <a href="/yuridicheskiy-perevod/stroitelstvo-development/" class="industry-card">
          <strong>Строительство и девелопмент</strong>
          <span>Договоры подряда, проектная документация, разрешения</span>
        </a>
        <a href="/yuridicheskiy-perevod/neftegaz-energetika/" class="industry-card">
          <strong>Нефтегаз и энергетика</strong>
          <span>Концессионные соглашения, СРП, контракты на бурение</span>
        </a>
        <a href="/yuridicheskiy-perevod/it-tekhnologii/" class="industry-card">
          <strong>IT и технологии</strong>
          <span>Лицензионные соглашения на ПО, пользовательские договоры, SLA</span>
        </a>
        <a href="/yuridicheskiy-perevod/farmatsevtika/" class="industry-card">
          <strong>Фармацевтика</strong>
          <span>Лицензионные договоры, регуляторные соглашения, договоры GMP</span>
        </a>
        <a href="/yuridicheskiy-perevod/transport-logistika/" class="industry-card">
          <strong>Транспорт и логистика</strong>
          <span>Договоры перевозки, коносаменты, транспортные соглашения</span>
        </a>
        <a href="/yuridicheskiy-perevod/nedvizhimost/" class="industry-card">
          <strong>Недвижимость</strong>
          <span>Договоры купли-продажи, аренды, ипотечные соглашения</span>
        </a>
        <a href="/yuridicheskiy-perevod/media-is/" class="industry-card">
          <strong>Медиа и ИС</strong>
          <span>Авторские договоры, договоры на торговые марки, лицензии</span>
        </a>
      </div>
    </div>
  </section>

  <!-- ════════ ОТЗЫВЫ ════════ -->
  <section class="sec sec--alt sec-reviews">
    <div class="container">
      <div class="reviews-head">
        <div>
          <h2 class="sec-title">Что говорят клиенты</h2>
          <p class="sec-sub" style="margin-top:6px">Реальные отзывы о юридических переводах</p>
        </div>
        <div class="review-rating-block">
          <div class="review-rating-score">4.98</div>
          <div class="review-stars-row">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          </div>
          <div class="review-rating-label">средний рейтинг</div>
        </div>
        <div class="review-platform-links">
          <a href="https://yandex.ru/maps/org/remarka/51867347382/reviews/" class="review-platform-link" target="_blank" rel="noopener">Яндекс</a>
          <a href="https://maps.app.goo.gl/d8BKJYw81PqBHXvz7" class="review-platform-link" target="_blank" rel="noopener">Google</a>
        </div>
      </div>
      <div class="reviews-grid" style="grid-template-columns:repeat(3,1fr)">
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">В</div>
            <div class="review-meta"><div class="review-name">Владимир Смирнов</div><div class="review-src">Яндекс · ноябрь 2024</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Переводили рамочный договор поставки с английского для зарубежного партнёра. Юридические формулировки точные, терминология соответствует российскому праву. Партнёр замечаний не имел.</p>
          <div class="review-author-role">Юрисконсульт, «ГлобалТрейд»</div>
        </div>
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">А</div>
            <div class="review-meta"><div class="review-name">Анна Белова</div><div class="review-src">Google · сентябрь 2024</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Постоянно заказываю перевод судебных документов для клиентов с иностранным элементом. Бюро работает аккуратно, все ссылки на нормы права переданы корректно.</p>
          <div class="review-author-role">Адвокат</div>
        </div>
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">Д</div>
            <div class="review-meta"><div class="review-name">Дмитрий Коваль</div><div class="review-src">Яндекс · июнь 2024</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Потребовался срочный перевод устава на немецкий для регистрации дочерней компании. Успели за 1,5 дня, претензий при регистрации не возникло.</p>
          <div class="review-author-role">Директор, ООО «ИнвестПроект»</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ FAQ ════════ -->
  <section class="sec sec-faq">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Часто задаваемые вопросы</h2>
        <p class="sec-sub">Не нашли ответа — напишите Ольге в чат, ответим за минуту</p>
      </div>
      <div class="faq-list">

        <details class="faq-item">
          <summary class="faq-summary">Нужно ли нотариальное заверение юридического перевода?</summary>
          <div class="faq-body">
            <p>Зависит от назначения. Для подачи в российский госорган, суд или посольство, как правило, требуется нотариальное заверение подписи переводчика. Для деловых переговоров, ВЭД и корпоративного документооборота достаточно оговорки переводчика. Мы организуем нотариальное заверение по запросу — уточним требования бесплатно.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Что такое оговорка переводчика?</summary>
          <div class="faq-body">
            <p>Оговорка переводчика (certification statement / translator\'s certificate) — письменное удостоверение точности и полноты перевода, подписанное переводчиком с указанием его квалификации. Требуется при подаче документов в суд, нотариусу, в государственный орган, а также является обязательным элементом при последующем нотариальном заверении. Мы включаем оговорку по запросу без доплаты.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Вы переводите документы для международного арбитража?</summary>
          <div class="faq-body">
            <p>Да, работаем с материалами для МКАС, ICC, LCIA, SCC, VIAC и других арбитражных институтов. Переводчики знают процессуальные требования к оформлению документов — исковые заявления, ответы на иск, экспертные заключения, доказательства и решения. При работе с крупными делами возможна команда из нескольких специализированных переводчиков с единым глоссарием.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Как обеспечивается конфиденциальность при переводе сделок M&A?</summary>
          <div class="faq-body">
            <p>Каждый переводчик подписывает NDA до получения файлов. По запросу заключаем соглашение о конфиденциальности с компанией. Для сделок M&A используется закрытая Translation Memory — данные клиента не попадают в общие облачные базы и не используются для обучения ИИ. Документы хранятся только на защищённых серверах. Возможно подписание соглашения с расширенной ответственностью.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Переводите ли вы типовые договоры для постоянных клиентов?</summary>
          <div class="faq-body">
            <p>Да, сохраняем шаблоны и глоссарии клиента. При повторном заказе похожего документа скорость выше, а стоимость может быть ниже за счёт Translation Memory — повторяющиеся формулировки тарифицируются с дисконтом.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Как переводчик работает с коллизией правовых систем в тексте?</summary>
          <div class="faq-body">
            <p>Это ключевой навык юридического переводчика. Например, при переводе с английского договора по английскому праву на русский понятия «breach», «indemnity», «warranty» не имеют прямых эквивалентов в российском праве. Наш переводчик либо использует устоявшийся российский аналог с пояснением, либо транслитерирует термин с примечанием — в зависимости от требований клиента. Аналогично для GmbH/AG при переводе с немецкого.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Сколько стоит срочный юридический перевод?</summary>
          <div class="faq-body">
            <p>Срочность до 24 часов — коэффициент ×1,5. При объёме до 5 страниц и заказе утром — перевод готов к вечеру того же дня.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Где заказать юридический перевод в Москве быстро?</summary>
          <div class="faq-body">
            <p>Бюро переводов «Ремарка» принимает заявки онлайн круглосуточно. Юридический перевод в Москве быстро: напишите в чат — Ольга ответит в течение 5 минут и назначит переводчика.</p>
          </div>
        </details>

      </div>
    </div>
  </section>

  <!-- ════════ КАЛЬКУЛЯТОР СТОИМОСТИ ════════ -->',
        'tekhnicheskiy-perevod' => '<section class="sec sec-intro">
    <div class="container">
      <div class="intro-grid">
        <div class="intro-text">
          <p class="intro-label">Бюро переводов «Ремарка», Москва</p>
          <h1 class="intro-title">Технический перевод в Москве</h1>
          <p class="intro-tagline">Инженерная точность в каждом термине</p>
          <p class="intro-body">Технический текст не прощает приблизительности: за конструкторской документацией стоят реальные производственные решения, за патентным описанием — правовая защита изобретения, за инструкцией по безопасности — жизнь оператора. Именно поэтому к техническим текстам мы допускаем только переводчиков с профильным инженерным образованием.</p>
          <p class="intro-body">Технический перевод в московском бюро «Ремарка» всегда сопровождается работой с отраслевыми глоссариями. Единица измерения, аббревиатура стандарта, название детали — всё фиксируется в базе терминов клиента и точно воспроизводится при каждом последующем заказе.</p>
          <p class="intro-body">Заказать технический перевод документов у нас означает получить не просто лингвиста, а специалиста из вашей отрасли. Конструкторы переводят КД, химики — паспорта безопасности, энергетики — технические регламенты.</p>
        </div>
        <div class="intro-visual">
          <!-- Decorative illustration: translation flow -->
          <div class="intro-illus" aria-hidden="true">
            <svg viewBox="0 0 480 200" fill="none" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <linearGradient id="ip-g1" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#783CF0"/><stop offset="100%" stop-color="#00A0F0"/></linearGradient>
                <linearGradient id="ip-g2" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#00A0F0"/><stop offset="100%" stop-color="#a78bfa"/></linearGradient>
              </defs>
              <rect x="10" y="20" width="140" height="168" rx="12" fill="rgba(120,60,240,.10)" stroke="rgba(120,60,240,.35)" stroke-width="1.5"/>
              <rect x="10" y="20" width="140" height="38" rx="12" fill="rgba(120,60,240,.20)"/>
              <rect x="10" y="43" width="140" height="15" fill="rgba(120,60,240,.20)"/>
              <rect x="24" y="29" width="32" height="16" rx="8" fill="rgba(0,160,240,.40)"/>
              <rect x="28" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="42" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="24" y="76" width="112" height="5" rx="2.5" fill="rgba(167,139,250,.45)"/>
              <rect x="24" y="88" width="84" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="100" width="102" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="112" width="70" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="124" width="110" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="136" width="90" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="148" width="98" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="160" width="76" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="330" y="20" width="140" height="168" rx="12" fill="rgba(0,160,240,.10)" stroke="rgba(0,160,240,.35)" stroke-width="1.5"/>
              <rect x="330" y="20" width="140" height="38" rx="12" fill="rgba(0,160,240,.20)"/>
              <rect x="330" y="43" width="140" height="15" fill="rgba(0,160,240,.20)"/>
              <rect x="344" y="29" width="32" height="16" rx="8" fill="rgba(120,60,240,.40)"/>
              <rect x="348" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="362" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="344" y="76" width="112" height="5" rx="2.5" fill="rgba(0,160,240,.45)"/>
              <rect x="344" y="88" width="96" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="100" width="86" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="112" width="112" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="124" width="74" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="136" width="106" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="148" width="88" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="160" width="100" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <circle cx="240" cy="104" r="46" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
              <circle cx="240" cy="104" r="30" fill="rgba(120,60,240,.18)"/>
              <path d="M152 104h56M288 104h40" stroke="rgba(120,60,240,.40)" stroke-width="1.5" stroke-dasharray="4 3"/>
              <path d="M224 104h32M246 96l10 8-10 8" stroke="rgba(167,139,250,.95)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="intro-features">
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
              </div>
              <div><strong>Инженерное образование</strong><span>Переводчик знает предмет, а не только язык</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              </div>
              <div><strong>Терминологические базы</strong><span>Отраслевые глоссарии и базы терминов по каждой специализации</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
              </div>
              <div><strong>CAT-инструменты</strong><span>Translation Memory снижает стоимость повторного перевода</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
              </div>
              <div><strong>Сохранение структуры</strong><span>Таблицы, схемы и чертежи сохраняются в переведённом документе</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ЦИФРЫ ════════ -->
  <section class="sec-stats">
    <div class="container">
      <div class="stats-row">
        <div class="stat-item">
          <span class="stat-num">25<span class="stat-suffix">+</span></span>
          <span class="stat-label">лет на рынке</span>
        </div>
        <div class="stat-item">
          <span class="stat-num">60<span class="stat-suffix">+</span></span>
          <span class="stat-label">языков перевода</span>
        </div>
        <div class="stat-item">
          <span class="stat-num">2 400<span class="stat-suffix">+</span></span>
          <span class="stat-label">выполненных заказов</span>
        </div>
        <div class="stat-item">
          <span class="stat-num">4.98<span class="stat-suffix">★</span></span>
          <span class="stat-label">средний рейтинг</span>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ФОРМАТЫ И ЯЗЫКИ ════════ -->
  <section class="sec sec-formats-langs">
    <div class="container">
      <div class="fl-grid">
        <div class="fl-col">
          <h3 class="fl-title">Что можно прислать</h3>
          <div class="fl-items">
            <div class="fl-item"><span class="fl-tag">PDF</span><span class="fl-desc">сканированный или текстовый</span></div>
            <div class="fl-item"><span class="fl-tag">DOCX / DOC</span><span class="fl-desc">Word-документы, шаблоны</span></div>
            <div class="fl-item"><span class="fl-tag">XLS / XLSX</span><span class="fl-desc">таблицы BOM, спецификации</span></div>
            <div class="fl-item"><span class="fl-tag">DWG / DXF</span><span class="fl-desc">чертежи AutoCAD → пришлите PDF-версию или файл</span></div>
            <div class="fl-item"><span class="fl-tag">JPG / PNG</span><span class="fl-desc">фото документов, шильдики</span></div>
            <div class="fl-item"><span class="fl-tag">PPT / PPTX</span><span class="fl-desc">презентации, технические слайды</span></div>
          </div>
          <div class="fl-note">Не нашли свой формат? Напишите в чат — разберёмся.</div>
        </div>
        <div class="fl-divider"></div>
        <div class="fl-col">
          <h3 class="fl-title">Что получаете в ответ</h3>
          <div class="fl-items">
            <div class="fl-item"><span class="fl-tag">DOCX</span><span class="fl-desc">редактируемый Word с сохранением структуры</span></div>
            <div class="fl-item"><span class="fl-tag">XLSX</span><span class="fl-desc">таблица с переведёнными ячейками</span></div>
            <div class="fl-item"><span class="fl-tag">PDF</span><span class="fl-desc">финальная версия с подписью переводчика</span></div>
            <div class="fl-item"><span class="fl-tag">Глоссарий</span><span class="fl-desc">терминологическая база — остаётся у вас</span></div>
          </div>
          <div class="fl-note">Оригинальную вёрстку FrameMaker / InDesign сохраняем по отдельному запросу.</div>
        </div>
        <div class="fl-divider"></div>
        <div class="fl-col">
          <h3 class="fl-title">Языковые пары</h3>
          <div class="fl-langs">
            <span class="fl-lang-pair">EN ↔ RU</span>
            <span class="fl-lang-pair">DE ↔ RU</span>
            <span class="fl-lang-pair">ZH ↔ RU</span>
            <span class="fl-lang-pair">FR ↔ RU</span>
            <span class="fl-lang-pair">IT ↔ RU</span>
            <span class="fl-lang-pair">JA ↔ RU</span>
            <span class="fl-lang-pair">KO ↔ RU</span>
            <span class="fl-lang-pair">ES ↔ RU</span>
            <span class="fl-lang-pair">PL ↔ RU</span>
            <span class="fl-lang-pair">CS ↔ RU</span>
            <span class="fl-lang-pair">NL ↔ RU</span>
            <span class="fl-lang-pair">+50 языков</span>
          </div>
          <div class="fl-note">Немецкое и китайское оборудование — основной поток. Японские и корейские инструкции — в наличии специалисты.</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ КАКИЕ ДОКУМЕНТЫ ════════ -->
  <section class="sec sec--alt sec-docs">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Какие документы мы переводим</h2>
        <p class="sec-sub">Раскройте нужную категорию — посмотрите конкретные виды документов</p>
      </div>
      <div class="docs-accordion">

        <details class="doc-item" open>
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            <span>Конструкторская документация (КД)</span>
          </summary>
          <div class="doc-body">
            <p>Технические описания, спецификации, чертежи, 3D-модели, сборочные чертежи.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
            <span>Руководства по эксплуатации</span>
          </summary>
          <div class="doc-body">
            <p>Инструкции к оборудованию, станкам, приборам, системам управления.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <span>Патентная документация</span>
          </summary>
          <div class="doc-body">
            <p>Заявки РОСПАТЕНТ/ВОИС, описания изобретений, формулы, реферативные части.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            <span>Стандарты и регламенты</span>
          </summary>
          <div class="doc-body">
            <p>ГОСТы, ISO, IEC, технические регламенты ТС/ЕАЭС, нормы безопасности.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            <span>Техническая переписка</span>
          </summary>
          <div class="doc-body">
            <p>Технические задания, коммерческие предложения, спецификации на оборудование.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
            <span>Промышленная безопасность</span>
          </summary>
          <div class="doc-body">
            <p>Декларации ПБ, паспорта безопасности материалов (SDS/MSDS), отчёты.</p>
          </div>
        </details>

      </div>
    </div>
  </section>

  <!-- ════════ НАШИ ПЕРЕВОДЧИКИ ════════ -->
  <section class="sec sec-translators">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Переводчики-инженеры в Москве</h2>
        <p class="sec-sub">Технические переводчики московского бюро «Ремарка» не берутся за чужие специализации — каждый работает только в своей отрасли</p>
      </div>
      <div class="translators-grid">
        <div class="translator-card">
          <div class="translator-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
          </div>
          <h3>Инженерный + лингвистический диплом</h3>
          <p>Каждый переводчик имеет оба диплома: по лингвистике и по профильной инженерной специальности.</p>
        </div>
        <div class="translator-card">
          <div class="translator-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><path d="M21 12c0 4.97-4.03 9-9 9S3 16.97 3 12 7.03 3 12 3s9 4.03 9 9z"/></svg>
          </div>
          <h3>Опыт в промышленности от 5 лет</h3>
          <p>Знают реальные производственные процессы, а не только терминологию из учебников.</p>
        </div>
        <div class="translator-card">
          <div class="translator-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
          </div>
          <h3>SDL Trados и memoQ</h3>
          <p>Работают в CAT-системах — единая терминология в рамках всего проекта клиента.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ КОНТРОЛЬ КАЧЕСТВА — SPLIT SECTION ════════ -->
  <section class="sec sec-split">
    <div class="container">
      <div class="split-row split-row--rev">
        <div class="split-text">
          <p class="sec-label">Инженерная точность</p>
          <h2>Как мы обеспечиваем точность технического перевода</h2>
          <p>Технический перевод требует не только знания языка, но и понимания инженерной логики документа. Наш переводчик-инженер работает с КД, ГОСТами и ISO-стандартами так же, как с ними работает проектировщик.</p>
          <p>Терминологическая база формируется для каждого клиента: единые глоссарии позволяют сохранять консистентность терминов на протяжении всего проекта и при повторных заказах.</p>
          <ul class="split-checklist">
            <li>Переводчик с профильным инженерным образованием</li>
            <li>Собственные глоссарии по машиностроению, электронике, химии</li>
            <li>Работа в CAT-системах для единства терминологии</li>
            <li>Редакторская проверка технической точности перед сдачей</li>
          </ul>
        </div>
        <div class="split-visual" aria-hidden="true">
          <svg viewBox="0 0 380 320" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <linearGradient id="sq-g1-t" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#783CF0"/><stop offset="100%" stop-color="#00A0F0"/></linearGradient>
              <linearGradient id="sq-g2-t" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#783CF0" stop-opacity=".5"/><stop offset="100%" stop-color="#00A0F0" stop-opacity=".5"/></linearGradient>
            </defs>
            <circle cx="190" cy="160" r="130" fill="rgba(120,60,240,.07)"/>
            <rect x="60" y="30" width="260" height="64" rx="16" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="62" r="20" fill="url(#sq-g1-t)" opacity=".85"/>
            <path d="M88 62l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="50" width="170" height="9" rx="4" fill="rgba(167,139,250,.55)"/>
            <rect x="126" y="66" width="120" height="7" rx="3" fill="rgba(167,139,250,.30)"/>
            <line x1="190" y1="94" x2="190" y2="118" stroke="url(#sq-g2-t)" stroke-width="2" stroke-dasharray="4 3"/>
            <rect x="60" y="118" width="260" height="64" rx="16" fill="rgba(0,160,240,.12)" stroke="rgba(0,160,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="150" r="20" fill="url(#sq-g1-t)" opacity=".85"/>
            <path d="M88 150l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="138" width="155" height="9" rx="4" fill="rgba(0,160,240,.55)"/>
            <rect x="126" y="154" width="110" height="7" rx="3" fill="rgba(0,160,240,.30)"/>
            <line x1="190" y1="182" x2="190" y2="206" stroke="url(#sq-g2-t)" stroke-width="2" stroke-dasharray="4 3"/>
            <rect x="60" y="206" width="260" height="64" rx="16" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="238" r="20" fill="url(#sq-g1-t)" opacity=".85"/>
            <path d="M88 238l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="226" width="175" height="9" rx="4" fill="rgba(167,139,250,.55)"/>
            <rect x="126" y="242" width="130" height="7" rx="3" fill="rgba(167,139,250,.30)"/>
            <circle cx="310" cy="290" r="26" fill="rgba(0,200,100,.12)" stroke="rgba(0,200,100,.35)" stroke-width="1.5"/>
            <path d="M300 290l8 8 14-16" stroke="rgba(0,200,100,.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ КАК ПРОХОДИТ РАБОТА (6 шагов) ════════ -->
  <section class="sec sec--alt sec-how-pro">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Как проходит работа</h2>
        <p class="sec-sub">Шесть шагов от файла до готового перевода — с контролем качества на каждом этапе</p>
      </div>
      <div class="steps-row steps-row--6">
        <div class="step-item">
          <div class="step-num">01</div>
          <h3>Загрузите файл</h3>
          <p>Через сайт, мессенджер, email или принесите в офис</p>
        </div>
        <div class="step-item">
          <div class="step-num">02</div>
          <h3>Анализ документа</h3>
          <p>Оцениваем объём, тематику и сложность — за несколько минут</p>
        </div>
        <div class="step-item">
          <div class="step-num">03</div>
          <h3>Подбор специалиста</h3>
          <p>Выбираем переводчика с профильным образованием в вашей области</p>
        </div>
        <div class="step-item">
          <div class="step-num">04</div>
          <h3>Перевод</h3>
          <p>Специалист работает с соблюдением терминологии и стиля оригинала</p>
        </div>
        <div class="step-item">
          <div class="step-num">05</div>
          <h3>Корректура</h3>
          <p>Редактор проверяет точность, стиль и соответствие заданию</p>
        </div>
        <div class="step-item">
          <div class="step-num">06</div>
          <h3>Готовый перевод</h3>
          <p>Отправляем файл или вы забираете из офиса — точно в срок</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ГАРАНТИИ КАЧЕСТВА ════════ -->
  <section class="sec sec-guarantees">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Наши гарантии</h2>
        <p class="sec-sub">Мы отвечаем за результат — не только словами</p>
      </div>
      <div class="guarantees-grid">
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.9"/></svg>
          </div>
          <h3>Бесплатная правка</h3>
          <p>Исправим бесплатно в течение 7 дней, если перевод не соответствует заданию или содержит неточности</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </div>
          <h3>Конфиденциальность</h3>
          <p>NDA по запросу. Ваши документы не передаются третьим лицам и не хранятся на внешних серверах</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </div>
          <h3>Соблюдение сроков</h3>
          <p>Если не уложились в оговорённый срок по нашей вине — возвращаем предоплату или делаем скидку на следующий заказ</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
          </div>
          <h3>База терминологии</h3>
          <p>Сохраняем глоссарий вашей компании — при повторных заказах терминология остаётся единой, перевод быстрее</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ СРОКИ И СТОИМОСТЬ ════════ -->
  <section class="sec sec--alt sec-vol-pricing">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Сроки и стоимость</h2>
        <p class="sec-sub">Цена зависит от объёма. Точный расчёт — бесплатно, за несколько минут</p>
      </div>
      <div class="vol-table-wrap">
        <table class="vol-table">
          <thead>
            <tr>
              <th>Объём</th>
              <th>Срок</th>
              <th>Стоимость</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>До 5 страниц</strong></td>
              <td>1 рабочий день</td>
              <td class="vol-price">от 3 000 ₽</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>5–20 страниц</strong></td>
              <td>2–3 рабочих дня</td>
              <td class="vol-price">от 400 ₽/стр.</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>20–50 страниц</strong></td>
              <td>3–5 рабочих дней</td>
              <td class="vol-price">от 380 ₽/стр.</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>Свыше 50 страниц</strong></td>
              <td>по договорённости</td>
              <td class="vol-price">от 350 ₽/стр.</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
          </tbody>
        </table>
      </div>
      <p class="vol-note">Срочный технический перевод — коэффициент ×1,5. Расчёт после анализа файла.</p>
    </div>
  </section>

  <!-- ════════ СРАВНЕНИЕ ФОРМАТОВ ════════ -->
  <section class="sec sec-comparison">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Какой формат вам подходит</h2>
        <p class="sec-sub">Сравните три уровня сервиса и выберите оптимальный для вашей задачи</p>
      </div>
      <div class="cmp-table-wrap">
        <table class="cmp-table">
          <thead>
            <tr>
              <th></th>
              <th>Постредактирование ИИ</th>
              <th class="cmp-featured">Профессиональный перевод</th>
              <th>Премиум-перевод</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>Перевод с нуля специалистом</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Профильное образование переводчика</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Корректура и вычитка</td><td class="cmp-part">частичная</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Редактура профильным экспертом</td><td class="cmp-no">✗</td><td class="cmp-no cmp-featured">✗</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Вычитка носителем языка</td><td class="cmp-no">✗</td><td class="cmp-no cmp-featured">✗</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Подходит для публикаций и тендеров</td><td class="cmp-no">✗</td><td class="cmp-part cmp-featured">условно</td><td class="cmp-yes">✓</td></tr>
            <tr class="cmp-meta"><td>Срок</td><td>от 1 дня</td><td class="cmp-featured">1–3 дня</td><td>2–5 дней</td></tr>
            <tr class="cmp-meta cmp-price-row"><td>Стоимость</td><td>от 250 ₽/стр.</td><td class="cmp-featured">от 500 ₽/стр.</td><td>от 800 ₽/стр.</td></tr>
          </tbody>
          <tfoot>
            <tr>
              <td></td>
              <td><a href="/stoimost-perevoda/" class="price-btn price-btn--outline">Выбрать</a></td>
              <td class="cmp-featured"><a href="/stoimost-perevoda/" class="price-btn price-btn--primary">Выбрать</a></td>
              <td><a href="/stoimost-perevoda/" class="price-btn price-btn--outline">Выбрать</a></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </section>

  <!-- ════════ СПРАВОЧНИК ПО ДОКУМЕНТАМ ════════ -->
  <section class="sec sec--alt sec-doc-reference">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Справочник по документам</h2>
        <p class="sec-sub">Выберите тип документа — узнайте особенности перевода</p>
      </div>
      <div class="doc-ref-grid">

        <a href="/tekhnicheskiy-perevod/instrukcii-po-ekspluatatsii/" class="doc-ref-card">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
          <strong>Перевод инструкций</strong>
          <span>Технические инструкции к оборудованию, приборам и производственным линиям</span>
        </a>

        <a href="/tekhnicheskiy-perevod/konstruktorskaya-dokumentatsiya/" class="doc-ref-card">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
          <strong>Конструкторская документация</strong>
          <span>КД, ЕСКД, сборочные чертежи, спецификации, BOM</span>
        </a>

        <a href="/tekhnicheskiy-perevod/chertezhi-skhemy-autocad/" class="doc-ref-card">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 9h6M9 12h6M9 15h4"/></svg>
          <strong>Перевод чертежей AutoCAD</strong>
          <span>Чертежи, схемы, аксонометрии с сохранением условных обозначений</span>
        </a>

        <a href="/tekhnicheskiy-perevod/tendernaya-dokumentatsiya/" class="doc-ref-card">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
          <strong>Технические спецификации</strong>
          <span>Спецификации к проектам, поставкам и тендерным документам</span>
        </a>

        <a href="/tekhnicheskiy-perevod/sds-msds/" class="doc-ref-card">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          <strong>Паспорта безопасности (SDS/MSDS)</strong>
          <span>Листы данных по безопасности химических веществ</span>
        </a>

        <a href="/tekhnicheskiy-perevod/instruktsii-montazha/" class="doc-ref-card">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          <strong>Инструкции по монтажу</strong>
          <span>Монтажные инструкции, схемы подключения, commissioning guide</span>
        </a>

        <a href="/tekhnicheskiy-perevod/rukovodstva-polzovatelya/" class="doc-ref-card">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
          <strong>Руководства по эксплуатации</strong>
          <span>Пошаговые инструкции для операторов и обслуживающего персонала</span>
        </a>

        <a href="/tekhnicheskiy-perevod/avarijno-eho-procedury/" class="doc-ref-card">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
          <strong>Руководства по безопасности</strong>
          <span>Нормативные требования, инструктажи, правила охраны труда</span>
        </a>

        <a href="/tekhnicheskiy-perevod/tekhnicheskie-pasporta/" class="doc-ref-card">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
          <strong>Технические паспорта</strong>
          <span>Data sheet, machine passport, паспорта материалов и оборудования</span>
        </a>

        <a href="/tekhnicheskiy-perevod/nauchnye-otchety/" class="doc-ref-card">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <strong>Технические отчёты</strong>
          <span>Feasibility study, engineering report, FEED, HAZOP-анализ</span>
        </a>

        <a href="/tekhnicheskiy-perevod/katalogi-zapchastej/" class="doc-ref-card">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
          <strong>Перевод технических каталогов</strong>
          <span>Каталоги оборудования, запчастей, материалов</span>
        </a>

        <a href="/tekhnicheskiy-perevod/protokoly-ispytanij/" class="doc-ref-card">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
          <strong>Протоколы испытаний</strong>
          <span>FAT/SAT, test reports, commissioning, протоколы НК</span>
        </a>

      </div>
    </div>
  </section>

  <!-- ════════ ПЕРЕВОДЫ ПО ОТРАСЛЯМ ════════ -->
  <section class="sec sec-industries">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Переводы по отраслям</h2>
        <p class="sec-sub">Специфика технического перевода в разных производственных сферах</p>
      </div>
      <div class="industries-grid">

        <a href="/tekhnicheskiy-perevod/mashinostroenie/" class="industry-card">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
          <h3>Машиностроение</h3>
          <p>КД, спецификации, технологические карты, инструкции по сборке.</p>
          <ul class="industry-docs">
            <li>Конструкторская документация</li>
            <li>Технологические регламенты</li>
            <li>Руководства по ремонту</li>
          </ul>
        </a>

        <a href="/tekhnicheskiy-perevod/aviastroenie/" class="industry-card">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
          <h3>Авиастроение</h3>
          <p>Авиационные регламенты, технические бюллетени, руководства AMM/CMM.</p>
          <ul class="industry-docs">
            <li>Aircraft Maintenance Manual (AMM)</li>
            <li>Технические бюллетени</li>
            <li>Авиационные регламенты</li>
          </ul>
        </a>

        <a href="/tekhnicheskiy-perevod/neft-gaz/" class="industry-card">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
          <h3>Нефтегаз</h3>
          <p>Технологические регламенты, SDS, контракты на бурение, стандарты API.</p>
          <ul class="industry-docs">
            <li>Паспорта безопасности SDS/MSDS</li>
            <li>Стандарты API и ISO</li>
            <li>Проектная документация скважин</li>
          </ul>
        </a>

        <a href="/tekhnicheskiy-perevod/energetika/" class="industry-card">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
          <h3>Энергетика</h3>
          <p>Технические регламенты электростанций, инструкции к турбинам, ПТЭ.</p>
          <ul class="industry-docs">
            <li>Правила технической эксплуатации (ПТЭ)</li>
            <li>Инструкции к турбогенераторам</li>
            <li>Технические регламенты</li>
          </ul>
        </a>

        <a href="/tekhnicheskiy-perevod/khimicheskaya-promyshlennost/" class="industry-card">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          <h3>Химическая промышленность</h3>
          <p>Паспорта безопасности, технологические инструкции, регламенты.</p>
          <ul class="industry-docs">
            <li>Паспорта безопасности (SDS)</li>
            <li>Технологические регламенты</li>
            <li>Декларации промышленной безопасности</li>
          </ul>
        </a>

        <a href="/tekhnicheskiy-perevod/metallurgiya/" class="industry-card">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
          <h3>Металлургия</h3>
          <p>Технологические инструкции, стандарты ГОСТ, ТУ на металлопродукцию.</p>
          <ul class="industry-docs">
            <li>Технические условия (ТУ)</li>
            <li>Стандарты ГОСТ на металлы</li>
            <li>Технологические карты</li>
          </ul>
        </a>

        <a href="/tekhnicheskiy-perevod/sudostroenie/" class="industry-card">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 20h20M4 20V10l8-6 8 6v10"/><path d="M10 20v-6h4v6"/></svg>
          <h3>Судостроение</h3>
          <p>Конструкторская документация, регламенты классификационных обществ.</p>
          <ul class="industry-docs">
            <li>Правила классификационных обществ</li>
            <li>Конструкторская документация судов</li>
            <li>Судовые инструкции</li>
          </ul>
        </a>

        <a href="/tekhnicheskiy-perevod/avtomobilnaya-promyshlennost/" class="industry-card">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
          <h3>Автомобилестроение</h3>
          <p>КД, руководства по ремонту, технические бюллетени OEM.</p>
          <ul class="industry-docs">
            <li>Технические бюллетени OEM</li>
            <li>Workshop Manual</li>
            <li>Спецификации на комплектующие</li>
          </ul>
        </a>

        <a href="/tekhnicheskiy-perevod/elektronika-priborostroenie/" class="industry-card">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
          <h3>Компьютерное оборудование</h3>
          <p>Технические описания, руководства, спецификации серверов.</p>
          <ul class="industry-docs">
            <li>Технические описания серверов</li>
            <li>Руководства по администрированию</li>
            <li>Спецификации оборудования</li>
          </ul>
        </a>

      </div>
    </div>
  </section>

  <!-- ════════ ОТЗЫВЫ ════════ -->
  <section class="sec sec--alt sec-reviews">
    <div class="container">
      <div class="reviews-head">
        <div>
          <h2 class="sec-title">Что говорят клиенты</h2>
          <p class="sec-sub" style="margin-top:6px">Реальные отзывы о технических переводах</p>
        </div>
        <div class="review-rating-block">
          <div class="review-rating-score">4.98</div>
          <div class="review-stars-row">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          </div>
          <div class="review-rating-label">средний рейтинг</div>
        </div>
        <div class="review-platform-links">
          <a href="https://yandex.ru/maps/org/remarka/51867347382/reviews/" class="review-platform-link" target="_blank" rel="noopener">Яндекс</a>
          <a href="https://maps.app.goo.gl/d8BKJYw81PqBHXvz7" class="review-platform-link" target="_blank" rel="noopener">Google</a>
          <a href="https://2gis.ru/moscow/search/%D0%B1%D1%8E%D1%80%D0%BE%20%D0%BF%D0%B5%D1%80%D0%B5%D0%B2%D0%BE%D0%B4%D0%BE%D0%B2%20%D1%80%D0%B5%D0%BC%D0%B0%D1%80%D0%BA%D0%B0" class="review-platform-link" target="_blank" rel="noopener">2ГИС</a>
        </div>
      </div>
      <div class="reviews-grid" style="grid-template-columns:repeat(3,1fr)">
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">А</div>
            <div class="review-meta"><div class="review-name">Алексей Воронов</div><div class="review-src">Яндекс · октябрь 2024</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Заказывали технический перевод конструкторской документации с немецкого — около 90 страниц по промышленным насосам. Терминология выверена, сроки соблюдены. Глоссарий сохранили в базе, следующий заказ прошёл заметно быстрее.</p>
          <div class="review-role">Главный инженер, ООО «МеханоСервис»</div>
        </div>
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">И</div>
            <div class="review-meta"><div class="review-name">Игорь Петренко</div><div class="review-src">Google · август 2024</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Переводили паспорта безопасности материалов с английского для нашего производства. Все SDS оформлены по российскому стандарту, химическая терминология без нареканий. Рекомендую.</p>
          <div class="review-role">Специалист по ОТ, АО «ХимПром»</div>
        </div>
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">Н</div>
            <div class="review-meta"><div class="review-name">Наталья Сорокина</div><div class="review-src">2ГИС · май 2024</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Несколько патентных заявок с японского. Сложная специализация, но справились хорошо. Особо отмечу точность передачи формулы изобретения.</p>
          <div class="review-role">Патентный поверенный</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ FAQ ════════ -->
  <section class="sec sec-faq">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Часто задаваемые вопросы</h2>
        <p class="sec-sub">Не нашли ответа — напишите Ольге в чат, ответим за минуту</p>
      </div>
      <div class="faq-list">

        <details class="faq-item">
          <summary class="faq-summary">Что входит в технический перевод?</summary>
          <div class="faq-body">
            <p>Перевод любых технических материалов: КД, руководств, патентов, стандартов, спецификаций. Обязательно: исполнитель с профильным образованием, работа с терминологической базой, двухэтапная проверка.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Как вы обеспечиваете единство терминологии в большом проекте?</summary>
          <div class="faq-body">
            <p>Создаём глоссарий на этапе первого заказа, фиксируем все ключевые термины клиента. При последующих заказах переводчик и редактор работают с той же базой — расхождений в терминологии нет.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Переводите ли вы файлы AutoCAD, SOLIDWORKS и другие CAD-форматы?</summary>
          <div class="faq-body">
            <p>Текстовые элементы чертежей переводим: подписи, примечания, технические требования. Редактирование самих CAD-файлов — по отдельному согласованию. Чаще работаем с PDF или Word-версиями.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Сколько стоит срочный технический перевод?</summary>
          <div class="faq-body">
            <p>Срочность до 24 часов — коэффициент ×1,5 к базовой цене. При объёме до 5 страниц и заказе до полудня перевод готов в тот же день без надбавки.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Нужен ли нотариус для технической документации?</summary>
          <div class="faq-body">
            <p>Как правило, нет. Нотариальное заверение требуется для документов, предъявляемых в государственные органы, суды или посольства. Большинство технических документов достаточно перевести с подписью переводчика.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Где заказать технический перевод в Москве срочно?</summary>
          <div class="faq-body">
            <p>Бюро переводов «Ремарка» принимает заявки онлайн круглосуточно. Технический перевод в Москве срочно: до 5 страниц — за 1 рабочий день. Отправьте файл в чат — ответим за 5 минут.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Что если у меня немецкое/китайское/японское оборудование?</summary>
          <div class="faq-body">
            <p>Это наш основной поток. По немецкому оборудованию (Siemens, TRUMPF, Kuka, Bosch Rexroth) — постоянные переводчики с машиностроительным образованием. По китайской технике (Haas, XCMG, Midea Industrial) — переводчики с опытом именно в технической документации КНР. По японскому инструменту — уточняйте в чате.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Сохраняется ли структура таблиц и нумерация в BOM при переводе Excel?</summary>
          <div class="faq-body">
            <p>Да. При переводе BOM (Bill of Materials) в Excel переводим только содержимое текстовых ячеек: наименования, описания, примечания. Формулы, номера позиций, артикулы и числовые данные остаются нетронутыми. Клиент получает файл в том же формате, что и прислал.</p>
          </div>
        </details>

      </div>
    </div>
  </section>

  <!-- ════════ КАЛЬКУЛЯТОР СТОИМОСТИ ════════ -->',
        'meditsinskiy-perevod' => '<section class="sec sec-intro">
    <div class="container">
      <div class="intro-grid">
        <div class="intro-text">
          <p class="intro-label">Бюро переводов «Ремарка», Москва</p>
          <h1 class="intro-title">Медицинский перевод в Москве</h1>
          <p class="intro-tagline">Точность, от которой зависит здоровье</p>
          <p class="intro-body">Ошибка в медицинском переводе — не стилистический недостаток. Неправильная дозировка в инструкции к препарату, перепутанное название диагноза в медкарте или неточная передача протокола клинического исследования может иметь последствия, несопоставимые со стоимостью самого перевода. Именно поэтому к медицинским текстам допускаем только специалистов с профильным образованием.</p>
          <p class="intro-body">Медицинский перевод в московском бюро «Ремарка» выполняют врачи, провизоры и медицинские биологи, прошедшие лингвистическую подготовку. Они знают МКБ-10 и МКБ-11, понимают структуру клинического исследования, умеют работать с латинскими наименованиями препаратов.</p>
          <p class="intro-body">Заказать медицинский перевод документов у нас означает, что ваш текст не попадёт к лингвисту, который ищет термины в словаре. Каждый заказ передаётся специалисту с практическим опытом в нужной медицинской области.</p>
        </div>
        <div class="intro-visual">
          <div class="intro-illus" aria-hidden="true">
            <svg viewBox="0 0 480 200" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect x="10" y="20" width="140" height="168" rx="12" fill="rgba(120,60,240,.10)" stroke="rgba(120,60,240,.35)" stroke-width="1.5"/>
              <rect x="10" y="20" width="140" height="38" rx="12" fill="rgba(120,60,240,.20)"/>
              <rect x="10" y="43" width="140" height="15" fill="rgba(120,60,240,.20)"/>
              <rect x="24" y="29" width="32" height="16" rx="8" fill="rgba(0,160,240,.40)"/>
              <rect x="28" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="42" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="24" y="76" width="112" height="5" rx="2.5" fill="rgba(167,139,250,.45)"/>
              <rect x="24" y="88" width="84" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="100" width="102" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="112" width="70" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="124" width="110" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="136" width="90" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="148" width="98" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="160" width="76" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="330" y="20" width="140" height="168" rx="12" fill="rgba(0,160,240,.10)" stroke="rgba(0,160,240,.35)" stroke-width="1.5"/>
              <rect x="330" y="20" width="140" height="38" rx="12" fill="rgba(0,160,240,.20)"/>
              <rect x="330" y="43" width="140" height="15" fill="rgba(0,160,240,.20)"/>
              <rect x="344" y="29" width="32" height="16" rx="8" fill="rgba(120,60,240,.40)"/>
              <rect x="348" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="362" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="344" y="76" width="112" height="5" rx="2.5" fill="rgba(0,160,240,.45)"/>
              <rect x="344" y="88" width="96" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="100" width="86" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="112" width="112" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="124" width="74" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="136" width="106" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="148" width="88" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="160" width="100" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <circle cx="240" cy="104" r="46" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
              <circle cx="240" cy="104" r="30" fill="rgba(120,60,240,.18)"/>
              <path d="M152 104h56M288 104h40" stroke="rgba(120,60,240,.40)" stroke-width="1.5" stroke-dasharray="4 3"/>
              <path d="M224 104h32M246 96l10 8-10 8" stroke="rgba(167,139,250,.95)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="intro-features">
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
              </div>
              <div><strong>Медицинское образование</strong><span>Врачи, провизоры, биохимики — не просто лингвисты</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
              </div>
              <div><strong>МКБ-10/11 и ICD</strong><span>Правильная трактовка диагностических кодов и классификаций</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
              </div>
              <div><strong>GCP и GMP</strong><span>Соответствие требованиям к клиническим исследованиям</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
              </div>
              <div><strong>Двойная проверка</strong><span>Медицинский редактор контролирует каждый текст после перевода</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

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

  <!-- ════════ КАКИЕ ДОКУМЕНТЫ ════════ -->
  <section class="sec sec--alt sec-docs">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Какие документы мы переводим</h2>
        <p class="sec-sub">Раскройте нужную категорию — посмотрите конкретные виды документов</p>
      </div>
      <div class="docs-accordion">

        <details class="doc-item" open>
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            <span>Медицинские карты и выписки</span>
          </summary>
          <div class="doc-body">
            <p>Истории болезни, выписные эпикризы, справки МСЭ, амбулаторные карты.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            <span>Инструкции к препаратам</span>
          </summary>
          <div class="doc-body">
            <p>Листки-вкладыши, SmPC, регуляторные досье для регистрации лекарств.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
            <span>Клинические исследования</span>
          </summary>
          <div class="doc-body">
            <p>Протоколы, отчёты CSR, информированные согласия ICF, PSUR.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
            <span>Медицинские справки и заключения</span>
          </summary>
          <div class="doc-body">
            <p>Заключения специалистов для иностранных клиник и страховых компаний.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
            <span>Медицинское оборудование</span>
          </summary>
          <div class="doc-body">
            <p>Технические инструкции IFU, паспорта приборов, руководства операторов.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            <span>Фармацевтическая документация</span>
          </summary>
          <div class="doc-body">
            <p>Регистрационные досье NDA, ANDA, фармакопейные статьи.</p>
          </div>
        </details>

      </div>
    </div>
  </section>

  <!-- ════════ НАШИ ПЕРЕВОДЧИКИ ════════ -->
  <section class="sec sec-translators">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Медицинские переводчики в Москве</h2>
        <p class="sec-sub">Медицинские переводчики московского бюро «Ремарка» — это врачи и фармацевты, а не лингвисты с медицинским словарём</p>
      </div>
      <div class="translators-grid">
        <div class="translator-card">
          <div class="translator-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
          </div>
          <h3>Медицинский диплом</h3>
          <p>Врачи общей практики, провизоры, медицинские биологи с лингвистической подготовкой.</p>
        </div>
        <div class="translator-card">
          <div class="translator-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
          </div>
          <h3>Опыт в GCP/GMP</h3>
          <p>Работают с клиническими исследованиями и фармацевтической документацией.</p>
        </div>
        <div class="translator-card">
          <div class="translator-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </div>
          <h3>GDPR и конфиденциальность</h3>
          <p>Персональные медицинские данные под строгим NDA.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="sec sec-split">
    <div class="container">
      <div class="split-row split-row--rev">
        <div class="split-text">
          <p class="sec-label">Медицинская точность</p>
          <h2>Двойной контроль в медицинском переводе</h2>
          <p>Ошибка в медицинском тексте — это не стилистический недочёт. Неправильно переведённая дозировка, ошибочный диагноз или неверно указанный препарат могут иметь реальные последствия для здоровья пациента.</p>
          <p>Именно поэтому мы применяем двухэтапную проверку: переводчик с медицинским или фармацевтическим образованием выполняет перевод, затем медицинский редактор проверяет точность каждого термина и соответствие МКБ-классификаторам.</p>
          <ul class="split-checklist">
            <li>Переводчик с медицинским или фармацевтическим образованием</li>
            <li>Независимый медицинский редактор на каждом проекте</li>
            <li>Соответствие МКБ-10/11, ICD и требованиям GCP/GMP</li>
            <li>Полная конфиденциальность медицинских данных (NDA)</li>
          </ul>
        </div>
        <div class="split-visual" aria-hidden="true">
          <svg viewBox="0 0 380 320" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <linearGradient id="sq-g1-m" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#783CF0"/><stop offset="100%" stop-color="#00A0F0"/></linearGradient>
              <linearGradient id="sq-g2-m" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#783CF0" stop-opacity=".5"/><stop offset="100%" stop-color="#00A0F0" stop-opacity=".5"/></linearGradient>
            </defs>
            <circle cx="190" cy="160" r="130" fill="rgba(120,60,240,.07)"/>
            <rect x="60" y="30" width="260" height="64" rx="16" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="62" r="20" fill="url(#sq-g1-m)" opacity=".85"/>
            <path d="M88 62l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="50" width="170" height="9" rx="4" fill="rgba(167,139,250,.55)"/>
            <rect x="126" y="66" width="120" height="7" rx="3" fill="rgba(167,139,250,.30)"/>
            <line x1="190" y1="94" x2="190" y2="118" stroke="url(#sq-g2-m)" stroke-width="2" stroke-dasharray="4 3"/>
            <rect x="60" y="118" width="260" height="64" rx="16" fill="rgba(0,160,240,.12)" stroke="rgba(0,160,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="150" r="20" fill="url(#sq-g1-m)" opacity=".85"/>
            <path d="M88 150l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="138" width="155" height="9" rx="4" fill="rgba(0,160,240,.55)"/>
            <rect x="126" y="154" width="110" height="7" rx="3" fill="rgba(0,160,240,.30)"/>
            <line x1="190" y1="182" x2="190" y2="206" stroke="url(#sq-g2-m)" stroke-width="2" stroke-dasharray="4 3"/>
            <rect x="60" y="206" width="260" height="64" rx="16" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="238" r="20" fill="url(#sq-g1-m)" opacity=".85"/>
            <path d="M88 238l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="226" width="175" height="9" rx="4" fill="rgba(167,139,250,.55)"/>
            <rect x="126" y="242" width="130" height="7" rx="3" fill="rgba(167,139,250,.30)"/>
            <circle cx="310" cy="290" r="26" fill="rgba(0,200,100,.12)" stroke="rgba(0,200,100,.35)" stroke-width="1.5"/>
            <path d="M300 290l8 8 14-16" stroke="rgba(0,200,100,.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ КАК ПРОХОДИТ РАБОТА (6 шагов) ════════ -->
  <section class="sec sec--alt sec-how-pro">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Как проходит работа</h2>
        <p class="sec-sub">Шесть шагов от файла до готового перевода — с контролем качества на каждом этапе</p>
      </div>
      <div class="steps-row steps-row--6">
        <div class="step-item">
          <div class="step-num">01</div>
          <h3>Загрузите файл</h3>
          <p>Через сайт, мессенджер, email или принесите в офис</p>
        </div>
        <div class="step-item">
          <div class="step-num">02</div>
          <h3>Анализ документа</h3>
          <p>Оцениваем объём, тематику и сложность — за несколько минут</p>
        </div>
        <div class="step-item">
          <div class="step-num">03</div>
          <h3>Подбор специалиста</h3>
          <p>Выбираем переводчика с профильным медицинским образованием</p>
        </div>
        <div class="step-item">
          <div class="step-num">04</div>
          <h3>Перевод</h3>
          <p>Специалист работает с соблюдением медицинской терминологии и стиля оригинала</p>
        </div>
        <div class="step-item">
          <div class="step-num">05</div>
          <h3>Медицинская редактура</h3>
          <p>Редактор с медицинским образованием проверяет точность терминов и соответствие заданию</p>
        </div>
        <div class="step-item">
          <div class="step-num">06</div>
          <h3>Готовый перевод</h3>
          <p>Отправляем файл или вы забираете из офиса — точно в срок</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ГАРАНТИИ КАЧЕСТВА ════════ -->
  <section class="sec sec-guarantees">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Наши гарантии</h2>
        <p class="sec-sub">Мы отвечаем за результат — не только словами</p>
      </div>
      <div class="guarantees-grid">
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.9"/></svg>
          </div>
          <h3>Бесплатная правка</h3>
          <p>Исправим бесплатно в течение 7 дней, если перевод не соответствует заданию или содержит неточности</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </div>
          <h3>Конфиденциальность</h3>
          <p>NDA подписывается до передачи документов. Закрытая TM — данные клиента не попадают в облачные базы. Переводчики работают под NDA и не разглашают сведения о препаратах и исследованиях</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </div>
          <h3>Соблюдение сроков</h3>
          <p>Если не уложились в оговорённый срок по нашей вине — возвращаем предоплату или делаем скидку на следующий заказ</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
          </div>
          <h3>База терминологии</h3>
          <p>Сохраняем медицинский глоссарий вашей организации — при повторных заказах терминология остаётся единой</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ РЕГУЛЯТОРНЫЙ ПРОЦЕСС ════════ -->
  <section class="sec sec--alt sec-reg-process">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Процесс для регуляторных документов</h2>
        <p class="sec-sub">Специальная процедура для CTD, SmPC, IFU, GMP/GCP-документации — с документированием каждого шага</p>
      </div>
      <div class="reg-steps">
        <div class="reg-step">
          <div class="rs-num">01</div>
          <h3>NDA до передачи файлов</h3>
          <p>Соглашение о неразглашении подписывается до того, как вы отправите первый файл. Данные о незарегистрированных препаратах и текущих исследованиях защищены.</p>
        </div>
        <div class="reg-step">
          <div class="rs-num">02</div>
          <h3>Согласование глоссария</h3>
          <p>МНН, торговые наименования, коды разделов CTD, стандартные фразы SmPC — всё фиксируется в глоссарии и применяется последовательно во всех модулях досье.</p>
        </div>
        <div class="reg-step">
          <div class="rs-num">03</div>
          <h3>Перевод в закрытой TM</h3>
          <p>Работаем в SDL Trados или memoQ с закрытой Translation Memory клиента. Данные не уходят в общие облачные базы или AI-сервисы.</p>
        </div>
        <div class="reg-step">
          <div class="rs-num">04</div>
          <h3>Медицинская редактура</h3>
          <p>Независимый редактор с профильным образованием проверяет терминологическое соответствие, клиническую точность и соответствие требованиям регулятора.</p>
        </div>
        <div class="reg-step">
          <div class="rs-num">05</div>
          <h3>Обратный перевод (при необходимости)</h3>
          <p>Для ICF, опросников и документов, где ICH E6(R2) требует back-translation — выполняется независимым третьим переводчиком. Предоставляется сравнительная таблица расхождений.</p>
        </div>
        <div class="reg-step">
          <div class="rs-num">06</div>
          <h3>Итоговый QC и сдача</h3>
          <p>Финальная версия сопровождается подписью переводчика и редактора. По запросу — письмо-сертификат о соответствии перевода оригиналу для подачи регулятору.</p>
        </div>
      </div>
      <div class="reg-vendor-note">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        <span>Включение в Approved Vendor List (AVL) — предоставляем анкету квалификации поставщика и Quality Agreement по форме клиента.</span>
      </div>
    </div>
  </section>

  <!-- ════════ СРОКИ И СТОИМОСТЬ ════════ -->
  <section class="sec sec--alt sec-vol-pricing">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Сроки и стоимость</h2>
        <p class="sec-sub">Цена зависит от объёма. Точный расчёт — бесплатно, за несколько минут</p>
      </div>
      <div class="vol-table-wrap">
        <table class="vol-table">
          <thead>
            <tr>
              <th>Объём</th>
              <th>Срок</th>
              <th>Стоимость</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>До 5 страниц</strong></td>
              <td>1 рабочий день</td>
              <td class="vol-price">от 3 500 ₽</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>5–20 страниц</strong></td>
              <td>2–3 рабочих дня</td>
              <td class="vol-price">от 450 ₽/стр.</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>20–50 страниц</strong></td>
              <td>3–5 рабочих дней</td>
              <td class="vol-price">от 430 ₽/стр.</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>Свыше 50 страниц</strong></td>
              <td>по договорённости</td>
              <td class="vol-price">от 400 ₽/стр.</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
          </tbody>
        </table>
      </div>
      <p class="vol-note">Срочный перевод (менее 24 часов) — коэффициент ×1,5 к базовой стоимости. Точный расчёт сделаем после анализа вашего файла.</p>
    </div>
  </section>

  <!-- ════════ СРАВНЕНИЕ ФОРМАТОВ ════════ -->
  <section class="sec sec-comparison">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Какой формат вам подходит</h2>
        <p class="sec-sub">Сравните три уровня сервиса и выберите оптимальный для вашей задачи</p>
      </div>
      <div class="cmp-table-wrap">
        <table class="cmp-table">
          <thead>
            <tr>
              <th></th>
              <th>Постредактирование ИИ</th>
              <th class="cmp-featured">Профессиональный перевод</th>
              <th>Премиум-перевод</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>Перевод специалистом с медицинским/фарм. образованием</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Медицинская редактура (двойная проверка)</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Соответствие терминологии ICH / GCP / GMP</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Корпоративный глоссарий и Translation Memory</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Независимый эксперт-рецензент (третья проверка)</td><td class="cmp-no">✗</td><td class="cmp-no cmp-featured">✗</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Вычитка носителем целевого языка</td><td class="cmp-no">✗</td><td class="cmp-no cmp-featured">✗</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Обратный перевод (back-translation, GCP ICF)</td><td class="cmp-no">✗</td><td class="cmp-part cmp-featured">по запросу</td><td class="cmp-yes">✓ включён</td></tr>
            <tr><td>Подходит для регуляторной подачи (CTD, MAA, NDA)</td><td class="cmp-no">✗</td><td class="cmp-part cmp-featured">условно</td><td class="cmp-yes">✓</td></tr>
            <tr class="cmp-meta"><td>Срок</td><td>от 1 дня</td><td class="cmp-featured">2–4 дня</td><td>3–7 дней</td></tr>
            <tr class="cmp-meta cmp-price-row"><td>Стоимость</td><td>от 200 ₽/стр.</td><td class="cmp-featured">от 450 ₽/стр.</td><td>от 750 ₽/стр.</td></tr>
          </tbody>
          <tfoot>
            <tr>
              <td></td>
              <td><a href="#calc-section" class="price-btn price-btn--outline">Выбрать</a></td>
              <td class="cmp-featured"><a href="#calc-section" class="price-btn price-btn--primary">Выбрать</a></td>
              <td><a href="#calc-section" class="price-btn price-btn--outline">Выбрать</a></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </section>

  <!-- ════════ СПРАВОЧНИК ПО ДОКУМЕНТАМ (NEW) ════════ -->
  <section class="sec sec--alt sec-doc-reference">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Справочник по документам</h2>
        <p class="sec-sub">Выберите тип медицинского документа — узнайте особенности перевода</p>
      </div>
      <div class="doc-ref-grid">
        <a href="/meditsinskiy-perevod/instruktsii-k-preparatam/" class="doc-ref-card">
          <strong>Перевод инструкций к препаратам</strong>
          <span>Листки-вкладыши, SmPC, инструкции на все языки ЕС</span>
        </a>
        <a href="/meditsinskiy-perevod/protokoly-klinicheskih-issledovaniy/" class="doc-ref-card">
          <strong>Перевод протоколов клинических исследований</strong>
          <span>Полные протоколы согласно ICH E6(R2)</span>
        </a>
        <a href="/meditsinskiy-perevod/informirovannye-soglasiya-icf/" class="doc-ref-card">
          <strong>Перевод информированных согласий (ICF)</strong>
          <span>Документы для участников клинических исследований</span>
        </a>
        <a href="/meditsinskiy-perevod/farmakopeynyye-stati/" class="doc-ref-card">
          <strong>Перевод фармакопейных статей</strong>
          <span>Стандарты качества и методы контроля препаратов</span>
        </a>
        <a href="/meditsinskiy-perevod/regulyatornye-dosye/" class="doc-ref-card">
          <strong>Перевод регуляторных досье</strong>
          <span>NDA, ANDA, MAA для регистрации лекарственных средств</span>
        </a>
        <a href="/meditsinskiy-perevod/ifu-medoborudovaniya/" class="doc-ref-card">
          <strong>Перевод IFU медоборудования</strong>
          <span>Инструкции по применению медицинских устройств и приборов</span>
        </a>
        <a href="/meditsinskiy-perevod/psur-otchety/" class="doc-ref-card">
          <strong>Перевод PSUR и отчётов фармаконадзора</strong>
          <span>Периодические отчёты по безопасности препаратов</span>
        </a>
        <a href="/meditsinskiy-perevod/otchyoty-klinicheskikh-issledovanij/" class="doc-ref-card">
          <strong>Перевод клинических отчётов (CSR)</strong>
          <span>Итоговые отчёты по ICH E3, синопсисы, DSUR</span>
        </a>
        <a href="/meditsinskiy-perevod/gmp-gcp-glp-dokumenty/" class="doc-ref-card">
          <strong>Перевод документации GMP/GCP/GLP</strong>
          <span>SOPs, валидационные протоколы, аудиторские отчёты</span>
        </a>
        <a href="/meditsinskiy-perevod/tehnicheskie-fajly-medoborudovaniya/" class="doc-ref-card">
          <strong>Перевод технических файлов MDR</strong>
          <span>MDR technical file, FDA 510k, DHF для медустройств</span>
        </a>
        <a href="/meditsinskiy-perevod/etiketki-upakovka-farma/" class="doc-ref-card">
          <strong>Перевод этикеток и упаковки</strong>
          <span>Artwork, PIL, маркировка для регистрации в ЕАЭС</span>
        </a>
        <a href="/meditsinskiy-perevod/kliniceskie-rukovodstva/" class="doc-ref-card">
          <strong>Перевод клинических руководств</strong>
          <span>Guidelines WHO, ESMO, ESC, AHA для внедрения в практику</span>
        </a>
      </div>
    </div>
  </section>

  <!-- ════════ ПЕРЕВОДЫ ПО ОТРАСЛЯМ (NEW) ════════ -->
  <section class="sec sec-industries">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Переводы по отраслям</h2>
        <p class="sec-sub">Медицинский перевод для клиник, фарм-компаний и исследовательских организаций</p>
      </div>
      <div class="industries-grid">
        <a href="/meditsinskiy-perevod/farmatsevtika/" class="industry-card">
          <strong>Фармацевтика</strong>
          <span>Регуляторные досье, инструкции SmPC, GMP-документация, клинические отчёты</span>
        </a>
        <a href="/meditsinskiy-perevod/klinicheskie-issledovaniya-cro/" class="industry-card">
          <strong>Клинические исследования (CRO)</strong>
          <span>Протоколы, ICF, отчёты CSR, PSUR для мультицентровых исследований</span>
        </a>
        <a href="/meditsinskiy-perevod/medicinskie-ustroystva/" class="industry-card">
          <strong>Медицинские устройства</strong>
          <span>IFU, MDR-технические файлы, документы CE-маркировки</span>
        </a>
        <a href="/meditsinskiy-perevod/strakhovye-kompanii/" class="industry-card">
          <strong>Страховые компании</strong>
          <span>Медицинские заключения, полисы, аналитика страховых случаев</span>
        </a>
        <a href="/meditsinskiy-perevod/stomatologiya/" class="industry-card">
          <strong>Стоматология</strong>
          <span>Документация имплантатов, материалов, клинические руководства</span>
        </a>
        <a href="/meditsinskiy-perevod/veterinariya/" class="industry-card">
          <strong>Ветеринария</strong>
          <span>Ветпрепараты, регистрационные досье, клинические протоколы</span>
        </a>
        <a href="/meditsinskiy-perevod/biotekhnologii-genetika/" class="industry-card">
          <strong>Биотехнологии и геномика</strong>
          <span>CRISPR-протоколы, геномные исследования, биоинформатика</span>
        </a>
        <a href="/meditsinskiy-perevod/onkologiya/" class="industry-card">
          <strong>Онкология</strong>
          <span>Протоколы химиотерапии, иммунотерапии, guidelines ESMO, NCCN</span>
        </a>
        <a href="/meditsinskiy-perevod/kardiologiya/" class="industry-card">
          <strong>Кардиология</strong>
          <span>Клинические руководства ESC, AHA, протоколы исследований</span>
        </a>
      </div>
    </div>
  </section>

  <!-- ════════ ОТЗЫВЫ ════════ -->
  <section class="sec sec--alt sec-reviews">
    <div class="container">
      <div class="reviews-head">
        <div>
          <h2 class="sec-title">Что говорят клиенты</h2>
          <p class="sec-sub" style="margin-top:6px">Реальные отзывы о медицинских переводах</p>
        </div>
        <div class="review-rating-block">
          <div class="review-rating-score">4.98</div>
          <div class="review-stars-row">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          </div>
          <div class="review-rating-label">средний рейтинг</div>
        </div>
        <div class="review-platform-links">
          <a href="https://yandex.ru/maps/org/remarka/51867347382/reviews/" class="review-platform-link" target="_blank" rel="noopener">Яндекс</a>
          <a href="https://maps.app.goo.gl/d8BKJYw81PqBHXvz7" class="review-platform-link" target="_blank" rel="noopener">Google</a>
        </div>
      </div>
      <div class="reviews-grid" style="grid-template-columns:repeat(3,1fr)">
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">М</div>
            <div class="review-meta"><div class="review-name">Марина Павлова</div><div class="review-src">Яндекс · октябрь 2024</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Переводим протоколы клинических исследований и ICF регулярно. Переводчики явно понимают, о чём пишут: термины GCP не приходится объяснять, структура документов сохраняется.</p>
          <p class="review-author-role" style="font-size:12px;color:var(--text-muted);margin-top:6px">Менеджер клинических исследований, CRO «ФармТест»</p>
        </div>
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">Е</div>
            <div class="review-meta"><div class="review-name">Елена Захарова</div><div class="review-src">Google · сентябрь 2024</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Переводили регуляторное досье CTD для регистрации в ЕАЭС — больше 300 страниц в нескольких модулях. Терминология согласована с нашим глоссарием, единство МНН и торговых наименований сохранено. Замечаний от регулятора по переводу не было.</p>
          <p class="review-author-role" style="font-size:12px;color:var(--text-muted);margin-top:6px">Руководитель отдела регуляторных вопросов, «МедТехГрупп»</p>
        </div>
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">И</div>
            <div class="review-meta"><div class="review-name">Ирина Соколова</div><div class="review-src">2ГИС · апрель 2024</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Переводим инструкции к препаратам для регистрации в странах СНГ. Отличное знание фармацевтической терминологии, SmPC оформляются по требованиям регуляторов.</p>
          <p class="review-author-role" style="font-size:12px;color:var(--text-muted);margin-top:6px">Провизор, «ФармаГрупп»</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ FAQ ════════ -->
  <section class="sec sec-faq">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Часто задаваемые вопросы</h2>
        <p class="sec-sub">Не нашли ответа — напишите Ольге в чат, ответим за минуту</p>
      </div>
      <div class="faq-list">

        <details class="faq-item">
          <summary class="faq-summary">Переводите ли клиническую документацию для международного сотрудничества?</summary>
          <div class="faq-body">
            <p>Да. Переводим историй болезней, выписки, рентгенологические заключения и результаты исследований для международных медицинских учреждений, страховых компаний и организаций медицинского туризма. Работаем только с юридическими лицами и ИП.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Вы переводите протоколы клинических исследований по стандарту GCP?</summary>
          <div class="faq-body">
            <p>Да, работаем с полными протоколами, информированными согласиями, отчётами CSR и PSUR. Наши переводчики знают требования ICH E6(R2) и стандарты GCP/GMP.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Как долго переводится инструкция к препарату?</summary>
          <div class="faq-body">
            <p>До 10 страниц — 1 рабочий день. SmPC стандартного объёма (15–25 страниц) — 2–3 дня. При параллельном переводе на несколько языков — сроки согласуются отдельно.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Есть ли у переводчиков опыт работы с требованиями ВОЗ, EMA, FDA, Росздравнадзора?</summary>
          <div class="faq-body">
            <p>Да. Наши переводчики работали с требованиями ВОЗ (WHO), Европейского агентства по лекарственным средствам (EMA), FDA и Росздравнадзора непосредственно — они имеют профильное медицинское или фармацевтическое образование и опыт в регуляторной документации.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Принимаете ли вы фотографии или сканы медицинских документов?</summary>
          <div class="faq-body">
            <p>Да, работаем с фото, сканами и PDF любого качества. При плохом качестве исходника предупредим о возможных неточностях.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Где заказать медицинский перевод в Москве срочно?</summary>
          <div class="faq-body">
            <p>Бюро переводов «Ремарка» принимает заявки онлайн круглосуточно. Медицинский перевод в Москве срочно: до 5 страниц — за 1 рабочий день. Напишите Ольге в чат — ответим за 5 минут.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Выполняете ли вы обратный перевод (back-translation) для ICF и GCP-документов?</summary>
          <div class="faq-body">
            <p>Да. Обратный перевод выполняется независимым третьим переводчиком, не видевшим оригинала. По итогам предоставляем сравнительную таблицу расхождений (reconciliation table) по форме, принятой в вашей компании. Это соответствует требованиям ICH E6(R2) для переводов ICF и QoL-опросников.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Можем ли мы включить вас в наш Approved Vendor List (AVL)?</summary>
          <div class="faq-body">
            <p>Да. Предоставляем заполненную анкету квалификации поставщика услуг перевода, документацию по процессам обеспечения качества и подписываем Quality Agreement по форме вашей компании. Уточните детали в чате — вышлем пакет документов в течение рабочего дня.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Как вы защищаете данные о незарегистрированных препаратах?</summary>
          <div class="faq-body">
            <p>NDA подписывается до передачи первого файла. Перевод выполняется в закрытой Translation Memory клиента — данные не загружаются в публичные облачные CAT-сервисы и не используются в AI-обучении. Переводчики и редакторы индивидуально подписывают соглашение о конфиденциальности по вашим условиям.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Переводите ли многоязычные пакеты для регистрации в ЕАЭС и ЕС?</summary>
          <div class="faq-body">
            <p>Да. Для регистрации в ЕАЭС работаем на русский, казахский, белорусский, армянский и кыргызский языки — с единым глоссарием. Для европейской регистрации (MAA/EMA) — на все 24 официальных языка ЕС при параллельном переводе нескольких языковых пар. Координацию многоязычного проекта берём на себя.</p>
          </div>
        </details>

      </div>
    </div>
  </section>

  <!-- ════════ КАЛЬКУЛЯТОР СТОИМОСТИ ════════ -->',
        'it-perevod' => '<section class="sec sec-intro">
    <div class="container">
      <div class="intro-grid">
        <div class="intro-text">
          <p class="intro-label">Бюро переводов «Ремарка», Москва</p>
          <h1 class="intro-title">IT-перевод в Москве</h1>
          <p class="intro-tagline">Код понять — половина дела</p>
          <p class="intro-body">IT-текст переводить без технического понимания нельзя. Переводчик должен знать, что переменная в строке «Hello, {username}!» — это не ошибка и не имя, что кнопка Cancel в одном контексте должна называться «Отмена», а в другом «Прервать», и почему нельзя переводить строки короче 20 символов без проверки в реальном интерфейсе.</p>
          <p class="intro-body">IT-перевод в московском бюро «Ремарка» выполняют программисты, технические писатели и системные аналитики с лингвистической подготовкой. Они работают с форматами XLIFF, PO, JSON, RESX, Markdown и знают, как устроены современные инструменты локализации.</p>
          <p class="intro-body">Заказать перевод IT-документации у нас означает получить специалиста, который понимает продукт. Мы работаем с Crowdin, Lokalise, Phrase и SDL Trados — интегрируемся в ваш workflow без лишних шагов.</p>
        </div>
        <div class="intro-visual">
          <div class="intro-illus" aria-hidden="true">
            <svg viewBox="0 0 480 200" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect x="10" y="20" width="140" height="168" rx="12" fill="rgba(120,60,240,.10)" stroke="rgba(120,60,240,.35)" stroke-width="1.5"/>
              <rect x="10" y="20" width="140" height="38" rx="12" fill="rgba(120,60,240,.20)"/>
              <rect x="10" y="43" width="140" height="15" fill="rgba(120,60,240,.20)"/>
              <rect x="24" y="29" width="32" height="16" rx="8" fill="rgba(0,160,240,.40)"/>
              <rect x="28" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="42" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="24" y="76" width="112" height="5" rx="2.5" fill="rgba(167,139,250,.45)"/>
              <rect x="24" y="88" width="84" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="100" width="102" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="112" width="70" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="124" width="110" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="136" width="90" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="148" width="98" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="160" width="76" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="330" y="20" width="140" height="168" rx="12" fill="rgba(0,160,240,.10)" stroke="rgba(0,160,240,.35)" stroke-width="1.5"/>
              <rect x="330" y="20" width="140" height="38" rx="12" fill="rgba(0,160,240,.20)"/>
              <rect x="330" y="43" width="140" height="15" fill="rgba(0,160,240,.20)"/>
              <rect x="344" y="29" width="32" height="16" rx="8" fill="rgba(120,60,240,.40)"/>
              <rect x="348" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="362" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="344" y="76" width="112" height="5" rx="2.5" fill="rgba(0,160,240,.45)"/>
              <rect x="344" y="88" width="96" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="100" width="86" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="112" width="112" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="124" width="74" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="136" width="106" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="148" width="88" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="160" width="100" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <circle cx="240" cy="104" r="46" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
              <circle cx="240" cy="104" r="30" fill="rgba(120,60,240,.18)"/>
              <path d="M152 104h56M288 104h40" stroke="rgba(120,60,240,.40)" stroke-width="1.5" stroke-dasharray="4 3"/>
              <path d="M224 104h32M246 96l10 8-10 8" stroke="rgba(167,139,250,.95)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="intro-features">
            <div class="intro-feat">
              <div class="intro-feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg></div>
              <div><strong>IT-background</strong><span>Разработчики и аналитики, понимают архитектуру продуктов</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg></div>
              <div><strong>Форматы разработчиков</strong><span>XLIFF, PO, JSON, RESX, Markdown, YAML, Properties</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg></div>
              <div><strong>Контекст интерфейса</strong><span>Учитываем длину строк и UI-ограничения реального продукта</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></div>
              <div><strong>RTL и CJK</strong><span>Арабский, иврит, китайский, японский, корейский — включая направление текста</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

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

  <!-- ════════ ПРИМЕР ПЕРЕВОДА — КОД ════════ -->
  <section class="sec sec--alt sec-code-ex">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Как это выглядит на практике</h2>
        <p class="sec-sub">Реальные фрагменты файлов — структура и плейсхолдеры остаются нетронутыми</p>
      </div>
      <div class="code-ex-tabs" role="tablist">
        <button class="code-ex-tab active" data-tab="cex-json" role="tab" aria-selected="true">JSON (i18n)</button>
        <button class="code-ex-tab" data-tab="cex-xliff" role="tab" aria-selected="false">XLIFF</button>
        <button class="code-ex-tab" data-tab="cex-api" role="tab" aria-selected="false">API Docs (Markdown)</button>
      </div>
      <div class="code-ex-panels">
        <div class="code-ex-panel active" id="cex-json" role="tabpanel">
          <div class="ba-grid">
            <div class="ba-side">
              <div class="ba-label ba-label--src">Исходный файл (RU)</div>
              <pre class="ba-code"><code>{
  "billing.upgrade": "Улучшить тариф",
  "billing.trial":   "Пробный период: {days} дн.",
  "billing.failed":  "Платёж не прошёл.",
  "billing.card":    "Обновите способ оплаты",
  "nav.settings":    "Настройки",
  "nav.logout":      "Выйти из аккаунта"
}</code></pre>
            </div>
            <div class="ba-arrow" aria-hidden="true">→</div>
            <div class="ba-side">
              <div class="ba-label ba-label--dst">Перевод (EN)</div>
              <pre class="ba-code ba-code--translated"><code>{
  "billing.upgrade": "Upgrade your plan",
  "billing.trial":   "Trial: {days} days left",
  "billing.failed":  "Payment failed.",
  "billing.card":    "Update payment method",
  "nav.settings":    "Settings",
  "nav.logout":      "Sign out"
}</code></pre>
            </div>
          </div>
          <p class="ba-note">Плейсхолдер <code>{days}</code>, ключи и структура JSON сохранены. Строки адаптированы под типичную длину кнопок и плашек UI.</p>
        </div>
        <div class="code-ex-panel" id="cex-xliff" role="tabpanel">
          <div class="ba-grid">
            <div class="ba-side">
              <div class="ba-label ba-label--src">XLIFF (source)</div>
              <pre class="ba-code"><code>&lt;trans-unit id="login.error"&gt;
  &lt;source&gt;Неверный логин или пароль&lt;/source&gt;
  &lt;target state="needs-translation"/&gt;
&lt;/trans-unit&gt;
&lt;trans-unit id="logout.confirm"&gt;
  &lt;source&gt;Выйти из аккаунта?&lt;/source&gt;
  &lt;target state="needs-translation"/&gt;
&lt;/trans-unit&gt;</code></pre>
            </div>
            <div class="ba-arrow" aria-hidden="true">→</div>
            <div class="ba-side">
              <div class="ba-label ba-label--dst">XLIFF (translated)</div>
              <pre class="ba-code ba-code--translated"><code>&lt;trans-unit id="login.error"&gt;
  &lt;source&gt;Неверный логин или пароль&lt;/source&gt;
  &lt;target state="translated"&gt;
    Invalid username or password&lt;/target&gt;
&lt;/trans-unit&gt;
&lt;trans-unit id="logout.confirm"&gt;
  &lt;source&gt;Выйти из аккаунта?&lt;/source&gt;
  &lt;target state="translated"&gt;
    Sign out?&lt;/target&gt;
&lt;/trans-unit&gt;</code></pre>
            </div>
          </div>
          <p class="ba-note">Теги XML не тронуты. Атрибут <code>state</code> обновлён до <code>"translated"</code>. Возвращаем файл в исходном формате — загружаете обратно в Xcode или Android Studio.</p>
        </div>
        <div class="code-ex-panel" id="cex-api" role="tabpanel">
          <div class="ba-grid">
            <div class="ba-side">
              <div class="ba-label ba-label--src">Документация (RU)</div>
              <pre class="ba-code"><code>## POST /api/v2/payments

Создаёт новую транзакцию.

**Параметры тела запроса:**
- `amount` (integer) — сумма в копейках
- `currency` (string) — код валюты ISO 4217
- `idempotency_key` (string) — ключ
  идемпотентности</code></pre>
            </div>
            <div class="ba-arrow" aria-hidden="true">→</div>
            <div class="ba-side">
              <div class="ba-label ba-label--dst">Документация (EN)</div>
              <pre class="ba-code ba-code--translated"><code>## POST /api/v2/payments

Creates a new transaction.

**Request body parameters:**
- `amount` (integer) — amount in kopecks
- `currency` (string) — currency code ISO 4217
- `idempotency_key` (string) — idempotency
  key</code></pre>
            </div>
          </div>
          <p class="ba-note">Markdown-разметка, URL эндпойнтов и имена параметров (<code>idempotency_key</code>) не переводятся. Переводчик с API-background — понимает разницу между полем и описанием.</p>
        </div>
      </div>
      <div class="ba-formats">
        <span class="ba-fmt-label">Принимаем и возвращаем в исходном формате:</span>
        <span class="ba-fmt-chip">.po / .pot</span>
        <span class="ba-fmt-chip">.xliff / .xlf</span>
        <span class="ba-fmt-chip">.json</span>
        <span class="ba-fmt-chip">.strings</span>
        <span class="ba-fmt-chip">.resx</span>
        <span class="ba-fmt-chip">.arb</span>
        <span class="ba-fmt-chip">.yaml</span>
        <span class="ba-fmt-chip">.properties</span>
        <span class="ba-fmt-chip">Markdown</span>
        <span class="ba-fmt-chip">CSV / Excel</span>
      </div>
    </div>
  </section>

  <!-- ════════ КАКИЕ ДОКУМЕНТЫ ════════ -->
  <section class="sec sec--alt sec-docs">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Какие документы мы переводим</h2>
        <p class="sec-sub">Раскройте нужную категорию — посмотрите конкретные виды IT-документов</p>
      </div>
      <div class="docs-accordion">

        <details class="doc-item" open>
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
            <span>API и SDK документация</span>
          </summary>
          <div class="doc-body">
            <p>endpoints, параметры, примеры кода, Swagger/OpenAPI, README</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            <span>Пользовательские интерфейсы</span>
          </summary>
          <div class="doc-body">
            <p>меню, кнопки, сообщения об ошибках, тултипы, онбординг</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            <span>Пользовательские руководства</span>
          </summary>
          <div class="doc-body">
            <p>help-системы, базы знаний, статьи поддержки</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <span>Политики и юридические тексты</span>
          </summary>
          <div class="doc-body">
            <p>Privacy Policy, Terms of Service, GDPR-уведомления, Cookie Policy</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="5 3 19 12 5 21 5 3"/></svg>
            <span>Игры и интерактивный контент</span>
          </summary>
          <div class="doc-body">
            <p>диалоги, квесты, системные сообщения, субтитры видео</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
            <span>Мобильные приложения</span>
          </summary>
          <div class="doc-body">
            <p>App Store / Google Play листинги, push-уведомления, системные строки</p>
          </div>
        </details>

      </div>
    </div>
  </section>

  <!-- ════════ НАШИ ПЕРЕВОДЧИКИ ════════ -->
  <section class="sec sec-translators">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">IT-переводчики в Москве</h2>
        <p class="sec-sub">IT-переводчики московского бюро «Ремарка» понимают продукт, а не просто текст</p>
      </div>
      <div class="translators-grid">
        <div class="translator-card">
          <div class="translator-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
          </div>
          <h3>Технический background</h3>
          <p>Программисты и аналитики, переквалифицировавшиеся в технические писатели и переводчики.</p>
        </div>
        <div class="translator-card">
          <div class="translator-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
          </div>
          <h3>CAT и L10n инструменты</h3>
          <p>Crowdin, Lokalise, Phrase, SDL Trados, memoQ — работают в вашей системе.</p>
        </div>
        <div class="translator-card">
          <div class="translator-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
          </div>
          <h3>Localization QA</h3>
          <p>Тестируют переводы в реальном интерфейсе — не только в таблице.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="sec sec-split">
    <div class="container">
      <div class="split-row split-row--rev">
        <div class="split-text">
          <p class="sec-label">Технический IT-подход</p>
          <h2>Локализация — это больше, чем перевод строк</h2>
          <p>IT-переводчик работает не с отдельными фразами, а с продуктом в целом. Строка в интерфейсе ограничена в длину, переменная внутри строки должна остаться переменной, а кнопка «OK» в одном контексте — это подтверждение, в другом — закрытие диалога.</p>
          <p>Наши IT-переводчики — разработчики и технические писатели. Они открывают XLIFF в том же инструменте, что и разработчик, понимают структуру JSON и не ломают плейсхолдеры.</p>
          <ul class="split-checklist">
            <li>Переводчики с IT-образованием (разработчики, аналитики)</li>
            <li>Работа с XLIFF, PO, JSON, RESX, Markdown, YAML, .strings</li>
            <li>Localization QA — тест перевода в реальном интерфейсе</li>
            <li>Интеграция с Crowdin, Lokalise, Phrase, SDL Trados</li>
            <li>Подключение к вашему CI/CD — через GitHub Actions, вебхуки TMS или прямую работу в платформе без ручного экспорта файлов</li>
            <li>Возврат файлов в исходном формате — загружаете обратно в проект без конвертации</li>
          </ul>
        </div>
        <div class="split-visual" aria-hidden="true">
          <svg viewBox="0 0 380 320" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <linearGradient id="sq-g1-i" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#783CF0"/><stop offset="100%" stop-color="#00A0F0"/></linearGradient>
              <linearGradient id="sq-g2-i" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#783CF0" stop-opacity=".5"/><stop offset="100%" stop-color="#00A0F0" stop-opacity=".5"/></linearGradient>
            </defs>
            <circle cx="190" cy="160" r="130" fill="rgba(120,60,240,.07)"/>
            <rect x="60" y="30" width="260" height="64" rx="16" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="62" r="20" fill="url(#sq-g1-i)" opacity=".85"/>
            <path d="M88 62l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="50" width="170" height="9" rx="4" fill="rgba(167,139,250,.55)"/>
            <rect x="126" y="66" width="120" height="7" rx="3" fill="rgba(167,139,250,.30)"/>
            <line x1="190" y1="94" x2="190" y2="118" stroke="url(#sq-g2-i)" stroke-width="2" stroke-dasharray="4 3"/>
            <rect x="60" y="118" width="260" height="64" rx="16" fill="rgba(0,160,240,.12)" stroke="rgba(0,160,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="150" r="20" fill="url(#sq-g1-i)" opacity=".85"/>
            <path d="M88 150l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="138" width="155" height="9" rx="4" fill="rgba(0,160,240,.55)"/>
            <rect x="126" y="154" width="110" height="7" rx="3" fill="rgba(0,160,240,.30)"/>
            <line x1="190" y1="182" x2="190" y2="206" stroke="url(#sq-g2-i)" stroke-width="2" stroke-dasharray="4 3"/>
            <rect x="60" y="206" width="260" height="64" rx="16" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="238" r="20" fill="url(#sq-g1-i)" opacity=".85"/>
            <path d="M88 238l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="226" width="175" height="9" rx="4" fill="rgba(167,139,250,.55)"/>
            <rect x="126" y="242" width="130" height="7" rx="3" fill="rgba(167,139,250,.30)"/>
            <circle cx="310" cy="290" r="26" fill="rgba(0,200,100,.12)" stroke="rgba(0,200,100,.35)" stroke-width="1.5"/>
            <path d="M300 290l8 8 14-16" stroke="rgba(0,200,100,.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ КАК ПРОХОДИТ РАБОТА (6 шагов) ════════ -->
  <section class="sec sec--alt sec-how-pro">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Как проходит работа</h2>
        <p class="sec-sub">Шесть шагов от файла до готового перевода — с контролем качества на каждом этапе</p>
      </div>
      <div class="steps-row steps-row--6">
        <div class="step-item">
          <div class="step-num">01</div>
          <h3>Загрузите файл</h3>
          <p>Через сайт, мессенджер, email или напрямую в L10n-платформу</p>
        </div>
        <div class="step-item">
          <div class="step-num">02</div>
          <h3>Анализ документа</h3>
          <p>Оцениваем объём, формат, контекст строк и UI-ограничения</p>
        </div>
        <div class="step-item">
          <div class="step-num">03</div>
          <h3>Подбор специалиста</h3>
          <p>Выбираем IT-переводчика с профильным техническим background</p>
        </div>
        <div class="step-item">
          <div class="step-num">04</div>
          <h3>Перевод</h3>
          <p>Специалист работает с учётом контекста, переменных и терминологии</p>
        </div>
        <div class="step-item">
          <div class="step-num">05</div>
          <h3>Localization QA</h3>
          <p>Проверяем строки в реальном интерфейсе, длины и RTL-поддержку</p>
        </div>
        <div class="step-item">
          <div class="step-num">06</div>
          <h3>Готовый перевод</h3>
          <p>Отдаём файл в исходном формате или через вашу платформу</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ГАРАНТИИ КАЧЕСТВА ════════ -->
  <section class="sec sec-guarantees">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Наши гарантии</h2>
        <p class="sec-sub">Мы отвечаем за результат — не только словами</p>
      </div>
      <div class="guarantees-grid">
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.9"/></svg>
          </div>
          <h3>Бесплатная правка</h3>
          <p>Исправим бесплатно в течение 7 дней, если перевод не соответствует заданию или содержит неточности</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </div>
          <h3>Конфиденциальность</h3>
          <p>NDA по запросу. Ваши файлы и исходный код не передаются третьим лицам и не хранятся на внешних серверах</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </div>
          <h3>Соблюдение сроков</h3>
          <p>Если не уложились в оговорённый срок по нашей вине — возвращаем предоплату или делаем скидку на следующий заказ</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          </div>
          <h3>Единый глоссарий</h3>
          <p>Ведём term base вашего проекта. «Endpoint» → «эндпойнт» — всегда одинаково во всех файлах. Translation Memory снижает стоимость повторных строк на 30–70%</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          </div>
          <h3>Чат с переводчиком в TMS</h3>
          <p>Задайте вопрос напрямую в задаче — переводчик объяснит, почему выбрал именно этот вариант, или уточнит контекст UI-строки без длинных переписок по email</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ СРОКИ И СТОИМОСТЬ ════════ -->
  <section class="sec sec--alt sec-vol-pricing">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Сроки и стоимость</h2>
        <p class="sec-sub">Цена зависит от объёма. Точный расчёт — бесплатно, за несколько минут</p>
      </div>
      <div class="vol-table-wrap">
        <table class="vol-table">
          <thead>
            <tr>
              <th>Объём</th>
              <th>Срок</th>
              <th>Стоимость</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>До 1 000 слов</strong></td>
              <td>1 рабочий день</td>
              <td class="vol-price">от 3 000 ₽</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>1 000–5 000 слов</strong></td>
              <td>2–3 рабочих дня</td>
              <td class="vol-price">от 3 ₽/слово</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>5 000–20 000 слов</strong></td>
              <td>3–7 рабочих дней</td>
              <td class="vol-price">от 2,8 ₽/слово</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>Свыше 20 000 слов</strong></td>
              <td>по договорённости</td>
              <td class="vol-price">от 2,5 ₽/слово</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
          </tbody>
        </table>
      </div>
      <p class="vol-note">Возможна оплата за страницу (400 ₽). Translation Memory снижает стоимость повторных строк.</p>
    </div>
  </section>

  <!-- ════════ СРАВНЕНИЕ ФОРМАТОВ ════════ -->
  <section class="sec sec-comparison">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Какой формат вам подходит</h2>
        <p class="sec-sub">Сравните три уровня сервиса и выберите оптимальный для вашей задачи</p>
      </div>
      <div class="cmp-table-wrap">
        <table class="cmp-table">
          <thead>
            <tr>
              <th></th>
              <th>Постредактирование ИИ</th>
              <th class="cmp-featured">Профессиональный перевод</th>
              <th>Премиум-перевод</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>Перевод с нуля IT-специалистом</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Понимание переменных и плейсхолдеров</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Работа с L10n-форматами напрямую</td><td class="cmp-part">частичная</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Localization QA в реальном интерфейсе</td><td class="cmp-no">✗</td><td class="cmp-no cmp-featured">✗</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Вычитка носителем языка</td><td class="cmp-no">✗</td><td class="cmp-no cmp-featured">✗</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Подходит для публичного релиза</td><td class="cmp-no">✗</td><td class="cmp-part cmp-featured">условно</td><td class="cmp-yes">✓</td></tr>
            <tr class="cmp-meta"><td>Срок</td><td>от 1 дня</td><td class="cmp-featured">1–3 дня</td><td>2–5 дней</td></tr>
            <tr class="cmp-meta cmp-price-row"><td>Стоимость</td><td>от 1,5 ₽/слово</td><td class="cmp-featured">от 3 ₽/слово</td><td>от 5 ₽/слово</td></tr>
          </tbody>
          <tfoot>
            <tr>
              <td></td>
              <td><a href="#calc-section" class="price-btn price-btn--outline">Выбрать</a></td>
              <td class="cmp-featured"><a href="#calc-section" class="price-btn price-btn--primary">Выбрать</a></td>
              <td><a href="#calc-section" class="price-btn price-btn--outline">Выбрать</a></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </section>

  <!-- ════════ СПРАВОЧНИК ПО ДОКУМЕНТАМ ════════ -->
  <section class="sec sec--alt sec-doc-reference">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Справочник по документам</h2>
        <p class="sec-sub">Выберите тип IT-документа — узнайте особенности перевода</p>
      </div>
      <div class="doc-ref-grid">
        <a href="/it-perevod/api-dokumentatsiya/" class="doc-ref-card">
          <strong>Перевод API-документации</strong>
          <span>REST API, GraphQL, Swagger, OpenAPI, Postman коллекции</span>
        </a>
        <a href="/it-perevod/ui-stroki/" class="doc-ref-card">
          <strong>Перевод UI-строк</strong>
          <span>Интерфейсные строки из XLIFF, PO, JSON, RESX, ARB файлов</span>
        </a>
        <a href="/it-perevod/readme-wiki/" class="doc-ref-card">
          <strong>Перевод README и Wiki</strong>
          <span>Документация на GitHub, Confluence, Notion</span>
        </a>
        <a href="/it-perevod/polzovatelskie-rukovodstva/" class="doc-ref-card">
          <strong>Перевод пользовательских руководств</strong>
          <span>Help-статьи, базы знаний, FAQ-разделы</span>
        </a>
        <a href="/it-perevod/privacy-policy/" class="doc-ref-card">
          <strong>Перевод Privacy Policy</strong>
          <span>Политики конфиденциальности для разных юрисдикций</span>
        </a>
        <a href="/it-perevod/terms-of-service/" class="doc-ref-card">
          <strong>Перевод Terms of Service</strong>
          <span>Пользовательские соглашения, SLA, условия использования</span>
        </a>
        <a href="/it-perevod/app-store-google-play/" class="doc-ref-card">
          <strong>Перевод App Store / Google Play</strong>
          <span>Описания приложений, ключевые слова, скриншоты</span>
        </a>
        <a href="/it-perevod/sdk-dokumentatsiya/" class="doc-ref-card">
          <strong>Перевод SDK документации</strong>
          <span>Интеграционные руководства, примеры кода</span>
        </a>
        <a href="/it-perevod/release-notes/" class="doc-ref-card">
          <strong>Перевод Release Notes</strong>
          <span>Журналы изменений, анонсы обновлений, changelog</span>
        </a>
        <a href="/it-perevod/gdpr-dokumenty/" class="doc-ref-card">
          <strong>Перевод GDPR документов</strong>
          <span>Уведомления об обработке данных, формы согласия</span>
        </a>
        <a href="/it-perevod/tehnicheskie-zadaniya/" class="doc-ref-card">
          <strong>Перевод технических заданий</strong>
          <span>ТЗ на разработку, функциональные требования, RFP/RFI</span>
        </a>
        <a href="/it-perevod/igrovye-teksty/" class="doc-ref-card">
          <strong>Перевод игровых текстов</strong>
          <span>Сюжеты, диалоги, описания предметов, системные сообщения</span>
        </a>
      </div>
    </div>
  </section>

  <!-- ════════ ПЕРЕВОДЫ ПО ОТРАСЛЯМ ════════ -->
  <section class="sec sec-industries">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Переводы по отраслям</h2>
        <p class="sec-sub">IT-перевод и локализация для разных технологических сегментов</p>
      </div>
      <div class="industries-grid">
        <a href="/it-perevod/saas-oblachnye-servisy/" class="industry-card">
          <strong>SaaS и облачные сервисы</strong>
          <span>UI, онбординг, справка, API-документация</span>
        </a>
        <a href="/it-perevod/mobilnaya-razrabotka/" class="industry-card">
          <strong>Мобильная разработка</strong>
          <span>App Store/Play листинги, системные строки, push-уведомления</span>
        </a>
        <a href="/it-perevod/igrovaya-industriya/" class="industry-card">
          <strong>Игровая индустрия</strong>
          <span>Нарративы, диалоги, интерфейс, субтитры катсцен</span>
        </a>
        <a href="/it-perevod/kiberbezopasnost/" class="industry-card">
          <strong>Кибербезопасность</strong>
          <span>Отчёты пентестов, технические описания, интерфейсы</span>
        </a>
        <a href="/it-perevod/e-commerce/" class="industry-card">
          <strong>E-commerce</strong>
          <span>Карточки товаров, интерфейс магазина, email-рассылки</span>
        </a>
        <a href="/it-perevod/fintech-banking/" class="industry-card">
          <strong>Финтех и банкинг</strong>
          <span>Интерфейсы приложений, политики, финансовые термины</span>
        </a>
        <a href="/it-perevod/edtech/" class="industry-card">
          <strong>EdTech</strong>
          <span>Курсы, тесты, платформы дистанционного обучения</span>
        </a>
        <a href="/it-perevod/healthtech/" class="industry-card">
          <strong>HealthTech</strong>
          <span>Медицинские приложения, интерфейс MIS, GDPR-документы</span>
        </a>
        <a href="/it-perevod/korporativnoe-po/" class="industry-card">
          <strong>Корпоративное ПО</strong>
          <span>ERP, CRM, HRM — интерфейс и пользовательская документация</span>
        </a>
      </div>
    </div>
  </section>

  <!-- ════════ ПРИМЕРЫ ПО ОТРАСЛЯМ ════════ -->
  <section class="sec sec--alt sec-ind-examples">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Примеры переводов по отраслям</h2>
        <p class="sec-sub">Реальные фрагменты текстов — до и после</p>
      </div>
      <div class="ind-ex-tabs" role="tablist">
        <button class="ind-ex-tab active" data-tab="iex-saas" role="tab">SaaS</button>
        <button class="ind-ex-tab" data-tab="iex-mobile" role="tab">Мобайл</button>
        <button class="ind-ex-tab" data-tab="iex-game" role="tab">Игры</button>
        <button class="ind-ex-tab" data-tab="iex-sec" role="tab">Кибербез</button>
        <button class="ind-ex-tab" data-tab="iex-ecom" role="tab">E-com</button>
        <button class="ind-ex-tab" data-tab="iex-fin" role="tab">Финтех</button>
        <button class="ind-ex-tab" data-tab="iex-edu" role="tab">EdTech</button>
        <button class="ind-ex-tab" data-tab="iex-health" role="tab">HealthTech</button>
        <button class="ind-ex-tab" data-tab="iex-erp" role="tab">Корп. ПО</button>
      </div>
      <div class="ind-ex-panels">

        <div class="ind-ex-panel active" id="iex-saas">
          <div class="iex-meta">Тип документа: UI-строки · JSON · RU → EN</div>
          <div class="ba-grid">
            <div class="ba-side">
              <div class="ba-label ba-label--src">До</div>
              <pre class="ba-code"><code>Добро пожаловать в {workspace}!
Ваша пробная версия истекает
  через {days} дн.
Перейти на платный план →
Отменить подписку</code></pre>
            </div>
            <div class="ba-arrow" aria-hidden="true">→</div>
            <div class="ba-side">
              <div class="ba-label ba-label--dst">После</div>
              <pre class="ba-code ba-code--translated"><code>Welcome to {workspace}!
Your trial expires
  in {days} days.
Upgrade to a paid plan →
Cancel subscription</code></pre>
            </div>
          </div>
          <p class="ba-note">«Перейти на платный план» → «Upgrade» — принятый SaaS-паттерн. «Истекает» → «expires» — без russianism «ends».</p>
        </div>

        <div class="ind-ex-panel" id="iex-mobile">
          <div class="iex-meta">Тип документа: App Store / Google Play листинг · RU → EN</div>
          <div class="ba-grid">
            <div class="ba-side">
              <div class="ba-label ba-label--src">До</div>
              <pre class="ba-code"><code>Умный планировщик задач для
команд. Создавайте задачи,
назначайте исполнителей и
отслеживайте прогресс в режиме
реального времени.</code></pre>
            </div>
            <div class="ba-arrow" aria-hidden="true">→</div>
            <div class="ba-side">
              <div class="ba-label ba-label--dst">После</div>
              <pre class="ba-code ba-code--translated"><code>Smart task planner for teams.
Create tasks, assign members,
and track progress
in real time.</code></pre>
            </div>
          </div>
          <p class="ba-note">Краткость критична — App Store обрезает текст на 170 символах. «В режиме реального времени» → «in real time» — без лишних слов.</p>
        </div>

        <div class="ind-ex-panel" id="iex-game">
          <div class="iex-meta">Тип документа: игровые диалоги · RU → EN</div>
          <div class="ba-grid">
            <div class="ba-side">
              <div class="ba-label ba-label--src">До</div>
              <pre class="ba-code"><code>— Ты опоздал, охотник.
  Руины уже охраняет
  Страж Теней.
— Тогда мне нужно торопиться.</code></pre>
            </div>
            <div class="ba-arrow" aria-hidden="true">→</div>
            <div class="ba-side">
              <div class="ba-label ba-label--dst">После</div>
              <pre class="ba-code ba-code--translated"><code>— You\'re late, hunter.
  The ruins are guarded
  by the Shadow Warden now.
— Then I need to hurry.</code></pre>
            </div>
          </div>
          <p class="ba-note">«Страж Теней» — имя собственное, сохранено с заглавной. Переводчик проверяет длину строки под субтитровую разбивку (42 символа на строку).</p>
        </div>

        <div class="ind-ex-panel" id="iex-sec">
          <div class="iex-meta">Тип документа: отчёт пентеста · RU → EN</div>
          <div class="ba-grid">
            <div class="ba-side">
              <div class="ba-label ba-label--src">До</div>
              <pre class="ba-code"><code>Уязвимость CVE-2024-1234 типа
SQL-инъекция обнаружена в
параметре user_id эндпойнта
/api/v2/users.
CVSS: 9.8 (Критический).</code></pre>
            </div>
            <div class="ba-arrow" aria-hidden="true">→</div>
            <div class="ba-side">
              <div class="ba-label ba-label--dst">После</div>
              <pre class="ba-code ba-code--translated"><code>Vulnerability CVE-2024-1234
(SQL Injection) identified
in the user_id parameter of
/api/v2/users endpoint.
CVSS: 9.8 (Critical).</code></pre>
            </div>
          </div>
          <p class="ba-note">CVE-идентификатор, параметр <code>user_id</code>, путь <code>/api/v2/users</code> и CVSS-оценка не переводятся — только дескриптивный текст.</p>
        </div>

        <div class="ind-ex-panel" id="iex-ecom">
          <div class="iex-meta">Тип документа: карточка товара · RU → EN</div>
          <div class="ba-grid">
            <div class="ba-side">
              <div class="ba-label ba-label--src">До</div>
              <pre class="ba-code"><code>Умная колонка с голосовым
управлением. Встроенный
ИИ-ассистент. Поддержка
Zigbee и Wi-Fi 6.
В наличии: 14 шт.</code></pre>
            </div>
            <div class="ba-arrow" aria-hidden="true">→</div>
            <div class="ba-side">
              <div class="ba-label ba-label--dst">После</div>
              <pre class="ba-code ba-code--translated"><code>Smart speaker with voice
control. Built-in AI assistant.
Supports Zigbee and Wi-Fi 6.
In stock: 14 units.</code></pre>
            </div>
          </div>
          <p class="ba-note">«ИИ» → «AI» (не «Artificial Intelligence» — в карточке). Zigbee и Wi-Fi 6 — технические бренды, без перевода.</p>
        </div>

        <div class="ind-ex-panel" id="iex-fin">
          <div class="iex-meta">Тип документа: юридическое уведомление в финтех-приложении · RU → EN</div>
          <div class="ba-grid">
            <div class="ba-side">
              <div class="ba-label ba-label--src">До</div>
              <pre class="ba-code"><code>Перевод денежных средств
на счёт контрагента будет
выполнен в течение одного
операционного дня с момента
подтверждения операции.</code></pre>
            </div>
            <div class="ba-arrow" aria-hidden="true">→</div>
            <div class="ba-side">
              <div class="ba-label ba-label--dst">После</div>
              <pre class="ba-code ba-code--translated"><code>Funds will be transferred
to the counterparty\'s account
within one business day
of transaction
confirmation.</code></pre>
            </div>
          </div>
          <p class="ba-note">«Операционный день» → «business day» (принятый финансовый термин). «С момента подтверждения» → «of confirmation» — без кальки.</p>
        </div>

        <div class="ind-ex-panel" id="iex-edu">
          <div class="iex-meta">Тип документа: задание к курсу по программированию · RU → EN</div>
          <div class="ba-grid">
            <div class="ba-side">
              <div class="ba-label ba-label--src">До</div>
              <pre class="ba-code"><code>Задание 3. Используя концепцию
рекурсии, напишите функцию
для вычисления числа Фибоначчи.
Сложность: O(n).</code></pre>
            </div>
            <div class="ba-arrow" aria-hidden="true">→</div>
            <div class="ba-side">
              <div class="ba-label ba-label--dst">После</div>
              <pre class="ba-code ba-code--translated"><code>Exercise 3. Using recursion,
write a function to calculate
a Fibonacci number.
Time complexity: O(n).</code></pre>
            </div>
          </div>
          <p class="ba-note">«Сложность» → «Time complexity» — в CS-контексте термин полный, не сокращённый. «Концепцию рекурсии» → просто «recursion» — без лишнего «концепцию».</p>
        </div>

        <div class="ind-ex-panel" id="iex-health">
          <div class="iex-meta">Тип документа: push-уведомление медицинского приложения · RU → EN</div>
          <div class="ba-grid">
            <div class="ba-side">
              <div class="ba-label ba-label--src">До</div>
              <pre class="ba-code"><code>Ваш пульс 72 уд/мин.
Это нормальный показатель
для состояния покоя.
Следующее измерение — 30 мин.</code></pre>
            </div>
            <div class="ba-arrow" aria-hidden="true">→</div>
            <div class="ba-side">
              <div class="ba-label ba-label--dst">После</div>
              <pre class="ba-code ba-code--translated"><code>Your heart rate: 72 bpm.
Normal resting rate.
Next reading in 30 min.</code></pre>
            </div>
          </div>
          <p class="ba-note">Push-уведомления ограничены ~50 символами. «Уд/мин» → «bpm» (международный стандарт). Без повторения «ваш» в каждой строке.</p>
        </div>

        <div class="ind-ex-panel" id="iex-erp">
          <div class="iex-meta">Тип документа: системное сообщение ERP · RU → EN</div>
          <div class="ba-grid">
            <div class="ba-side">
              <div class="ba-label ba-label--src">До</div>
              <pre class="ba-code"><code>Документ отправлен на
согласование руководителю
подразделения. Ожидайте
ответа в течение 2 рабочих
дней.</code></pre>
            </div>
            <div class="ba-arrow" aria-hidden="true">→</div>
            <div class="ba-side">
              <div class="ba-label ba-label--dst">После</div>
              <pre class="ba-code ba-code--translated"><code>Document submitted for
approval by the department
manager. Expect a response
within 2 business days.</code></pre>
            </div>
          </div>
          <p class="ba-note">«Согласование» → «approval» (ERP/workflow-термин). «Ожидайте ответа» → «Expect a response» — не «Wait for reply» (разговорное).</p>
        </div>

      </div>
    </div>
  </section>

  <!-- ════════ ОТЗЫВЫ ════════ -->
  <section class="sec sec--alt sec-reviews">
    <div class="container">
      <div class="reviews-head">
        <div>
          <h2 class="sec-title">Что говорят клиенты</h2>
          <p class="sec-sub" style="margin-top:6px">Реальные отзывы об IT-переводах</p>
        </div>
        <div class="review-rating-block">
          <div class="review-rating-score">4.98</div>
          <div class="review-stars-row">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          </div>
          <div class="review-rating-label">средний рейтинг</div>
        </div>
        <div class="review-platform-links">
          <a href="https://yandex.ru/maps/org/remarka/51867347382/reviews/" class="review-platform-link" target="_blank" rel="noopener">Яндекс</a>
          <a href="https://maps.app.goo.gl/d8BKJYw81PqBHXvz7" class="review-platform-link" target="_blank" rel="noopener">Google</a>
          <a href="https://2gis.ru/krasnodar/search/%D0%B1%D1%8E%D1%80%D0%BE%20%D0%BF%D0%B5%D1%80%D0%B5%D0%B2%D0%BE%D0%B4%D0%BE%D0%B2%20%D1%80%D0%B5%D0%BC%D0%B0%D1%80%D0%BA%D0%B0/firm/70000001041287881" class="review-platform-link" target="_blank" rel="noopener">2ГИС</a>
        </div>
      </div>
      <div class="reviews-grid" style="grid-template-columns:repeat(3,1fr)">
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">А</div>
            <div class="review-meta"><div class="review-name">Артём Волков, CTO, стартап «CloudBase»</div><div class="review-src">Яндекс · ноябрь 2024</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Локализовали наш SaaS-продукт на английский. Переводчик самостоятельно разобрался с нашими XLIFF-файлами, учёл контекст UI-строк. Результат прошёл проверку носителями без замечаний.</p>
        </div>
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">С</div>
            <div class="review-meta"><div class="review-name">Светлана Кравец, Product Manager, «MobileApps»</div><div class="review-src">Google · август 2024</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Переводили App Store листинг и системные строки приложения на 5 языков. Координация отличная, сроки соблюдены, ASO-текст написан с учётом поисковых запросов.</p>
        </div>
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">Д</div>
            <div class="review-meta"><div class="review-name">Денис Миронов, Lead Developer</div><div class="review-src">2ГИС · май 2024</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Заказывали перевод технической документации к нашему API. Переводчик не только правильно передал смысл, но и заметил несколько неточностей в оригинале.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ FAQ ════════ -->
  <section class="sec sec-faq">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Часто задаваемые вопросы</h2>
        <p class="sec-sub">Не нашли ответа — напишите Ольге в чат, ответим за минуту</p>
      </div>
      <div class="faq-list">

        <details class="faq-item">
          <summary class="faq-summary">В каких форматах вы принимаете файлы на IT-перевод?</summary>
          <div class="faq-body">
            <p>Принимаем XLIFF (.xliff, .xlf), PO, POT, JSON, RESX, ARB, YAML, Properties, CSV, Excel, Word, Markdown, HTML и другие. Если ваш формат не упомянут — уточните, скорее всего работаем.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Вы работаете с Crowdin, Lokalise и Phrase?</summary>
          <div class="faq-body">
            <p>Да, интегрируемся в ваш L10n-процесс. Работаем напрямую в платформе или принимаем экспортированные файлы.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Как считается стоимость — по страницам или словам?</summary>
          <div class="faq-body">
            <p>Для IT-текстов стандартно считаем по словам (от 3 ₽/слово). Для документации в формате Word — по страницам (от 400 ₽/стр.). Выберем удобный для вас способ.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Переводите ли вы строки с переменными и плейсхолдерами?</summary>
          <div class="faq-body">
            <p>Да, переводчики знают, что {username}, %s и \\n трогать нельзя. При необходимости оставляем комментарии к неоднозначным строкам.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Можете ли вы локализовать мобильное приложение целиком?</summary>
          <div class="faq-body">
            <p>Да: UI-строки, App Store / Google Play листинг, push-уведомления, письма, юридические тексты. Координируем весь проект из одного места.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Где заказать IT-перевод в Москве?</summary>
          <div class="faq-body">
            <p>Бюро переводов «Ремарка» работает с IT-компаниями онлайн. Пришлите файлы в чат — скажем стоимость и сроки за несколько минут.</p>
          </div>
        </details>

      </div>
    </div>
  </section>

  <!-- ════════ КАЛЬКУЛЯТОР СТОИМОСТИ ════════ -->',
        'perevod-saytov' => '<section class="sec sec-intro">
    <div class="container">
      <div class="intro-grid">
        <div class="intro-text">
          <p class="intro-label">Бюро переводов «Ремарка», Москва</p>
          <h1 class="intro-title">Перевод сайтов в Москве</h1>
          <p class="intro-tagline">Перевод сайта — это не конвертация слов, а выход на рынок</p>
          <p class="intro-body">Перевод сайта отличается от перевода документа тем, что результат должен работать в поиске. «Дословный» перевод title и description — это путь к нулевым позициям на зарубежном рынке. Нужны ключевые слова, которые реально ищет аудитория в Германии, Китае или США, — и они отличаются от прямого перевода русских запросов.</p>
          <p class="intro-body">Перевод сайтов в московском бюро «Ремарка» включает: SEO-адаптацию мета-тегов и заголовков, корректную структуру hreflang, культурную адаптацию контента (тон, примеры, единицы измерения), перевод пользовательских соглашений и политики конфиденциальности под требования целевой юрисдикции.</p>
          <p class="intro-body">Работаем с WordPress, Tilda, Shopify, OpenCart, 1C-Битрикс и кастомными HTML-сайтами. При наличии доступа вносим переводы напрямую в CMS — вам не нужно разбираться в файлах локализации.</p>
        </div>
        <div class="intro-visual">
          <div class="intro-illus" aria-hidden="true">
            <svg viewBox="0 0 480 200" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect x="10" y="20" width="200" height="168" rx="12" fill="rgba(120,60,240,.10)" stroke="rgba(120,60,240,.35)" stroke-width="1.5"/>
              <rect x="10" y="20" width="200" height="38" rx="12" fill="rgba(120,60,240,.20)"/>
              <rect x="10" y="43" width="200" height="15" fill="rgba(120,60,240,.20)"/>
              <circle cx="30" cy="36" r="6" fill="rgba(255,100,100,.5)"/>
              <circle cx="46" cy="36" r="6" fill="rgba(255,200,0,.5)"/>
              <circle cx="62" cy="36" r="6" fill="rgba(0,200,100,.5)"/>
              <rect x="80" y="31" width="120" height="10" rx="5" fill="rgba(255,255,255,.25)"/>
              <rect x="24" y="72" width="168" height="7" rx="3.5" fill="rgba(167,139,250,.55)"/>
              <rect x="24" y="86" width="130" height="5" rx="2.5" fill="rgba(167,139,250,.30)"/>
              <rect x="24" y="100" width="150" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="114" width="110" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="130" width="168" height="28" rx="8" fill="rgba(120,60,240,.30)"/>
              <rect x="40" y="139" width="80" height="10" rx="5" fill="rgba(255,255,255,.5)"/>
              <rect x="24" y="164" width="80" height="16" rx="6" fill="rgba(0,160,240,.40)"/>
              <rect x="112" y="164" width="80" height="16" rx="6" fill="rgba(167,139,250,.30)"/>
              <circle cx="270" cy="104" r="42" fill="rgba(0,160,240,.12)" stroke="rgba(0,160,240,.30)" stroke-width="1.5"/>
              <circle cx="270" cy="104" r="26" fill="rgba(0,160,240,.18)"/>
              <path d="M212 104h26M296 104h42" stroke="rgba(0,160,240,.40)" stroke-width="1.5" stroke-dasharray="4 3"/>
              <path d="M255 104h30M273 96l10 8-10 8" stroke="rgba(0,160,240,.95)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <rect x="340" y="20" width="130" height="168" rx="12" fill="rgba(0,160,240,.10)" stroke="rgba(0,160,240,.35)" stroke-width="1.5"/>
              <rect x="340" y="20" width="130" height="38" rx="12" fill="rgba(0,160,240,.20)"/>
              <rect x="340" y="43" width="130" height="15" fill="rgba(0,160,240,.20)"/>
              <circle cx="358" cy="36" r="6" fill="rgba(255,100,100,.5)"/>
              <circle cx="374" cy="36" r="6" fill="rgba(255,200,0,.5)"/>
              <circle cx="390" cy="36" r="6" fill="rgba(0,200,100,.5)"/>
              <rect x="354" y="72" width="102" height="7" rx="3.5" fill="rgba(0,160,240,.55)"/>
              <rect x="354" y="86" width="80" height="5" rx="2.5" fill="rgba(0,160,240,.30)"/>
              <rect x="354" y="100" width="95" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="354" y="114" width="70" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="354" y="130" width="102" height="28" rx="8" fill="rgba(0,160,240,.30)"/>
              <rect x="368" y="139" width="60" height="10" rx="5" fill="rgba(255,255,255,.5)"/>
            </svg>
          </div>
          <div class="intro-features">
            <div class="intro-feat">
              <div class="intro-feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></div>
              <div><strong>SEO-адаптация</strong><span>Title, description, H1-H6, alt-атрибуты, ключевые слова целевого рынка</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></div>
              <div><strong>hreflang</strong><span>Готовый код тегов и рекомендации по URL-структуре</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg></div>
              <div><strong>CMS-интеграция</strong><span>WordPress, Tilda, Shopify, Bitrix, OpenCart, custom HTML</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
              <div><strong>Культурная адаптация</strong><span>Тон, образы, примеры, валюты, форматы дат под целевую аудиторию</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

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

  <!-- ════════ ЧТО МЫ ПЕРЕВОДИМ ════════ -->
  <section class="sec sec--alt sec-docs">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Какие сайты мы переводим</h2>
        <p class="sec-sub">Раскройте нужную категорию — узнайте особенности перевода</p>
      </div>
      <div class="docs-accordion">

        <details class="doc-item" open>
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            <span>Лендинги и корпоративные сайты</span>
          </summary>
          <div class="doc-body">
            <p>Главная страница, раздел «О компании», страницы услуг, контакты — с SEO-адаптацией заголовков и мета-тегов под целевой рынок</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            <span>Интернет-магазины и e-commerce</span>
          </summary>
          <div class="doc-body">
            <p>Карточки товаров, категории, фильтры, корзина, checkout, email-уведомления, политика возврата — с учётом локального законодательства</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            <span>Блоги и контентные сайты</span>
          </summary>
          <div class="doc-body">
            <p>Статьи, гайды, кейсы — перевод с сохранением SEO-ценности: внутренние ссылки, анкоры, структура заголовков</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            <span>Help-центры и базы знаний</span>
          </summary>
          <div class="doc-body">
            <p>Zendesk, Intercom, Freshdesk, Confluence — статьи поддержки, FAQ, руководства пользователя</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            <span>SaaS-продукты и маркетинговые страницы</span>
          </summary>
          <div class="doc-body">
            <p>Pricing-страницы, функциональные описания, отзывы клиентов, интеграционные страницы — с учётом отраслевой терминологии</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <span>Юридические страницы</span>
          </summary>
          <div class="doc-body">
            <p>Политика конфиденциальности, пользовательское соглашение, Cookie Policy, GDPR-уведомления, условия доставки и оплаты</p>
          </div>
        </details>

      </div>
    </div>
  </section>

  <!-- ════════ ПОДДЕРЖИВАЕМЫЕ CMS ════════ -->
  <section class="sec sec-translators">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Поддерживаемые CMS</h2>
        <p class="sec-sub">Работаем с основными системами управления контентом</p>
      </div>
      <div class="translators-grid">
        <div class="translator-card">
          <div class="translator-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
          </div>
          <h3>WordPress</h3>
          <p>WPML, Polylang, TranslatePress. Переводим через плагин или передаём готовый контент для ручного внесения.</p>
        </div>
        <div class="translator-card">
          <div class="translator-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8.56 2.75c4.37 6.03 6.02 9.42 8.03 17.72m2.54-15.38c-3.72 4.35-8.94 5.66-16.88 5.85m19.5 1.9c-3.5-.93-6.63-.82-8.94 0-2.58.92-5.01 2.86-7.44 6.32"/></svg>
          </div>
          <h3>Tilda</h3>
          <p>Экспорт текстов через Zero Block или ручная редактура — предоставляем готовые тексты для вставки.</p>
        </div>
        <div class="translator-card">
          <div class="translator-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          </div>
          <h3>Shopify</h3>
          <p>Работаем с Shopify Translate & Adapt или через CSV-экспорт товаров и страниц.</p>
        </div>
        <div class="translator-card">
          <div class="translator-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
          </div>
          <h3>OpenCart</h3>
          <p>Языковые файлы PHP, переводы товаров и категорий через CSV или напрямую в административной панели.</p>
        </div>
        <div class="translator-card">
          <div class="translator-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          </div>
          <h3>1C-Битрикс</h3>
          <p>Мультиязычные сайты на Битриксе — через встроенные языковые версии или экспорт контента.</p>
        </div>
        <div class="translator-card">
          <div class="translator-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
          </div>
          <h3>Custom HTML / React / Next.js</h3>
          <p>Кастомные сайты — принимаем HTML-файлы, JSON для i18n, или работаем с выгрузкой текстов в Excel.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ПРОЦЕСС РАБОТЫ ════════ -->
  <section class="sec sec--alt sec-how-pro">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Как проходит перевод сайта</h2>
        <p class="sec-sub">Шесть шагов от аудита сайта до готовой языковой версии с настроенным SEO</p>
      </div>
      <div class="steps-row steps-row--6">
        <div class="step-item">
          <div class="step-num">01</div>
          <h3>Аудит сайта</h3>
          <p>Анализируем структуру, определяем объём контента, CMS, наличие мета-тегов и технические особенности</p>
        </div>
        <div class="step-item">
          <div class="step-num">02</div>
          <h3>Извлечение контента</h3>
          <p>Выгружаем тексты через CMS, экспорт или парсинг — сохраняем структуру страниц и форматирование</p>
        </div>
        <div class="step-item">
          <div class="step-num">03</div>
          <h3>SEO-анализ</h3>
          <p>Подбираем ключевые слова для целевого рынка: семантика отличается от прямого перевода русских запросов</p>
        </div>
        <div class="step-item">
          <div class="step-num">04</div>
          <h3>Перевод с SEO</h3>
          <p>Переводим тексты, адаптируем title, description, H1-H6 и alt-атрибуты под целевые ключевые слова</p>
        </div>
        <div class="step-item">
          <div class="step-num">05</div>
          <h3>hreflang и URL</h3>
          <p>Готовим код hreflang-тегов, рекомендации по URL-структуре и sitemap для языковой версии</p>
        </div>
        <div class="step-item">
          <div class="step-num">06</div>
          <h3>QA и внедрение</h3>
          <p>Проверяем текст в контексте страниц, при необходимости вносим в CMS самостоятельно</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ SEO-ПРЕИМУЩЕСТВА ════════ -->
  <section class="sec sec-split">
    <div class="container">
      <div class="split-row split-row--rev">
        <div class="split-text">
          <p class="sec-label">SEO-локализация сайтов</p>
          <h2>Перевод, который работает в поиске</h2>
          <p>Перевести сайт дословно — значит получить версию, которую не найдут в Google или Bing. Аудитория в Германии ищет иначе, чем в России. Мы исследуем реальные поисковые запросы целевого рынка и встраиваем их в тексты, заголовки и мета-теги.</p>
          <p>hreflang — сигнал для Google о том, какая версия страницы предназначена для какой аудитории. Без него поисковик может показывать русскую версию немецким пользователям. Мы предоставляем готовый код тегов для вставки в &lt;head&gt;.</p>
          <ul class="split-checklist">
            <li>Локализованные title и meta description с целевыми ключевыми словами</li>
            <li>Готовый код hreflang для всех языковых версий</li>
            <li>Рекомендации по URL-структуре (поддомены vs. подпапки)</li>
            <li>Адаптация alt-атрибутов изображений</li>
            <li>Выявление изображений с встроенным текстом</li>
            <li>Перевод и адаптация Schema.org-разметки</li>
          </ul>
        </div>
        <div class="split-visual" aria-hidden="true">
          <svg viewBox="0 0 380 320" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <linearGradient id="sq-g1-s" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#783CF0"/><stop offset="100%" stop-color="#00A0F0"/></linearGradient>
              <linearGradient id="sq-g2-s" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#783CF0" stop-opacity=".5"/><stop offset="100%" stop-color="#00A0F0" stop-opacity=".5"/></linearGradient>
            </defs>
            <circle cx="190" cy="160" r="130" fill="rgba(120,60,240,.07)"/>
            <rect x="40" y="20" width="300" height="52" rx="14" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
            <circle cx="72" cy="46" r="18" fill="url(#sq-g1-s)" opacity=".85"/>
            <path d="M62 46c0-5.52 4.48-10 10-10s10 4.48 10 10" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/>
            <line x1="62" y1="46" x2="62" y2="56" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/>
            <line x1="82" y1="46" x2="82" y2="56" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/>
            <rect x="100" y="33" width="180" height="9" rx="4" fill="rgba(167,139,250,.55)"/>
            <rect x="100" y="48" width="130" height="7" rx="3" fill="rgba(167,139,250,.30)"/>
            <line x1="190" y1="72" x2="190" y2="96" stroke="url(#sq-g2-s)" stroke-width="2" stroke-dasharray="4 3"/>
            <rect x="40" y="96" width="300" height="52" rx="14" fill="rgba(0,160,240,.12)" stroke="rgba(0,160,240,.30)" stroke-width="1.5"/>
            <circle cx="72" cy="122" r="18" fill="url(#sq-g1-s)" opacity=".85"/>
            <circle cx="72" cy="122" r="9" stroke="#fff" stroke-width="1.8" fill="none"/>
            <line x1="79" y1="129" x2="86" y2="136" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
            <rect x="100" y="109" width="200" height="9" rx="4" fill="rgba(0,160,240,.55)"/>
            <rect x="100" y="124" width="150" height="7" rx="3" fill="rgba(0,160,240,.30)"/>
            <line x1="190" y1="148" x2="190" y2="172" stroke="url(#sq-g2-s)" stroke-width="2" stroke-dasharray="4 3"/>
            <rect x="40" y="172" width="300" height="52" rx="14" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
            <circle cx="72" cy="198" r="18" fill="url(#sq-g1-s)" opacity=".85"/>
            <path d="M64 198l8 8 14-14" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="100" y="185" width="220" height="9" rx="4" fill="rgba(167,139,250,.55)"/>
            <rect x="100" y="200" width="170" height="7" rx="3" fill="rgba(167,139,250,.30)"/>
            <line x1="190" y1="224" x2="190" y2="248" stroke="url(#sq-g2-s)" stroke-width="2" stroke-dasharray="4 3"/>
            <circle cx="310" cy="270" r="28" fill="rgba(0,200,100,.12)" stroke="rgba(0,200,100,.35)" stroke-width="1.5"/>
            <path d="M299 270l9 9 16-18" stroke="rgba(0,200,100,.9)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ГАРАНТИИ КАЧЕСТВА ════════ -->
  <section class="sec sec-guarantees">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Наши гарантии</h2>
        <p class="sec-sub">Мы отвечаем за результат — не только словами</p>
      </div>
      <div class="guarantees-grid">
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.9"/></svg>
          </div>
          <h3>Бесплатная правка</h3>
          <p>Исправим бесплатно в течение 7 дней, если перевод не соответствует заданию или содержит неточности</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </div>
          <h3>Конфиденциальность</h3>
          <p>NDA по запросу. Доступ к CMS и контент сайта не передаются третьим лицам и не хранятся на внешних серверах</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </div>
          <h3>Соблюдение сроков</h3>
          <p>Если не уложились в оговорённый срок по нашей вине — возвращаем предоплату или делаем скидку на следующий заказ</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          </div>
          <h3>Translation Memory</h3>
          <p>Сохраняем переводы вашего сайта — при обновлении контента платите только за новые и изменённые тексты</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ СРОКИ И СТОИМОСТЬ ════════ -->
  <section class="sec sec--alt sec-vol-pricing">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Сроки и стоимость</h2>
        <p class="sec-sub">Оплата по страницам, по словам или фиксированная цена на весь сайт. Точный расчёт — бесплатно</p>
      </div>
      <div class="vol-table-wrap">
        <table class="vol-table">
          <thead>
            <tr>
              <th>Объём</th>
              <th>Срок</th>
              <th>Стоимость</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>До 10 страниц (лендинг)</strong></td>
              <td>2–3 рабочих дня</td>
              <td class="vol-price">от 400 ₽/стр.</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>10–50 страниц (сайт)</strong></td>
              <td>4–7 рабочих дней</td>
              <td class="vol-price">от 3 ₽/слово</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>50–200 страниц (интернет-магазин)</strong></td>
              <td>7–14 рабочих дней</td>
              <td class="vol-price">от 2,8 ₽/слово</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>Весь сайт (пакет)</strong></td>
              <td>по договорённости</td>
              <td class="vol-price">индивидуально</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
          </tbody>
        </table>
      </div>
      <p class="vol-note">SEO-адаптация мета-тегов и рекомендации по hreflang включены в стоимость. Внесение в CMS — по договорённости.</p>
    </div>
  </section>

  <!-- ════════ СРАВНЕНИЕ ФОРМАТОВ ════════ -->
  <section class="sec sec-comparison">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Какой формат вам подходит</h2>
        <p class="sec-sub">Сравните три уровня сервиса и выберите оптимальный для вашей задачи</p>
      </div>
      <div class="cmp-table-wrap">
        <table class="cmp-table">
          <thead>
            <tr>
              <th></th>
              <th>Постредактирование ИИ</th>
              <th class="cmp-featured">Профессиональный перевод</th>
              <th>Премиум-перевод</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>Перевод с нуля профессиональным переводчиком</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>SEO-адаптация мета-тегов и заголовков</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Рекомендации по hreflang</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Культурная адаптация контента</td><td class="cmp-no">✗</td><td class="cmp-part cmp-featured">частично</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Вычитка носителем языка</td><td class="cmp-no">✗</td><td class="cmp-no cmp-featured">✗</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Подходит для публичного запуска</td><td class="cmp-no">✗</td><td class="cmp-part cmp-featured">условно</td><td class="cmp-yes">✓</td></tr>
            <tr class="cmp-meta"><td>Срок</td><td>от 1 дня</td><td class="cmp-featured">2–5 дней</td><td>3–7 дней</td></tr>
            <tr class="cmp-meta cmp-price-row"><td>Стоимость</td><td>от 1,5 ₽/слово</td><td class="cmp-featured">от 3 ₽/слово</td><td>от 5 ₽/слово</td></tr>
          </tbody>
          <tfoot>
            <tr>
              <td></td>
              <td><a href="#calc-section" class="price-btn price-btn--outline">Выбрать</a></td>
              <td class="cmp-featured"><a href="#calc-section" class="price-btn price-btn--primary">Выбрать</a></td>
              <td><a href="#calc-section" class="price-btn price-btn--outline">Выбрать</a></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </section>

  <!-- ════════ ОТЗЫВЫ ════════ -->
  <section class="sec sec--alt sec-reviews">
    <div class="container">
      <div class="reviews-head">
        <div>
          <h2 class="sec-title">Что говорят клиенты</h2>
          <p class="sec-sub" style="margin-top:6px">Реальные отзывы о переводе сайтов</p>
        </div>
        <div class="review-rating-block">
          <div class="review-rating-score">4.98</div>
          <div class="review-stars-row">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          </div>
          <div class="review-rating-label">средний рейтинг</div>
        </div>
        <div class="review-platform-links">
          <a href="https://yandex.ru/maps/org/remarka/51867347382/reviews/" class="review-platform-link" target="_blank" rel="noopener">Яндекс</a>
          <a href="https://maps.app.goo.gl/d8BKJYw81PqBHXvz7" class="review-platform-link" target="_blank" rel="noopener">Google</a>
          <a href="https://2gis.ru/moscow/search/%D0%B1%D1%8E%D1%80%D0%BE%20%D0%BF%D0%B5%D1%80%D0%B5%D0%B2%D0%BE%D0%B4%D0%BE%D0%B2%20%D1%80%D0%B5%D0%BC%D0%B0%D1%80%D0%BA%D0%B0" class="review-platform-link" target="_blank" rel="noopener">2ГИС</a>
        </div>
      </div>
      <div class="reviews-grid" style="grid-template-columns:repeat(3,1fr)">
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">И</div>
            <div class="review-meta"><div class="review-name">Ирина Соловьёва, Директор по маркетингу, «МеталлТрейд»</div><div class="review-src">Яндекс · март 2025</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Переводили корпоративный сайт на английский и немецкий для выхода на европейский рынок. Ремарка не только перевела тексты, но и адаптировала SEO-мета и дала рекомендации по hreflang. Через 3 месяца позиции в Google.de есть.</p>
        </div>
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">А</div>
            <div class="review-meta"><div class="review-name">Александр Чернов, владелец, интернет-магазин «TechGadgets»</div><div class="review-src">Google · январь 2025</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Перевели Shopify-магазин на польский и чешский — около 800 карточек товаров плюс страницы. Ремарка работала через CSV-экспорт, вернули готовый файл, я просто импортировал. Всё оформление и переменные сохранились.</p>
        </div>
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">О</div>
            <div class="review-meta"><div class="review-name">Ольга Рябова, SEO-специалист, «DigitalPromo»</div><div class="review-src">2ГИС · октябрь 2024</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Заказывала перевод блога клиента (около 60 статей) на английский. Переводчики понимают SEO-требования — title не просто перевели, а переписали под английскую семантику. Это заметно по CTR в Google Search Console.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ FAQ ════════ -->
  <section class="sec sec-faq">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Часто задаваемые вопросы</h2>
        <p class="sec-sub">Не нашли ответа — напишите Ольге в чат, ответим за минуту</p>
      </div>
      <div class="faq-list">

        <details class="faq-item">
          <summary class="faq-summary">Вы настраиваете hreflang для переведённых страниц?</summary>
          <div class="faq-body">
            <p>Да, предоставляем готовый код hreflang-тегов для каждой языковой версии страницы. Даём рекомендации по URL-структуре (поддомены, подпапки или параметры) с учётом требований Google и Яндекса. Hreflang без ошибок — ключевое условие правильной индексации мультиязычного сайта.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Вы можете работать напрямую в CMS — WordPress, Tilda, Shopify?</summary>
          <div class="faq-body">
            <p>Да, при наличии редакторского доступа работаем напрямую в CMS. Для WordPress интегрируемся с WPML, Polylang или TranslatePress. Для Tilda и Shopify — через экспорт/импорт или ручную редактуру. Для 1C-Битрикс — через встроенный мультиязычный модуль.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Что делать с изображениями, на которых есть текст?</summary>
          <div class="faq-body">
            <p>В процессе работы выявляем все изображения с встроенным текстом и предоставляем полный список с переводами. Если у вас есть исходники (PSD, Figma, Illustrator) — указываем, что именно нужно заменить. Замену графики выполняет ваш дизайнер или мы можем организовать это отдельно.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Как переводить динамический контент — отзывы, каталог, блог?</summary>
          <div class="faq-body">
            <p>Предлагаем две модели: разовый перевод текущего контента с последующим регулярным сопровождением (по заявкам), или абонентское обслуживание с фиксированным объёмом слов в месяц. TM сохраняется — повторяющийся контент переводится быстрее и дешевле.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Адаптируете ли вы мета-теги под SEO целевого рынка?</summary>
          <div class="faq-body">
            <p>Да, это стандартная часть нашего сервиса при переводе сайтов. Исследуем реальные поисковые запросы целевого рынка (не прямой перевод русских ключевых слов) и встраиваем их в title, description и H1. Дополнительная плата за SEO-адаптацию мета-тегов не взимается.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Где заказать перевод сайта в Москве?</summary>
          <div class="faq-body">
            <p>Бюро переводов «Ремарка» работает с сайтами любого масштаба — от одностраничных лендингов до крупных интернет-магазинов. Пришлите ссылку на сайт в чат — скажем стоимость, срок и предложим оптимальную схему работы.</p>
          </div>
        </details>

      </div>
    </div>
  </section>

  <!-- ════════ КАЛЬКУЛЯТОР СТОИМОСТИ ════════ -->',
        'finansovyy-perevod' => '<section class="sec sec-intro">
    <div class="container">
      <div class="intro-grid">
        <div class="intro-text">
          <p class="intro-label">Бюро переводов «Ремарка», Москва</p>
          <h1 class="intro-title">Финансовый перевод в Москве</h1>
          <p class="intro-tagline">Каждая цифра имеет значение</p>
          <p class="intro-body">Финансовый документ — не просто текст с цифрами. Одна неточно переведённая статья МСФО-отчётности или неверно интерпретированное понятие GAAP может исказить картину финансового положения компании для инвестора или регулятора. Переводчик без экономического образования не понимает разницы между EBITDA и операционной прибылью, не знает, что «accrual basis» — это метод начисления, а не «база начислений».</p>
          <p class="intro-body">Финансовый перевод в московском бюро «Ремарка» выполняют специалисты с двойной квалификацией: финансово-экономическое образование плюс лингвистическая подготовка. Они понимают регуляторные требования, умеют работать с финансовыми моделями, таблицами и сложными примечаниями к отчётности.</p>
          <p class="intro-body">Работаем только с корпоративными клиентами: финансовые директора, юридические и финансовые департаменты, инвестиционные банки, аудиторы. NDA подписывается до передачи файлов.</p>
        </div>
        <div class="intro-visual">
          <div class="intro-illus" aria-hidden="true">
            <svg viewBox="0 0 480 200" fill="none" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <linearGradient id="ip-g1" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#783CF0"/><stop offset="100%" stop-color="#00A0F0"/></linearGradient>
              </defs>
              <rect x="10" y="20" width="140" height="168" rx="12" fill="rgba(120,60,240,.10)" stroke="rgba(120,60,240,.35)" stroke-width="1.5"/>
              <rect x="10" y="20" width="140" height="38" rx="12" fill="rgba(120,60,240,.20)"/>
              <rect x="10" y="43" width="140" height="15" fill="rgba(120,60,240,.20)"/>
              <rect x="24" y="29" width="32" height="16" rx="8" fill="rgba(0,160,240,.40)"/>
              <rect x="28" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="42" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="24" y="76" width="112" height="5" rx="2.5" fill="rgba(167,139,250,.45)"/>
              <rect x="24" y="88" width="84" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="100" width="102" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="112" width="70" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="124" width="110" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="136" width="90" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="148" width="98" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="160" width="76" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="330" y="20" width="140" height="168" rx="12" fill="rgba(0,160,240,.10)" stroke="rgba(0,160,240,.35)" stroke-width="1.5"/>
              <rect x="330" y="20" width="140" height="38" rx="12" fill="rgba(0,160,240,.20)"/>
              <rect x="330" y="43" width="140" height="15" fill="rgba(0,160,240,.20)"/>
              <rect x="344" y="29" width="32" height="16" rx="8" fill="rgba(120,60,240,.40)"/>
              <rect x="348" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="362" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="344" y="76" width="112" height="5" rx="2.5" fill="rgba(0,160,240,.45)"/>
              <rect x="344" y="88" width="96" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="100" width="86" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="112" width="112" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="124" width="74" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="136" width="106" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="148" width="88" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="160" width="100" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <circle cx="240" cy="104" r="46" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
              <circle cx="240" cy="104" r="30" fill="rgba(120,60,240,.18)"/>
              <path d="M152 104h56M288 104h40" stroke="rgba(120,60,240,.40)" stroke-width="1.5" stroke-dasharray="4 3"/>
              <path d="M224 104h32M246 96l10 8-10 8" stroke="rgba(167,139,250,.95)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="intro-features">
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
              </div>
              <div><strong>Финансовое образование</strong><span>Переводчик понимает МСФО, GAAP, ФСБУ — не только язык</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              </div>
              <div><strong>Конфиденциальность</strong><span>NDA до передачи файлов, документы не покидают защищённый контур</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
              </div>
              <div><strong>Точность таблиц</strong><span>Числа и суммы вычитываются дважды, форматирование сохраняется</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
              </div>
              <div><strong>Глоссарий клиента</strong><span>Единая терминология во всех отчётах и документах компании</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ЦИФРЫ ════════ -->
  <section class="sec-stats">
    <div class="container">
      <div class="stats-row">
        <div class="stat-item">
          <span class="stat-num">25<span class="stat-suffix">+</span></span>
          <span class="stat-label">лет на рынке</span>
        </div>
        <div class="stat-item">
          <span class="stat-num">60<span class="stat-suffix">+</span></span>
          <span class="stat-label">языков перевода</span>
        </div>
        <div class="stat-item">
          <span class="stat-num">2 400<span class="stat-suffix">+</span></span>
          <span class="stat-label">выполненных заказов</span>
        </div>
        <div class="stat-item">
          <span class="stat-num">4.98<span class="stat-suffix">★</span></span>
          <span class="stat-label">средний рейтинг</span>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ЯЗЫКОВЫЕ ПАРЫ ════════ -->
  <section class="sec sec--alt sec-lang-pairs">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Языковые пары для финансовых документов</h2>
        <p class="sec-sub">Переводчики знают финансовые стандарты страны оригинала — не только язык</p>
      </div>
      <div class="fl-grid" style="grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:20px">
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">EN ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Английский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">SEC filings, US GAAP, IFRS (IASB), проспекты для LSE/NYSE, аудиторские заключения Big Four</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">DE ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Немецкий</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">HGB, IFRS по немецким стандартам, Jahresabschluss, финансовые документы DACH-компаний</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">FR ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Французский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Plan Comptable Général, IFRS-отчётность французских компаний, проспекты AMF</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">ZH ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Китайский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Финансовая отчётность китайских компаний (CAS), документы CSRC, инвестиционные меморандумы по сделкам с КНР</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">AR ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Арабский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Финансовые документы ОАЭ, Саудовской Аравии, исламские финансовые инструменты (сукук), документы Gulf Cooperation Council</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">IT ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Итальянский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Bilancio d\'esercizio, отчётность по итальянским GAAP и IFRS, финансовые договоры с итальянскими банками</p>
        </div>
      </div>
      <p style="text-align:center;font-size:13px;color:var(--text-secondary)">Другие языки — японский, корейский, португальский, нидерландский и ещё 50+. <a href="#calc-section" style="color:var(--accent)">Уточните наличие переводчика</a></p>
    </div>
  </section>

  <!-- ════════ КАКИЕ ДОКУМЕНТЫ ════════ -->
  <section class="sec sec-docs">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Какие документы мы переводим</h2>
        <p class="sec-sub">Раскройте нужную категорию — посмотрите конкретные виды документов</p>
      </div>
      <div class="docs-accordion">

        <details class="doc-item" open>
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            <span>Годовые отчёты</span>
          </summary>
          <div class="doc-body">
            <p>Годовые и полугодовые отчёты компаний для акционеров, инвесторов и регуляторов. Сохраняем структуру, инфографику, таблицы и примечания к отчётности.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
            <span>Финансовая отчётность МСФО/GAAP</span>
          </summary>
          <div class="doc-body">
            <p>Отчёты о финансовом положении, о прибылях и убытках, о движении денежных средств, об изменениях капитала. Примечания к финансовой отчётности. Применяем корректную терминологию МСФО (IFRS), US GAAP и ФСБУ.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <span>Аудиторские заключения</span>
          </summary>
          <div class="doc-body">
            <p>Аудиторские заключения с выражением мнения, заключения по специальным вопросам, отчёты о согласованных процедурах. Знаем стандарты ISA и МСА.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            <span>Проспекты эмиссии облигаций</span>
          </summary>
          <div class="doc-body">
            <p>Проспекты для размещения облигаций на биржах (Московская биржа, LSE, Eurobond), базовые проспекты, дополнения к проспектам. Знаем требования регуляторов.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 3H8a2 2 0 0 0-2 2v2h12V5a2 2 0 0 0-2-2z"/></svg>
            <span>Банковская документация</span>
          </summary>
          <div class="doc-body">
            <p>Кредитные соглашения, договоры синдицированного кредитования, соглашения об обеспечении, банковские гарантии, аккредитивы, ISDA-соглашения.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            <span>Инвестиционные меморандумы</span>
          </summary>
          <div class="doc-body">
            <p>Инвестиционные меморандумы, информационные меморандумы (IM), тизеры для M&A-сделок, материалы pre-IPO road show, отчёты по due diligence.</p>
          </div>
        </details>

      </div>
    </div>
  </section>

  <!-- ════════ КОНТРОЛЬ КАЧЕСТВА — SPLIT SECTION ════════ -->
  <section class="sec sec-split">
    <div class="container">
      <div class="split-row split-row--rev">
        <div class="split-text">
          <p class="sec-label">Финансовая точность</p>
          <h2>Почему финансовый перевод требует особого подхода</h2>
          <p>Финансовые документы содержат термины, строго определённые международными стандартами. «Revenue» в US GAAP и «Revenue» в IFRS 15 — схожие, но не идентичные понятия. «Provision» — это и резерв, и оценочное обязательство в зависимости от контекста. Ошибка в терминологии меняет финансовый смысл.</p>
          <p>Наши переводчики прошли подготовку по финансам и бухгалтерскому учёту. Они понимают, как правильно передать понятия МСФО, GAAP и ФСБУ, умеют работать с консолидированной отчётностью и примечаниями.</p>
          <ul class="split-checklist">
            <li>Переводчик с финансово-экономическим образованием</li>
            <li>Знание МСФО (IFRS), US GAAP, ФСБУ, HGB</li>
            <li>NDA до передачи файлов — строгий режим конфиденциальности</li>
            <li>Двойная проверка числовых данных и таблиц</li>
          </ul>
        </div>
        <div class="split-visual" aria-hidden="true">
          <svg viewBox="0 0 380 320" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <linearGradient id="sq-g1-f" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#783CF0"/><stop offset="100%" stop-color="#00A0F0"/></linearGradient>
              <linearGradient id="sq-g2-f" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#783CF0" stop-opacity=".5"/><stop offset="100%" stop-color="#00A0F0" stop-opacity=".5"/></linearGradient>
            </defs>
            <circle cx="190" cy="160" r="130" fill="rgba(120,60,240,.07)"/>
            <rect x="60" y="30" width="260" height="64" rx="16" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="62" r="20" fill="url(#sq-g1-f)" opacity=".85"/>
            <path d="M88 62l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="50" width="170" height="9" rx="4" fill="rgba(167,139,250,.55)"/>
            <rect x="126" y="66" width="120" height="7" rx="3" fill="rgba(167,139,250,.30)"/>
            <line x1="190" y1="94" x2="190" y2="118" stroke="url(#sq-g2-f)" stroke-width="2" stroke-dasharray="4 3"/>
            <rect x="60" y="118" width="260" height="64" rx="16" fill="rgba(0,160,240,.12)" stroke="rgba(0,160,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="150" r="20" fill="url(#sq-g1-f)" opacity=".85"/>
            <path d="M88 150l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="138" width="155" height="9" rx="4" fill="rgba(0,160,240,.55)"/>
            <rect x="126" y="154" width="110" height="7" rx="3" fill="rgba(0,160,240,.30)"/>
            <line x1="190" y1="182" x2="190" y2="206" stroke="url(#sq-g2-f)" stroke-width="2" stroke-dasharray="4 3"/>
            <rect x="60" y="206" width="260" height="64" rx="16" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="238" r="20" fill="url(#sq-g1-f)" opacity=".85"/>
            <path d="M88 238l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="226" width="175" height="9" rx="4" fill="rgba(167,139,250,.55)"/>
            <rect x="126" y="242" width="130" height="7" rx="3" fill="rgba(167,139,250,.30)"/>
            <circle cx="310" cy="290" r="26" fill="rgba(0,200,100,.12)" stroke="rgba(0,200,100,.35)" stroke-width="1.5"/>
            <path d="M300 290l8 8 14-16" stroke="rgba(0,200,100,.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ КАК ПРОХОДИТ РАБОТА (6 шагов) ════════ -->
  <section class="sec sec--alt sec-how-pro">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Как проходит работа</h2>
        <p class="sec-sub">Шесть шагов от финансового документа до готового перевода — с контролем на каждом этапе</p>
      </div>
      <div class="steps-row steps-row--6">
        <div class="step-item">
          <div class="step-num">01</div>
          <h3>Загрузите документ</h3>
          <p>Через сайт, мессенджер, email. Принимаем PDF, DOCX, XLSX, PPT и другие форматы</p>
        </div>
        <div class="step-item">
          <div class="step-num">02</div>
          <h3>Анализ и NDA</h3>
          <p>Оцениваем объём и сложность. Переводчик подписывает NDA до получения файлов</p>
        </div>
        <div class="step-item">
          <div class="step-num">03</div>
          <h3>Подбор специалиста</h3>
          <p>Выбираем переводчика с финансово-экономическим образованием под ваш тип документа</p>
        </div>
        <div class="step-item">
          <div class="step-num">04</div>
          <h3>Перевод</h3>
          <p>Специалист работает с применением глоссария МСФО/GAAP, соблюдает форматирование</p>
        </div>
        <div class="step-item">
          <div class="step-num">05</div>
          <h3>Финансовая редактура</h3>
          <p>Финансовый редактор проверяет корректность терминологии и точность числовых данных</p>
        </div>
        <div class="step-item">
          <div class="step-num">06</div>
          <h3>Готовый перевод</h3>
          <p>Передаём файл в исходном формате — точно в срок, с сохранением структуры и таблиц</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ГАРАНТИИ КАЧЕСТВА ════════ -->
  <section class="sec sec-guarantees">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Наши гарантии</h2>
        <p class="sec-sub">Мы отвечаем за результат — не только словами</p>
      </div>
      <div class="guarantees-grid">
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.9"/></svg>
          </div>
          <h3>Бесплатная правка</h3>
          <p>Исправим бесплатно в течение 7 дней, если перевод не соответствует заданию или стандартам МСФО/GAAP</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </div>
          <h3>Конфиденциальность</h3>
          <p>NDA до передачи файлов. Документы не передаются третьим лицам и не хранятся на внешних серверах</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </div>
          <h3>Соблюдение сроков</h3>
          <p>Если не уложились в оговорённый срок по нашей вине — возвращаем предоплату или даём скидку на следующий заказ</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
          </div>
          <h3>Глоссарий МСФО/GAAP</h3>
          <p>Сохраняем терминологический глоссарий вашей компании. Единая терминология во всех отчётах и документах</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ СРОКИ И СТОИМОСТЬ ════════ -->
  <section class="sec sec--alt sec-vol-pricing">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Сроки и стоимость</h2>
        <p class="sec-sub">Цена зависит от объёма и сложности документа. Точный расчёт — бесплатно, за несколько минут</p>
      </div>
      <div class="vol-table-wrap">
        <table class="vol-table">
          <thead>
            <tr>
              <th>Объём</th>
              <th>Срок</th>
              <th>Стоимость</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>До 10 страниц</strong></td>
              <td>1–2 рабочих дня</td>
              <td class="vol-price">от 4 500 ₽</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>10–30 страниц</strong></td>
              <td>2–4 рабочих дня</td>
              <td class="vol-price">от 450 ₽/стр.</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>30–100 страниц</strong></td>
              <td>4–10 рабочих дней</td>
              <td class="vol-price">от 420 ₽/стр.</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>Свыше 100 страниц</strong></td>
              <td>по договорённости</td>
              <td class="vol-price">от 390 ₽/стр.</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
          </tbody>
        </table>
      </div>
      <p class="vol-note">Срочный перевод (менее 24 часов) — коэффициент ×1,5 к базовой стоимости. Для крупных проектов (годовые отчёты, due diligence) — индивидуальное согласование сроков и стоимости.</p>
    </div>
  </section>

  <!-- ════════ СРАВНЕНИЕ ФОРМАТОВ ════════ -->
  <section class="sec sec-comparison">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Какой формат вам подходит</h2>
        <p class="sec-sub">Сравните три уровня сервиса и выберите оптимальный для вашей задачи</p>
      </div>
      <div class="cmp-table-wrap">
        <table class="cmp-table">
          <thead>
            <tr>
              <th></th>
              <th>Постредактирование ИИ</th>
              <th class="cmp-featured">Профессиональный перевод</th>
              <th>Премиум-перевод</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>Переводчик с экономическим/финансовым образованием</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Терминология МСФО/GAAP/ФСБУ</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>NDA до передачи файлов</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>TM клиента (закрытая база)</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Корректура финансовым редактором</td><td class="cmp-no">✗</td><td class="cmp-no cmp-featured">✗</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Вычитка носителем языка</td><td class="cmp-no">✗</td><td class="cmp-no cmp-featured">✗</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Подходит для аудиторов / регуляторов</td><td class="cmp-no">✗</td><td class="cmp-part cmp-featured">условно</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Оговорка переводчика (certification)</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr class="cmp-meta"><td>Срок</td><td>от 1 дня</td><td class="cmp-featured">1–4 дня</td><td>2–7 дней</td></tr>
            <tr class="cmp-meta cmp-price-row"><td>Стоимость</td><td>от 280 ₽/стр.</td><td class="cmp-featured">от 450 ₽/стр.</td><td>от 750 ₽/стр.</td></tr>
          </tbody>
          <tfoot>
            <tr>
              <td></td>
              <td><a href="#calc-section" class="price-btn price-btn--outline">Выбрать</a></td>
              <td class="cmp-featured"><a href="#calc-section" class="price-btn price-btn--primary">Выбрать</a></td>
              <td><a href="#calc-section" class="price-btn price-btn--outline">Выбрать</a></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </section>

  <!-- ════════ СПРАВОЧНИК ПО ДОКУМЕНТАМ ════════ -->
  <section class="sec sec--alt sec-doc-reference">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Справочник по документам</h2>
        <p class="sec-sub">Выберите тип финансового документа — узнайте особенности перевода</p>
      </div>
      <div class="doc-ref-grid">
        <a href="#calc-section" class="doc-ref-card">
          <strong>Годовые отчёты</strong>
          <span>Корпоративные annual reports для акционеров и инвесторов</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>МСФО-отчётность</strong>
          <span>Финансовая отчётность по стандартам IFRS</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>GAAP-отчётность</strong>
          <span>Финансовая отчётность по US GAAP для SEC и инвесторов</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Аудиторские заключения</strong>
          <span>Заключения Big Four и других аудиторских фирм</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Проспекты эмиссии облигаций</strong>
          <span>Документы для размещения на LSE, Московской бирже, Eurobond</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Инвестиционные меморандумы</strong>
          <span>IM, тизеры и материалы для M&amp;A road show</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Банковские соглашения</strong>
          <span>Кредитные договоры, ISDA, синдицированные кредиты</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Договоры факторинга</strong>
          <span>Факторинговые соглашения и документы по торговому финансированию</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Отчёты рейтинговых агентств</strong>
          <span>Рейтинговые отчёты Moody\'s, S&amp;P, Fitch, АКРА, Эксперт РА</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Финансовые модели</strong>
          <span>Описания финансовых моделей, DCF-анализ, презентации оценки</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Tax due diligence</strong>
          <span>Налоговые заключения и отчёты по налоговой проверке</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Материалы для IPO</strong>
          <span>Проспекты, раскрытие информации, материалы для инвесторов</span>
        </a>
      </div>
    </div>
  </section>

  <!-- ════════ ОТЗЫВЫ ════════ -->
  <section class="sec sec--alt sec-reviews">
    <div class="container">
      <div class="reviews-head">
        <div>
          <h2 class="sec-title">Что говорят клиенты</h2>
          <p class="sec-sub" style="margin-top:6px">Реальные отзывы о финансовых переводах</p>
        </div>
        <div class="review-rating-block">
          <div class="review-rating-score">4.98</div>
          <div class="review-stars-row">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          </div>
          <div class="review-rating-label">средний рейтинг</div>
        </div>
        <div class="review-platform-links">
          <a href="https://yandex.ru/maps/org/remarka/51867347382/reviews/" class="review-platform-link" target="_blank" rel="noopener">Яндекс</a>
          <a href="https://maps.app.goo.gl/d8BKJYw81PqBHXvz7" class="review-platform-link" target="_blank" rel="noopener">Google</a>
        </div>
      </div>
      <div class="reviews-grid" style="grid-template-columns:repeat(3,1fr)">
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">М</div>
            <div class="review-meta"><div class="review-name">Михаил Тарасов</div><div class="review-src">Яндекс · февраль 2025</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Переводили МСФО-отчётность за три года для иностранного акционера. Терминология выдержана единообразно во всех периодах, числа выверены. Аудитор замечаний не имел.</p>
          <div class="review-author-role">Финансовый директор, производственная группа</div>
        </div>
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">Е</div>
            <div class="review-meta"><div class="review-name">Екатерина Волкова</div><div class="review-src">Google · ноябрь 2024</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Работаем с бюро на переводе инвестиционных меморандумов для M&A-сделок. Понимают финансовый контекст, NDA соблюдается строго. Рекомендую коллегам.</p>
          <div class="review-author-role">Директор, инвестиционный банк</div>
        </div>
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">А</div>
            <div class="review-meta"><div class="review-name">Андрей Кириллов</div><div class="review-src">Яндекс · август 2024</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Заказывали перевод аудиторских заключений для иностранного банка-кредитора. Переводчик хорошо знает стандарты ISA, документ принят без доработок.</p>
          <div class="review-author-role">Партнёр, аудиторская фирма</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ FAQ ════════ -->
  <section class="sec sec-faq">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Часто задаваемые вопросы</h2>
        <p class="sec-sub">Не нашли ответа — напишите Ольге в чат, ответим за минуту</p>
      </div>
      <div class="faq-list">

        <details class="faq-item">
          <summary class="faq-summary">В чём разница между терминологией МСФО и GAAP при переводе?</summary>
          <div class="faq-body">
            <p>МСФО (IFRS) и US GAAP — разные стандарты с разными определениями статей. Например, IFRS допускает оценку запасов по ФИФО или средней стоимости, тогда как US GAAP также разрешает ЛИФО (хотя практически выведен из употребления). «Goodwill» тестируется на обесценение по-разному. Наши переводчики знают обе стандарты и применяют корректную русскую терминологию в зависимости от применимого стандарта.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Как вы переводите финансовые таблицы?</summary>
          <div class="faq-body">
            <p>Таблицы переносятся с сохранением структуры и форматирования. Все числовые данные вычитываются дважды — переводчиком и финансовым редактором. Разделители тысяч и десятичные знаки адаптируются под целевую аудиторию (пробел + запятая для русского, запятая + точка для английского). Итоговые суммы сверяются с исходным документом.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Как обеспечивается конфиденциальность M&A-документов?</summary>
          <div class="faq-body">
            <p>Каждый переводчик подписывает NDA до получения файлов. Для M&A и pre-IPO материалов работаем с закрытой Translation Memory — данные клиента не попадают в общие облачные базы и не используются для обучения ИИ-систем. По запросу заключаем корпоративное соглашение с расширенной ответственностью. Документы хранятся только на защищённых серверах.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Нужен ли заверенный перевод финансовой отчётности для регулятора?</summary>
          <div class="faq-body">
            <p>Требования различаются в зависимости от регулятора. ЦБ РФ, SEC, FCA и другие регуляторы предъявляют разные требования к оформлению переводов. Как правило, требуется оговорка переводчика или нотариальное заверение. Мы организуем оба варианта и бесплатно уточним требования конкретного регулятора перед началом работы.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Каковы сроки перевода годовых отчётов?</summary>
          <div class="faq-body">
            <p>Годовой отчёт на 100–200 страниц переводится за 10–15 рабочих дней в стандартном режиме. При срочной необходимости подключаем команду специализированных переводчиков с единым глоссарием — сроки согласуем индивидуально. Рекомендуем планировать перевод заранее: это позволяет сохранить комфортный срок и оптимальную стоимость.</p>
          </div>
        </details>

      </div>
    </div>
  </section>

  <!-- ════════ КАЛЬКУЛЯТОР СТОИМОСТИ ════════ -->',
        'marketingovyy-perevod' => '<section class="sec sec-intro">
    <div class="container">
      <div class="intro-grid">
        <div class="intro-text">
          <p class="intro-label">Бюро переводов «Ремарка», Москва</p>
          <h1 class="intro-title">Перевод маркетинговых материалов в Москве</h1>
          <p class="intro-tagline">Послание должно резонировать, а не просто быть точным</p>
          <p class="intro-body">Маркетинговый перевод — это не переложение слов с одного языка на другой. Это создание текста, который производит на новую аудиторию ровно тот же эффект, что и оригинал: вызывает нужные эмоции, побуждает к действию, сохраняет голос бренда. Дословный перевод рекламного слогана чаще всего звучит нейтрально или нелепо — вместо того чтобы продавать.</p>
          <p class="intro-body">В московском бюро «Ремарка» маркетинговые материалы переводят специалисты с опытом в копирайтинге и маркетинге. Они понимают культурный контекст, умеют адаптировать игру слов, метафоры и культурные референсы — так, чтобы текст звучал естественно и работал.</p>
          <p class="intro-body">Работаем с маркетинговыми директорами, CMO, бренд-менеджерами и рекламными агентствами. Создаём и храним глоссарий бренда для единообразия всех переводов.</p>
        </div>
        <div class="intro-visual">
          <div class="intro-illus" aria-hidden="true">
            <svg viewBox="0 0 480 200" fill="none" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <linearGradient id="ip-g1" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#783CF0"/><stop offset="100%" stop-color="#00A0F0"/></linearGradient>
              </defs>
              <rect x="10" y="20" width="140" height="168" rx="12" fill="rgba(120,60,240,.10)" stroke="rgba(120,60,240,.35)" stroke-width="1.5"/>
              <rect x="10" y="20" width="140" height="38" rx="12" fill="rgba(120,60,240,.20)"/>
              <rect x="10" y="43" width="140" height="15" fill="rgba(120,60,240,.20)"/>
              <rect x="24" y="29" width="32" height="16" rx="8" fill="rgba(0,160,240,.40)"/>
              <rect x="28" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="42" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="24" y="76" width="112" height="5" rx="2.5" fill="rgba(167,139,250,.45)"/>
              <rect x="24" y="88" width="84" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="100" width="102" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="112" width="70" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="124" width="110" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="136" width="90" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="148" width="98" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="160" width="76" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="330" y="20" width="140" height="168" rx="12" fill="rgba(0,160,240,.10)" stroke="rgba(0,160,240,.35)" stroke-width="1.5"/>
              <rect x="330" y="20" width="140" height="38" rx="12" fill="rgba(0,160,240,.20)"/>
              <rect x="330" y="43" width="140" height="15" fill="rgba(0,160,240,.20)"/>
              <rect x="344" y="29" width="32" height="16" rx="8" fill="rgba(120,60,240,.40)"/>
              <rect x="348" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="362" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="344" y="76" width="112" height="5" rx="2.5" fill="rgba(0,160,240,.45)"/>
              <rect x="344" y="88" width="96" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="100" width="86" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="112" width="112" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="124" width="74" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="136" width="106" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="148" width="88" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="160" width="100" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <circle cx="240" cy="104" r="46" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
              <circle cx="240" cy="104" r="30" fill="rgba(120,60,240,.18)"/>
              <path d="M152 104h56M288 104h40" stroke="rgba(120,60,240,.40)" stroke-width="1.5" stroke-dasharray="4 3"/>
              <path d="M224 104h32M246 96l10 8-10 8" stroke="rgba(167,139,250,.95)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="intro-features">
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
              </div>
              <div><strong>Транскреация</strong><span>Адаптация послания, а не дословный перевод</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
              </div>
              <div><strong>Культурная адаптация</strong><span>Учёт местного культурного контекста и трендов</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
              </div>
              <div><strong>SEO-адаптация</strong><span>Ключевые слова под поиск целевого рынка, не дословный перевод</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
              </div>
              <div><strong>Глоссарий бренда</strong><span>Единый голос бренда во всех переводах и материалах</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ЦИФРЫ ════════ -->
  <section class="sec-stats">
    <div class="container">
      <div class="stats-row">
        <div class="stat-item">
          <span class="stat-num">25<span class="stat-suffix">+</span></span>
          <span class="stat-label">лет на рынке</span>
        </div>
        <div class="stat-item">
          <span class="stat-num">60<span class="stat-suffix">+</span></span>
          <span class="stat-label">языков перевода</span>
        </div>
        <div class="stat-item">
          <span class="stat-num">2 400<span class="stat-suffix">+</span></span>
          <span class="stat-label">выполненных заказов</span>
        </div>
        <div class="stat-item">
          <span class="stat-num">4.98<span class="stat-suffix">★</span></span>
          <span class="stat-label">средний рейтинг</span>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ЯЗЫКОВЫЕ ПАРЫ ════════ -->
  <section class="sec sec--alt sec-lang-pairs">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Языковые пары для маркетинговых материалов</h2>
        <p class="sec-sub">Носители языка с маркетинговым опытом — переводчики знают местный рынок</p>
      </div>
      <div class="fl-grid" style="grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:20px">
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">EN ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Английский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Глобальные кампании, перевод для US/UK/AU рынков, локализация для российского рынка</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">DE ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Немецкий</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Маркетинговые материалы для немецкого, австрийского и швейцарского рынков (DACH)</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">FR ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Французский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Французский, бельгийский и канадский рынки. Адаптация с учётом различий французского и квебекского</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">ZH ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Китайский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Адаптация для китайского рынка (упрощённый/традиционный), WeChat, Weibo, Tmall</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">IT ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Итальянский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Luxury, fashion, food&beverage — сегменты, где важна эмоциональность итальянского языка</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">ES ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Испанский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Испания и латиноамериканские рынки. Адаптация под regional Spanish с учётом локальных различий</p>
        </div>
      </div>
      <p style="text-align:center;font-size:13px;color:var(--text-secondary)">Другие языки — японский, корейский, арабский, португальский и ещё 50+. <a href="#calc-section" style="color:var(--accent)">Уточните наличие переводчика</a></p>
    </div>
  </section>

  <!-- ════════ ЧТО МЫ ПЕРЕВОДИМ ════════ -->
  <section class="sec sec-docs">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Что мы переводим</h2>
        <p class="sec-sub">Раскройте нужную категорию — посмотрите конкретные виды материалов</p>
      </div>
      <div class="docs-accordion">

        <details class="doc-item" open>
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            <span>Рекламные тексты и слоганы</span>
          </summary>
          <div class="doc-body">
            <p>Слоганы, taglines, рекламные объявления, тексты для баннеров и OOH-рекламы. Транскреация с сохранением эмоционального воздействия и запоминаемости.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
            <span>Корпоративные презентации</span>
          </summary>
          <div class="doc-body">
            <p>PowerPoint, Keynote и Google Slides — перевод с сохранением форматирования, адаптация заголовков и call-to-action под целевую аудиторию.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            <span>Контент сайтов и SEO-тексты</span>
          </summary>
          <div class="doc-body">
            <p>Перевод и SEO-адаптация страниц сайта: заголовки, мета-теги, тексты разделов, блог. Ключевые слова подбираются под поисковые запросы целевого рынка.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
            <span>Описания товаров и каталоги</span>
          </summary>
          <div class="doc-body">
            <p>Карточки товаров для маркетплейсов (Ozon, Wildberries, Amazon, Tmall), каталоги продукции, спецификации с маркетинговым наполнением.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            <span>Email-рассылки и пресс-релизы</span>
          </summary>
          <div class="doc-body">
            <p>Маркетинговые письма, welcome-цепочки, nurture-кампании. Пресс-релизы для иностранных СМИ с адаптацией под местные медиастандарты.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
            <span>Контент соцсетей и видео-субтитры</span>
          </summary>
          <div class="doc-body">
            <p>Посты для Instagram, LinkedIn, Facebook, VK. Субтитры для YouTube и рекламных роликов с учётом тайминга и синхронизации с видеорядом.</p>
          </div>
        </details>

      </div>
    </div>
  </section>

  <!-- ════════ КОНТРОЛЬ КАЧЕСТВА — SPLIT SECTION ════════ -->
  <section class="sec sec-split">
    <div class="container">
      <div class="split-row split-row--rev">
        <div class="split-text">
          <p class="sec-label">Маркетинговая адаптация</p>
          <h2>Почему маркетинговый перевод — это транскреация</h2>
          <p>Рекламный текст работает не потому, что он точен, а потому что он задевает. Слоган Nike «Just Do It» в дословном переводе — это просто инструкция, а не мотивирующий призыв. Маркетинговый переводчик думает о том, как текст будет восприниматься читателем — и при необходимости переосмысляет его.</p>
          <p>Наши специалисты совмещают лингвистическую подготовку с маркетинговым опытом. Они понимают, как работают разные культуры потребления, знают местные тренды и могут адаптировать tone of voice бренда для новой аудитории.</p>
          <ul class="split-checklist">
            <li>Транскреация — адаптация послания, а не дословный перевод</li>
            <li>Сохранение tone of voice и ценностей бренда</li>
            <li>SEO-адаптация ключевых слов под целевой рынок</li>
            <li>Вычитка носителем языка — естественность гарантирована</li>
          </ul>
        </div>
        <div class="split-visual" aria-hidden="true">
          <svg viewBox="0 0 380 320" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <linearGradient id="sq-g1-m" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#783CF0"/><stop offset="100%" stop-color="#00A0F0"/></linearGradient>
              <linearGradient id="sq-g2-m" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#783CF0" stop-opacity=".5"/><stop offset="100%" stop-color="#00A0F0" stop-opacity=".5"/></linearGradient>
            </defs>
            <circle cx="190" cy="160" r="130" fill="rgba(120,60,240,.07)"/>
            <rect x="60" y="30" width="260" height="64" rx="16" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="62" r="20" fill="url(#sq-g1-m)" opacity=".85"/>
            <path d="M88 62l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="50" width="170" height="9" rx="4" fill="rgba(167,139,250,.55)"/>
            <rect x="126" y="66" width="120" height="7" rx="3" fill="rgba(167,139,250,.30)"/>
            <line x1="190" y1="94" x2="190" y2="118" stroke="url(#sq-g2-m)" stroke-width="2" stroke-dasharray="4 3"/>
            <rect x="60" y="118" width="260" height="64" rx="16" fill="rgba(0,160,240,.12)" stroke="rgba(0,160,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="150" r="20" fill="url(#sq-g1-m)" opacity=".85"/>
            <path d="M88 150l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="138" width="155" height="9" rx="4" fill="rgba(0,160,240,.55)"/>
            <rect x="126" y="154" width="110" height="7" rx="3" fill="rgba(0,160,240,.30)"/>
            <line x1="190" y1="182" x2="190" y2="206" stroke="url(#sq-g2-m)" stroke-width="2" stroke-dasharray="4 3"/>
            <rect x="60" y="206" width="260" height="64" rx="16" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="238" r="20" fill="url(#sq-g1-m)" opacity=".85"/>
            <path d="M88 238l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="226" width="175" height="9" rx="4" fill="rgba(167,139,250,.55)"/>
            <rect x="126" y="242" width="130" height="7" rx="3" fill="rgba(167,139,250,.30)"/>
            <circle cx="310" cy="290" r="26" fill="rgba(0,200,100,.12)" stroke="rgba(0,200,100,.35)" stroke-width="1.5"/>
            <path d="M300 290l8 8 14-16" stroke="rgba(0,200,100,.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ КАК ПРОХОДИТ РАБОТА (6 шагов) ════════ -->
  <section class="sec sec--alt sec-how-pro">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Как проходит работа</h2>
        <p class="sec-sub">Шесть шагов от брифа до готовых маркетинговых материалов</p>
      </div>
      <div class="steps-row steps-row--6">
        <div class="step-item">
          <div class="step-num">01</div>
          <h3>Загрузите материалы</h3>
          <p>Через сайт, мессенджер или email. Принимаем DOCX, PPT, PDF, IDML, экспорты из Figma</p>
        </div>
        <div class="step-item">
          <div class="step-num">02</div>
          <h3>Бриф и глоссарий</h3>
          <p>Фиксируем tone of voice, глоссарий бренда, запрещённые формулировки и ожидания</p>
        </div>
        <div class="step-item">
          <div class="step-num">03</div>
          <h3>Подбор специалиста</h3>
          <p>Выбираем переводчика-копирайтера с опытом в вашей отрасли и знанием целевого рынка</p>
        </div>
        <div class="step-item">
          <div class="step-num">04</div>
          <h3>Транскреация</h3>
          <p>Специалист адаптирует послание с учётом культурного контекста и целевой аудитории</p>
        </div>
        <div class="step-item">
          <div class="step-num">05</div>
          <h3>Вычитка носителем</h3>
          <p>Носитель языка проверяет естественность, tone of voice и соответствие бренду</p>
        </div>
        <div class="step-item">
          <div class="step-num">06</div>
          <h3>Готовые материалы</h3>
          <p>Передаём файлы в исходном формате — точно в срок, готовые к публикации</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ГАРАНТИИ КАЧЕСТВА ════════ -->
  <section class="sec sec-guarantees">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Наши гарантии</h2>
        <p class="sec-sub">Мы отвечаем за результат — не только словами</p>
      </div>
      <div class="guarantees-grid">
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.9"/></svg>
          </div>
          <h3>Бесплатная правка</h3>
          <p>Исправим бесплатно в течение 7 дней, если перевод не соответствует брифу или tone of voice бренда</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
          </div>
          <h3>Глоссарий бренда</h3>
          <p>Сохраняем глоссарий и Translation Memory вашего бренда. Единый голос во всех переводах</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </div>
          <h3>Соблюдение сроков</h3>
          <p>Если не уложились в оговорённый срок по нашей вине — возвращаем предоплату или даём скидку</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
          </div>
          <h3>Вычитка носителем</h3>
          <p>Финальная проверка носителем целевого языка — текст звучит естественно и убедительно</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ СРОКИ И СТОИМОСТЬ ════════ -->
  <section class="sec sec--alt sec-vol-pricing">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Сроки и стоимость</h2>
        <p class="sec-sub">Маркетинговые материалы тарифицируются по словам, страницам или проектно. Точный расчёт — бесплатно</p>
      </div>
      <div class="vol-table-wrap">
        <table class="vol-table">
          <thead>
            <tr>
              <th>Формат</th>
              <th>Срок</th>
              <th>Стоимость</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>До 2 000 слов</strong></td>
              <td>1–2 рабочих дня</td>
              <td class="vol-price">от 3 500 ₽</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>2 000–10 000 слов</strong></td>
              <td>2–5 рабочих дней</td>
              <td class="vol-price">от 18 ₽/слово</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>Сайт (до 50 страниц)</strong></td>
              <td>5–10 рабочих дней</td>
              <td class="vol-price">от 350 ₽/стр.</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>Крупный проект (кампания)</strong></td>
              <td>по договорённости</td>
              <td class="vol-price">индивидуально</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
          </tbody>
        </table>
      </div>
      <p class="vol-note">Транскреация слоганов и рекламных концепций тарифицируется как творческая работа — по согласованию. Для регулярных заказов предусмотрены условия партнёрства.</p>
    </div>
  </section>

  <!-- ════════ СРАВНЕНИЕ ФОРМАТОВ ════════ -->
  <section class="sec sec-comparison">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Какой формат вам подходит</h2>
        <p class="sec-sub">Сравните три уровня сервиса и выберите оптимальный для вашей задачи</p>
      </div>
      <div class="cmp-table-wrap">
        <table class="cmp-table">
          <thead>
            <tr>
              <th></th>
              <th>Постредактирование ИИ</th>
              <th class="cmp-featured">Профессиональный перевод</th>
              <th>Премиум (транскреация)</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>Переводчик с маркетинговым опытом</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Культурная адаптация (транскреация)</td><td class="cmp-no">✗</td><td class="cmp-part cmp-featured">частично</td><td class="cmp-yes">✓</td></tr>
            <tr><td>SEO-адаптация ключевых слов</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Глоссарий бренда / TM клиента</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Вычитка носителем языка</td><td class="cmp-no">✗</td><td class="cmp-no cmp-featured">✗</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Проверка tone of voice</td><td class="cmp-no">✗</td><td class="cmp-part cmp-featured">частично</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Адаптация слоганов и концепций</td><td class="cmp-no">✗</td><td class="cmp-no cmp-featured">✗</td><td class="cmp-yes">✓</td></tr>
            <tr class="cmp-meta"><td>Срок</td><td>от 1 дня</td><td class="cmp-featured">1–5 дней</td><td>2–7 дней</td></tr>
            <tr class="cmp-meta cmp-price-row"><td>Стоимость</td><td>от 200 ₽/стр.</td><td class="cmp-featured">от 350 ₽/стр.</td><td>от 600 ₽/стр.</td></tr>
          </tbody>
          <tfoot>
            <tr>
              <td></td>
              <td><a href="#calc-section" class="price-btn price-btn--outline">Выбрать</a></td>
              <td class="cmp-featured"><a href="#calc-section" class="price-btn price-btn--primary">Выбрать</a></td>
              <td><a href="#calc-section" class="price-btn price-btn--outline">Выбрать</a></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </section>

  <!-- ════════ СПРАВОЧНИК ПО МАТЕРИАЛАМ ════════ -->
  <section class="sec sec--alt sec-doc-reference">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Справочник по материалам</h2>
        <p class="sec-sub">Выберите тип материала — узнайте особенности маркетингового перевода</p>
      </div>
      <div class="doc-ref-grid">
        <a href="#calc-section" class="doc-ref-card">
          <strong>Рекламные слоганы</strong>
          <span>Транскреация слоганов и taglines для нового рынка</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Корпоративные брошюры</strong>
          <span>Имиджевые и продуктовые брошюры, буклеты, листовки</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Презентации PowerPoint</strong>
          <span>Корпоративные и продуктовые презентации для партнёров</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Описания продуктов</strong>
          <span>Карточки товаров для маркетплейсов и интернет-магазинов</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Пресс-релизы</strong>
          <span>Пресс-релизы для иностранных СМИ и медиа-питч</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Email-кампании</strong>
          <span>Welcome-серии, nurture-цепочки, промо-рассылки</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>SEO-статьи</strong>
          <span>Контент для блога с адаптацией ключевых слов под рынок</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Контент соцсетей</strong>
          <span>Посты для LinkedIn, Instagram, Facebook, ВКонтакте</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Видео-субтитры</strong>
          <span>Субтитры для YouTube, рекламных роликов, обучающих видео</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Упаковка и этикетки</strong>
          <span>Текст упаковки с учётом требований рынка и регулятора</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Годовые корпоративные отчёты</strong>
          <span>Нарративная часть отчётов с маркетинговым наполнением</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Landing pages</strong>
          <span>Посадочные страницы с адаптацией CTA и заголовков</span>
        </a>
      </div>
    </div>
  </section>

  <!-- ════════ ОТЗЫВЫ ════════ -->
  <section class="sec sec--alt sec-reviews">
    <div class="container">
      <div class="reviews-head">
        <div>
          <h2 class="sec-title">Что говорят клиенты</h2>
          <p class="sec-sub" style="margin-top:6px">Реальные отзывы о переводе маркетинговых материалов</p>
        </div>
        <div class="review-rating-block">
          <div class="review-rating-score">4.98</div>
          <div class="review-stars-row">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          </div>
          <div class="review-rating-label">средний рейтинг</div>
        </div>
        <div class="review-platform-links">
          <a href="https://yandex.ru/maps/org/remarka/51867347382/reviews/" class="review-platform-link" target="_blank" rel="noopener">Яндекс</a>
          <a href="https://maps.app.goo.gl/d8BKJYw81PqBHXvz7" class="review-platform-link" target="_blank" rel="noopener">Google</a>
        </div>
      </div>
      <div class="reviews-grid" style="grid-template-columns:repeat(3,1fr)">
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">Н</div>
            <div class="review-meta"><div class="review-name">Наталья Громова</div><div class="review-src">Яндекс · март 2025</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Переводили сайт и брошюры на немецкий для выхода на рынок DACH. Переводчик сохранил наш tone of voice, адаптировал слоганы под аудиторию. Немецкие партнёры отметили профессиональный уровень текстов.</p>
          <div class="review-author-role">Директор по маркетингу, производственная компания</div>
        </div>
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">С</div>
            <div class="review-meta"><div class="review-name">Сергей Фёдоров</div><div class="review-src">Google · декабрь 2024</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Работаем постоянно с переводом email-кампаний и социальных сетей на английский. Глоссарий нашего бренда уже создан и сохранён. Каждый текст выходит в единой стилистике.</p>
          <div class="review-author-role">Бренд-менеджер, FMCG-компания</div>
        </div>
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">О</div>
            <div class="review-meta"><div class="review-name">Ольга Власова</div><div class="review-src">Яндекс · октябрь 2024</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Переводили 3 000 карточек товаров на китайский для Tmall. Переводчик понимает специфику площадки, описания звучат по-китайски естественно. Конверсия выросла после переключения с машинного перевода на этот.</p>
          <div class="review-author-role">Владелец, e-commerce компания</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ FAQ ════════ -->
  <section class="sec sec-faq">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Часто задаваемые вопросы</h2>
        <p class="sec-sub">Не нашли ответа — напишите Ольге в чат, ответим за минуту</p>
      </div>
      <div class="faq-list">

        <details class="faq-item">
          <summary class="faq-summary">Допустим ли дословный перевод для маркетинговых текстов?</summary>
          <div class="faq-body">
            <p>Нет. Дословный перевод убивает эмоциональное воздействие текста. Рекламный слоган, который работает на одном языке, в дословном переводе на другой чаще всего звучит нейтрально или странно. Маркетинговый перевод — это транскреация: переводчик думает не о словах, а о том, какой эффект текст должен произвести на читателя, и создаёт текст, который достигает этого эффекта.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Как вы работаете с брендовыми терминами и tone of voice?</summary>
          <div class="faq-body">
            <p>Перед началом работы фиксируем глоссарий бренда: названия продуктов, устоявшиеся переводы ключевых понятий, слоганы, tone of voice (официальный/дружелюбный/экспертный), запрещённые формулировки. Эти данные хранятся в Translation Memory клиента и применяются автоматически во всех последующих заказах. Если у вас уже есть Brand Style Guide — передайте нам, переводчик будет следовать ему.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Как проходит SEO-адаптация при переводе сайта?</summary>
          <div class="faq-body">
            <p>Мы не переводим ключевые слова дословно — мы подбираем семантически близкие запросы с нужной частотностью на целевом рынке. Meta title, description, H1/H2, alt-тексты изображений и URL-структура адаптируются отдельно с учётом требований поисковых систем целевой страны (Google, Yandex, Baidu). По запросу предоставляем таблицу с адаптированными ключевыми словами.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Можете ли вы работать с дизайн-файлами (InDesign, Figma)?</summary>
          <div class="faq-body">
            <p>Да. Принимаем IDML-файлы InDesign, PDF с выделенными слоями, экспорты текстового содержимого из Figma. Возвращаем файл в исходном формате с замещённым текстом. Предупреждаем о возможном переполнении текстовых блоков: русский текст обычно на 15–30% длиннее английского. При необходимости согласуем с вашим дизайнером адаптацию макета.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Каковы сроки перевода крупных маркетинговых кампаний?</summary>
          <div class="faq-body">
            <p>Кампания на 10 000 слов для одного языка — 3–5 рабочих дней с вычиткой носителем. При одновременном переводе на несколько языков подключаем параллельные команды с единым глоссарием — сроки в этом случае не увеличиваются кратно. Для крупных проектов рекомендуем планировать заранее — свяжитесь с Ольгей для обсуждения сроков и поэтапной работы.</p>
          </div>
        </details>

      </div>
    </div>
  </section>

  <!-- ════════ КАЛЬКУЛЯТОР СТОИМОСТИ ════════ -->',
        'ved-perevod' => '<section class="sec sec-intro">
    <div class="container">
      <div class="intro-grid">
        <div class="intro-text">
          <p class="intro-label">Бюро переводов «Ремарка», Москва</p>
          <h1 class="intro-title">Перевод документов для ВЭД и таможни</h1>
          <p class="intro-tagline">Ошибка в Incoterms или коде ТН ВЭД — это задержка груза</p>
          <p class="intro-body">Внешнеэкономическая деятельность требует точной терминологии. Неправильно переведённое условие Incoterms меняет, кто несёт расходы и риски при транспортировке. Ошибочная интерпретация кода ТН ВЭД в технической документации может привести к неправильной классификации товара на таможне и задержке оформления. Переводчик без знания ВЭД-специфики может не заметить таких рисков.</p>
          <p class="intro-body">В московском бюро «Ремарка» ВЭД-документы переводят специалисты с опытом в международной торговле и логистике. Они знают терминологию Инкотермс 2020, систему ТН ВЭД ЕАЭС, требования таможенных органов и типичные форматы товаросопроводительных документов.</p>
          <p class="intro-body">Работаем с менеджерами ВЭД, таможенными брокерами, логистическими компаниями и директорами по цепочкам поставок. Принимаем срочные заявки для таможенного оформления.</p>
        </div>
        <div class="intro-visual">
          <div class="intro-illus" aria-hidden="true">
            <svg viewBox="0 0 480 200" fill="none" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <linearGradient id="ip-g1" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#783CF0"/><stop offset="100%" stop-color="#00A0F0"/></linearGradient>
              </defs>
              <rect x="10" y="20" width="140" height="168" rx="12" fill="rgba(120,60,240,.10)" stroke="rgba(120,60,240,.35)" stroke-width="1.5"/>
              <rect x="10" y="20" width="140" height="38" rx="12" fill="rgba(120,60,240,.20)"/>
              <rect x="10" y="43" width="140" height="15" fill="rgba(120,60,240,.20)"/>
              <rect x="24" y="29" width="32" height="16" rx="8" fill="rgba(0,160,240,.40)"/>
              <rect x="28" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="42" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="24" y="76" width="112" height="5" rx="2.5" fill="rgba(167,139,250,.45)"/>
              <rect x="24" y="88" width="84" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="100" width="102" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="112" width="70" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="124" width="110" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="136" width="90" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="148" width="98" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="160" width="76" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="330" y="20" width="140" height="168" rx="12" fill="rgba(0,160,240,.10)" stroke="rgba(0,160,240,.35)" stroke-width="1.5"/>
              <rect x="330" y="20" width="140" height="38" rx="12" fill="rgba(0,160,240,.20)"/>
              <rect x="330" y="43" width="140" height="15" fill="rgba(0,160,240,.20)"/>
              <rect x="344" y="29" width="32" height="16" rx="8" fill="rgba(120,60,240,.40)"/>
              <rect x="348" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="362" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="344" y="76" width="112" height="5" rx="2.5" fill="rgba(0,160,240,.45)"/>
              <rect x="344" y="88" width="96" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="100" width="86" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="112" width="112" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="124" width="74" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="136" width="106" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="148" width="88" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="160" width="100" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <circle cx="240" cy="104" r="46" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
              <circle cx="240" cy="104" r="30" fill="rgba(120,60,240,.18)"/>
              <path d="M152 104h56M288 104h40" stroke="rgba(120,60,240,.40)" stroke-width="1.5" stroke-dasharray="4 3"/>
              <path d="M224 104h32M246 96l10 8-10 8" stroke="rgba(167,139,250,.95)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="intro-features">
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 3H8a2 2 0 0 0-2 2v2h12V5a2 2 0 0 0-2-2z"/></svg>
              </div>
              <div><strong>Знание Incoterms 2020</strong><span>Корректная передача условий поставки без искажения смысла</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              </div>
              <div><strong>Срочные переводы</strong><span>Перевод для таможни за 4–6 часов при срочном запросе</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
              </div>
              <div><strong>ТН ВЭД и ЕАЭС</strong><span>Знание товарной номенклатуры и таможенных требований ЕАЭС</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
              </div>
              <div><strong>Форматы перевозок</strong><span>B/L, CMR, AWB, коносаменты — знаем форматы всех видов транспорта</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ЦИФРЫ ════════ -->
  <section class="sec-stats">
    <div class="container">
      <div class="stats-row">
        <div class="stat-item">
          <span class="stat-num">25<span class="stat-suffix">+</span></span>
          <span class="stat-label">лет на рынке</span>
        </div>
        <div class="stat-item">
          <span class="stat-num">60<span class="stat-suffix">+</span></span>
          <span class="stat-label">языков перевода</span>
        </div>
        <div class="stat-item">
          <span class="stat-num">2 400<span class="stat-suffix">+</span></span>
          <span class="stat-label">выполненных заказов</span>
        </div>
        <div class="stat-item">
          <span class="stat-num">4.98<span class="stat-suffix">★</span></span>
          <span class="stat-label">средний рейтинг</span>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ЯЗЫКОВЫЕ ПАРЫ ════════ -->
  <section class="sec sec--alt sec-lang-pairs">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Языковые пары для ВЭД-документов</h2>
        <p class="sec-sub">Переводчики знают специфику торговых отношений с каждой страной</p>
      </div>
      <div class="fl-grid" style="grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:20px">
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">EN ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Английский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Международные торговые контракты, документация по UCP 600 (аккредитивы), ISDA, документы для IMO и IATA</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">ZH ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Китайский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Цепочки поставок из КНР: контракты, инвойсы, упаковочные листы, технические паспорта, сертификаты качества</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">DE ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Немецкий</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Торговые отношения с ЕС: CMR-накладные, EUR.1, технические регламенты, CE-документация</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">TR ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Турецкий</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Турецкие внешнеторговые контракты, сертификаты происхождения Form A, транзитные документы через Турцию</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">KO ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Корейский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Контракты с корейскими производителями (Samsung, Hyundai, LG и др.), технические паспорта, сертификаты</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">AR ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Арабский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">ВЭД со странами Ближнего Востока: ОАЭ, Саудовская Аравия, Египет. Сертификаты халяль, документы GCC</p>
        </div>
      </div>
      <p style="text-align:center;font-size:13px;color:var(--text-secondary)">Другие языки — японский, вьетнамский, индийские языки, португальский и ещё 50+. <a href="#calc-section" style="color:var(--accent)">Уточните наличие переводчика</a></p>
    </div>
  </section>

  <!-- ════════ КАКИЕ ДОКУМЕНТЫ ════════ -->
  <section class="sec sec-docs">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Какие документы мы переводим</h2>
        <p class="sec-sub">Раскройте нужную категорию — посмотрите конкретные виды документов</p>
      </div>
      <div class="docs-accordion">

        <details class="doc-item" open>
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            <span>Внешнеторговые контракты</span>
          </summary>
          <div class="doc-body">
            <p>Договоры купли-продажи товаров, контракты на поставку, агентские и дистрибьюторские соглашения. Переводим с корректным воспроизведением условий Incoterms 2020, платёжных условий и условий приёмки товара.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
            <span>Таможенные декларации и инвойсы</span>
          </summary>
          <div class="doc-body">
            <p>Таможенные декларации, коммерческие инвойсы, проформа-инвойсы, упаковочные листы. Точная передача наименований товаров в соответствии с ТН ВЭД.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            <span>Сертификаты происхождения</span>
          </summary>
          <div class="doc-body">
            <p>Сертификаты происхождения СТ-1 (для СНГ), EUR.1 (для торговли с ЕС), Form A (GSP), Certificate of Origin. Знаем требования к форматам разных таможенных союзов.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 3H8a2 2 0 0 0-2 2v2h12V5a2 2 0 0 0-2-2z"/></svg>
            <span>Товаросопроводительные документы</span>
          </summary>
          <div class="doc-body">
            <p>Коносаменты (Bill of Lading), морские накладные, CMR-накладные для автоперевозок, авиа-накладные AWB, железнодорожные накладные CIM/СМГС. Знаем стандартные форматы и реквизиты каждого документа.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <span>Фитосанитарные и ветеринарные сертификаты</span>
          </summary>
          <div class="doc-body">
            <p>Фитосанитарные сертификаты на растительную продукцию, ветеринарные сертификаты, сертификаты соответствия санитарным нормам ЕАЭС. Знаем требования Россельхознадзора.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            <span>Технические паспорта и разрешительная документация</span>
          </summary>
          <div class="doc-body">
            <p>Технические паспорта на ввозимое оборудование, руководства пользователя, декларации о соответствии техническим регламентам ЕАЭС, MSDS/SDS листы безопасности.</p>
          </div>
        </details>

      </div>
    </div>
  </section>

  <!-- ════════ КОНТРОЛЬ КАЧЕСТВА — SPLIT SECTION ════════ -->
  <section class="sec sec-split">
    <div class="container">
      <div class="split-row split-row--rev">
        <div class="split-text">
          <p class="sec-label">Точность для таможни</p>
          <h2>Почему ВЭД-документы требуют специализированного перевода</h2>
          <p>В ВЭД-документах нет места приблизительности. «FOB Shanghai» и «CIF Novorossiysk» — это не просто слова, а юридически значимые условия, определяющие, кто несёт риск и расходы на каждом этапе перевозки. Неточно переведённый термин Incoterms может стоить компании сотен тысяч рублей в спорной ситуации.</p>
          <p>Наши переводчики прошли подготовку по ВЭД и логистике. Они знают стандартные форматы коносаментов, CMR, AWB, требования к сертификатам происхождения для разных таможенных союзов, и понимают, какие именно реквизиты критичны для таможенного оформления.</p>
          <ul class="split-checklist">
            <li>Знание терминологии Incoterms 2020 и ТН ВЭД ЕАЭС</li>
            <li>Опыт с товаросопроводительными документами всех видов транспорта</li>
            <li>Срочные переводы для таможенного оформления</li>
            <li>Оговорка переводчика по запросу — без доплаты</li>
          </ul>
        </div>
        <div class="split-visual" aria-hidden="true">
          <svg viewBox="0 0 380 320" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <linearGradient id="sq-g1-v" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#783CF0"/><stop offset="100%" stop-color="#00A0F0"/></linearGradient>
              <linearGradient id="sq-g2-v" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#783CF0" stop-opacity=".5"/><stop offset="100%" stop-color="#00A0F0" stop-opacity=".5"/></linearGradient>
            </defs>
            <circle cx="190" cy="160" r="130" fill="rgba(120,60,240,.07)"/>
            <rect x="60" y="30" width="260" height="64" rx="16" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="62" r="20" fill="url(#sq-g1-v)" opacity=".85"/>
            <path d="M88 62l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="50" width="170" height="9" rx="4" fill="rgba(167,139,250,.55)"/>
            <rect x="126" y="66" width="120" height="7" rx="3" fill="rgba(167,139,250,.30)"/>
            <line x1="190" y1="94" x2="190" y2="118" stroke="url(#sq-g2-v)" stroke-width="2" stroke-dasharray="4 3"/>
            <rect x="60" y="118" width="260" height="64" rx="16" fill="rgba(0,160,240,.12)" stroke="rgba(0,160,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="150" r="20" fill="url(#sq-g1-v)" opacity=".85"/>
            <path d="M88 150l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="138" width="155" height="9" rx="4" fill="rgba(0,160,240,.55)"/>
            <rect x="126" y="154" width="110" height="7" rx="3" fill="rgba(0,160,240,.30)"/>
            <line x1="190" y1="182" x2="190" y2="206" stroke="url(#sq-g2-v)" stroke-width="2" stroke-dasharray="4 3"/>
            <rect x="60" y="206" width="260" height="64" rx="16" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="238" r="20" fill="url(#sq-g1-v)" opacity=".85"/>
            <path d="M88 238l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="226" width="175" height="9" rx="4" fill="rgba(167,139,250,.55)"/>
            <rect x="126" y="242" width="130" height="7" rx="3" fill="rgba(167,139,250,.30)"/>
            <circle cx="310" cy="290" r="26" fill="rgba(0,200,100,.12)" stroke="rgba(0,200,100,.35)" stroke-width="1.5"/>
            <path d="M300 290l8 8 14-16" stroke="rgba(0,200,100,.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ КАК ПРОХОДИТ РАБОТА (6 шагов) ════════ -->
  <section class="sec sec--alt sec-how-pro">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Как проходит работа</h2>
        <p class="sec-sub">Шесть шагов от ВЭД-документа до готового перевода — с учётом таможенных сроков</p>
      </div>
      <div class="steps-row steps-row--6">
        <div class="step-item">
          <div class="step-num">01</div>
          <h3>Загрузите документ</h3>
          <p>Через сайт, WhatsApp, email или Telegram — принимаем любой формат файла</p>
        </div>
        <div class="step-item">
          <div class="step-num">02</div>
          <h3>Анализ и срок</h3>
          <p>Оцениваем объём, тип документа, срочность — ответ за несколько минут</p>
        </div>
        <div class="step-item">
          <div class="step-num">03</div>
          <h3>Подбор специалиста</h3>
          <p>Выбираем переводчика с опытом в ВЭД и знанием вашего языка и отрасли</p>
        </div>
        <div class="step-item">
          <div class="step-num">04</div>
          <h3>Перевод</h3>
          <p>Специалист применяет корректную ВЭД-терминологию, Incoterms и ТН ВЭД</p>
        </div>
        <div class="step-item">
          <div class="step-num">05</div>
          <h3>Проверка</h3>
          <p>Редактор вычитывает перевод, проверяет реквизиты и соответствие оригиналу</p>
        </div>
        <div class="step-item">
          <div class="step-num">06</div>
          <h3>Передача</h3>
          <p>Отправляем файл с оговоркой переводчика (по запросу) — в оговорённый срок</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ГАРАНТИИ КАЧЕСТВА ════════ -->
  <section class="sec sec-guarantees">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Наши гарантии</h2>
        <p class="sec-sub">Мы отвечаем за результат — не только словами</p>
      </div>
      <div class="guarantees-grid">
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </div>
          <h3>Срочные переводы</h3>
          <p>Принимаем срочные заявки для таможни. Стандартный ВЭД-документ — за 4–6 часов при заказе до полудня</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.9"/></svg>
          </div>
          <h3>Бесплатная правка</h3>
          <p>Исправим бесплатно в течение 7 дней, если перевод не принят таможенным органом по вине переводчика</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </div>
          <h3>Конфиденциальность</h3>
          <p>NDA по запросу. Коммерческая информация о ВЭД-сделках не передаётся третьим лицам</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
          </div>
          <h3>Глоссарий ВЭД</h3>
          <p>Сохраняем глоссарий наименований товаров и контрагентов. Единая терминология во всех ваших ВЭД-документах</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ СРОКИ И СТОИМОСТЬ ════════ -->
  <section class="sec sec--alt sec-vol-pricing">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Сроки и стоимость</h2>
        <p class="sec-sub">Срочные переводы для таможни — отдельные тарифы. Точный расчёт бесплатно</p>
      </div>
      <div class="vol-table-wrap">
        <table class="vol-table">
          <thead>
            <tr>
              <th>Режим</th>
              <th>Срок</th>
              <th>Стоимость</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>Стандарт (до 10 стр.)</strong></td>
              <td>1 рабочий день</td>
              <td class="vol-price">от 4 000 ₽</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>Срочно (до 5 стр.)</strong></td>
              <td>4–6 часов</td>
              <td class="vol-price">от 3 000 ₽</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>Пакет документов (10–30 стр.)</strong></td>
              <td>2–3 рабочих дня</td>
              <td class="vol-price">от 400 ₽/стр.</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>Постоянный поток ВЭД</strong></td>
              <td>по договорённости</td>
              <td class="vol-price">индивидуально</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
          </tbody>
        </table>
      </div>
      <p class="vol-note">Экспресс-перевод в тот же день — коэффициент ×1,5. Для регулярных ВЭД-клиентов с потоком документов предусмотрены партнёрские условия.</p>
    </div>
  </section>

  <!-- ════════ СРАВНЕНИЕ ФОРМАТОВ ════════ -->
  <section class="sec sec-comparison">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Какой формат вам подходит</h2>
        <p class="sec-sub">Сравните три уровня сервиса и выберите оптимальный для вашей задачи</p>
      </div>
      <div class="cmp-table-wrap">
        <table class="cmp-table">
          <thead>
            <tr>
              <th></th>
              <th>Постредактирование ИИ</th>
              <th class="cmp-featured">Профессиональный перевод</th>
              <th>Премиум-перевод</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>Переводчик с опытом в ВЭД/логистике</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Знание Incoterms 2020 и ТН ВЭД</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Глоссарий наименований товаров / TM</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Оговорка переводчика (certification)</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Корректура редактором</td><td class="cmp-no">✗</td><td class="cmp-no cmp-featured">✗</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Вычитка носителем языка</td><td class="cmp-no">✗</td><td class="cmp-no cmp-featured">✗</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Подходит для таможенного оформления</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Срочное выполнение (24ч)</td><td class="cmp-yes">✓</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-part">по запросу</td></tr>
            <tr class="cmp-meta"><td>Срок</td><td>от 2 часов</td><td class="cmp-featured">4 ч – 2 дня</td><td>1–3 дня</td></tr>
            <tr class="cmp-meta cmp-price-row"><td>Стоимость</td><td>от 250 ₽/стр.</td><td class="cmp-featured">от 400 ₽/стр.</td><td>от 700 ₽/стр.</td></tr>
          </tbody>
          <tfoot>
            <tr>
              <td></td>
              <td><a href="#calc-section" class="price-btn price-btn--outline">Выбрать</a></td>
              <td class="cmp-featured"><a href="#calc-section" class="price-btn price-btn--primary">Выбрать</a></td>
              <td><a href="#calc-section" class="price-btn price-btn--outline">Выбрать</a></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </section>

  <!-- ════════ СПРАВОЧНИК ПО ДОКУМЕНТАМ ════════ -->
  <section class="sec sec--alt sec-doc-reference">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Справочник по документам</h2>
        <p class="sec-sub">Выберите тип ВЭД-документа — узнайте особенности перевода</p>
      </div>
      <div class="doc-ref-grid">
        <a href="#calc-section" class="doc-ref-card">
          <strong>Внешнеторговые контракты</strong>
          <span>Договоры поставки с Incoterms, платёжными условиями, условиями приёмки</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Инвойсы и проформы</strong>
          <span>Коммерческие инвойсы, проформа-инвойсы, упаковочные листы</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Коносаменты (B/L)</strong>
          <span>Морские коносаменты, морские накладные Sea Waybill</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>CMR-накладные</strong>
          <span>Международные автомобильные накладные для грузоперевозок</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Авиа-накладные (AWB)</strong>
          <span>Air Waybill для авиационных грузоперевозок (IATA)</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Упаковочные листы</strong>
          <span>Packing list с детализацией мест, веса и объёма груза</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Сертификаты происхождения СТ-1/EUR.1</strong>
          <span>Сертификаты для льготного таможенного режима</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Фитосанитарные сертификаты</strong>
          <span>Phytosanitary certificate на растительную продукцию</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Технические регламенты ЕАЭС</strong>
          <span>ТР ЕАЭС, технические условия, стандарты на импортируемые товары</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Декларации о соответствии</strong>
          <span>Декларации ЕАС, CE-сертификаты, декларации о соответствии ТР</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>MSDS/SDS листы безопасности</strong>
          <span>Паспорта безопасности химической продукции по GHS/СГС и ГОСТ 30333</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Тендерная документация для ВЭД</strong>
          <span>Конкурсная документация для международных закупок и тендеров</span>
        </a>
      </div>
    </div>
  </section>

  <!-- ════════ ОТЗЫВЫ ════════ -->
  <section class="sec sec--alt sec-reviews">
    <div class="container">
      <div class="reviews-head">
        <div>
          <h2 class="sec-title">Что говорят клиенты</h2>
          <p class="sec-sub" style="margin-top:6px">Реальные отзывы о переводах для ВЭД и таможни</p>
        </div>
        <div class="review-rating-block">
          <div class="review-rating-score">4.98</div>
          <div class="review-stars-row">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          </div>
          <div class="review-rating-label">средний рейтинг</div>
        </div>
        <div class="review-platform-links">
          <a href="https://yandex.ru/maps/org/remarka/51867347382/reviews/" class="review-platform-link" target="_blank" rel="noopener">Яндекс</a>
          <a href="https://maps.app.goo.gl/d8BKJYw81PqBHXvz7" class="review-platform-link" target="_blank" rel="noopener">Google</a>
        </div>
      </div>
      <div class="reviews-grid" style="grid-template-columns:repeat(3,1fr)">
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">И</div>
            <div class="review-meta"><div class="review-name">Игорь Лебедев</div><div class="review-src">Яндекс · апрель 2025</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Работаем с большим объёмом китайской документации — контракты, инвойсы, технические паспорта. Переводы точные, терминология соответствует требованиям российской таможни. Глоссарий наших товаров уже сохранён в бюро.</p>
          <div class="review-author-role">Менеджер ВЭД, производственная компания</div>
        </div>
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">В</div>
            <div class="review-meta"><div class="review-name">Виктор Сорокин</div><div class="review-src">Google · январь 2025</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Нужен был срочный перевод коносамента и сертификата происхождения — успели за 3 часа. Перевод принят таможней без замечаний. Надёжные партнёры для срочных ВЭД-задач.</p>
          <div class="review-author-role">Таможенный брокер</div>
        </div>
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">Т</div>
            <div class="review-meta"><div class="review-name">Татьяна Рогова</div><div class="review-src">Яндекс · октябрь 2024</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Ведём крупные импортные проекты с поставщиками из разных стран. Бюро переводит документацию для нескольких языков — качество стабильное, сроки соблюдаются, глоссарий наш сохранён.</p>
          <div class="review-author-role">Директор по логистике, торговая компания</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ FAQ ════════ -->
  <section class="sec sec-faq">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Часто задаваемые вопросы</h2>
        <p class="sec-sub">Не нашли ответа — напишите Ольге в чат, ответим за минуту</p>
      </div>
      <div class="faq-list">

        <details class="faq-item">
          <summary class="faq-summary">Можно ли сделать срочный перевод для таможни?</summary>
          <div class="faq-body">
            <p>Да, принимаем срочные заявки для таможенного оформления. Стандартный ВЭД-документ объёмом до 5 страниц переводим за 4–6 часов при заявке до полудня. Для экспресс-переводов к вечеру того же дня — пишите в WhatsApp напрямую: Ольга согласует точные сроки за несколько минут. Срочный перевод — коэффициент ×1,5 к базовой стоимости.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Нужен ли заверенный перевод для таможни?</summary>
          <div class="faq-body">
            <p>Для большинства таможенных документов (инвойсы, коносаменты, CMR, сертификаты происхождения) достаточно подписи переводчика с оговоркой о точности перевода. Нотариальное заверение требуется в отдельных случаях — например, для регистрации оборудования или отдельных видов разрешительных документов. Мы бесплатно уточним требования конкретного таможенного органа и организуем нотариальное заверение по запросу.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Как вы работаете с кодами ТН ВЭД?</summary>
          <div class="faq-body">
            <p>Наши переводчики знают систему ТН ВЭД ЕАЭС и её соответствие HS-кодам. При переводе технической документации на ввозимые товары мы обращаем внимание на наименования, описания и характеристики, которые влияют на классификацию по ТН ВЭД. При неоднозначном описании товара в оригинале — сообщаем клиенту до передачи готового перевода, чтобы исключить риск неверной классификации.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Переводите ли вы MSDS/SDS листы безопасности?</summary>
          <div class="faq-body">
            <p>Да, переводим паспорта безопасности (MSDS, SDS, ПСББ) в соответствии с требованиями ГОСТ 30333-2007 и регламентом REACH. Переводчики знают стандартную структуру 16-раздельного SDS и обязательную терминологию системы ГХС/СГС (GHS). Перевод выполняется с учётом требований ЕАЭС к маркировке опасных веществ.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Как вы работаете с документами от китайских поставщиков?</summary>
          <div class="faq-body">
            <p>Регулярно переводим документацию по китайским цепочкам поставок: контракты, инвойсы, упаковочные листы, сертификаты качества, технические паспорта на оборудование. Переводчики знают типичные форматы китайских торговых документов и их особенности. При выявлении расхождений между реквизитами разных документов (например, между инвойсом и упаковочным листом) — сообщаем клиенту отдельно.</p>
          </div>
        </details>

      </div>
    </div>
  </section>

  <!-- ════════ КАЛЬКУЛЯТОР СТОИМОСТИ ════════ -->',
        'patentnye-perevody' => '<section class="sec sec-intro">
    <div class="container">
      <div class="intro-grid">
        <div class="intro-text">
          <p class="intro-label">Бюро переводов «Ремарка», Москва</p>
          <h1 class="intro-title">Перевод патентов и интеллектуальной собственности в Москве</h1>
          <p class="intro-tagline">На пересечении технического знания и юридической точности</p>
          <p class="intro-body">Патентный перевод — это не просто перевод технического текста. Формула изобретения — юридический документ: неточный термин может сузить объём правовой охраны, сделать патентную заявку уязвимой или вовсе непригодной для подачи. Переводчик без патентного опыта этого не увидит.</p>
          <p class="intro-body">Московское бюро «Ремарка» привлекает переводчиков с двойной квалификацией — техническим образованием в профильной области и знанием патентного права. Они работают с требованиями ВОИС, ЕПО, USPTO и Роспатента и знают, как термин читается в каждой из этих систем.</p>
          <p class="intro-body">Работаем исключительно с корпоративными клиентами: патентными поверенными, IP-отделами, R&amp;D-подразделениями и фармацевтическими компаниями. NDA подписывается до передачи файлов.</p>
        </div>
        <div class="intro-visual">
          <div class="intro-illus" aria-hidden="true">
            <svg viewBox="0 0 480 200" fill="none" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <linearGradient id="ip-g1" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#783CF0"/><stop offset="100%" stop-color="#00A0F0"/></linearGradient>
              </defs>
              <rect x="10" y="20" width="140" height="168" rx="12" fill="rgba(120,60,240,.10)" stroke="rgba(120,60,240,.35)" stroke-width="1.5"/>
              <rect x="10" y="20" width="140" height="38" rx="12" fill="rgba(120,60,240,.20)"/>
              <rect x="10" y="43" width="140" height="15" fill="rgba(120,60,240,.20)"/>
              <rect x="24" y="29" width="32" height="16" rx="8" fill="rgba(0,160,240,.40)"/>
              <rect x="24" y="76" width="112" height="5" rx="2.5" fill="rgba(167,139,250,.45)"/>
              <rect x="24" y="88" width="84" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="100" width="102" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="112" width="70" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="124" width="110" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="136" width="90" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="330" y="20" width="140" height="168" rx="12" fill="rgba(0,160,240,.10)" stroke="rgba(0,160,240,.35)" stroke-width="1.5"/>
              <rect x="330" y="20" width="140" height="38" rx="12" fill="rgba(0,160,240,.20)"/>
              <rect x="330" y="43" width="140" height="15" fill="rgba(0,160,240,.20)"/>
              <rect x="344" y="29" width="32" height="16" rx="8" fill="rgba(120,60,240,.40)"/>
              <rect x="344" y="76" width="112" height="5" rx="2.5" fill="rgba(0,160,240,.45)"/>
              <rect x="344" y="88" width="96" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="100" width="86" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="112" width="112" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="124" width="74" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <circle cx="240" cy="104" r="46" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
              <circle cx="240" cy="104" r="30" fill="rgba(120,60,240,.18)"/>
              <path d="M152 104h56M288 104h40" stroke="rgba(120,60,240,.40)" stroke-width="1.5" stroke-dasharray="4 3"/>
              <path d="M224 104h32M246 96l10 8-10 8" stroke="rgba(167,139,250,.95)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="intro-features">
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
              </div>
              <div><strong>Техническое + юридическое образование</strong><span>Переводчик понимает изобретение и патентную систему</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              </div>
              <div><strong>Конфиденциальность</strong><span>NDA до передачи файлов, закрытая Translation Memory</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
              </div>
              <div><strong>Знание патентных систем</strong><span>ВОИС, ЕПО, USPTO, JPO, Роспатент — требования каждого</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
              </div>
              <div><strong>Точная передача формулы</strong><span>Объём охраны сохраняется — нумерация и структура неизменны</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ЦИФРЫ ════════ -->
  <section class="sec-stats">
    <div class="container">
      <div class="stats-row">
        <div class="stat-item">
          <span class="stat-num">25<span class="stat-suffix">+</span></span>
          <span class="stat-label">лет на рынке</span>
        </div>
        <div class="stat-item">
          <span class="stat-num">60<span class="stat-suffix">+</span></span>
          <span class="stat-label">языков перевода</span>
        </div>
        <div class="stat-item">
          <span class="stat-num">2 400<span class="stat-suffix">+</span></span>
          <span class="stat-label">выполненных заказов</span>
        </div>
        <div class="stat-item">
          <span class="stat-num">4.98<span class="stat-suffix">★</span></span>
          <span class="stat-label">средний рейтинг</span>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ЯЗЫКОВЫЕ ПАРЫ ════════ -->
  <section class="sec sec--alt sec-lang-pairs">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Языковые пары для патентных документов</h2>
        <p class="sec-sub">Переводчики знают патентную систему страны оригинала — не только язык</p>
      </div>
      <div class="fl-grid" style="grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:20px">
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">EN ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Английский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Заявки USPTO, PCT на английском, EPO (EN), патентные лицензии по праву США/UK</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">DE ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Немецкий</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Заявки EPO на немецком, патенты Германии (DPMA), технические описания немецких изобретений</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">FR ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Французский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Заявки EPO на французском, патенты INPI (Франция), документы ВОИС на французском</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">ZH ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Китайский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Патенты CNIPA (Китай), PCT-заявки на китайском, лицензионные договоры ИС с КНР</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">JP ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Японский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Патенты JPO (Япония), PCT-заявки на японском, технические описания японских компаний</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">KO ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Корейский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Патенты KIPO (Корея), документы ИС корейских технологических компаний</p>
        </div>
      </div>
      <p style="text-align:center;font-size:13px;color:var(--text-secondary)">Другие языки — испанский, португальский, нидерландский, итальянский и ещё 50+. <a href="#calc-section" style="color:var(--accent)">Уточните наличие патентного переводчика</a></p>
    </div>
  </section>

  <!-- ════════ КАКИЕ ДОКУМЕНТЫ ════════ -->
  <section class="sec sec-docs">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Какие документы мы переводим</h2>
        <p class="sec-sub">Раскройте нужную категорию — посмотрите конкретные виды патентных документов</p>
      </div>
      <div class="docs-accordion">

        <details class="doc-item" open>
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            <span>Патентные заявки PCT (ВОИС)</span>
          </summary>
          <div class="doc-body">
            <p>Полные пакеты PCT-заявок: описание изобретения, формула, реферат, чертежи с подписями. Перевод с учётом требований Международного бюро ВОИС.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            <span>Патентные заявки EPO</span>
          </summary>
          <div class="doc-body">
            <p>Заявки в Европейское патентное ведомство на английском, немецком и французском языках. Перевод с соблюдением требований EPC.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="8" y1="8" x2="16" y2="8"/></svg>
            <span>Патентные заявки USPTO</span>
          </summary>
          <div class="doc-body">
            <p>Заявки в Патентное ведомство США: utility patents, design patents, provisional applications. Терминология американской патентной практики.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <span>Описания изобретений и формулы</span>
          </summary>
          <div class="doc-body">
            <p>Описания изобретений с сохранением технической точности. Формулы изобретений с точной передачей независимых и зависимых пунктов.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            <span>Полезные модели и промышленные образцы</span>
          </summary>
          <div class="doc-body">
            <p>Заявки на полезные модели (utility models) и промышленные образцы (design patents) для российских и иностранных ведомств.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
            <span>Товарные знаки и лицензионные договоры ИС</span>
          </summary>
          <div class="doc-body">
            <p>Заявки на регистрацию товарных знаков (ВОИС, национальные ведомства), лицензионные договоры на ИС, договоры уступки патентных прав.</p>
          </div>
        </details>

      </div>
    </div>
  </section>

  <!-- ════════ SPLIT ════════ -->
  <section class="sec sec-split">
    <div class="container">
      <div class="split-row split-row--rev">
        <div class="split-text">
          <p class="sec-label">Патентная точность</p>
          <h2>Почему патентный перевод требует специалиста</h2>
          <p>Формула изобретения — это юридический документ, который определяет границы исключительного права. Каждый пункт формулы должен быть переведён так, чтобы объём охраны в целевой стране соответствовал замыслу изобретателя.</p>
          <p>Переводчик без патентного опыта может непреднамеренно сузить или расширить объём притязаний, использовать термин, уже занятый в патентной базе, или нарушить формальные требования ведомства — всё это ведёт к отказу или ослаблению защиты.</p>
          <ul class="split-checklist">
            <li>Переводчик с техническим профильным образованием</li>
            <li>Знание требований ВОИС, ЕПО, USPTO, JPO, Роспатента</li>
            <li>Точная передача нумерации и структуры формулы</li>
            <li>Корректура патентным экспертом для Премиум-формата</li>
          </ul>
        </div>
        <div class="split-visual" aria-hidden="true">
          <svg viewBox="0 0 380 320" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <linearGradient id="sq-g1-p" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#783CF0"/><stop offset="100%" stop-color="#00A0F0"/></linearGradient>
              <linearGradient id="sq-g2-p" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#783CF0" stop-opacity=".5"/><stop offset="100%" stop-color="#00A0F0" stop-opacity=".5"/></linearGradient>
            </defs>
            <circle cx="190" cy="160" r="130" fill="rgba(120,60,240,.07)"/>
            <rect x="60" y="30" width="260" height="64" rx="16" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="62" r="20" fill="url(#sq-g1-p)" opacity=".85"/>
            <path d="M88 62l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="50" width="170" height="9" rx="4" fill="rgba(167,139,250,.55)"/>
            <rect x="126" y="66" width="120" height="7" rx="3" fill="rgba(167,139,250,.30)"/>
            <line x1="190" y1="94" x2="190" y2="118" stroke="url(#sq-g2-p)" stroke-width="2" stroke-dasharray="4 3"/>
            <rect x="60" y="118" width="260" height="64" rx="16" fill="rgba(0,160,240,.12)" stroke="rgba(0,160,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="150" r="20" fill="url(#sq-g1-p)" opacity=".85"/>
            <path d="M88 150l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="138" width="155" height="9" rx="4" fill="rgba(0,160,240,.55)"/>
            <rect x="126" y="154" width="110" height="7" rx="3" fill="rgba(0,160,240,.30)"/>
            <line x1="190" y1="182" x2="190" y2="206" stroke="url(#sq-g2-p)" stroke-width="2" stroke-dasharray="4 3"/>
            <rect x="60" y="206" width="260" height="64" rx="16" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="238" r="20" fill="url(#sq-g1-p)" opacity=".85"/>
            <path d="M88 238l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="226" width="175" height="9" rx="4" fill="rgba(167,139,250,.55)"/>
            <rect x="126" y="242" width="130" height="7" rx="3" fill="rgba(167,139,250,.30)"/>
            <circle cx="310" cy="290" r="26" fill="rgba(0,200,100,.12)" stroke="rgba(0,200,100,.35)" stroke-width="1.5"/>
            <path d="M300 290l8 8 14-16" stroke="rgba(0,200,100,.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ КАК ПРОХОДИТ РАБОТА ════════ -->
  <section class="sec sec--alt sec-how-pro">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Как проходит работа</h2>
        <p class="sec-sub">Шесть шагов от патентного файла до готового перевода</p>
      </div>
      <div class="steps-row steps-row--6">
        <div class="step-item">
          <div class="step-num">01</div>
          <h3>Загрузите файл</h3>
          <p>Через сайт, мессенджер, email или принесите в офис</p>
        </div>
        <div class="step-item">
          <div class="step-num">02</div>
          <h3>Анализ документа</h3>
          <p>Определяем патентное ведомство, тематику и объём</p>
        </div>
        <div class="step-item">
          <div class="step-num">03</div>
          <h3>Подбор специалиста</h3>
          <p>Выбираем переводчика с профильным техническим и патентным опытом</p>
        </div>
        <div class="step-item">
          <div class="step-num">04</div>
          <h3>Перевод</h3>
          <p>Точная передача формулы, описания и реферата с сохранением структуры</p>
        </div>
        <div class="step-item">
          <div class="step-num">05</div>
          <h3>Корректура</h3>
          <p>Редактор проверяет терминологию ВОИС/ЕПО и соответствие требованиям ведомства</p>
        </div>
        <div class="step-item">
          <div class="step-num">06</div>
          <h3>Готовый перевод</h3>
          <p>Отправляем файл в нужном формате — точно в срок</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ СРАВНЕНИЕ ФОРМАТОВ ════════ -->
  <section class="sec sec-comparison">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Какой формат вам подходит</h2>
        <p class="sec-sub">Сравните три уровня сервиса для патентных переводов</p>
      </div>
      <div class="cmp-table-wrap">
        <table class="cmp-table">
          <thead>
            <tr>
              <th></th>
              <th>Постредактирование ИИ</th>
              <th class="cmp-featured">Профессиональный перевод</th>
              <th>Премиум-перевод</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>Переводчик с техническим + юридическим образованием</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Знание патентной системы страны назначения</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Точная передача формулы изобретения</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Терминология ВОИС / ЕПО</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>NDA переводчика (до передачи файлов)</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Корректура патентным экспертом</td><td class="cmp-no">✗</td><td class="cmp-no cmp-featured">✗</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Вычитка носителем языка</td><td class="cmp-no">✗</td><td class="cmp-no cmp-featured">✗</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Подходит для подачи в патентное ведомство</td><td class="cmp-no">✗</td><td class="cmp-part cmp-featured">условно</td><td class="cmp-yes">✓</td></tr>
            <tr class="cmp-meta"><td>Срок</td><td>от 1 дня</td><td class="cmp-featured">1–3 дня</td><td>2–5 дней</td></tr>
            <tr class="cmp-meta cmp-price-row"><td>Стоимость</td><td>от 300 ₽/стр.</td><td class="cmp-featured">от 500 ₽/стр.</td><td>от 900 ₽/стр.</td></tr>
          </tbody>
          <tfoot>
            <tr>
              <td></td>
              <td><a href="#calc-section" class="price-btn price-btn--outline">Выбрать</a></td>
              <td class="cmp-featured"><a href="#calc-section" class="price-btn price-btn--primary">Выбрать</a></td>
              <td><a href="#calc-section" class="price-btn price-btn--outline">Выбрать</a></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </section>

  <!-- ════════ СРОКИ И СТОИМОСТЬ ════════ -->
  <section class="sec sec--alt sec-vol-pricing">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Сроки и стоимость</h2>
        <p class="sec-sub">Цена зависит от объёма и сложности. Точный расчёт — бесплатно, за несколько минут</p>
      </div>
      <div class="vol-table-wrap">
        <table class="vol-table">
          <thead>
            <tr>
              <th>Объём</th>
              <th>Срок</th>
              <th>Стоимость</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>До 10 страниц</strong></td>
              <td>1–2 рабочих дня</td>
              <td class="vol-price">от 5 000 ₽</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>10–30 страниц</strong></td>
              <td>2–4 рабочих дня</td>
              <td class="vol-price">от 500 ₽/стр.</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>30–80 страниц</strong></td>
              <td>4–7 рабочих дней</td>
              <td class="vol-price">от 470 ₽/стр.</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>Свыше 80 страниц</strong></td>
              <td>по договорённости</td>
              <td class="vol-price">от 430 ₽/стр.</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
          </tbody>
        </table>
      </div>
      <p class="vol-note">Срочный перевод (менее 24 часов) — коэффициент ×1,5 к базовой стоимости. Точный расчёт после анализа вашего файла.</p>
    </div>
  </section>

  <!-- ════════ СПРАВОЧНИК ПО ДОКУМЕНТАМ ════════ -->
  <section class="sec sec-doc-reference">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Справочник по патентным документам</h2>
        <p class="sec-sub">Выберите тип документа — получите расчёт стоимости перевода</p>
      </div>
      <div class="doc-ref-grid">
        <a href="#calc-section" class="doc-ref-card">
          <strong>Описания изобретений</strong>
          <span>Полные описания с техническими характеристиками и областью применения</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Формулы изобретений</strong>
          <span>Независимые и зависимые пункты с сохранением объёма охраны</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Рефераты PCT</strong>
          <span>Краткие описания для Международного бюро ВОИС</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Заявки ВОИС (PCT)</strong>
          <span>Полные пакеты PCT-заявок для международной охраны</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Заявки в ЕПО</strong>
          <span>Европейские патентные заявки — EN, DE, FR</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Заявки USPTO</strong>
          <span>Utility patents, design patents, provisional applications</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Полезные модели</strong>
          <span>Заявки на полезные модели для российских и иностранных ведомств</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Промышленные образцы</strong>
          <span>Design patents и заявки на промышленные образцы</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Товарные знаки</strong>
          <span>Заявки на регистрацию ТЗ в ВОИС, Роспатент, национальные ведомства</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Лицензионные договоры ИС</strong>
          <span>Лицензии на патенты, know-how, программное обеспечение</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Договоры уступки патентных прав</strong>
          <span>Полная и частичная уступка прав на изобретения и ИС</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Отчёты о патентном поиске</strong>
          <span>ISR, IPRP, национальные отчёты о поиске и экспертизе</span>
        </a>
      </div>
    </div>
  </section>

  <!-- ════════ ГАРАНТИИ ════════ -->
  <section class="sec sec--alt sec-guarantees">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Наши гарантии</h2>
        <p class="sec-sub">Мы отвечаем за результат — не только словами</p>
      </div>
      <div class="guarantees-grid">
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.9"/></svg>
          </div>
          <h3>Бесплатная правка</h3>
          <p>Исправим бесплатно в течение 7 дней, если перевод не соответствует требованиям ведомства или содержит неточности</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </div>
          <h3>Конфиденциальность</h3>
          <p>NDA до передачи файлов. Закрытая Translation Memory — данные не попадают в общие облачные базы</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </div>
          <h3>Соблюдение сроков</h3>
          <p>Если не уложились по нашей вине — возвращаем предоплату или делаем скидку на следующий заказ</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
          </div>
          <h3>Глоссарий клиента</h3>
          <p>Сохраняем терминологию вашего портфеля патентов — единые термины во всех будущих заказах</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ОТЗЫВЫ ════════ -->
  <section class="sec sec-reviews">
    <div class="container">
      <div class="reviews-head">
        <div>
          <h2 class="sec-title">Что говорят клиенты</h2>
          <p class="sec-sub" style="margin-top:6px">Реальные отзывы о патентных переводах</p>
        </div>
        <div class="review-rating-block">
          <div class="review-rating-score">4.98</div>
          <div class="review-stars-row">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          </div>
          <div class="review-rating-label">средний рейтинг</div>
        </div>
        <div class="review-platform-links">
          <a href="https://yandex.ru/maps/org/remarka/51867347382/reviews/" class="review-platform-link" target="_blank" rel="noopener">Яндекс</a>
          <a href="https://maps.app.goo.gl/d8BKJYw81PqBHXvz7" class="review-platform-link" target="_blank" rel="noopener">Google</a>
        </div>
      </div>
      <div class="reviews-grid" style="grid-template-columns:repeat(3,1fr)">
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">М</div>
            <div class="review-meta"><div class="review-name">Михаил Захаров</div><div class="review-src">Яндекс · январь 2025</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Заказывал перевод PCT-заявки по химии. Формула изобретения передана точно, нумерация сохранена, терминология соответствует требованиям ВОИС. Заявка принята без замечаний.</p>
          <div class="review-author-role">Патентный поверенный, Москва</div>
        </div>
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">Е</div>
            <div class="review-meta"><div class="review-name">Елена Сорокина</div><div class="review-src">Google · октябрь 2024</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Переводим лицензионные договоры ИС для нашей фармацевтической компании. Бюро знает специфику отрасли, конфиденциальность соблюдается строго. Рекомендую.</p>
          <div class="review-author-role">Директор по ИС, фармацевтическая компания</div>
        </div>
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">А</div>
            <div class="review-meta"><div class="review-name">Андрей Петров</div><div class="review-src">Яндекс · июль 2024</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Заказывал перевод немецких патентов по машиностроению для нашего R&amp;D-отдела. Технические термины переданы точно, переводчик понимает предметную область.</p>
          <div class="review-author-role">Директор R&amp;D, машиностроительная компания</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ FAQ ════════ -->
  <section class="sec sec--alt sec-faq">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Часто задаваемые вопросы</h2>
        <p class="sec-sub">Не нашли ответа — напишите Ольге в чат, ответим за минуту</p>
      </div>
      <div class="faq-list">

        <details class="faq-item">
          <summary class="faq-summary">Каковы требования Роспатента к переводу патентной заявки?</summary>
          <div class="faq-body">
            <p>Роспатент принимает переводы на русский язык, выполненные профессиональным переводчиком. Перевод должен точно воспроизводить структуру оригинала: описание изобретения, формулу, реферат, подписи к чертежам. Нумерация пунктов формулы сохраняется строго. Мы оформляем пакет документов в соответствии с Административным регламентом Роспатента.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Как вы работаете с нумерацией пунктов формулы изобретения?</summary>
          <div class="faq-body">
            <p>Нумерация пунктов формулы сохраняется строго в соответствии с оригиналом. Независимые и зависимые пункты переводятся с учётом их юридического значения: конструкция «по п.1» или «отличающийся тем, что» требует точной передачи. Переводчик не перестраивает порядок пунктов и не объединяет их без явного поручения клиента.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Как вы переводите химические формулы и технические чертежи?</summary>
          <div class="faq-body">
            <p>Химические формулы и структурные формулы воспроизводятся без изменений — они универсальны. Подписи к чертежам переводятся с сохранением номеров позиций. Если требуется замена текстовых надписей внутри чертежа, работаем с исходными форматами (AI, DWG, PDF с векторным слоем) или выполняем правку в редакторе.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Каковы сроки перевода PCT-заявки для ВОИС?</summary>
          <div class="faq-body">
            <p>Стандартный срок перевода PCT-заявки объёмом 20–40 страниц — 2–4 рабочих дня. Срочный перевод (до 24 часов) возможен при объёме до 15 страниц с коэффициентом ×1,5. Для крупных пакетов формируем команду переводчиков с единым глоссарием и параллельной работой.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Нужен ли заверенный перевод для подачи в патентное ведомство?</summary>
          <div class="faq-body">
            <p>Требования зависят от ведомства. Для Роспатента, как правило, достаточно перевода с оговоркой переводчика. Для отдельных национальных ведомств (например, при входе в национальную фазу PCT) может потребоваться нотариальное заверение. Мы уточняем требования конкретного ведомства и организуем заверение по запросу.</p>
          </div>
        </details>

      </div>
    </div>
  </section>

  <!-- ════════ КАЛЬКУЛЯТОР ════════ -->',
        'nauchnyy-perevod' => '<section class="sec sec-intro">
    <div class="container">
      <div class="intro-grid">
        <div class="intro-text">
          <p class="intro-label">Бюро переводов «Ремарка», Москва</p>
          <h1 class="intro-title">Научный перевод в Москве</h1>
          <p class="intro-tagline">Уровень peer-review — не просто перевод слов</p>
          <p class="intro-body">Научный перевод требует понимания методологии, статистической терминологии и конвенций научного письма в конкретной дисциплине. Переводчик без профильного образования может правильно перевести слова — и при этом нарушить смысл раздела «Методы» или неверно интерпретировать p-значение.</p>
          <p class="intro-body">Бюро «Ремарка» привлекает переводчиков с учёными степенями в профильных областях: биологии, химии, физике, медицине, технических и социальных науках. Они знают стили APA, Vancouver, Chicago и требования конкретных журналов Scopus и Web of Science.</p>
          <p class="intro-body">Работаем с университетами, НИИ, R&amp;D-отделами корпораций и издательствами. Конфиденциальность гарантирована — NDA по запросу.</p>
        </div>
        <div class="intro-visual">
          <div class="intro-illus" aria-hidden="true">
            <svg viewBox="0 0 480 200" fill="none" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <linearGradient id="ip-g1" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#783CF0"/><stop offset="100%" stop-color="#00A0F0"/></linearGradient>
              </defs>
              <rect x="10" y="20" width="140" height="168" rx="12" fill="rgba(120,60,240,.10)" stroke="rgba(120,60,240,.35)" stroke-width="1.5"/>
              <rect x="10" y="20" width="140" height="38" rx="12" fill="rgba(120,60,240,.20)"/>
              <rect x="10" y="43" width="140" height="15" fill="rgba(120,60,240,.20)"/>
              <rect x="24" y="29" width="32" height="16" rx="8" fill="rgba(0,160,240,.40)"/>
              <rect x="24" y="76" width="112" height="5" rx="2.5" fill="rgba(167,139,250,.45)"/>
              <rect x="24" y="88" width="84" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="100" width="102" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="112" width="70" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="124" width="110" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="136" width="90" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="330" y="20" width="140" height="168" rx="12" fill="rgba(0,160,240,.10)" stroke="rgba(0,160,240,.35)" stroke-width="1.5"/>
              <rect x="330" y="20" width="140" height="38" rx="12" fill="rgba(0,160,240,.20)"/>
              <rect x="330" y="43" width="140" height="15" fill="rgba(0,160,240,.20)"/>
              <rect x="344" y="29" width="32" height="16" rx="8" fill="rgba(120,60,240,.40)"/>
              <rect x="344" y="76" width="112" height="5" rx="2.5" fill="rgba(0,160,240,.45)"/>
              <rect x="344" y="88" width="96" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="100" width="86" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="112" width="112" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="124" width="74" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <circle cx="240" cy="104" r="46" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
              <circle cx="240" cy="104" r="30" fill="rgba(120,60,240,.18)"/>
              <path d="M152 104h56M288 104h40" stroke="rgba(120,60,240,.40)" stroke-width="1.5" stroke-dasharray="4 3"/>
              <path d="M224 104h32M246 96l10 8-10 8" stroke="rgba(167,139,250,.95)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="intro-features">
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
              </div>
              <div><strong>Профильная учёная степень</strong><span>Переводчик — специалист в вашей области науки</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
              </div>
              <div><strong>Стили APA / Vancouver / Chicago</strong><span>Оформление по требованиям целевого журнала</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
              </div>
              <div><strong>Scopus / Web of Science / ВАК</strong><span>Знаем требования ведущих баз научной литературы</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              </div>
              <div><strong>Конфиденциальность</strong><span>NDA по запросу, данные не передаются третьим лицам</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ЦИФРЫ ════════ -->
  <section class="sec-stats">
    <div class="container">
      <div class="stats-row">
        <div class="stat-item">
          <span class="stat-num">25<span class="stat-suffix">+</span></span>
          <span class="stat-label">лет на рынке</span>
        </div>
        <div class="stat-item">
          <span class="stat-num">60<span class="stat-suffix">+</span></span>
          <span class="stat-label">языков перевода</span>
        </div>
        <div class="stat-item">
          <span class="stat-num">2 400<span class="stat-suffix">+</span></span>
          <span class="stat-label">выполненных заказов</span>
        </div>
        <div class="stat-item">
          <span class="stat-num">4.98<span class="stat-suffix">★</span></span>
          <span class="stat-label">средний рейтинг</span>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ЯЗЫКОВЫЕ ПАРЫ ════════ -->
  <section class="sec sec--alt sec-lang-pairs">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Языковые пары для научных документов</h2>
        <p class="sec-sub">Переводчики знают научные конвенции и стандарты публикации в каждой стране</p>
      </div>
      <div class="fl-grid" style="grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:20px">
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">EN ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Английский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Статьи для Nature, Science, Elsevier, Springer; гранты RFBR, RSF; требования APA и Vancouver</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">DE ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Немецкий</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Научные труды немецких университетов и НИИ, DFG-заявки, монографии издательств Springer/Wiley</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">FR ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Французский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Публикации французских университетов, ANR-заявки, стиль CNRS</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">ZH ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Китайский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Публикации китайских НИИ и университетов, NSFC-гранты, совместные исследовательские проекты</p>
        </div>
      </div>
      <p style="text-align:center;font-size:13px;color:var(--text-secondary)">Другие языки — японский, корейский, испанский, итальянский и ещё 50+. <a href="#calc-section" style="color:var(--accent)">Уточните наличие профильного переводчика</a></p>
    </div>
  </section>

  <!-- ════════ КАКИЕ ДОКУМЕНТЫ ════════ -->
  <section class="sec sec-docs">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Какие документы мы переводим</h2>
        <p class="sec-sub">Раскройте нужную категорию — посмотрите конкретные виды научных материалов</p>
      </div>
      <div class="docs-accordion">

        <details class="doc-item" open>
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            <span>Научные статьи</span>
          </summary>
          <div class="doc-body">
            <p>Оригинальные исследования, обзорные статьи, краткие сообщения для журналов Scopus и Web of Science. Оформление по стилю APA, Vancouver, Chicago или требованиям конкретного издания.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            <span>Диссертации и авторефераты</span>
          </summary>
          <div class="doc-body">
            <p>Кандидатские и докторские диссертации в полном объёме. Авторефераты для российских и иностранных диссертационных советов. Точное воспроизведение структуры и научного стиля.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="8" y1="8" x2="16" y2="8"/></svg>
            <span>Гранты и заявки на финансирование</span>
          </summary>
          <div class="doc-body">
            <p>Заявки в РНФ, РФФИ, Horizon Europe, DFG, ANR, NSFC. Перевод проектного описания, бюджетного обоснования и CV научного руководителя.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <span>Отчёты НИР и НИОКР</span>
          </summary>
          <div class="doc-body">
            <p>Промежуточные и итоговые научно-исследовательские отчёты. Технические отчёты по опытно-конструкторским работам. Оформление по ГОСТ и международным стандартам.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            <span>Тезисы конференций и монографии</span>
          </summary>
          <div class="doc-body">
            <p>Конференционные тезисы (abstracts) объёмом 200–500 слов. Монографии и книги глав. Лабораторные протоколы и технические регламенты.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
            <span>Стандарты и технические регламенты</span>
          </summary>
          <div class="doc-body">
            <p>Стандарты ИСО/ГОСТ, отраслевые нормативы, технические регламенты ЕС. Лабораторные журналы и SOP (стандартные операционные процедуры).</p>
          </div>
        </details>

      </div>
    </div>
  </section>

  <!-- ════════ ОБЛАСТИ НАУКИ ════════ -->
  <section class="sec sec--alt sec-industries-legal">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Области науки</h2>
        <p class="sec-sub">Переводчики с профильным образованием в каждой дисциплине</p>
      </div>
      <div class="industry-grid">
        <div class="industry-card">
          <strong>Физика и химия</strong>
          <span>Квантовая механика, органическая и неорганическая химия, материаловедение</span>
        </div>
        <div class="industry-card">
          <strong>Биология и медицина</strong>
          <span>Молекулярная биология, клинические исследования, фармакология, генетика</span>
        </div>
        <div class="industry-card">
          <strong>Технические науки</strong>
          <span>Машиностроение, электроника, строительство, энергетика, IT</span>
        </div>
        <div class="industry-card">
          <strong>Экономика и социология</strong>
          <span>Econometrics, социальные исследования, маркетинг, статистика</span>
        </div>
        <div class="industry-card">
          <strong>Геология и экология</strong>
          <span>Геохимия, климатология, природопользование, горное дело</span>
        </div>
        <div class="industry-card">
          <strong>Математика и IT</strong>
          <span>Прикладная математика, алгоритмы, машинное обучение, computer science</span>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ SPLIT ════════ -->
  <section class="sec sec-split">
    <div class="container">
      <div class="split-row split-row--rev">
        <div class="split-text">
          <p class="sec-label">Научная точность</p>
          <h2>Почему научный перевод требует профильного эксперта</h2>
          <p>Научный текст — это не просто сложная лексика. Это методология, которую нужно понимать. Неверно переведённый раздел «Методы» может сделать статью нерепродуцируемой. Неточная передача статистики — поводом для отклонения рецензентом.</p>
          <p>Наши переводчики работают с научными текстами как коллеги-учёные: они знают конвенции IMRAD, требования к описанию выборки, нотацию статистических тестов и правила цитирования в конкретном стиле.</p>
          <ul class="split-checklist">
            <li>Переводчик с профильной учёной степенью (PhD / к.н. / д.н.)</li>
            <li>Знание стандартов публикации (APA, Vancouver, Chicago, ГОСТ)</li>
            <li>Терминология вашей научной области</li>
            <li>Корректура научным редактором — носителем языка (Премиум)</li>
          </ul>
        </div>
        <div class="split-visual" aria-hidden="true">
          <svg viewBox="0 0 380 320" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <linearGradient id="sq-g1-n" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#783CF0"/><stop offset="100%" stop-color="#00A0F0"/></linearGradient>
              <linearGradient id="sq-g2-n" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#783CF0" stop-opacity=".5"/><stop offset="100%" stop-color="#00A0F0" stop-opacity=".5"/></linearGradient>
            </defs>
            <circle cx="190" cy="160" r="130" fill="rgba(120,60,240,.07)"/>
            <rect x="60" y="30" width="260" height="64" rx="16" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="62" r="20" fill="url(#sq-g1-n)" opacity=".85"/>
            <path d="M88 62l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="50" width="170" height="9" rx="4" fill="rgba(167,139,250,.55)"/>
            <rect x="126" y="66" width="120" height="7" rx="3" fill="rgba(167,139,250,.30)"/>
            <line x1="190" y1="94" x2="190" y2="118" stroke="url(#sq-g2-n)" stroke-width="2" stroke-dasharray="4 3"/>
            <rect x="60" y="118" width="260" height="64" rx="16" fill="rgba(0,160,240,.12)" stroke="rgba(0,160,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="150" r="20" fill="url(#sq-g1-n)" opacity=".85"/>
            <path d="M88 150l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="138" width="155" height="9" rx="4" fill="rgba(0,160,240,.55)"/>
            <rect x="126" y="154" width="110" height="7" rx="3" fill="rgba(0,160,240,.30)"/>
            <line x1="190" y1="182" x2="190" y2="206" stroke="url(#sq-g2-n)" stroke-width="2" stroke-dasharray="4 3"/>
            <rect x="60" y="206" width="260" height="64" rx="16" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="238" r="20" fill="url(#sq-g1-n)" opacity=".85"/>
            <path d="M88 238l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="226" width="175" height="9" rx="4" fill="rgba(167,139,250,.55)"/>
            <rect x="126" y="242" width="130" height="7" rx="3" fill="rgba(167,139,250,.30)"/>
            <circle cx="310" cy="290" r="26" fill="rgba(0,200,100,.12)" stroke="rgba(0,200,100,.35)" stroke-width="1.5"/>
            <path d="M300 290l8 8 14-16" stroke="rgba(0,200,100,.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ КАК ПРОХОДИТ РАБОТА ════════ -->
  <section class="sec sec--alt sec-how-pro">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Как проходит работа</h2>
        <p class="sec-sub">Шесть шагов от научного файла до публикационно-готового перевода</p>
      </div>
      <div class="steps-row steps-row--6">
        <div class="step-item">
          <div class="step-num">01</div>
          <h3>Загрузите файл</h3>
          <p>Через сайт, мессенджер, email или принесите в офис</p>
        </div>
        <div class="step-item">
          <div class="step-num">02</div>
          <h3>Анализ документа</h3>
          <p>Определяем область науки, целевой журнал и стиль оформления</p>
        </div>
        <div class="step-item">
          <div class="step-num">03</div>
          <h3>Подбор специалиста</h3>
          <p>Выбираем переводчика с учёной степенью в вашей дисциплине</p>
        </div>
        <div class="step-item">
          <div class="step-num">04</div>
          <h3>Перевод</h3>
          <p>Точная передача методологии, терминологии и научного стиля</p>
        </div>
        <div class="step-item">
          <div class="step-num">05</div>
          <h3>Корректура</h3>
          <p>Научный редактор проверяет терминологию и соответствие стилю издания</p>
        </div>
        <div class="step-item">
          <div class="step-num">06</div>
          <h3>Готовый перевод</h3>
          <p>Отправляем файл в нужном формате — точно в срок</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ СРАВНЕНИЕ ФОРМАТОВ ════════ -->
  <section class="sec sec-comparison">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Какой формат вам подходит</h2>
        <p class="sec-sub">Сравните три уровня сервиса для научных переводов</p>
      </div>
      <div class="cmp-table-wrap">
        <table class="cmp-table">
          <thead>
            <tr>
              <th></th>
              <th>Постредактирование ИИ</th>
              <th class="cmp-featured">Профессиональный перевод</th>
              <th>Премиум-перевод</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>Переводчик с профильной учёной степенью</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Знание стиля научного текста (APA/Vancouver)</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Терминология конкретной области науки</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>NDA переводчика (до передачи файлов)</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Корректура научным редактором</td><td class="cmp-no">✗</td><td class="cmp-no cmp-featured">✗</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Вычитка носителем языка</td><td class="cmp-no">✗</td><td class="cmp-no cmp-featured">✗</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Подходит для публикации в Scopus/Web of Science</td><td class="cmp-no">✗</td><td class="cmp-part cmp-featured">условно</td><td class="cmp-yes">✓</td></tr>
            <tr class="cmp-meta"><td>Срок</td><td>от 1 дня</td><td class="cmp-featured">1–4 дня</td><td>2–6 дней</td></tr>
            <tr class="cmp-meta cmp-price-row"><td>Стоимость</td><td>от 280 ₽/стр.</td><td class="cmp-featured">от 450 ₽/стр.</td><td>от 850 ₽/стр.</td></tr>
          </tbody>
          <tfoot>
            <tr>
              <td></td>
              <td><a href="#calc-section" class="price-btn price-btn--outline">Выбрать</a></td>
              <td class="cmp-featured"><a href="#calc-section" class="price-btn price-btn--primary">Выбрать</a></td>
              <td><a href="#calc-section" class="price-btn price-btn--outline">Выбрать</a></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </section>

  <!-- ════════ СРОКИ И СТОИМОСТЬ ════════ -->
  <section class="sec sec--alt sec-vol-pricing">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Сроки и стоимость</h2>
        <p class="sec-sub">Цена зависит от объёма и области науки. Точный расчёт — бесплатно, за несколько минут</p>
      </div>
      <div class="vol-table-wrap">
        <table class="vol-table">
          <thead>
            <tr>
              <th>Объём</th>
              <th>Срок</th>
              <th>Стоимость</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>Тезисы / аннотация</strong></td>
              <td>1 рабочий день</td>
              <td class="vol-price">от 2 500 ₽</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>Статья (до 20 стр.)</strong></td>
              <td>2–3 рабочих дня</td>
              <td class="vol-price">от 450 ₽/стр.</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>Диссертация (20–100 стр.)</strong></td>
              <td>5–10 рабочих дней</td>
              <td class="vol-price">от 420 ₽/стр.</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>Отчёт НИР / монография</strong></td>
              <td>по договорённости</td>
              <td class="vol-price">от 390 ₽/стр.</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
          </tbody>
        </table>
      </div>
      <p class="vol-note">Срочный перевод (менее 24 часов) — коэффициент ×1,5. Точный расчёт после анализа вашего файла.</p>
    </div>
  </section>

  <!-- ════════ СПРАВОЧНИК ════════ -->
  <section class="sec sec-doc-reference">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Справочник по научным документам</h2>
        <p class="sec-sub">Выберите тип документа — получите расчёт стоимости перевода</p>
      </div>
      <div class="doc-ref-grid">
        <a href="#calc-section" class="doc-ref-card">
          <strong>Научные статьи Scopus/WoS</strong>
          <span>Оригинальные исследования и обзоры для индексируемых журналов</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Диссертации (кандидатские/докторские)</strong>
          <span>Полный текст диссертации с сохранением структуры и научного стиля</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Авторефераты</strong>
          <span>Расширенные резюме диссертаций для диссертационных советов</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Заявки на гранты (РНФ, РФФИ)</strong>
          <span>Проектное описание, обоснование, CV руководителя</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Отчёты НИР</strong>
          <span>Промежуточные и итоговые отчёты по НИР и НИОКР</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Тезисы конференций</strong>
          <span>Abstracts и extended abstracts для международных конференций</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Монографии</strong>
          <span>Научные книги и главы книг для российских и иностранных издательств</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Лабораторные журналы</strong>
          <span>Протоколы экспериментов, SOP, лабораторные регламенты</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Технические регламенты</strong>
          <span>Отраслевые технические регламенты и нормативы</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Стандарты ИСО/ГОСТ</strong>
          <span>Перевод международных и российских стандартов</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Патентные описания</strong>
          <span>Описания изобретений как часть научной документации</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Академические резюме (CV)</strong>
          <span>Научные CV для международных грантов и позиций</span>
        </a>
      </div>
    </div>
  </section>

  <!-- ════════ ГАРАНТИИ ════════ -->
  <section class="sec sec--alt sec-guarantees">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Наши гарантии</h2>
        <p class="sec-sub">Мы отвечаем за результат — не только словами</p>
      </div>
      <div class="guarantees-grid">
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.9"/></svg>
          </div>
          <h3>Бесплатная правка</h3>
          <p>Исправим бесплатно в течение 7 дней, если перевод не соответствует стилю издания или содержит неточности</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </div>
          <h3>Конфиденциальность</h3>
          <p>NDA по запросу. Данные исследования не передаются третьим лицам и не используются для обучения ИИ</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </div>
          <h3>Соблюдение сроков</h3>
          <p>Если не уложились по нашей вине — возвращаем предоплату или делаем скидку на следующий заказ</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
          </div>
          <h3>Глоссарий клиента</h3>
          <p>Сохраняем терминологию ваших публикаций — при повторных заказах единая терминология и скидка по TM</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ОТЗЫВЫ ════════ -->
  <section class="sec sec-reviews">
    <div class="container">
      <div class="reviews-head">
        <div>
          <h2 class="sec-title">Что говорят клиенты</h2>
          <p class="sec-sub" style="margin-top:6px">Реальные отзывы о научных переводах</p>
        </div>
        <div class="review-rating-block">
          <div class="review-rating-score">4.98</div>
          <div class="review-stars-row">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          </div>
          <div class="review-rating-label">средний рейтинг</div>
        </div>
        <div class="review-platform-links">
          <a href="https://yandex.ru/maps/org/remarka/51867347382/reviews/" class="review-platform-link" target="_blank" rel="noopener">Яндекс</a>
          <a href="https://maps.app.goo.gl/d8BKJYw81PqBHXvz7" class="review-platform-link" target="_blank" rel="noopener">Google</a>
        </div>
      </div>
      <div class="reviews-grid" style="grid-template-columns:repeat(3,1fr)">
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">И</div>
            <div class="review-meta"><div class="review-name">Ирина Волкова</div><div class="review-src">Яндекс · февраль 2025</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Перевели нашу статью по молекулярной биологии для Elsevier. Переводчик явно понимает предмет — методы переданы точно, терминология соответствует стандартам журнала. Статья принята рецензентами.</p>
          <div class="review-author-role">Профессор, МГУ им. М.В. Ломоносова</div>
        </div>
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">С</div>
            <div class="review-meta"><div class="review-name">Сергей Кузнецов</div><div class="review-src">Google · ноябрь 2024</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Заказывали перевод итоговых отчётов НИР на английский для международного партнёра. Бюро справилось в срок, технические термины переданы корректно. Партнёр доволен.</p>
          <div class="review-author-role">Директор НИИ, Москва</div>
        </div>
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">Н</div>
            <div class="review-meta"><div class="review-name">Наталья Жукова</div><div class="review-src">Яндекс · август 2024</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Переводила заявку на грант Horizon Europe. Помогли с оформлением по требованиям программы, бюджетное обоснование переведено грамотно. Заявка прошла первый отбор.</p>
          <div class="review-author-role">Менеджер по грантам, исследовательский центр</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ FAQ ════════ -->
  <section class="sec sec--alt sec-faq">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Часто задаваемые вопросы</h2>
        <p class="sec-sub">Не нашли ответа — напишите Ольге в чат, ответим за минуту</p>
      </div>
      <div class="faq-list">

        <details class="faq-item">
          <summary class="faq-summary">Каковы требования к переводу диссертации?</summary>
          <div class="faq-body">
            <p>Перевод диссертации воспроизводит структуру оригинала: введение, обзор литературы, методы, результаты, обсуждение, заключение, список источников. Терминология соответствует принятым стандартам дисциплины. Для признания учёной степени за рубежом может потребоваться нотариальное заверение — уточняем требования конкретной страны бесплатно.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Какие требования Scopus/Web of Science к переводу статей?</summary>
          <div class="faq-body">
            <p>Редакции журналов Scopus и WoS требуют академического английского уровня native speaker. Аннотация, ключевые слова и список литературы оформляются по стилю издания (APA, Vancouver, Chicago, MLA). Наши переводчики знают требования конкретных журналов и при необходимости уточняют у клиента целевое издание до начала работы.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Как вы переводите формулы и химические уравнения?</summary>
          <div class="faq-body">
            <p>Математические и химические формулы воспроизводятся без изменений — они универсальны. Текстовые пояснения к формулам переводятся с сохранением нотации. Если документ в LaTeX — работаем с исходниками. Если формулы в Word (MathType, встроенный редактор) — не разрушаем форматирование при переводе.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Каковы сроки перевода для конференционных тезисов?</summary>
          <div class="faq-body">
            <p>Тезисы объёмом 200–500 слов переводим за 1 рабочий день. При срочном дедлайне (менее 24 часов) — коэффициент ×1,5. Рекомендуем присылать тезисы за 2–3 дня до дедлайна конференции, чтобы оставалось время для возможной доработки.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Нужен ли заверенный перевод для признания учёной степени?</summary>
          <div class="faq-body">
            <p>Для нострификации диплома и признания учёной степени за рубежом, как правило, требуется нотариально заверенный перевод диплома и диссертации (или автореферата). Мы переводим и организуем нотариальное заверение в Москве по запросу — без отдельных поездок.</p>
          </div>
        </details>

      </div>
    </div>
  </section>

  <!-- ════════ КАЛЬКУЛЯТОР ════════ -->',
        'delovaya-perepiska' => '<section class="sec sec-intro">
    <div class="container">
      <div class="intro-grid">
        <div class="intro-text">
          <p class="intro-label">Бюро переводов «Ремарка», Москва</p>
          <h1 class="intro-title">Перевод деловой переписки в Москве</h1>
          <p class="intro-tagline">Тон важен не меньше, чем текст</p>
          <p class="intro-body">Деловая переписка — это не просто информация. Это тон, регистр и сигнал об отношениях между компаниями. Слишком официальный перевод неформального письма создаёт дистанцию там, где её не должно быть. Слишком небрежный — производит впечатление непрофессионализма перед иностранным партнёром.</p>
          <p class="intro-body">Московское бюро «Ремарка» переводит деловую переписку с сохранением корпоративного tone of voice. Мы изучаем стиль вашей компании — и воспроизводим его в переводе. Для компаний с постоянным потоком писем доступен корпоративный абонемент с приоритетной обработкой.</p>
          <p class="intro-body">Работаем исключительно с корпоративными клиентами: международными компаниями, PR-отделами, отделами ВЭД, секретариатами и бизнес-ассистентами руководителей.</p>
        </div>
        <div class="intro-visual">
          <div class="intro-illus" aria-hidden="true">
            <svg viewBox="0 0 480 200" fill="none" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <linearGradient id="ip-g1" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#783CF0"/><stop offset="100%" stop-color="#00A0F0"/></linearGradient>
              </defs>
              <rect x="10" y="20" width="140" height="168" rx="12" fill="rgba(120,60,240,.10)" stroke="rgba(120,60,240,.35)" stroke-width="1.5"/>
              <rect x="10" y="20" width="140" height="38" rx="12" fill="rgba(120,60,240,.20)"/>
              <rect x="10" y="43" width="140" height="15" fill="rgba(120,60,240,.20)"/>
              <rect x="24" y="29" width="32" height="16" rx="8" fill="rgba(0,160,240,.40)"/>
              <rect x="24" y="76" width="112" height="5" rx="2.5" fill="rgba(167,139,250,.45)"/>
              <rect x="24" y="88" width="84" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="100" width="102" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="112" width="70" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="124" width="110" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="330" y="20" width="140" height="168" rx="12" fill="rgba(0,160,240,.10)" stroke="rgba(0,160,240,.35)" stroke-width="1.5"/>
              <rect x="330" y="20" width="140" height="38" rx="12" fill="rgba(0,160,240,.20)"/>
              <rect x="330" y="43" width="140" height="15" fill="rgba(0,160,240,.20)"/>
              <rect x="344" y="29" width="32" height="16" rx="8" fill="rgba(120,60,240,.40)"/>
              <rect x="344" y="76" width="112" height="5" rx="2.5" fill="rgba(0,160,240,.45)"/>
              <rect x="344" y="88" width="96" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="100" width="86" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="112" width="112" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="124" width="74" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <circle cx="240" cy="104" r="46" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
              <circle cx="240" cy="104" r="30" fill="rgba(120,60,240,.18)"/>
              <path d="M152 104h56M288 104h40" stroke="rgba(120,60,240,.40)" stroke-width="1.5" stroke-dasharray="4 3"/>
              <path d="M224 104h32M246 96l10 8-10 8" stroke="rgba(167,139,250,.95)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="intro-features">
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
              </div>
              <div><strong>Сохранение тона</strong><span>Адаптируем перевод под корпоративный tone of voice</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              </div>
              <div><strong>Быстрые сроки</strong><span>Небольшие письма — в тот же рабочий день</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
              </div>
              <div><strong>Шаблоны для типовых ситуаций</strong><span>Создаём библиотеку готовых формулировок для команды</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              </div>
              <div><strong>Конфиденциальность</strong><span>NDA по запросу, переписка не передаётся третьим лицам</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ЦИФРЫ ════════ -->
  <section class="sec-stats">
    <div class="container">
      <div class="stats-row">
        <div class="stat-item">
          <span class="stat-num">25<span class="stat-suffix">+</span></span>
          <span class="stat-label">лет на рынке</span>
        </div>
        <div class="stat-item">
          <span class="stat-num">60<span class="stat-suffix">+</span></span>
          <span class="stat-label">языков перевода</span>
        </div>
        <div class="stat-item">
          <span class="stat-num">2 400<span class="stat-suffix">+</span></span>
          <span class="stat-label">выполненных заказов</span>
        </div>
        <div class="stat-item">
          <span class="stat-num">4.98<span class="stat-suffix">★</span></span>
          <span class="stat-label">средний рейтинг</span>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ЯЗЫКОВЫЕ ПАРЫ ════════ -->
  <section class="sec sec--alt sec-lang-pairs">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Языковые пары для деловой переписки</h2>
        <p class="sec-sub">Переводчики понимают культурные конвенции деловой коммуникации каждой страны</p>
      </div>
      <div class="fl-grid" style="grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:20px">
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">EN ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Английский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Переписка с британскими, американскими и международными партнёрами. Стиль UK/US business English</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">DE ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Немецкий</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Переписка с немецкими, австрийскими и швейцарскими партнёрами. Немецкий деловой стиль с учётом формальности</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">FR ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Французский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Переписка с французскими и франкоязычными партнёрами. Стиль французской деловой корреспонденции</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">ZH ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Китайский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Переписка с китайскими партнёрами с учётом культурных норм коммуникации и иерархии</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">IT ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Итальянский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Деловая переписка с итальянскими компаниями, письма в итальянском деловом стиле</p>
        </div>
        <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <span class="fl-lang-pair">AR ↔ RU</span>
            <span style="font-size:12px;color:var(--text-secondary)">Арабский</span>
          </div>
          <p style="font-size:13px;color:var(--text-secondary);margin:0">Переписка с партнёрами из ОАЭ, Саудовской Аравии и других стран Ближнего Востока</p>
        </div>
      </div>
      <p style="text-align:center;font-size:13px;color:var(--text-secondary)">Другие языки — испанский, японский, корейский, турецкий и ещё 50+. <a href="#calc-section" style="color:var(--accent)">Уточните наличие переводчика</a></p>
    </div>
  </section>

  <!-- ════════ КАКИЕ ДОКУМЕНТЫ ════════ -->
  <section class="sec sec-docs">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Какие документы мы переводим</h2>
        <p class="sec-sub">Раскройте нужную категорию — посмотрите конкретные виды деловой переписки</p>
      </div>
      <div class="docs-accordion">

        <details class="doc-item" open>
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            <span>Деловые письма и email</span>
          </summary>
          <div class="doc-body">
            <p>Письма-запросы, письма-ответы, официальные уведомления, коммерческие предложения, ответы на претензии, поздравительные письма партнёрам.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
            <span>Протоколы совещаний и переговоров</span>
          </summary>
          <div class="doc-body">
            <p>Протоколы деловых переговоров, встреч с иностранными партнёрами, совещаний совета директоров. Сохранение структуры и точных формулировок принятых решений.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="8" y1="8" x2="16" y2="8"/></svg>
            <span>Меморандумы о взаимопонимании (MoU) и LOI</span>
          </summary>
          <div class="doc-body">
            <p>Меморандумы о взаимопонимании (MoU), письма о намерениях (LOI), соглашения о сотрудничестве. Перевод с сохранением юридически нейтральных формулировок.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <span>Внутренние регламенты и корпоративные объявления</span>
          </summary>
          <div class="doc-body">
            <p>Внутренние приказы и распоряжения, корпоративные новости, пресс-релизы, HR-коммуникации для международных команд.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            <span>Отчёты и презентации для иностранных партнёров</span>
          </summary>
          <div class="doc-body">
            <p>Бизнес-отчёты, executive summaries, презентации для иностранных инвесторов и партнёров. Локализация числовых форматов и единиц измерения.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
            <span>Претензии и жалобы</span>
          </summary>
          <div class="doc-body">
            <p>Претензионные письма, жалобы иностранным контрагентам, ответы на претензии. Тон строгий, но дипломатичный — сохраняем баланс.</p>
          </div>
        </details>

      </div>
    </div>
  </section>

  <!-- ════════ ОСОБЕННОСТИ ════════ -->
  <section class="sec sec--alt sec-split">
    <div class="container">
      <div class="split-row split-row--rev">
        <div class="split-text">
          <p class="sec-label">Деловой тон</p>
          <h2>Почему перевод деловой переписки — это про отношения</h2>
          <p>В деловой переписке смысл часто в том, чего нет в словах. Британское «We would appreciate your earliest attention» — это вежливое давление. Немецкое лаконичное «Bitte bestätigen Sie» — норма, а не грубость. Китайский партнёр, получивший слишком прямой ответ, почувствует неуважение.</p>
          <p>Наши переводчики знают культурные конвенции деловой коммуникации каждой страны. Они адаптируют тон под получателя, сохраняя вашу позицию и смысл.</p>
          <ul class="split-checklist">
            <li>Сохранение корпоративного tone of voice</li>
            <li>Адаптация под культурные нормы страны получателя</li>
            <li>Быстрые сроки — небольшие письма в тот же день</li>
            <li>Корпоративный абонемент для постоянного потока</li>
          </ul>
        </div>
        <div class="split-visual" aria-hidden="true">
          <svg viewBox="0 0 380 320" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <linearGradient id="sq-g1-d" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#783CF0"/><stop offset="100%" stop-color="#00A0F0"/></linearGradient>
              <linearGradient id="sq-g2-d" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#783CF0" stop-opacity=".5"/><stop offset="100%" stop-color="#00A0F0" stop-opacity=".5"/></linearGradient>
            </defs>
            <circle cx="190" cy="160" r="130" fill="rgba(120,60,240,.07)"/>
            <rect x="60" y="30" width="260" height="64" rx="16" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="62" r="20" fill="url(#sq-g1-d)" opacity=".85"/>
            <path d="M88 62l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="50" width="170" height="9" rx="4" fill="rgba(167,139,250,.55)"/>
            <rect x="126" y="66" width="120" height="7" rx="3" fill="rgba(167,139,250,.30)"/>
            <line x1="190" y1="94" x2="190" y2="118" stroke="url(#sq-g2-d)" stroke-width="2" stroke-dasharray="4 3"/>
            <rect x="60" y="118" width="260" height="64" rx="16" fill="rgba(0,160,240,.12)" stroke="rgba(0,160,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="150" r="20" fill="url(#sq-g1-d)" opacity=".85"/>
            <path d="M88 150l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="138" width="155" height="9" rx="4" fill="rgba(0,160,240,.55)"/>
            <rect x="126" y="154" width="110" height="7" rx="3" fill="rgba(0,160,240,.30)"/>
            <line x1="190" y1="182" x2="190" y2="206" stroke="url(#sq-g2-d)" stroke-width="2" stroke-dasharray="4 3"/>
            <rect x="60" y="206" width="260" height="64" rx="16" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="238" r="20" fill="url(#sq-g1-d)" opacity=".85"/>
            <path d="M88 238l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="226" width="175" height="9" rx="4" fill="rgba(167,139,250,.55)"/>
            <rect x="126" y="242" width="130" height="7" rx="3" fill="rgba(167,139,250,.30)"/>
            <circle cx="310" cy="290" r="26" fill="rgba(0,200,100,.12)" stroke="rgba(0,200,100,.35)" stroke-width="1.5"/>
            <path d="M300 290l8 8 14-16" stroke="rgba(0,200,100,.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ КАК ПРОХОДИТ РАБОТА ════════ -->
  <section class="sec sec-how-pro">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Как проходит работа</h2>
        <p class="sec-sub">Шесть шагов от письма до готового перевода</p>
      </div>
      <div class="steps-row steps-row--6">
        <div class="step-item">
          <div class="step-num">01</div>
          <h3>Загрузите файл</h3>
          <p>Через сайт, мессенджер, email или принесите в офис</p>
        </div>
        <div class="step-item">
          <div class="step-num">02</div>
          <h3>Анализ</h3>
          <p>Определяем получателя, тон и требования к стилю</p>
        </div>
        <div class="step-item">
          <div class="step-num">03</div>
          <h3>Подбор переводчика</h3>
          <p>Выбираем специалиста с опытом деловой коммуникации в нужной стране</p>
        </div>
        <div class="step-item">
          <div class="step-num">04</div>
          <h3>Перевод</h3>
          <p>Точная передача смысла с адаптацией тона под получателя</p>
        </div>
        <div class="step-item">
          <div class="step-num">05</div>
          <h3>Корректура</h3>
          <p>Редактор проверяет стиль и соответствие вашему tone of voice</p>
        </div>
        <div class="step-item">
          <div class="step-num">06</div>
          <h3>Готово</h3>
          <p>Отправляем файл — точно в срок, чаще быстрее</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ СРАВНЕНИЕ ФОРМАТОВ ════════ -->
  <section class="sec sec--alt sec-comparison">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Какой формат вам подходит</h2>
        <p class="sec-sub">Сравните три уровня сервиса для деловой переписки</p>
      </div>
      <div class="cmp-table-wrap">
        <table class="cmp-table">
          <thead>
            <tr>
              <th></th>
              <th>Постредактирование ИИ</th>
              <th class="cmp-featured">Профессиональный перевод</th>
              <th>Премиум-перевод</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>Переводчик с опытом деловой коммуникации</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Адаптация тона под получателя</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Учёт корпоративного tone of voice</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>NDA переводчика</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Создание шаблонов для типовых писем</td><td class="cmp-no">✗</td><td class="cmp-no cmp-featured">✗</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Вычитка носителем языка</td><td class="cmp-no">✗</td><td class="cmp-no cmp-featured">✗</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Приоритетная обработка (корпоративный абонемент)</td><td class="cmp-no">✗</td><td class="cmp-part cmp-featured">по запросу</td><td class="cmp-yes">✓</td></tr>
            <tr class="cmp-meta"><td>Срок (письмо 1–2 стр.)</td><td>от 2 часов</td><td class="cmp-featured">2–4 часа</td><td>4–8 часов</td></tr>
            <tr class="cmp-meta cmp-price-row"><td>Стоимость</td><td>от 200 ₽/стр.</td><td class="cmp-featured">от 400 ₽/стр.</td><td>от 700 ₽/стр.</td></tr>
          </tbody>
          <tfoot>
            <tr>
              <td></td>
              <td><a href="#calc-section" class="price-btn price-btn--outline">Выбрать</a></td>
              <td class="cmp-featured"><a href="#calc-section" class="price-btn price-btn--primary">Выбрать</a></td>
              <td><a href="#calc-section" class="price-btn price-btn--outline">Выбрать</a></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </section>

  <!-- ════════ СРОКИ И СТОИМОСТЬ ════════ -->
  <section class="sec sec-vol-pricing">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Сроки и стоимость</h2>
        <p class="sec-sub">Гибкое ценообразование: по письму, по странице или по абонементу</p>
      </div>
      <div class="vol-table-wrap">
        <table class="vol-table">
          <thead>
            <tr>
              <th>Формат заказа</th>
              <th>Срок</th>
              <th>Стоимость</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>Одно письмо (до 500 слов)</strong></td>
              <td>2–4 часа</td>
              <td class="vol-price">от 1 500 ₽</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>Пакет писем (до 5 стр.)</strong></td>
              <td>1 рабочий день</td>
              <td class="vol-price">от 400 ₽/стр.</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>Протоколы / MoU (5–20 стр.)</strong></td>
              <td>1–2 рабочих дня</td>
              <td class="vol-price">от 380 ₽/стр.</td>
              <td><a href="#calc-section" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>Корпоративный абонемент</strong></td>
              <td>приоритет</td>
              <td class="vol-price">от 25 000 ₽/мес.</td>
              <td><a href="#calc-section" class="vol-btn">Обсудить</a></td>
            </tr>
          </tbody>
        </table>
      </div>
      <p class="vol-note">Срочный перевод письма (менее 2 часов) — коэффициент ×1,5. Корпоративный абонемент включает приоритетную обработку и скидку на объём.</p>
    </div>
  </section>

  <!-- ════════ СПРАВОЧНИК ════════ -->
  <section class="sec sec--alt sec-doc-reference">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Справочник по документам деловой переписки</h2>
        <p class="sec-sub">Выберите тип документа — получите расчёт стоимости</p>
      </div>
      <div class="doc-ref-grid">
        <a href="#calc-section" class="doc-ref-card">
          <strong>Деловые письма-запросы</strong>
          <span>Запросы коммерческих предложений, информации, документов</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Письма-ответы</strong>
          <span>Официальные ответы на запросы и обращения партнёров</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Коммерческие предложения</strong>
          <span>КП для иностранных клиентов с адаптацией под рынок</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Протоколы переговоров</strong>
          <span>Minutes of meeting, протоколы деловых встреч</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Меморандумы MoU</strong>
          <span>Memoranda of Understanding для международного партнёрства</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>LOI (письма о намерениях)</strong>
          <span>Letters of Intent для сделок и сотрудничества</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Внутренние приказы и распоряжения</strong>
          <span>Локализация HR-документов для международных команд</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Пресс-релизы</strong>
          <span>Корпоративные пресс-релизы для иностранных СМИ</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Корпоративные новости</strong>
          <span>Внутренние корпоративные коммуникации на нескольких языках</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Жалобы и претензионные письма</strong>
          <span>Дипломатичные претензии с сохранением строгого тона</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Ответы на претензии</strong>
          <span>Ответы на жалобы с сохранением профессионального тона</span>
        </a>
        <a href="#calc-section" class="doc-ref-card">
          <strong>Поздравительные письма партнёрам</strong>
          <span>Корпоративные поздравления с учётом культурных норм</span>
        </a>
      </div>
    </div>
  </section>

  <!-- ════════ ГАРАНТИИ ════════ -->
  <section class="sec sec-guarantees">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Наши гарантии</h2>
        <p class="sec-sub">Мы отвечаем за результат — не только словами</p>
      </div>
      <div class="guarantees-grid">
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.9"/></svg>
          </div>
          <h3>Бесплатная правка</h3>
          <p>Исправим бесплатно в течение 7 дней, если перевод не соответствует заданному стилю или содержит неточности</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </div>
          <h3>Конфиденциальность</h3>
          <p>NDA по запросу. Деловая переписка не передаётся третьим лицам и не используется в обучающих целях</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </div>
          <h3>Соблюдение сроков</h3>
          <p>Если не уложились по нашей вине — возвращаем предоплату или делаем скидку на следующий заказ</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
          </div>
          <h3>Библиотека шаблонов</h3>
          <p>Сохраняем переведённые шаблоны писем — повторные заказы быстрее и дешевле</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ОТЗЫВЫ ════════ -->
  <section class="sec sec--alt sec-reviews">
    <div class="container">
      <div class="reviews-head">
        <div>
          <h2 class="sec-title">Что говорят клиенты</h2>
          <p class="sec-sub" style="margin-top:6px">Реальные отзывы о переводе деловой переписки</p>
        </div>
        <div class="review-rating-block">
          <div class="review-rating-score">4.98</div>
          <div class="review-stars-row">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          </div>
          <div class="review-rating-label">средний рейтинг</div>
        </div>
        <div class="review-platform-links">
          <a href="https://yandex.ru/maps/org/remarka/51867347382/reviews/" class="review-platform-link" target="_blank" rel="noopener">Яндекс</a>
          <a href="https://maps.app.goo.gl/d8BKJYw81PqBHXvz7" class="review-platform-link" target="_blank" rel="noopener">Google</a>
        </div>
      </div>
      <div class="reviews-grid" style="grid-template-columns:repeat(3,1fr)">
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">О</div>
            <div class="review-meta"><div class="review-name">Ольга Тихонова</div><div class="review-src">Яндекс · март 2025</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Переводим всю переписку с иностранными партнёрами через «Ремарку» уже два года. Тон всегда точный, письма выглядят как написанные носителями. Корпоративный абонемент — очень удобно.</p>
          <div class="review-author-role">Исполнительный ассистент, международная компания</div>
        </div>
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">К</div>
            <div class="review-meta"><div class="review-name">Кирилл Орлов</div><div class="review-src">Google · декабрь 2024</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Заказывал перевод пресс-релиза для иностранных СМИ. Сделали быстро, стиль соответствует нашей подаче. Немецкие партнёры оценили уровень.</p>
          <div class="review-author-role">PR-директор, технологическая компания</div>
        </div>
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">В</div>
            <div class="review-meta"><div class="review-name">Виктория Лебедева</div><div class="review-src">Яндекс · сентябрь 2024</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Нужен был срочный перевод претензионного письма китайскому партнёру. Сделали за 3 часа, тон именно тот, который нужен: строгий, но без агрессии. Переговоры прошли успешно.</p>
          <div class="review-author-role">Менеджер по развитию бизнеса</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ FAQ ════════ -->
  <section class="sec sec-faq">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Часто задаваемые вопросы</h2>
        <p class="sec-sub">Не нашли ответа — напишите Ольге в чат, ответим за минуту</p>
      </div>
      <div class="faq-list">

        <details class="faq-item">
          <summary class="faq-summary">Как быстро вы переводите деловое письмо?</summary>
          <div class="faq-body">
            <p>Стандартное деловое письмо объёмом до 500 слов переводим за 2–4 часа в рабочее время. При заказе до 12:00 — как правило, готово к концу рабочего дня. Срочный перевод (2–3 часа) доступен с коэффициентом ×1,5. Клиенты с корпоративным абонементом получают приоритет.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Можете ли вы сохранить корпоративный стиль нашей компании?</summary>
          <div class="faq-body">
            <p>Да. При первом заказе просим прислать образцы уже переведённых писем или краткое описание tone of voice — формальный, нейтральный, дружелюбный. На основе этого формируем стайл-гайд для переводчика. При повторных заказах тон остаётся единым без дополнительных инструкций.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Вы работаете с шаблонами деловых email?</summary>
          <div class="faq-body">
            <p>Да, переводим и адаптируем email-шаблоны для CRM-систем и регулярных рассылок. Создаём наборы шаблонов для типовых ситуаций: запрос КП, ответ на претензию, напоминание о платеже, поздравление — чтобы сотрудники могли использовать готовые формулировки без обращения к переводчику.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Есть ли у вас абонемент для регулярной переписки?</summary>
          <div class="faq-body">
            <p>Да, корпоративный абонемент предусматривает фиксированный ежемесячный объём с приоритетной обработкой и скидкой до 20% по сравнению с разовыми заказами. Подходит для компаний, у которых каждую неделю появляются новые письма и протоколы. Условия обсуждаем индивидуально.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Как обеспечивается конфиденциальность внутренней переписки?</summary>
          <div class="faq-body">
            <p>NDA подписывается до передачи файлов. Для корпоративных клиентов с постоянным потоком документов заключаем соглашение о конфиденциальности с организацией. Переписка хранится только на защищённых серверах, не передаётся третьим лицам и не используется для обучения ИИ-систем.</p>
          </div>
        </details>

      </div>
    </div>
  </section>

  <!-- ════════ КАЛЬКУЛЯТОР ════════ -->',
        'srochnyy-perevod' => '<section class="sec sec-intro">
    <div class="container">
      <div class="intro-grid">
        <div class="intro-text">
          <p class="intro-label">Бюро переводов «Ремарка», Москва</p>
          <h1 class="intro-title">Срочный перевод в Москве</h1>
          <p class="intro-tagline">Когда времени нет — мы уже работаем</p>
          <p class="intro-body">Срочный перевод — это не отдельная услуга, а режим работы. Контракт нужно подписать завтра утром, посольство принимает документы только до пятницы, врач за рубежом ждёт выписку прямо сейчас. Мы принимаем такие заказы круглосуточно, включая выходные и праздники.</p>
          <p class="intro-body">Скорость не означает небрежность: срочный перевод в московском бюро «Ремарка» выполняет профильный специалист, а не первый попавшийся фрилансер. Из пула свободных переводчиков мы подбираем нужного — за 15–30 минут после подтверждения заказа.</p>
          <p class="intro-body">Заказать срочный перевод документов можно через сайт, Telegram или WhatsApp. Коэффициент срочности — ×1,5 к базовой стоимости. При объёме до 5 страниц и заказе до 12:00 перевод готов в тот же день.</p>
        </div>
        <div class="intro-visual">
          <div class="intro-illus" aria-hidden="true">
            <svg viewBox="0 0 480 200" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect x="10" y="20" width="140" height="168" rx="12" fill="rgba(120,60,240,.10)" stroke="rgba(120,60,240,.35)" stroke-width="1.5"/>
              <rect x="10" y="20" width="140" height="38" rx="12" fill="rgba(120,60,240,.20)"/>
              <rect x="10" y="43" width="140" height="15" fill="rgba(120,60,240,.20)"/>
              <rect x="24" y="29" width="32" height="16" rx="8" fill="rgba(0,160,240,.40)"/>
              <rect x="28" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="42" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="24" y="76" width="112" height="5" rx="2.5" fill="rgba(167,139,250,.45)"/>
              <rect x="24" y="88" width="84" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="100" width="102" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="112" width="70" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="124" width="110" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="136" width="90" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="148" width="98" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="160" width="76" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="330" y="20" width="140" height="168" rx="12" fill="rgba(0,160,240,.10)" stroke="rgba(0,160,240,.35)" stroke-width="1.5"/>
              <rect x="330" y="20" width="140" height="38" rx="12" fill="rgba(0,160,240,.20)"/>
              <rect x="330" y="43" width="140" height="15" fill="rgba(0,160,240,.20)"/>
              <rect x="344" y="29" width="32" height="16" rx="8" fill="rgba(120,60,240,.40)"/>
              <rect x="348" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="362" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="344" y="76" width="112" height="5" rx="2.5" fill="rgba(0,160,240,.45)"/>
              <rect x="344" y="88" width="96" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="100" width="86" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="112" width="112" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="124" width="74" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="136" width="106" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="148" width="88" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="160" width="100" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <circle cx="240" cy="104" r="46" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
              <circle cx="240" cy="104" r="30" fill="rgba(120,60,240,.18)"/>
              <path d="M152 104h56M288 104h40" stroke="rgba(120,60,240,.40)" stroke-width="1.5" stroke-dasharray="4 3"/>
              <path d="M224 104h32M246 96l10 8-10 8" stroke="rgba(167,139,250,.95)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="intro-features">
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              </div>
              <div><strong>Приём заявок 24/7</strong><span>через сайт, Telegram, WhatsApp, email</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
              </div>
              <div><strong>Перевод «день в день»</strong><span>до 5–7 страниц при заказе утром</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
              </div>
              <div><strong>Пул 50+ специалистов</strong><span>назначаем переводчика за 15–30 минут</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
              </div>
              <div><strong>Без снижения качества</strong><span>редактор даже при срочном заказе</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

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

  <!-- ════════ КАКИЕ ДОКУМЕНТЫ ════════ -->
  <section class="sec sec--alt sec-docs">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Какие документы мы переводим срочно</h2>
        <p class="sec-sub">Раскройте нужную категорию — посмотрите конкретные виды документов</p>
      </div>
      <div class="docs-accordion">

        <details class="doc-item" open>
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <span>Документы для посольств и виз</span>
          </summary>
          <div class="doc-body">
            <p>Анкеты, справки, свидетельства, дипломы — принимаем круглосуточно. Перевод для консульств и посольств с соблюдением требований к форматированию.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            <span>Деловые контракты и соглашения</span>
          </summary>
          <div class="doc-body">
            <p>Срочное подписание, тендерная документация, финальные версии договоров. Юрист-переводчик с пониманием коммерческого права.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
            <span>Медицинские документы</span>
          </summary>
          <div class="doc-body">
            <p>Экстренные ситуации: выписки для лечения за рубежом, медкарты, рецепты. Медицинский переводчик с профильным образованием.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
            <span>Технические документы</span>
          </summary>
          <div class="doc-body">
            <p>Срочные сертификации, таможенное оформление, технические паспорта на оборудование.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
            <span>Юридические материалы</span>
          </summary>
          <div class="doc-body">
            <p>Судебные заседания, нотариальные действия, апостили. Понимание процессуальных сроков и требований.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l19-9-9 19-2-8-8-2z"/></svg>
            <span>Маркетинговые и PR-тексты</span>
          </summary>
          <div class="doc-body">
            <p>Пресс-релизы, рекламные кампании, материалы к мероприятиям. Перевод с адаптацией под целевую аудиторию.</p>
          </div>
        </details>

      </div>
    </div>
  </section>

  <!-- ════════ НАШИ ПЕРЕВОДЧИКИ ════════ -->
  <section class="sec sec-translators">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Переводчики на срочных заказах в Москве</h2>
        <p class="sec-sub">Срочные переводчики московского бюро «Ремарка» — это не отдельная категория: те же профильные специалисты работают в ускоренном режиме, не теряя в качестве.</p>
      </div>
      <div class="translators-grid">
        <div class="translator-card">
          <div class="translator-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </div>
          <h3>Пул 50+ специалистов</h3>
          <p>всегда есть свободный эксперт по вашей теме и языковой паре</p>
        </div>
        <div class="translator-card">
          <div class="translator-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
          </div>
          <h3>Мгновенный старт</h3>
          <p>назначаем переводчика за 15–30 минут после подтверждения заказа</p>
        </div>
        <div class="translator-card">
          <div class="translator-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><path d="M21 12c0 4.97-4.03 9-9 9S3 16.97 3 12 7.03 3 12 3s9 4.03 9 9z"/></svg>
          </div>
          <h3>Контроль качества</h3>
          <p>редактор подключается параллельно, не замедляя работу</p>
        </div>
      </div>
    </div>
  </section>

  <section class="sec sec-split">
    <div class="container">
      <div class="split-row split-row--rev">
        <div class="split-text">
          <p class="sec-label">Скорость без потери качества</p>
          <h2>Как мы успеваем быстро и не ошибаемся</h2>
          <p>Срочный заказ — это не повод снижать качество. Ускорение достигается за счёт пула свободных специалистов и параллельной работы: пока переводчик работает над текстом, редактор готов принять работу немедленно.</p>
          <p>Мы не берём срочные заказы у случайных исполнителей. Переводчик назначается из проверенного пула, где у каждого — верифицированная специализация и задокументированный опыт срочных проектов.</p>
          <ul class="split-checklist">
            <li>Пул 50+ свободных специалистов по разным тематикам</li>
            <li>Назначение переводчика за 15–30 минут после оплаты</li>
            <li>Редактор работает параллельно, не задерживая сдачу</li>
            <li>Приём файлов 24/7 — Telegram, WhatsApp, сайт, email</li>
          </ul>
        </div>
        <div class="split-visual" aria-hidden="true">
          <svg viewBox="0 0 380 320" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <linearGradient id="sq-g1-s" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#783CF0"/><stop offset="100%" stop-color="#00A0F0"/></linearGradient>
              <linearGradient id="sq-g2-s" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#783CF0" stop-opacity=".5"/><stop offset="100%" stop-color="#00A0F0" stop-opacity=".5"/></linearGradient>
            </defs>
            <circle cx="190" cy="160" r="130" fill="rgba(120,60,240,.07)"/>
            <rect x="60" y="30" width="260" height="64" rx="16" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="62" r="20" fill="url(#sq-g1-s)" opacity=".85"/>
            <path d="M88 62l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="50" width="170" height="9" rx="4" fill="rgba(167,139,250,.55)"/>
            <rect x="126" y="66" width="120" height="7" rx="3" fill="rgba(167,139,250,.30)"/>
            <line x1="190" y1="94" x2="190" y2="118" stroke="url(#sq-g2-s)" stroke-width="2" stroke-dasharray="4 3"/>
            <rect x="60" y="118" width="260" height="64" rx="16" fill="rgba(0,160,240,.12)" stroke="rgba(0,160,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="150" r="20" fill="url(#sq-g1-s)" opacity=".85"/>
            <path d="M88 150l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="138" width="155" height="9" rx="4" fill="rgba(0,160,240,.55)"/>
            <rect x="126" y="154" width="110" height="7" rx="3" fill="rgba(0,160,240,.30)"/>
            <line x1="190" y1="182" x2="190" y2="206" stroke="url(#sq-g2-s)" stroke-width="2" stroke-dasharray="4 3"/>
            <rect x="60" y="206" width="260" height="64" rx="16" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="238" r="20" fill="url(#sq-g1-s)" opacity=".85"/>
            <path d="M88 238l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="226" width="175" height="9" rx="4" fill="rgba(167,139,250,.55)"/>
            <rect x="126" y="242" width="130" height="7" rx="3" fill="rgba(167,139,250,.30)"/>
            <circle cx="310" cy="290" r="26" fill="rgba(0,200,100,.12)" stroke="rgba(0,200,100,.35)" stroke-width="1.5"/>
            <path d="M300 290l8 8 14-16" stroke="rgba(0,200,100,.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ КАК ПРОХОДИТ РАБОТА (6 шагов) ════════ -->
  <section class="sec sec--alt sec-how-pro">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Как проходит работа</h2>
        <p class="sec-sub">Шесть шагов от файла до готового перевода — с контролем качества на каждом этапе</p>
      </div>
      <div class="steps-row steps-row--6">
        <div class="step-item">
          <div class="step-num">01</div>
          <h3>Загрузите файл</h3>
          <p>Через сайт, мессенджер, email или принесите в офис</p>
        </div>
        <div class="step-item">
          <div class="step-num">02</div>
          <h3>Анализ документа</h3>
          <p>Оцениваем объём, тематику и сложность — за несколько минут</p>
        </div>
        <div class="step-item">
          <div class="step-num">03</div>
          <h3>Подбор специалиста</h3>
          <p>Выбираем переводчика с профильным образованием в вашей области</p>
        </div>
        <div class="step-item">
          <div class="step-num">04</div>
          <h3>Перевод</h3>
          <p>Специалист работает с соблюдением терминологии и стиля оригинала</p>
        </div>
        <div class="step-item">
          <div class="step-num">05</div>
          <h3>Корректура</h3>
          <p>Редактор проверяет точность, стиль и соответствие заданию</p>
        </div>
        <div class="step-item">
          <div class="step-num">06</div>
          <h3>Готовый перевод</h3>
          <p>Отправляем файл или вы забираете из офиса — точно в срок</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ГАРАНТИИ КАЧЕСТВА ════════ -->
  <section class="sec sec-guarantees">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Наши гарантии</h2>
        <p class="sec-sub">Мы отвечаем за результат — не только словами</p>
      </div>
      <div class="guarantees-grid">
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.9"/></svg>
          </div>
          <h3>Бесплатная правка</h3>
          <p>Исправим бесплатно в течение 7 дней, если перевод не соответствует заданию или содержит неточности</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          </div>
          <h3>Конфиденциальность</h3>
          <p>NDA по запросу. Ваши документы не передаются третьим лицам и не хранятся на внешних серверах</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </div>
          <h3>Соблюдение сроков</h3>
          <p>Если не уложились в оговорённый срок по нашей вине — возвращаем предоплату или делаем скидку на следующий заказ</p>
        </div>
        <div class="guarantee-card">
          <div class="guarantee-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
          </div>
          <h3>База терминологии</h3>
          <p>Сохраняем глоссарий вашей компании — при повторных заказах терминология остаётся единой, перевод быстрее</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ СРОКИ И СТОИМОСТЬ (СРОЧНЫЙ ТАРИФ) ════════ -->
  <section class="sec sec--alt sec-vol-pricing">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Сроки и стоимость срочного перевода</h2>
        <p class="sec-sub">Точный расчёт — бесплатно, за 5 минут. Пришлите файл прямо сейчас.</p>
      </div>
      <div class="vol-table-wrap">
        <table class="vol-table">
          <thead>
            <tr>
              <th>Объём</th>
              <th>Срок</th>
              <th>Стоимость</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>1–2 страницы</strong></td>
              <td>от 3 часов</td>
              <td class="vol-price">от 750 ₽</td>
              <td><a href="/stoimost-perevoda/" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>До 5 страниц</strong></td>
              <td>1 рабочий день</td>
              <td class="vol-price">от 3 750 ₽</td>
              <td><a href="/stoimost-perevoda/" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>5–15 страниц</strong></td>
              <td>1–2 дня</td>
              <td class="vol-price">от 375 ₽/стр.</td>
              <td><a href="/stoimost-perevoda/" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>15–30 страниц</strong></td>
              <td>2–3 дня</td>
              <td class="vol-price">от 360 ₽/стр.</td>
              <td><a href="/stoimost-perevoda/" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>Свыше 30 страниц</strong></td>
              <td>по договорённости</td>
              <td class="vol-price">от 350 ₽/стр.</td>
              <td><a href="/stoimost-perevoda/" class="vol-btn">Заказать</a></td>
            </tr>
          </tbody>
        </table>
      </div>
      <p class="vol-note">Срочность ×1,5 к базовой цене. При объёме &gt; 30 страниц подключаем команду переводчиков.</p>
    </div>
  </section>

  <!-- ════════ СРАВНЕНИЕ ФОРМАТОВ ════════ -->
  <section class="sec sec-comparison">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Какой формат вам подходит</h2>
        <p class="sec-sub">Сравните три уровня сервиса и выберите оптимальный для вашей задачи</p>
      </div>
      <div class="cmp-table-wrap">
        <table class="cmp-table">
          <thead>
            <tr>
              <th></th>
              <th>Постредактирование ИИ</th>
              <th class="cmp-featured">Профессиональный перевод</th>
              <th>Премиум-перевод</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>Перевод с нуля специалистом</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Профильное образование переводчика</td><td class="cmp-no">✗</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Корректура и вычитка</td><td class="cmp-part">частичная</td><td class="cmp-yes cmp-featured">✓</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Редактура профильным экспертом</td><td class="cmp-no">✗</td><td class="cmp-no cmp-featured">✗</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Вычитка носителем языка</td><td class="cmp-no">✗</td><td class="cmp-no cmp-featured">✗</td><td class="cmp-yes">✓</td></tr>
            <tr><td>Подходит для публикаций и тендеров</td><td class="cmp-no">✗</td><td class="cmp-part cmp-featured">условно</td><td class="cmp-yes">✓</td></tr>
            <tr class="cmp-meta"><td>Срок</td><td>от 1 дня</td><td class="cmp-featured">1–3 дня</td><td>2–5 дней</td></tr>
            <tr class="cmp-meta cmp-price-row"><td>Стоимость</td><td>от 375 ₽/стр.</td><td class="cmp-featured">от 750 ₽/стр.</td><td>от 1200 ₽/стр.</td></tr>
          </tbody>
          <tfoot>
            <tr>
              <td></td>
              <td><a href="/stoimost-perevoda/" class="price-btn price-btn--outline">Выбрать</a></td>
              <td class="cmp-featured"><a href="/stoimost-perevoda/" class="price-btn price-btn--primary">Выбрать</a></td>
              <td><a href="/stoimost-perevoda/" class="price-btn price-btn--outline">Выбрать</a></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </section>

  <!-- ════════ СПРАВОЧНИК ПО ДОКУМЕНТАМ ════════ -->
  <section class="sec sec-doc-reference">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Справочник по документам</h2>
        <p class="sec-sub">Выберите тип документа — узнайте сроки и стоимость срочного перевода</p>
      </div>
      <div class="doc-ref-grid">

        <div class="doc-ref-card">
          <div class="doc-ref-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="2"/><path d="M9 9h6M9 12h6M9 15h4"/></svg>
          </div>
          <h3>Визовые документы</h3>
          <p>Анкеты, приглашения, справки для посольств. Принимаем круглосуточно, перевод готов от 3 часов.</p>
          <a href="#" class="doc-ref-more">Подробнее →</a>
        </div>

        <div class="doc-ref-card">
          <div class="doc-ref-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
          </div>
          <h3>Деловые контракты</h3>
          <p>Срочное подписание договоров с иностранными партнёрами. Юридически точный перевод без потери в скорости.</p>
          <a href="#" class="doc-ref-more">Подробнее →</a>
        </div>

        <div class="doc-ref-card">
          <div class="doc-ref-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
          </div>
          <h3>Судебные материалы</h3>
          <p>Исковые заявления, решения судов, доверенности для судебных заседаний. Понимаем процессуальные сроки.</p>
          <a href="#" class="doc-ref-more">Подробнее →</a>
        </div>

        <div class="doc-ref-card">
          <div class="doc-ref-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
          </div>
          <h3>Медицинские документы</h3>
          <p>Экстренные выписки, рецепты, истории болезни для лечения за рубежом. Медицинский переводчик 24/7.</p>
          <a href="#" class="doc-ref-more">Подробнее →</a>
        </div>

        <div class="doc-ref-card">
          <div class="doc-ref-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
          </div>
          <h3>Технические сертификаты</h3>
          <p>Для таможенного оформления, сертификации оборудования, деклараций соответствия.</p>
          <a href="#" class="doc-ref-more">Подробнее →</a>
        </div>

        <div class="doc-ref-card">
          <div class="doc-ref-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
          </div>
          <h3>Авиа и транспорт</h3>
          <p>Грузовые накладные, коносаменты, перевозочные документы. Срочно при таможенных задержках.</p>
          <a href="#" class="doc-ref-more">Подробнее →</a>
        </div>

        <div class="doc-ref-card">
          <div class="doc-ref-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/></svg>
          </div>
          <h3>Банковские документы</h3>
          <p>Выписки, гарантийные письма, SWIFT-документация для срочных финансовых операций.</p>
          <a href="#" class="doc-ref-more">Подробнее →</a>
        </div>

        <div class="doc-ref-card">
          <div class="doc-ref-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
          </div>
          <h3>Нотариальные акты</h3>
          <p>Доверенности, согласия, завещания. Перевод для нотариального заверения в ускоренном режиме.</p>
          <a href="#" class="doc-ref-more">Подробнее →</a>
        </div>

        <div class="doc-ref-card">
          <div class="doc-ref-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l19-9-9 19-2-8-8-2z"/></svg>
          </div>
          <h3>Пресс-релизы</h3>
          <p>Срочные PR-материалы к мероприятиям и новостным поводам. Адаптация под аудиторию.</p>
          <a href="#" class="doc-ref-more">Подробнее →</a>
        </div>

        <div class="doc-ref-card">
          <div class="doc-ref-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
          </div>
          <h3>Патентные заявки</h3>
          <p>Срочная подача в РОСПАТЕНТ или ВОИС — соблюдение дедлайнов приоритетной даты.</p>
          <a href="#" class="doc-ref-more">Подробнее →</a>
        </div>

        <div class="doc-ref-card">
          <div class="doc-ref-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          </div>
          <h3>Страховые документы</h3>
          <p>Полисы, заявления на выплату, медицинские заключения для страховых случаев.</p>
          <a href="#" class="doc-ref-more">Подробнее →</a>
        </div>

        <div class="doc-ref-card">
          <div class="doc-ref-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
          </div>
          <h3>Академические документы</h3>
          <p>Дипломы, транскрипты, рекомендательные письма для срочной подачи документов.</p>
          <a href="#" class="doc-ref-more">Подробнее →</a>
        </div>

      </div>
    </div>
  </section>

  <!-- ════════ ПЕРЕВОДЫ ПО ОТРАСЛЯМ ════════ -->
  <section class="sec sec--alt sec-industries">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Переводы по отраслям</h2>
        <p class="sec-sub">Срочный перевод в тех сферах, где дедлайны критичны</p>
      </div>
      <div class="industries-grid">

        <div class="industry-card">
          <div class="industry-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
          </div>
          <h3>Посольства и визовые центры</h3>
          <p>Срочная подготовка пакета документов для консульских требований.</p>
          <ul class="industry-docs">
            <li>Анкеты и приглашения</li>
            <li>Свидетельства о браке и рождении</li>
            <li>Справки о несудимости</li>
          </ul>
          <a href="#" class="industry-more">Подробнее →</a>
        </div>

        <div class="industry-card">
          <div class="industry-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
          </div>
          <h3>Международная торговля</h3>
          <p>Документы для экспорта и импорта с жёсткими таможенными сроками.</p>
          <ul class="industry-docs">
            <li>Контракты и спецификации</li>
            <li>Таможенные декларации</li>
            <li>Сертификаты происхождения</li>
          </ul>
          <a href="#" class="industry-more">Подробнее →</a>
        </div>

        <div class="industry-card">
          <div class="industry-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
          </div>
          <h3>Медицина и здравоохранение</h3>
          <p>Срочные переводы при экстренных медицинских ситуациях за рубежом.</p>
          <ul class="industry-docs">
            <li>Медкарты и эпикризы</li>
            <li>Направления на лечение</li>
            <li>Страховые документы</li>
          </ul>
          <a href="#" class="industry-more">Подробнее →</a>
        </div>

        <div class="industry-card">
          <div class="industry-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          </div>
          <h3>Юриспруденция</h3>
          <p>Юридические документы к судебным заседаниям с фиксированными датами.</p>
          <ul class="industry-docs">
            <li>Судебные решения и иски</li>
            <li>Доверенности и соглашения</li>
            <li>Арбитражные материалы</li>
          </ul>
          <a href="#" class="industry-more">Подробнее →</a>
        </div>

        <div class="industry-card">
          <div class="industry-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="18" rx="2"/><path d="M8 3v18M16 3v18M2 12h20"/></svg>
          </div>
          <h3>Промышленность и экспорт</h3>
          <p>Сертификация оборудования и таможенное оформление в сжатые сроки.</p>
          <ul class="industry-docs">
            <li>Технические паспорта</li>
            <li>Декларации соответствия</li>
            <li>Экспортные лицензии</li>
          </ul>
          <a href="#" class="industry-more">Подробнее →</a>
        </div>

        <div class="industry-card">
          <div class="industry-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
          </div>
          <h3>Медиа и PR</h3>
          <p>Срочные переводы для новостных поводов и международных мероприятий.</p>
          <ul class="industry-docs">
            <li>Пресс-релизы</li>
            <li>Интервью и комментарии</li>
            <li>Материалы к конференциям</li>
          </ul>
          <a href="#" class="industry-more">Подробнее →</a>
        </div>

        <div class="industry-card">
          <div class="industry-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
          </div>
          <h3>Банки и финансы</h3>
          <p>Срочные финансовые операции требуют переведённых документов день в день.</p>
          <ul class="industry-docs">
            <li>Банковские гарантии</li>
            <li>Кредитные соглашения</li>
            <li>Инвестиционные меморандумы</li>
          </ul>
          <a href="#" class="industry-more">Подробнее →</a>
        </div>

        <div class="industry-card">
          <div class="industry-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="2"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
          </div>
          <h3>Транспорт и логистика</h3>
          <p>Грузовые документы при срочных поставках и таможенных задержках.</p>
          <ul class="industry-docs">
            <li>Товарно-транспортные накладные</li>
            <li>Коносаменты</li>
            <li>Карнеты АТА</li>
          </ul>
          <a href="#" class="industry-more">Подробнее →</a>
        </div>

        <div class="industry-card">
          <div class="industry-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
          </div>
          <h3>Наука и академия</h3>
          <p>Дедлайны подачи документов в иностранные университеты и журналы.</p>
          <ul class="industry-docs">
            <li>Дипломы и транскрипты</li>
            <li>Рекомендательные письма</li>
            <li>Академические статьи</li>
          </ul>
          <a href="#" class="industry-more">Подробнее →</a>
        </div>

      </div>
    </div>
  </section>

  <!-- ════════ ОТЗЫВЫ ════════ -->
  <section class="sec sec--alt sec-reviews">
    <div class="container">
      <div class="reviews-head">
        <div>
          <h2 class="sec-title">Что говорят клиенты</h2>
          <p class="sec-sub" style="margin-top:6px">Реальные отзывы о срочных переводах</p>
        </div>
        <div class="review-rating-block">
          <div class="review-rating-score">4.98</div>
          <div class="review-stars-row">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          </div>
          <div class="review-rating-label">средний рейтинг</div>
        </div>
        <div class="review-platform-links">
          <a href="https://yandex.ru/maps/org/remarka/51867347382/reviews/" class="review-platform-link" target="_blank" rel="noopener">Яндекс</a>
          <a href="https://maps.app.goo.gl/d8BKJYw81PqBHXvz7" class="review-platform-link" target="_blank" rel="noopener">Google Maps</a>
          <a href="https://2gis.ru/" class="review-platform-link" target="_blank" rel="noopener">2ГИС</a>
        </div>
      </div>
      <div class="reviews-grid" style="grid-template-columns:repeat(3,1fr)">
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">А</div>
            <div class="review-meta"><div class="review-name">Артём Беляев</div><div class="review-src">Яндекс · Директор по логистике, ТЦ «Меркурий»</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Регулярно нужны срочные переводы транспортных документов — иногда ночью перед отправкой груза. Всегда берут в работу, всегда укладываются. За два года работы не подвели ни разу.</p>
        </div>
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">Е</div>
            <div class="review-meta"><div class="review-name">Елена Романова</div><div class="review-src">Google Maps · Юрист, международная практика</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Заказывала срочный перевод судебных документов для заседания на следующий день. 14 страниц, юридический английский — всё готово было к утру. Качество хорошее, никаких замечаний от коллег.</p>
        </div>
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">В</div>
            <div class="review-meta"><div class="review-name">Владимир Касаткин</div><div class="review-src">2ГИС · Собственник, ВЭД</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Отличное бюро для срочных заказов. Перевод контракта на немецкий за 4 часа — и качество не пострадало. Стоимость с коэффициентом разумная, ничего скрытого.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ FAQ ════════ -->
  <section class="sec sec-faq">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Часто задаваемые вопросы</h2>
        <p class="sec-sub">Не нашли ответа — напишите Ольге в чат, ответим за минуту</p>
      </div>
      <div class="faq-list">

        <details class="faq-item">
          <summary class="faq-summary">Можно ли заказать перевод ночью или в выходные?</summary>
          <div class="faq-body">
            <p>Да, принимаем заказы круглосуточно через сайт, Telegram и WhatsApp. Переводчик назначается в течение 15–30 минут после подтверждения. В выходные и праздники работаем в штатном режиме.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">За сколько часов можно перевести 1 страницу?</summary>
          <div class="faq-body">
            <p>1–2 страницы переводим за 3–4 часа. Точный срок зависит от тематики, языковой пары и текущей загрузки переводчиков. Напишите нам — скажем точно.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Не пострадает ли качество при срочном переводе?</summary>
          <div class="faq-body">
            <p>Нет. При любом заказе — обычном или срочном — перевод проходит редакторскую проверку. Редактор работает параллельно с переводчиком, не замедляя процесс.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Как рассчитывается надбавка за срочность?</summary>
          <div class="faq-body">
            <p>Коэффициент срочности — ×1,5 к базовой стоимости. Например, стандартная страница стоит 250 ₽, срочная — 375 ₽. Итоговая сумма рассчитывается при приёме заказа.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Переводите ли вы срочно на редкие языки?</summary>
          <div class="faq-body">
            <p>Работаем с 60+ языками, в том числе редкими. Для экзотических языковых пар время поиска переводчика может быть чуть больше — уточняйте при заказе.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Где заказать срочный перевод в Москве прямо сейчас?</summary>
          <div class="faq-body">
            <p>Напишите в Telegram или WhatsApp, или заполните форму на сайте. Ответим за 5 минут, назначим переводчика за 15–30 минут. Работаем круглосуточно.</p>
          </div>
        </details>

      </div>
    </div>
  </section>

  <!-- ════════ КАЛЬКУЛЯТОР СТОИМОСТИ ════════ -->',
        'khudozhestvennyy-perevod' => '<section class="sec sec-intro">
    <div class="container">
      <div class="intro-grid">
        <div class="intro-text">
          <p class="intro-label">Бюро переводов «Ремарка», Москва</p>
          <h1 class="intro-title">Художественный перевод в Москве</h1>
          <p class="intro-tagline">Перевести — значит написать заново</p>
          <p class="intro-body">Художественный текст живёт не словами, а интонацией, ритмом и пространством между строк. Именно поэтому его нельзя ни перевести буквально, ни отдать машине: автоматический перевод убивает то, что делает литературу литературой. Нужно не заменить слова, а написать текст заново — но так, чтобы читатель на другом языке почувствовал то же, что и читатель оригинала.</p>
          <p class="intro-body">Художественный перевод в московском бюро «Ремарка» выполняют практикующие литераторы: писатели, поэты, сценаристы и редакторы, у которых есть собственный литературный опыт. Они умеют слышать авторский голос и воспроизводить его, а не просто передавать значение.</p>
          <p class="intro-body">Заказать художественный перевод книги у нас означает получить текст, который пройдёт редактора издательства. Мы работаем с прозой, поэзией, детскими книгами, комиксами и сценариями — и для каждого жанра подбираем переводчика с соответствующим опытом.</p>
        </div>
        <div class="intro-visual">
          <div class="intro-illus" aria-hidden="true">
            <svg viewBox="0 0 480 200" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect x="10" y="20" width="140" height="168" rx="12" fill="rgba(120,60,240,.10)" stroke="rgba(120,60,240,.35)" stroke-width="1.5"/>
              <rect x="10" y="20" width="140" height="38" rx="12" fill="rgba(120,60,240,.20)"/>
              <rect x="10" y="43" width="140" height="15" fill="rgba(120,60,240,.20)"/>
              <rect x="24" y="29" width="32" height="16" rx="8" fill="rgba(0,160,240,.40)"/>
              <rect x="28" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="42" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="24" y="76" width="112" height="5" rx="2.5" fill="rgba(167,139,250,.45)"/>
              <rect x="24" y="88" width="84" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="100" width="102" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="112" width="70" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="124" width="110" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="136" width="90" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="148" width="98" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="24" y="160" width="76" height="5" rx="2.5" fill="rgba(167,139,250,.27)"/>
              <rect x="330" y="20" width="140" height="168" rx="12" fill="rgba(0,160,240,.10)" stroke="rgba(0,160,240,.35)" stroke-width="1.5"/>
              <rect x="330" y="20" width="140" height="38" rx="12" fill="rgba(0,160,240,.20)"/>
              <rect x="330" y="43" width="140" height="15" fill="rgba(0,160,240,.20)"/>
              <rect x="344" y="29" width="32" height="16" rx="8" fill="rgba(120,60,240,.40)"/>
              <rect x="348" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="362" y="34" width="10" height="5" rx="2" fill="rgba(255,255,255,.6)"/>
              <rect x="344" y="76" width="112" height="5" rx="2.5" fill="rgba(0,160,240,.45)"/>
              <rect x="344" y="88" width="96" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="100" width="86" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="112" width="112" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="124" width="74" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="136" width="106" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="148" width="88" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <rect x="344" y="160" width="100" height="5" rx="2.5" fill="rgba(0,160,240,.27)"/>
              <circle cx="240" cy="104" r="46" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
              <circle cx="240" cy="104" r="30" fill="rgba(120,60,240,.18)"/>
              <path d="M152 104h56M288 104h40" stroke="rgba(120,60,240,.40)" stroke-width="1.5" stroke-dasharray="4 3"/>
              <path d="M224 104h32M246 96l10 8-10 8" stroke="rgba(167,139,250,.95)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="intro-features">
            <div class="intro-feat">
              <div class="intro-feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg></div>
              <div><strong>Авторский голос</strong><span>Сохраняем стиль, ритм и тональность оригинала, а не только смысл</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></div>
              <div><strong>Культурная адаптация</strong><span>Аллюзии, реалии и игра слов переосмысляются, а не переводятся дословно</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg></div>
              <div><strong>Редакторская вычитка</strong><span>Литературный редактор работает с готовым текстом после переводчика</span></div>
            </div>
            <div class="intro-feat">
              <div class="intro-feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg></div>
              <div><strong>Опыт публикаций</strong><span>Переводчики работали с издательствами и знают редакционные стандарты</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

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

  <!-- ════════ ЖАНРЫ И ФОРМАТЫ ════════ -->
  <section class="sec sec--alt sec-docs">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Какие произведения мы переводим</h2>
        <p class="sec-sub">Раскройте нужный жанр — узнайте, что именно входит в работу</p>
      </div>
      <div class="docs-accordion">

        <details class="doc-item" open>
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            <span>Проза — романы, повести, рассказы, эссе, мемуары, автобиографии</span>
          </summary>
          <div class="doc-body">
            <p>Романы, повести, рассказы, эссе, мемуары, автобиографии. Работаем с классической и современной прозой, жанровой литературой, документальными текстами с художественной формой. Сохраняем темп, тональность и авторский синтаксис.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            <span>Поэзия — стихотворения, поэмы, лирика, верлибр, перевод с сохранением формы или смысла</span>
          </summary>
          <div class="doc-body">
            <p>Стихотворения, поэмы, лирика, верлибр. Предлагаем два подхода: метрический перевод (с сохранением рифмы и размера) и смысловой (с передачей образности и духа). Выбор зависит от задачи и согласовывается заранее.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            <span>Сценарии и пьесы — кино, сериалы, театральные пьесы, мюзиклы, радиопостановки</span>
          </summary>
          <div class="doc-body">
            <p>Кино, сериалы, театральные пьесы, мюзиклы, радиопостановки. Учитываем разговорную живость реплик, длину реплик для дублирования и сценическую актуальность диалога. Форматный перевод с соблюдением отраслевых стандартов.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
            <span>Детская литература — книги для читателей разных возрастов с учётом возрастной аудитории</span>
          </summary>
          <div class="doc-body">
            <p>Книги для читателей от 0 до 16 лет с учётом возрастной аудитории. Адаптируем словарный запас, темп и образы под конкретную возрастную группу. Особое внимание к звучанию текста вслух: ритму, звукоподражаниям, игровым элементам.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
            <span>Графические романы и комиксы — локализация текста в баблах, передача игры слов и темпа</span>
          </summary>
          <div class="doc-body">
            <p>Локализация текста в баблах, звукоподражаний, имён и игры слов. Работаем с ограничениями пространства в баблах, передаём темп и эмоциональную динамику визуальной истории. Переводчик координируется с верстальщиком при необходимости.</p>
          </div>
        </details>

        <details class="doc-item">
          <summary class="doc-summary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
            <span>Нон-фикшн и биографии — документальная проза, травелоги, путеводители, научпоп</span>
          </summary>
          <div class="doc-body">
            <p>Документальная проза, травелоги, путеводители, научно-популярные книги. Сохраняем авторский голос и личную интонацию при точной передаче фактического содержания. Особенно внимательно работаем с именами, топонимами и культурными отсылками.</p>
          </div>
        </details>

      </div>
    </div>
  </section>

  <section class="sec sec-split">
    <div class="container">
      <div class="split-row split-row--rev">
        <div class="split-text">
          <p class="sec-label">Литературное мастерство</p>
          <h2>Как сохранить авторский голос при переводе</h2>
          <p>Художественный перевод — это создание нового произведения на основе оригинала, а не механическая замена слов. Переводчик должен сам писать хорошо: чувствовать ритм, понимать, как строится образ, и уметь воссоздать игру слов на языке читателя.</p>
          <p>Наши литературные переводчики — практикующие авторы и редакторы. Многие публиковались в издательствах, переводили тексты, которые вышли в печать. Это не абстрактный «опыт», а конкретные публикации.</p>
          <ul class="split-checklist">
            <li>Переводчик-автор с литературным или переводческим образованием</li>
            <li>Глубокое знание культурного контекста исходного языка</li>
            <li>Финальная вычитка литературным редактором</li>
            <li>Обсуждение стилистических решений с заказчиком</li>
          </ul>
        </div>
        <div class="split-visual" aria-hidden="true">
          <svg viewBox="0 0 380 320" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <linearGradient id="sq-g1-k" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#783CF0"/><stop offset="100%" stop-color="#00A0F0"/></linearGradient>
              <linearGradient id="sq-g2-k" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#783CF0" stop-opacity=".5"/><stop offset="100%" stop-color="#00A0F0" stop-opacity=".5"/></linearGradient>
            </defs>
            <circle cx="190" cy="160" r="130" fill="rgba(120,60,240,.07)"/>
            <rect x="60" y="30" width="260" height="64" rx="16" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="62" r="20" fill="url(#sq-g1-k)" opacity=".85"/>
            <path d="M88 62l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="50" width="170" height="9" rx="4" fill="rgba(167,139,250,.55)"/>
            <rect x="126" y="66" width="120" height="7" rx="3" fill="rgba(167,139,250,.30)"/>
            <line x1="190" y1="94" x2="190" y2="118" stroke="url(#sq-g2-k)" stroke-width="2" stroke-dasharray="4 3"/>
            <rect x="60" y="118" width="260" height="64" rx="16" fill="rgba(0,160,240,.12)" stroke="rgba(0,160,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="150" r="20" fill="url(#sq-g1-k)" opacity=".85"/>
            <path d="M88 150l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="138" width="155" height="9" rx="4" fill="rgba(0,160,240,.55)"/>
            <rect x="126" y="154" width="110" height="7" rx="3" fill="rgba(0,160,240,.30)"/>
            <line x1="190" y1="182" x2="190" y2="206" stroke="url(#sq-g2-k)" stroke-width="2" stroke-dasharray="4 3"/>
            <rect x="60" y="206" width="260" height="64" rx="16" fill="rgba(120,60,240,.12)" stroke="rgba(120,60,240,.30)" stroke-width="1.5"/>
            <circle cx="96" cy="238" r="20" fill="url(#sq-g1-k)" opacity=".85"/>
            <path d="M88 238l6 6 10-12" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="126" y="226" width="175" height="9" rx="4" fill="rgba(167,139,250,.55)"/>
            <rect x="126" y="242" width="130" height="7" rx="3" fill="rgba(167,139,250,.30)"/>
            <circle cx="310" cy="290" r="26" fill="rgba(0,200,100,.12)" stroke="rgba(0,200,100,.35)" stroke-width="1.5"/>
            <path d="M300 290l8 8 14-16" stroke="rgba(0,200,100,.9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ НАШИ ПЕРЕВОДЧИКИ ════════ -->
  <section class="sec sec-translators">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Литературные переводчики в Москве</h2>
        <p class="sec-sub">Литературные переводчики московского бюро «Ремарка» — авторы и редакторы, для которых перевод это творчество, а не ремесло</p>
      </div>
      <div class="translators-grid">
        <div class="translator-card">
          <div class="translator-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
          </div>
          <h3>Литературный опыт</h3>
          <p>Переводчик сам является автором — понимает художественный замысел изнутри.</p>
        </div>
        <div class="translator-card">
          <div class="translator-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
          </div>
          <h3>Знание культурного контекста</h3>
          <p>Глубокое погружение в литературу страны исходного языка.</p>
        </div>
        <div class="translator-card">
          <div class="translator-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
          </div>
          <h3>Редакторская школа</h3>
          <p>Финальная вычитка литературным редактором по стандартам издательств.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ СРОКИ И СТОИМОСТЬ ════════ -->
  <section class="sec sec--alt sec-vol-pricing">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Сроки и стоимость</h2>
        <p class="sec-sub">Цена зависит от объёма и жанра. Точный расчёт — после ознакомления с текстом</p>
      </div>
      <div class="vol-table-wrap">
        <table class="vol-table">
          <thead>
            <tr>
              <th>Объём</th>
              <th>Срок</th>
              <th>Стоимость</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>До 5 страниц</strong><span>~2 500 слов</span></td>
              <td>2 рабочих дня</td>
              <td class="vol-price">от 3 000 ₽</td>
              <td><a href="/stoimost-perevoda/" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>5–30 страниц</strong><span></span></td>
              <td>3–7 рабочих дней</td>
              <td class="vol-price">от 600 ₽/стр.</td>
              <td><a href="/stoimost-perevoda/" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>30–100 страниц</strong><span></span></td>
              <td>1–3 недели</td>
              <td class="vol-price">от 550 ₽/стр.</td>
              <td><a href="/stoimost-perevoda/" class="vol-btn">Заказать</a></td>
            </tr>
            <tr>
              <td><strong>Полная книга (100+ стр.)</strong><span></span></td>
              <td>по договорённости</td>
              <td class="vol-price">от 500 ₽/стр.</td>
              <td><a href="/stoimost-perevoda/" class="vol-btn">Заказать</a></td>
            </tr>
          </tbody>
        </table>
      </div>
      <p class="vol-note">Поэзия рассчитывается индивидуально. Точный расчёт — после ознакомления с текстом.</p>
    </div>
  </section>

  <!-- ════════ СПРАВОЧНИК ПО ЖАНРАМ ════════ -->
  <section class="sec sec-doc-reference">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Справочник по жанрам</h2>
        <p class="sec-sub">Выберите жанр произведения — узнайте особенности художественного перевода</p>
      </div>
      <div class="doc-ref-grid">
        <div class="doc-ref-card">
          <span class="doc-ref-title">Перевод романов</span>
          <span class="doc-ref-desc">крупная проза: от классических романов до современной литературы</span>
        </div>
        <div class="doc-ref-card">
          <span class="doc-ref-title">Перевод повестей и рассказов</span>
          <span class="doc-ref-desc">малая и средняя проза, сборники и антологии</span>
        </div>
        <div class="doc-ref-card">
          <span class="doc-ref-title">Перевод поэзии</span>
          <span class="doc-ref-desc">лирика, эпос, верлибр, сонеты с сохранением формы или передачей духа</span>
        </div>
        <div class="doc-ref-card">
          <span class="doc-ref-title">Перевод сценариев</span>
          <span class="doc-ref-desc">кино и телесериалы, форматный перевод с тайм-кодами</span>
        </div>
        <div class="doc-ref-card">
          <span class="doc-ref-title">Перевод пьес</span>
          <span class="doc-ref-desc">театральные произведения с учётом сценической актуальности реплик</span>
        </div>
        <div class="doc-ref-card">
          <span class="doc-ref-title">Перевод детских книг</span>
          <span class="doc-ref-desc">адаптация для разных возрастных групп от 0 до 16 лет</span>
        </div>
        <div class="doc-ref-card">
          <span class="doc-ref-title">Перевод комиксов</span>
          <span class="doc-ref-desc">локализация баблов, звукоподражаний, имён и игры слов</span>
        </div>
        <div class="doc-ref-card">
          <span class="doc-ref-title">Перевод мемуаров и биографий</span>
          <span class="doc-ref-desc">документальная проза с сохранением личного голоса</span>
        </div>
        <div class="doc-ref-card">
          <span class="doc-ref-title">Перевод эссе и публицистики</span>
          <span class="doc-ref-desc">авторская и аналитическая проза, колонки и рецензии</span>
        </div>
        <div class="doc-ref-card">
          <span class="doc-ref-title">Перевод фэнтези и фантастики</span>
          <span class="doc-ref-desc">авторские миры, неологизмы, магические системы</span>
        </div>
        <div class="doc-ref-card">
          <span class="doc-ref-title">Перевод детективов и триллеров</span>
          <span class="doc-ref-desc">темп, интрига и разговорные регистры</span>
        </div>
        <div class="doc-ref-card">
          <span class="doc-ref-title">Перевод научпоп литературы</span>
          <span class="doc-ref-desc">научный материал в доступном художественном стиле</span>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ПЕРЕВОДЫ ПО СФЕРАМ ════════ -->
  <section class="sec sec--alt sec-industries">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Переводы по сферам</h2>
        <p class="sec-sub">Художественный перевод для издательств, кино, театра и цифровых платформ</p>
      </div>
      <div class="industries-grid">
        <div class="industry-card">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
          <div>
            <strong>Издательства</strong>
            <span>Романы, повести, детская литература, нон-фикшн для публикации</span>
          </div>
        </div>
        <div class="industry-card">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
          <div>
            <strong>Кино и телевидение</strong>
            <span>Сценарии, субтитры, закадровый текст, синхронный перевод</span>
          </div>
        </div>
        <div class="industry-card">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
          <div>
            <strong>Театр</strong>
            <span>Пьесы, мюзиклы, либретто, адаптированные для российской сцены</span>
          </div>
        </div>
        <div class="industry-card">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/><path d="M7 8l3 3-3 3M13 14h4"/></svg>
          <div>
            <strong>Игровая индустрия</strong>
            <span>Нарративы, диалоги, квесты, ролевые игры</span>
          </div>
        </div>
        <div class="industry-card">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>
          <div>
            <strong>Стриминговые платформы</strong>
            <span>Субтитры и дублирование для онлайн-кинотеатров</span>
          </div>
        </div>
        <div class="industry-card">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18v-6a9 9 0 0 1 18 0v6"/><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/></svg>
          <div>
            <strong>Аудиокниги и подкасты</strong>
            <span>Адаптация текста для аудиоформата</span>
          </div>
        </div>
        <div class="industry-card">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
          <div>
            <strong>Образование и академическая среда</strong>
            <span>Хрестоматии, антологии, учебные материалы</span>
          </div>
        </div>
        <div class="industry-card">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
          <div>
            <strong>Литературные агентства</strong>
            <span>Синопсисы, первые главы, переговорные материалы</span>
          </div>
        </div>
        <div class="industry-card">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
          <div>
            <strong>Самиздат и независимые авторы</strong>
            <span>Перевод авторских произведений для зарубежных платформ</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ ОТЗЫВЫ ════════ -->
  <section class="sec sec-reviews">
    <div class="container">
      <div class="reviews-head">
        <div>
          <h2 class="sec-title">Что говорят клиенты</h2>
          <p class="sec-sub" style="margin-top:6px">Реальные отзывы о художественных переводах</p>
        </div>
        <div class="review-rating-block">
          <div class="review-rating-score">4.98</div>
          <div class="review-stars-row">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          </div>
          <div class="review-rating-label">средний рейтинг</div>
        </div>
        <div class="review-platform-links">
          <a href="https://yandex.ru/maps/org/remarka/51867347382/reviews/" class="review-platform-link" target="_blank" rel="noopener">Яндекс</a>
          <a href="https://maps.app.goo.gl/d8BKJYw81PqBHXvz7" class="review-platform-link" target="_blank" rel="noopener">Google</a>
          <a href="https://2gis.ru/krasnodar/search/%D0%B1%D1%8E%D1%80%D0%BE%20%D0%BF%D0%B5%D1%80%D0%B5%D0%B2%D0%BE%D0%B4%D0%BE%D0%B2%20%D1%80%D0%B5%D0%BC%D0%B0%D1%80%D0%BA%D0%B0/firm/70000001041287881" class="review-platform-link" target="_blank" rel="noopener">2ГИС</a>
        </div>
      </div>
      <div class="reviews-grid" style="grid-template-columns:repeat(3,1fr)">
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">Е</div>
            <div class="review-meta"><div class="review-name">Елена Журавлёва</div><div class="review-src">Яндекс · сентябрь 2024</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Заказывали перевод скандинавского детектива. Переводчик отлично передал атмосферу и темп оригинала. Рукопись прошла редакцию с минимальными правками.</p>
          <p class="review-role">Редактор, издательство «Новый Свет»</p>
        </div>
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">Д</div>
            <div class="review-meta"><div class="review-name">Дмитрий Орлов</div><div class="review-src">Google · июль 2024</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Переводил американский пилот для российской адаптации. Разговорные регистры переданы точно, шутки переосмыслены — не переведены дословно. Именно то, что нужно.</p>
          <p class="review-role">Сценарист</p>
        </div>
        <div class="review-card">
          <div class="review-card-top">
            <div class="review-avatar">Н</div>
            <div class="review-meta"><div class="review-name">Нина Соколова</div><div class="review-src">2ГИС · март 2024</div></div>
          </div>
          <div class="review-stars"><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="15" height="15" viewBox="0 0 24 24" fill="#f5a623" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <p class="review-text">Переводила свой роман на английский для Amazon. Переводчик очень бережно отнёсся к авторскому стилю, предложил несколько вариантов для сложных мест.</p>
          <p class="review-role">Независимый автор</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ════════ FAQ ════════ -->
  <section class="sec sec--alt sec-faq">
    <div class="container">
      <div class="sec-head sec-head--center">
        <h2 class="sec-title">Часто задаваемые вопросы</h2>
        <p class="sec-sub">Не нашли ответа — напишите Ольге в чат, ответим за минуту</p>
      </div>
      <div class="faq-list">

        <details class="faq-item">
          <summary class="faq-summary">Можно ли перевести поэзию, сохранив рифму и ритм?</summary>
          <div class="faq-body">
            <p>Зависит от задачи. Метрический перевод (с сохранением рифмы и ритма) — это отдельная творческая работа, стоит дороже и требует больше времени. Для большинства целей точнее передать дух и образность, чем жертвовать смыслом ради формы.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Как вы сохраняете авторский стиль при переводе?</summary>
          <div class="faq-body">
            <p>Переводчик изучает другие произведения автора, биографические материалы, критику. При необходимости согласовываем с автором ключевые переводческие решения. Результат проверяет литературный редактор.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Переводите ли вы книги для издательств?</summary>
          <div class="faq-body">
            <p>Да, работаем напрямую с российскими издательствами и авторами. Предоставляем полный пакет: перевод, редактура, финальная корректура.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Сколько стоит перевод романа целиком?</summary>
          <div class="faq-body">
            <p>Стандартный роман (300–400 страниц) — от 150 000 ₽ при сроке 2–3 месяца. Точная стоимость зависит от языковой пары, жанра и требований к срокам.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Работаете ли вы с самиздатом и независимыми авторами?</summary>
          <div class="faq-body">
            <p>Да, с удовольствием. Переводим первые главы для питчинга зарубежным агентам, синопсисы, полные рукописи.</p>
          </div>
        </details>

        <details class="faq-item">
          <summary class="faq-summary">Где заказать художественный перевод в Москве?</summary>
          <div class="faq-body">
            <p>Бюро переводов «Ремарка» работает с авторами и издательствами онлайн. Пришлите фрагмент произведения в чат — бесплатно оценим сложность и подберём переводчика.</p>
          </div>
        </details>

      </div>
    </div>
  </section>

  <!-- ════════ КАЛЬКУЛЯТОР СТОИМОСТИ ════════ -->',
    ];
}
