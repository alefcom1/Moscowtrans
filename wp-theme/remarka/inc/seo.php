<?php
/**
 * SEO & Schema.org markup — AI search engine optimisation.
 *
 * Fires on every page (Organisation, LocalBusiness, WebSite) and
 * conditionally on specific templates (FAQPage, AggregateRating, etc.).
 */

/* ── 1. GLOBAL ORGANISATION + LOCAL BUSINESS SCHEMA ───────────────────────── */
function remarka_seo_global_schema(): void {
    $site_url = home_url('/');

    $org = [
        '@context'  => 'https://schema.org',
        '@type'     => ['LocalBusiness', 'ProfessionalService'],
        '@id'       => $site_url . '#organization',
        'name'      => 'Бюро переводов «Ремарка»',
        'legalName' => 'ИП Климанова Ю.А.',
        'taxID'     => '233406925261',
        'foundingDate' => '2001',
        'url'       => $site_url,
        'logo'      => $site_url . 'wp-content/themes/remarka/assets/images/logo.svg',
        'image'     => $site_url . 'wp-content/themes/remarka/assets/images/og-image.jpg',
        'telephone' => '+74959704413',
        'email'     => 'info@moscowtrans.ru',
        'address'   => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => 'Глинищевский пер., д. 6, оф. 2',
            'addressLocality' => 'Москва',
            'postalCode'      => '125009',
            'addressCountry'  => 'RU',
        ],
        'geo' => [
            '@type'     => 'GeoCoordinates',
            'latitude'  => 55.7585,
            'longitude' => 37.6146,
        ],
        'openingHoursSpecification' => [
            [
                '@type'     => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'opens'     => '09:00',
                'closes'    => '18:00',
            ],
        ],
        'contactPoint' => [
            [
                '@type'             => 'ContactPoint',
                'telephone'         => '+74959704413',
                'contactType'       => 'customer service',
                'contactOption'     => 'TollFree',
                'areaServed'        => 'RU',
                'availableLanguage' => ['Russian', 'English'],
            ],
            [
                '@type'        => 'ContactPoint',
                'telephone'    => '+79859704413',
                'contactType'  => 'customer service',
                'contactOption' => 'HearingImpairedSupported',
                'areaServed'   => 'RU',
            ],
        ],
        'priceRange'      => '₽₽',
        'currenciesAccepted' => 'RUB',
        'paymentAccepted' => 'Cash, Credit Card, Bank Transfer',
        'aggregateRating' => [
            '@type'       => 'AggregateRating',
            'ratingValue' => '4.98',
            'reviewCount' => '500',
            'bestRating'  => '5',
            'worstRating' => '1',
        ],
        'sameAs' => [
            'https://yandex.ru/maps/org/remarka/51867347382/',
            'https://maps.app.goo.gl/d8BKJYw81PqBHXvz7',
        ],
        'numberOfEmployees' => [ '@type' => 'QuantitativeValue', 'minValue' => 10, 'maxValue' => 50 ],
        'knowsAbout'        => [
            'Технический перевод', 'Юридический перевод', 'Медицинский перевод',
            'Финансовый перевод', 'Перевод документов', 'Письменный перевод',
        ],
        'knowsLanguage' => [
            'Russian', 'English', 'German', 'French', 'Italian', 'Spanish',
            'Portuguese', 'Chinese', 'Japanese', 'Arabic',
        ],
    ];

    $website = [
        '@context'        => 'https://schema.org',
        '@type'           => 'WebSite',
        '@id'             => $site_url . '#website',
        'url'             => $site_url,
        'name'            => 'Бюро переводов «Ремарка» — moscowtrans.ru',
        'inLanguage'      => 'ru-RU',
        'publisher'       => [ '@id' => $site_url . '#organization' ],
        'potentialAction' => [
            '@type'       => 'SearchAction',
            'target'      => [
                '@type'       => 'EntryPoint',
                'urlTemplate' => $site_url . '?s={search_term_string}',
            ],
            'query-input' => 'required name=search_term_string',
        ],
    ];

    echo '<script type="application/ld+json">' . wp_json_encode( $org, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
    echo '<script type="application/ld+json">' . wp_json_encode( $website, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'remarka_seo_global_schema', 6 );

/* ── 2. META DESCRIPTION (all pages) ──────────────────────────────────────── */
function remarka_seo_meta_description(): void {
    global $post;
    $desc = '';

    if ( $post ) {
        $desc = get_post_meta( $post->ID, '_meta_description', true );
    }

    if ( ! $desc ) {
        $tpl = $post ? get_page_template_slug( $post->ID ) : '';
        $defaults = [
            ''                                            => 'Бюро переводов «Ремарка» — профессиональный перевод на 60+ языков в Москве. Юридические, технические, медицинские документы. Дипломированные переводчики. От 400 ₽/стр.',
            'page-templates/template-pricing.php'        => 'Стоимость перевода в Москве — от 400 ₽/страницу. Калькулятор цен онлайн. Три уровня качества: ИИ-постредактирование, профессиональный, премиальный. Расчёт за 30 минут.',
            'page-templates/template-languages.php'      => 'Перевод на 60+ языков в бюро «Ремарка», Москва. Английский, немецкий, французский, китайский, японский и другие. Профильные переводчики. Цены от 400 ₽/стр.',
            'page-templates/template-contact.php'        => 'Контакты бюро переводов «Ремарка», Москва — Глинищевский пер., 6, оф. 2. Тел. +7 (495) 970-44-13, WhatsApp +7 (985) 970-44-13. Режим работы: Пн–Пт 9:00–18:00.',
            'page-templates/template-cases.php'          => 'Примеры переводов бюро «Ремарка» — реализованные проекты в юридической, технической, медицинской и финансовой сферах. Портфолио и кейсы.',
            'page-templates/template-service.php'        => '',
        ];

        $desc = isset( $defaults[ $tpl ] ) ? $defaults[ $tpl ] : $defaults[''];

        if ( is_front_page() ) {
            $desc = $defaults[''];
        }
    }

    if ( $desc ) {
        echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
    }
}
add_action( 'wp_head', 'remarka_seo_meta_description', 7 );

/* ── 3. OPEN GRAPH + TWITTER CARDS ──────────────────────────────────────────── */
function remarka_seo_og_tags(): void {
    global $post;

    $title    = wp_get_document_title();
    $desc     = '';
    $image    = home_url( '/wp-content/themes/remarka/assets/images/og-image.jpg' );
    $url      = home_url( '/' );
    $type     = 'website';

    if ( $post ) {
        $desc = get_post_meta( $post->ID, '_meta_description', true );
        $url  = get_permalink( $post->ID );
        if ( has_post_thumbnail( $post->ID ) ) {
            $img  = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large' );
            if ( $img ) $image = $img[0];
        }
        if ( $post->post_type === 'post' ) {
            $type = 'article';
        }
    }

    echo '<meta property="og:type" content="' . esc_attr( $type ) . '">' . "\n";
    echo '<meta property="og:site_name" content="Бюро переводов «Ремарка»">' . "\n";
    echo '<meta property="og:locale" content="ru_RU">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
    echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
    if ( $desc ) {
        echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
    }
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
    echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
}
add_action( 'wp_head', 'remarka_seo_og_tags', 8 );

/* ── 4. HOMEPAGE: AggregateRating + FAQPage ─────────────────────────────────── */
function remarka_seo_homepage_schema(): void {
    if ( ! is_front_page() ) return;

    $site_url = home_url('/');

    $faq_items = [
        [
            'q' => 'Сколько стоит перевод документа?',
            'a' => 'Стоимость перевода зависит от языковой пары, тематики и срочности. Базовая цена — от 400 ₽ за стандартную страницу (1 800 знаков с пробелами) для европейских языков. Китайский, японский, арабский — от 800–1 200 ₽/стр. Расчёт стоимости бесплатно в течение 30 минут после получения документа.',
        ],
        [
            'q' => 'Как долго занимает перевод?',
            'a' => 'Небольшие тексты (1–5 страниц) — 1 рабочий день. Стандартная скорость: 3–5 страниц в день на одного переводчика. Срочный перевод — в тот же день или за несколько часов при необходимости.',
        ],
        [
            'q' => 'Как заказать перевод?',
            'a' => 'Загрузите файл через форму на сайте, отправьте по email info@moscowtrans.ru или через WhatsApp +7 (985) 970-44-13. Менеджер свяжется в течение 30 минут и назовёт точную стоимость и срок.',
        ],
        [
            'q' => 'На каких языках вы переводите?',
            'a' => 'Бюро «Ремарка» переводит на 60+ языков: европейские (английский, немецкий, французский, итальянский, испанский, польский и др.), азиатские (китайский, японский, корейский), языки ближнего зарубежья и другие. Полный перечень на странице «Языки перевода».',
        ],
        [
            'q' => 'Есть ли гарантии качества перевода?',
            'a' => 'Да. Каждый перевод проходит двухэтапную проверку: перевод профильным специалистом + независимая редактура. Мы предоставляем бесплатные правки в течение 30 дней после сдачи. Рейтинг бюро — 4.98/5 по 500+ отзывам.',
        ],
        [
            'q' => 'Соблюдается ли конфиденциальность?',
            'a' => 'Да. До передачи документов подписывается NDA (соглашение о конфиденциальности). Файлы хранятся в защищённом хранилище и не передаются третьим лицам.',
        ],
    ];

    $faq_entities = array_map( function( $item ) {
        return [
            '@type'          => 'Question',
            'name'           => $item['q'],
            'acceptedAnswer' => [ '@type' => 'Answer', 'text' => $item['a'] ],
        ];
    }, $faq_items );

    $faq_schema = [
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => $faq_entities,
    ];

    echo '<script type="application/ld+json">' . wp_json_encode( $faq_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'remarka_seo_homepage_schema', 9 );

/* ── 5. CONTACT PAGE: extended LocalBusiness with hours ─────────────────────── */
function remarka_seo_contact_schema(): void {
    if ( ! is_page_template( 'page-templates/template-contact.php' ) ) return;

    $site_url = home_url('/');

    $schema = [
        '@context'  => 'https://schema.org',
        '@type'     => ['LocalBusiness', 'ProfessionalService'],
        '@id'       => $site_url . '#organization',
        'name'      => 'Бюро переводов «Ремарка»',
        'legalName' => 'ИП Климанова Ю.А.',
        'taxID'     => '233406925261',
        'foundingDate' => '2001',
        'url'       => $site_url,
        'telephone' => '+74959704413',
        'email'     => 'info@moscowtrans.ru',
        'address'   => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => 'Глинищевский пер., д. 6, оф. 2',
            'addressLocality' => 'Москва',
            'postalCode'      => '125009',
            'addressCountry'  => 'RU',
        ],
        'geo'       => [
            '@type'     => 'GeoCoordinates',
            'latitude'  => 55.7585,
            'longitude' => 37.6146,
        ],
        'openingHoursSpecification' => [
            [
                '@type'     => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'opens'     => '09:00',
                'closes'    => '18:00',
            ],
        ],
        'hasMap'    => 'https://yandex.ru/maps/org/remarka/51867347382/',
        'aggregateRating' => [
            '@type'       => 'AggregateRating',
            'ratingValue' => '4.98',
            'reviewCount' => '500',
            'bestRating'  => '5',
        ],
    ];

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'remarka_seo_contact_schema', 9 );

/* ── 6. SERVICE PAGES: Service schema ───────────────────────────────────────── */
function remarka_seo_service_schema(): void {
    if ( ! is_page_template( 'page-templates/template-service.php' ) ) return;
    global $post;
    if ( ! $post ) return;

    $site_url = home_url('/');
    $title    = get_the_title( $post->ID );
    $desc     = get_post_meta( $post->ID, '_meta_description', true )
              ?: wp_strip_all_tags( get_the_excerpt( $post->ID ) )
              ?: 'Профессиональный ' . mb_strtolower( $title ) . ' в Москве. Дипломированные переводчики, двойная проверка, гарантия 30 дней.';

    $schema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Service',
        'name'        => $title,
        'description' => $desc,
        'url'         => get_permalink( $post->ID ),
        'serviceType' => 'Перевод',
        'areaServed'  => [ '@type' => 'Country', 'name' => 'RU' ],
        'provider'    => [ '@id' => $site_url . '#organization' ],
        'aggregateRating' => [
            '@type'       => 'AggregateRating',
            'ratingValue' => '4.98',
            'reviewCount' => '500',
            'bestRating'  => '5',
        ],
    ];

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'remarka_seo_service_schema', 9 );

