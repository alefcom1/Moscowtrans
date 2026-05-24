<?php
defined('ABSPATH') || exit;

class RTAP_SEO {

    private static array $topics = [
        'tekhnicheskiy' => [
            'id'    => 'technical',
            'title' => 'Тест для технических переводчиков',
            'desc'  => 'Бесплатный профессиональный тест для переводчиков технической документации. 10 вопросов, 3 уровня сложности, сертификат при результате ≥70%.',
            'h1'    => 'Тест для технических переводчиков',
            'intro' => 'Проверьте профессиональный уровень технического перевода с английского на русский. Тест охватывает машиностроение, химию, строительство, электротехнику и промышленное оборудование.',
            'faq'   => [
                ['Что такое технический перевод?', 'Технический перевод — специализированный вид перевода документации, руководств, чертежей и стандартов. Требует знания отраслевой терминологии.'],
                ['Сколько вопросов в тесте?', 'В каждом тесте 10 вопросов, случайно выбранных из банка 50+ вопросов по каждому уровню.'],
                ['Можно ли получить сертификат?', 'Да! При результате ≥70% вы получаете именной сертификат с уникальным номером верификации.'],
            ],
        ],
        'yuridicheskiy' => [
            'id'    => 'legal',
            'title' => 'Тест для юридических переводчиков',
            'desc'  => 'Профессиональный тест по юридическому переводу с английского. Договоры, арбитраж, корпоративное право. Сертификат при 70%+.',
            'h1'    => 'Тест для юридических переводчиков',
            'intro' => 'Оцените уровень владения юридической терминологией при переводе с английского на русский. Тест включает вопросы по контрактному праву, арбитражу, корпоративному праву и международным договорам.',
            'faq'   => [
                ['Что такое юридический перевод?', 'Юридический перевод требует точного воспроизведения правовых понятий и конструкций. Ошибки в терминологии могут изменить смысл документа.'],
                ['Как разблокировать Advanced уровень?', 'Пройдите Intermediate с результатом ≥70%. Прогресс сохраняется в браузере.'],
                ['Используется ли тест при найме?', 'Да, бюро переводов «Ремарка» использует результаты для отбора квалифицированных переводчиков-партнёров.'],
            ],
        ],
        'meditsinskiy' => [
            'id'    => 'medical',
            'title' => 'Тест для медицинских переводчиков',
            'desc'  => 'Тест по медицинскому переводу (EN→RU): кардиология, фармакология, клинические исследования. Получите сертификат бюро «Ремарка».',
            'h1'    => 'Тест для медицинских переводчиков',
            'intro' => 'Медицинский перевод требует высокой точности — ошибки в терминологии недопустимы. Пройдите тест по переводу клинической документации, фармакологических справочников и историй болезни.',
            'faq'   => [
                ['Чем отличается медицинский перевод?', 'Медицинский перевод требует знания латинской терминологии, МКБ-классификации и специфики клинической документации.'],
                ['Сколько времени на каждый вопрос?', '30 секунд с обратным отсчётом. При истечении времени ответ засчитывается автоматически.'],
                ['Как использовать сертификат?', 'Укажите номер сертификата в резюме. Работодатель может верифицировать его на moscowtrans.ru/verify/'],
            ],
        ],
        'it' => [
            'id'    => 'it',
            'title' => 'Тест для IT-переводчиков',
            'desc'  => 'Тест по IT-переводу и локализации программного обеспечения (EN→RU). Интерфейсы, API-документация, кибербезопасность. Сертификат бесплатно.',
            'h1'    => 'Тест для IT-переводчиков и локализаторов',
            'intro' => 'IT-перевод и локализация ПО — высококонкурентная специализация. Тест проверяет знание стандартной терминологии Microsoft, правила перевода интерфейсов и технической документации.',
            'faq'   => [
                ['Что такое локализация ПО?', 'Локализация — адаптация программного продукта для конкретного рынка, включая перевод интерфейса, документации и маркетинговых материалов.'],
                ['Какие CAT-инструменты актуальны?', 'SDL Trados Studio, memoQ, Phrase (Memsource), OmegaT. Знание хотя бы одного инструмента — преимущество при найме.'],
                ['Как стать IT-переводчиком в Ремарке?', 'Пройдите тест, получите сертификат, заполните анкету — и мы свяжемся с вами с предложением сотрудничества.'],
            ],
        ],
    ];

