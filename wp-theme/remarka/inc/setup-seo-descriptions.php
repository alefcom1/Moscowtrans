<?php
/**
 * One-time setup: populate rank_math_description (and rank_math_title) for all pages.
 *
 * Run via: https://moscowtrans.ru/?remarka_setup_seo=1
 * Requires admin login.
 *
 * Safe to re-run: skips pages that already have a non-empty rank_math_description.
 * To force-overwrite: ?remarka_setup_seo=1&force=1
 */

add_action( 'init', function (): void {
    if ( ! isset( $_GET['remarka_setup_seo'] ) ) return;
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Access denied.' );

    @ini_set( 'memory_limit', '512M' );
    @set_time_limit( 300 );

    $force = ! empty( $_GET['force'] );

    /* ── Language data: slug fragment → [acc, price] ─────────────────────────── */
    $langs = [
        'perevod-na-angliyskiy'       => [ 'acc' => 'английский',     'price' => 400  ],
        'perevod-na-nemetskiy'        => [ 'acc' => 'немецкий',        'price' => 600  ],
        'perevod-na-frantsuzskiy'     => [ 'acc' => 'французский',     'price' => 600  ],
        'perevod-na-italyanskiy'      => [ 'acc' => 'итальянский',     'price' => 600  ],
        'perevod-na-ispanskiy'        => [ 'acc' => 'испанский',       'price' => 600  ],
        'perevod-na-portugalskiy'     => [ 'acc' => 'португальский',   'price' => 600  ],
        'perevod-na-kitayskiy'        => [ 'acc' => 'китайский',       'price' => 1000 ],
        'perevod-na-yaponskiy'        => [ 'acc' => 'японский',        'price' => 1200 ],
        'perevod-na-arabskiy'         => [ 'acc' => 'арабский',        'price' => 800  ],
        'perevod-na-koreyskiy'        => [ 'acc' => 'корейский',       'price' => 1000 ],
        'perevod-na-niderlandskiy'    => [ 'acc' => 'нидерландский',   'price' => 800  ],
        'perevod-na-polskiy'          => [ 'acc' => 'польский',        'price' => 600  ],
        'perevod-na-cheshskiy'        => [ 'acc' => 'чешский',         'price' => 600  ],
        'perevod-na-slovatskiy'       => [ 'acc' => 'словацкий',       'price' => 600  ],
        'perevod-na-vengerskiy'       => [ 'acc' => 'венгерский',      'price' => 900  ],
        'perevod-na-rumynskiy'        => [ 'acc' => 'румынский',       'price' => 600  ],
        'perevod-na-bolgarskiy'       => [ 'acc' => 'болгарский',      'price' => 800  ],
        'perevod-na-serbskiy'         => [ 'acc' => 'сербский',        'price' => 600  ],
        'perevod-na-khorvatskiy'      => [ 'acc' => 'хорватский',      'price' => 600  ],
        'perevod-na-slovenskiy'       => [ 'acc' => 'словенский',      'price' => 800  ],
        'perevod-na-grecheskiy'       => [ 'acc' => 'греческий',       'price' => 800  ],
        'perevod-na-finskiy'          => [ 'acc' => 'финский',         'price' => 1000 ],
        'perevod-na-shvedskiy'        => [ 'acc' => 'шведский',        'price' => 1000 ],
        'perevod-na-norvezhskiy'      => [ 'acc' => 'норвежский',      'price' => 1000 ],
        'perevod-na-datskiy'          => [ 'acc' => 'датский',         'price' => 1000 ],
        'perevod-na-estonskiy'        => [ 'acc' => 'эстонский',       'price' => 800  ],
        'perevod-na-latyshskiy'       => [ 'acc' => 'латышский',       'price' => 800  ],
        'perevod-na-litovskiy'        => [ 'acc' => 'литовский',       'price' => 800  ],
        'perevod-na-ukrainskiy'       => [ 'acc' => 'украинский',      'price' => 400  ],
        'perevod-na-belorusskiy'      => [ 'acc' => 'белорусский',     'price' => 600  ],
        'perevod-na-kazakhskiy'       => [ 'acc' => 'казахский',       'price' => 600  ],
        'perevod-na-azerbaydzhanskiy' => [ 'acc' => 'азербайджанский', 'price' => 500  ],
        'perevod-na-armyanskiy'       => [ 'acc' => 'армянский',       'price' => 600  ],
        'perevod-na-gruzinskiy'       => [ 'acc' => 'грузинский',      'price' => 600  ],
    ];

    /* ── Static descriptions for fixed templates ──────────────────────────────── */
    $static = [
        'page-templates/template-pricing.php'   => 'Стоимость перевода в Москве — от 400 ₽/стр. Калькулятор цен онлайн. Три уровня качества: ИИ-постредактирование, профессиональный, премиум. Расчёт за 30 минут.',
        'page-templates/template-languages.php' => 'Перевод на 60+ языков в бюро «Ремарка», Москва. Английский, немецкий, французский, китайский, японский, арабский и другие. Профильные переводчики. Цены от 400 ₽/стр.',
        'page-templates/template-contact.php'   => 'Контакты бюро переводов «Ремарка» — Москва, Глинищевский пер., 6, оф. 2. Тел. +7 (495) 970-44-13. WhatsApp +7 (985) 970-44-13. Пн–Пт 9:00–18:00.',
        'page-templates/template-cases.php'     => 'Кейсы бюро переводов «Ремарка» — реальные переводческие проекты: юридические, технические, медицинские, финансовые тексты. Объёмы, сроки, языки, результаты.',
    ];

    /* ── Helper: trim description to ≤160 chars ──────────────────────────────── */
    $trim = function ( string $d ): string {
        return mb_strlen( $d ) > 160 ? mb_substr( $d, 0, 157 ) . '…' : $d;
    };

    /* ── Helper: generate desc from post title ───────────────────────────────── */
    $from_title = function ( string $raw ) use ( $trim ): string {
        $subject = preg_split( '/\s*[|—]\s*/u', $raw )[0];
        $subject = trim( preg_replace( '/\s+(в\s+)?Москв[еа]\s*$/u', '', trim( $subject ) ) );
        return $trim( $subject . ' в Москве — бюро «Ремарка». Профессиональные переводчики, NDA, двойная проверка качества. Расчёт стоимости бесплатно.' );
    };

    /* ── Front page ──────────────────────────────────────────────────────────── */
    $front_id = (int) get_option( 'page_on_front' );
    $front_desc = 'Бюро переводов «Ремарка», Москва — профессиональный письменный перевод на 60+ языков. Юридические, технические, медицинские документы. Дипломированные переводчики, NDA. От 400 ₽/стр.';

    /* ── Get all published pages ──────────────────────────────────────────────── */
    $pages = get_posts( [
        'post_type'      => 'page',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'fields'         => 'ids',
    ] );

    $updated  = [];
    $skipped  = [];
    $no_match = [];

    foreach ( $pages as $page_id ) {
        $existing = get_post_meta( $page_id, 'rank_math_description', true );
        if ( $existing && ! $force ) {
            $skipped[] = get_the_title( $page_id );
            continue;
        }

        $tpl  = get_page_template_slug( $page_id );
        $slug = get_post_field( 'post_name', $page_id );
        $desc = '';

        if ( $page_id === $front_id ) {
            $desc = $front_desc;
        } elseif ( isset( $langs[ $slug ] ) ) {
            $l    = $langs[ $slug ];
            $desc = $trim( 'Профессиональный перевод на ' . $l['acc'] . ' язык в Москве. '
                . 'Юридические, технические, медицинские документы. '
                . 'Дипломированные переводчики, двойная проверка. '
                . 'От ' . $l['price'] . ' ₽/страницу. Срок от 1 дня.' );
        } elseif ( $tpl === 'page-templates/template-service.php'
                || $tpl === 'page-templates/template-subservice.php' ) {
            $desc = $from_title( get_post_field( 'post_title', $page_id ) );
        } elseif ( isset( $static[ $tpl ] ) ) {
            $desc = $static[ $tpl ];
        } else {
            $no_match[] = get_the_title( $page_id ) . ' [' . $tpl . ']';
            continue;
        }

        if ( $desc ) {
            update_post_meta( $page_id, 'rank_math_description', $desc );
            $updated[] = get_the_title( $page_id );
        }
    }

    /* ── Also handle blog posts (optional, only if posts exist) ──────────────── */
    $posts = get_posts( [
        'post_type'      => 'post',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'fields'         => 'ids',
    ] );

    $posts_updated = 0;
    foreach ( $posts as $post_id ) {
        $existing = get_post_meta( $post_id, 'rank_math_description', true );
        if ( $existing && ! $force ) continue;

        $excerpt = get_the_excerpt( $post_id );
        if ( $excerpt ) {
            $desc = $trim( wp_strip_all_tags( $excerpt ) );
            update_post_meta( $post_id, 'rank_math_description', $desc );
            $posts_updated++;
        }
    }

    /* ── Output results ──────────────────────────────────────────────────────── */
    header( 'Content-Type: text/html; charset=utf-8' );
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head><meta charset="utf-8"><title>SEO Descriptions Setup</title>
    <style>body{font-family:sans-serif;max-width:900px;margin:40px auto;padding:0 20px}
    h2{color:#2a2a2a}details{margin:8px 0}summary{cursor:pointer;color:#0073aa}
    .ok{color:#46b450}.warn{color:#f56e28}.muted{color:#888;font-size:.85em}</style>
    </head><body>
    <h2>✅ Описания заполнены</h2>
    <p class="ok"><strong>Обновлено страниц: <?php echo count( $updated ); ?></strong></p>
    <p class="muted">Постов блога обновлено: <?php echo $posts_updated; ?></p>
    <?php if ( $skipped ): ?>
    <details><summary class="muted">Пропущено (уже заполнены): <?php echo count( $skipped ); ?></summary>
    <ul><?php foreach ( $skipped as $t ) echo '<li>' . esc_html( $t ) . '</li>'; ?></ul>
    </details>
    <?php endif; ?>
    <?php if ( $no_match ): ?>
    <details><summary class="warn">Не удалось сгенерировать (нет шаблона): <?php echo count( $no_match ); ?></summary>
    <ul><?php foreach ( $no_match as $t ) echo '<li>' . esc_html( $t ) . '</li>'; ?></ul>
    </details>
    <?php endif; ?>
    <hr>
    <p class="muted">Чтобы перезаписать существующие описания: добавьте <code>&amp;force=1</code></p>
    </body></html>
    <?php
    exit;
} );