/* ── 7. PRICING PAGE: Service + PriceSpecification schema ───────────────────── */
function remarka_seo_pricing_schema(): void {
    if ( ! is_page_template( 'page-templates/template-pricing.php' ) ) return;

    $site_url = home_url('/');

    $schema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Service',
        'name'        => 'Перевод документов — стоимость',
        'description' => 'Стоимость письменного перевода в бюро «Ремарка». Три уровня качества: ИИ-постредактирование, профессиональный, премиальный. Калькулятор цен онлайн. Расчёт за 30 минут.',
        'url'         => home_url( '/stoimost-perevoda/' ),
        'serviceType' => 'Перевод',
        'areaServed'  => [ '@type' => 'Country', 'name' => 'RU' ],
        'provider'    => [ '@id' => $site_url . '#organization' ],
        'offers'      => [
            [
                '@type'         => 'Offer',
                'name'          => 'Профессиональный перевод',
                'price'         => '400',
                'priceCurrency' => 'RUB',
                'priceSpecification' => [
                    '@type'       => 'UnitPriceSpecification',
                    'price'       => '400',
                    'priceCurrency' => 'RUB',
                    'unitText'    => 'страница',
                ],
                'description'   => 'от 400 ₽/стр. для европейских языков',
            ],
            [
                '@type'         => 'Offer',
                'name'          => 'Премиальный перевод (носитель)',
                'price'         => '640',
                'priceCurrency' => 'RUB',
                'description'   => 'Перевод + редактор-носитель языка',
            ],
        ],
        'aggregateRating' => [
            '@type'       => 'AggregateRating',
            'ratingValue' => '4.98',
            'reviewCount' => '500',
            'bestRating'  => '5',
        ],
    ];

    $faq = [
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => [
            [
                '@type'          => 'Question',
                'name'           => 'Сколько стоит перевод на английский язык?',
                'acceptedAnswer' => [ '@type' => 'Answer', 'text' => 'Профессиональный перевод на английский язык — от 400 ₽ за стандартную страницу (1 800 знаков). Итоговая стоимость зависит от тематики, объёма и срочности.' ],
            ],
            [
                '@type'          => 'Question',
                'name'           => 'Сколько стоит срочный перевод?',
                'acceptedAnswer' => [ '@type' => 'Answer', 'text' => 'Срочный перевод (в тот же день) — коэффициент ×1.5 к базовой цене. Экспресс (за несколько часов) — коэффициент ×2.' ],
            ],
            [
                '@type'          => 'Question',
                'name'           => 'Как оплатить перевод?',
                'acceptedAnswer' => [ '@type' => 'Answer', 'text' => 'Оплата по факту выполненной работы — без предоплаты. Принимаем оплату безналичным расчётом (юрлицам и ИП — с закрывающими документами) и наличными в офисе.' ],
            ],
            [
                '@type'          => 'Question',
                'name'           => 'Есть ли скидки на большие объёмы?',
                'acceptedAnswer' => [ '@type' => 'Answer', 'text' => 'Да. Скидки на объём: от 10 страниц — 5%, от 20 страниц — 10%, от 50 страниц — 15%. Постоянным клиентам — индивидуальные условия.' ],
            ],
        ],
    ];

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
    echo '<script type="application/ld+json">' . wp_json_encode( $faq, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'remarka_seo_pricing_schema', 9 );