    public static function init(): void {
        add_action('wp_head',    [self::class, 'output_meta'],   5);
        add_action('wp_head',    [self::class, 'output_schema'], 6);
        add_filter('document_title_parts', [self::class, 'filter_title']);
    }

    public static function get_topic_data(string $slug): ?array {
        return self::$topics[$slug] ?? null;
    }

    public static function output_meta(): void {
        $data = self::current_topic();
        if (!$data) return;

        $url = get_permalink();
        printf('<meta name="description" content="%s" />', esc_attr($data['desc']));
        printf('<meta property="og:title" content="%s" />', esc_attr($data['title']));
        printf('<meta property="og:description" content="%s" />', esc_attr($data['desc']));
        printf('<meta property="og:type" content="website" />');
        printf('<meta property="og:url" content="%s" />', esc_url($url));
        printf('<link rel="canonical" href="%s" />', esc_url($url));
    }

    public static function output_schema(): void {
        $data = self::current_topic();
        if (!$data) return;

        $faq_items = array_map(fn($faq) => [
            '@type'          => 'Question',
            'name'           => $faq[0],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq[1]],
        ], $data['faq']);

        $schema = [
            '@context' => 'https://schema.org',
            '@graph'   => [
                [
                    '@type'            => 'FAQPage',
                    'mainEntity'       => $faq_items,
                ],
                [
                    '@type'        => 'BreadcrumbList',
                    'itemListElement' => [
                        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Главная',       'item' => get_site_url()],
                        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Тесты',         'item' => get_site_url() . '/test-perevodchika/'],
                        ['@type' => 'ListItem', 'position' => 3, 'name' => $data['title'],  'item' => get_permalink()],
                    ],
                ],
            ],
        ];

        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }

    public static function filter_title(array $parts): array {
        $data = self::current_topic();
        if ($data) { $parts['title'] = $data['title']; }
        return $parts;
    }

    private static function current_topic(): ?array {
        global $post;
        if (!$post) return null;
        foreach (self::$topics as $slug => $data) {
            $cpt = "rtap_{$slug}";
            if (get_post_type($post) === $cpt || is_page($post->ID)) {
                return $data;
            }
        }
        return null;
    }

    public static function hub_schema(): string {
        $schema = [
            '@context' => 'https://schema.org',
            '@type'    => 'HowTo',
            'name'     => 'Как пройти тест для переводчиков',
            'step'     => [
                ['@type' => 'HowToStep', 'name' => 'Выберите язык', 'text' => 'Выберите языковую пару (сейчас доступен английский → русский).'],
                ['@type' => 'HowToStep', 'name' => 'Выберите тематику', 'text' => 'Технический, юридический, медицинский или IT-перевод.'],
                ['@type' => 'HowToStep', 'name' => 'Выберите уровень', 'text' => 'Beginner (открыт всегда), Intermediate (≥60% на Beginner), Advanced (≥70% на Intermediate).'],
                ['@type' => 'HowToStep', 'name' => 'Пройдите тест', 'text' => '10 вопросов, 30 секунд на каждый. Типы: MC, Best Translation, Find Error, Term Matching и другие.'],
                ['@type' => 'HowToStep', 'name' => 'Получите сертификат', 'text' => 'При результате ≥70% скачайте именной сертификат в PNG или PDF.'],
            ],
        ];
        return '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE) . '</script>';
    }
}

add_action('init', ['RTAP_SEO', 'init']);
