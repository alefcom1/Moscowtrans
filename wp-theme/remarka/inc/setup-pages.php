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
        $icon = $r['action'] === 'created' ? '✅' : ($r['action'] === 'skipped' ? '⏭' : '❌');
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

    foreach ($service_pages as $p) {
        $r = remarka_insert_page([
            'slug'     => $p['slug'],
            'title'    => $p['title'],
            'template' => 'page-templates/template-service.php',
        ]);
        if ($r['action'] === 'created') {
            update_post_meta($r['id'], '_hero_greeting_1', $p['g1']);
            update_post_meta($r['id'], '_hero_greeting_2', $p['g2']);
            update_post_meta($r['id'], '_hero_greeting_3', $p['g3']);
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

    /* Check if page already exists */
    $existing = get_page_by_path($slug, OBJECT, 'page');
    if ($existing) {
        return ['action' => 'skipped', 'slug' => $slug, 'title' => $title, 'id' => $existing->ID];
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