/* ── 8. LANGUAGES PAGE: ItemList schema ─────────────────────────────────────── */
function remarka_seo_languages_schema(): void {
    if ( ! is_page_template( 'page-templates/template-languages.php' ) ) return;

    $site_url = home_url('/');

    $langs = [
        [ 'Английский', 'perevod-na-angliyskiy', '400' ],
        [ 'Немецкий',   'perevod-na-nemetskiy',  '600' ],
        [ 'Французский','perevod-na-frantsuzskiy','600' ],
        [ 'Итальянский','perevod-na-italyanskiy', '600' ],
        [ 'Испанский',  'perevod-na-ispanskiy',   '600' ],
        [ 'Китайский',  'perevod-na-kitayskiy',  '1000' ],
        [ 'Японский',   'perevod-na-yaponskiy',  '1200' ],
        [ 'Арабский',   'perevod-na-arabskiy',    '800' ],
    ];

    $items = [];
    foreach ( $langs as $i => [ $name, $slug, $price ] ) {
        $items[] = [
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'item'     => [
                '@type'       => 'Service',
                'name'        => 'Перевод на ' . mb_strtolower( $name ) . ' язык',
                'url'         => home_url( '/' . $slug . '/' ),
                'offers'      => [
                    '@type'         => 'Offer',
                    'price'         => $price,
                    'priceCurrency' => 'RUB',
                ],
                'provider'    => [ '@id' => $site_url . '#organization' ],
            ],
        ];
    }

    $schema = [
        '@context'        => 'https://schema.org',
        '@type'           => 'ItemList',
        'name'            => 'Языки перевода — бюро «Ремарка»',
        'description'     => 'Профессиональный перевод на 60+ языков. Полный перечень языков с ценами.',
        'numberOfItems'   => count( $items ),
        'itemListElement' => $items,
    ];

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'remarka_seo_languages_schema', 9 );

