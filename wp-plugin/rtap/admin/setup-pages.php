<?php
/**
 * Утилита создания SEO-страниц RTAP в WordPress (запускается вручную один раз).
 * wp-admin → RTAP → Настройки → нажать "Создать страницы тестов"
 */
defined('ABSPATH') || exit;

function rtap_create_pages(): array {
    $pages = [
        [
            'slug'     => 'test-perevodchika',
            'title'    => 'Тест для переводчиков',
            'content'  => '[rtap_quiz]',
            'meta_desc'=> 'Бесплатный профессиональный тест для переводчиков. 4 тематики, 3 уровня, сертификат при 70%+. Пройдите тест и получите предложение о сотрудничестве.',
        ],
        [
            'slug'     => 'test-perevodchika/tekhnicheskiy',
            'title'    => 'Тест для технических переводчиков',
            'content'  => '[rtap_quiz topic="technical" lang="en"]',
            'parent'   => 'test-perevodchika',
            'meta_desc'=> 'Бесплатный тест по техническому переводу (EN→RU). 10 вопросов, 3 уровня, сертификат. Машиностроение, химия, стандарты ISO/ГОСТ.',
        ],
        [
            'slug'     => 'test-perevodchika/yuridicheskiy',
            'title'    => 'Тест для юридических переводчиков',
            'content'  => '[rtap_quiz topic="legal" lang="en"]',
            'parent'   => 'test-perevodchika',
            'meta_desc'=> 'Тест по юридическому переводу с английского. Договоры, арбитраж, корпоративное право. Сертификат бюро «Ремарка».',
        ],
        [
            'slug'     => 'test-perevodchika/meditsinskiy',
            'title'    => 'Тест для медицинских переводчиков',
            'content'  => '[rtap_quiz topic="medical" lang="en"]',
            'parent'   => 'test-perevodchika',
            'meta_desc'=> 'Тест по медицинскому переводу (EN→RU). Кардиология, фармакология, клинические исследования. Получите сертификат бесплатно.',
        ],
        [
            'slug'     => 'test-perevodchika/it',
            'title'    => 'Тест для IT-переводчиков',
            'content'  => '[rtap_quiz topic="it" lang="en"]',
            'parent'   => 'test-perevodchika',
            'meta_desc'=> 'Тест по IT-переводу и локализации ПО. Интерфейсы, API-документация, кибербезопасность. Сертификат RTAP бесплатно.',
        ],
        [
            'slug'     => 'verify',
            'title'    => 'Верификация сертификата',
            'content'  => '[rtap_verify]',
            'meta_desc'=> 'Проверьте подлинность сертификата переводчика, выданного бюро переводов «Ремарка».',
        ],
    ];

    $created = [];
    $parent_ids = [];

    foreach ($pages as $page) {
        $existing = get_page_by_path($page['slug']);
        if ($existing) {
            $created[] = ['title' => $page['title'], 'status' => 'exists', 'url' => get_permalink($existing)];
            $parent_ids[$page['slug']] = $existing->ID;
            continue;
        }

        $parent_id = 0;
        if (!empty($page['parent']) && isset($parent_ids[$page['parent']])) {
            $parent_id = $parent_ids[$page['parent']];
        }

        $slug_part = basename($page['slug']);
        $page_id = wp_insert_post([
            'post_title'   => $page['title'],
            'post_name'    => $slug_part,
            'post_content' => $page['content'],
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_parent'  => $parent_id,
            'meta_input'   => [
                '_yoast_wpseo_metadesc'       => $page['meta_desc'],
                '_aioseo_description'          => $page['meta_desc'],
                '_rtap_meta_description'       => $page['meta_desc'],
            ],
        ]);

        if (!is_wp_error($page_id)) {
            $parent_ids[$page['slug']] = $page_id;
            $created[] = ['title' => $page['title'], 'status' => 'created', 'url' => get_permalink($page_id)];
        } else {
            $created[] = ['title' => $page['title'], 'status' => 'error', 'url' => ''];
        }
    }

    return $created;
}