/* ── 9. CANONICAL TAG ────────────────────────────────────────────────────────── */
function remarka_seo_canonical(): void {
    global $post;
    if ( ! $post ) return;

    $url = is_front_page() ? home_url('/') : get_permalink( $post->ID );
    if ( $url ) {
        echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";
    }
}
add_action( 'wp_head', 'remarka_seo_canonical', 7 );

/* ── 10. BREADCRUMB SCHEMA (non-language pages with breadcrumbs) ─────────────── */
function remarka_seo_breadcrumbs(): void {
    $site_url = home_url('/');

    if ( is_front_page() ) return;

    // Language pages have their own breadcrumb schema — skip
    if ( is_page_template( 'page-templates/template-language.php' ) ) return;

    global $post;
    if ( ! $post ) return;

    $tpl   = get_page_template_slug( $post->ID );
    $title = get_the_title( $post->ID );
    $url   = get_permalink( $post->ID );

    $tpl_map = [
        'page-templates/template-pricing.php'   => [ 'label' => 'Стоимость перевода' ],
        'page-templates/template-contact.php'   => [ 'label' => 'Контакты' ],
        'page-templates/template-languages.php' => [ 'label' => 'Языки перевода' ],
        'page-templates/template-cases.php'     => [ 'label' => 'Примеры работ' ],
        'page-templates/template-service.php'   => [ 'label' => $title ],
    ];

    if ( ! isset( $tpl_map[ $tpl ] ) ) return;

    $schema = [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => [
            [ '@type' => 'ListItem', 'position' => 1, 'name' => 'Главная',          'item' => $site_url ],
            [ '@type' => 'ListItem', 'position' => 2, 'name' => $tpl_map[$tpl]['label'], 'item' => $url ],
        ],
    ];

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'remarka_seo_breadcrumbs', 9 );
