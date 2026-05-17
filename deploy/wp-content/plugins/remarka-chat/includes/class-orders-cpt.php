<?php
defined( 'ABSPATH' ) || exit;

class Remarka_Orders {

    public function __construct() {
        add_action( 'init',                  [ $this, 'register_cpt' ] );
        add_action( 'add_meta_boxes',        [ $this, 'add_meta_boxes' ] );
        add_filter( 'manage_remarka_order_posts_columns',       [ $this, 'admin_columns' ] );
        add_action( 'manage_remarka_order_posts_custom_column', [ $this, 'admin_column_content' ], 10, 2 );
        add_filter( 'manage_edit-remarka_order_sortable_columns', [ $this, 'sortable_columns' ] );
        add_action( 'restrict_manage_posts', [ $this, 'filter_bar' ] );
        add_filter( 'parse_query',           [ $this, 'filter_query' ] );
    }

    /* ══════════════════════════════════════════════════════
       REGISTER CPT
    ══════════════════════════════════════════════════════ */
    public function register_cpt(): void {
        register_post_type( 'remarka_order', [
            'labels' => [
                'name'               => 'Заказы переводов',
                'singular_name'      => 'Заказ перевода',
                'add_new'            => 'Добавить заказ',
                'add_new_item'       => 'Добавить заказ',
                'edit_item'          => 'Редактировать заказ',
                'new_item'           => 'Новый заказ',
                'view_item'          => 'Просмотр заказа',
                'search_items'       => 'Найти заказы',
                'not_found'          => 'Заказов не найдено',
                'not_found_in_trash' => 'В корзине заказов нет',
                'menu_name'          => 'Заказы',
            ],
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => false,   // Прячем — показываем в нашем меню
            'show_in_admin_bar'   => false,
            'show_in_rest'        => false,
            'capability_type'     => 'post',
            'capabilities'        => [
                'create_posts' => 'manage_options',  // Создание только программно
            ],
            'map_meta_cap'        => true,
            'hierarchical'        => false,
            'supports'            => [ 'title' ],
            'has_archive'         => false,
            'rewrite'             => false,
        ] );
    }

    /* ══════════════════════════════════════════════════════
       META BOXES
    ══════════════════════════════════════════════════════ */
    public function add_meta_boxes(): void {
        add_meta_box(
            'remarka_order_details',
            '📋 Детали заказа',
            [ $this, 'meta_box_details' ],
            'remarka_order',
            'normal',
            'high'
        );
        add_meta_box(
            'remarka_order_actions',
            '⚡ Действия',
            [ $this, 'meta_box_actions' ],
            'remarka_order',
            'side',
            'high'
        );
    }

    public function meta_box_details( WP_Post $post ): void {
        $tariff       = get_post_meta( $post->ID, '_remarka_tariff',       true );
        $contact      = get_post_meta( $post->ID, '_remarka_contact',      true );
        $contact_type = get_post_meta( $post->ID, '_remarka_contact_type', true );
        $total        = get_post_meta( $post->ID, '_remarka_total',        true );
        $slots        = get_post_meta( $post->ID, '_remarka_slots',        true );
        $session_id   = get_post_meta( $post->ID, '_remarka_session_id',   true );

        if ( ! is_array( $slots ) ) $slots = [];

        $tariff_names = [
            'mtpe'    => 'MTPE (Вычитка AI)',
            'human'   => 'Профессиональный',
            'premium' => 'Premium Expert',
        ];
        $domain_names = [
            'general' => 'Общий', 'technical' => 'Технический', 'legal' => 'Юридический',
            'medical' => 'Медицинский', 'it' => 'IT', 'finance' => 'Финансовый',
            'marketing' => 'Маркетинговый',
        ];
        $urgency_names = [
            'standard' => '📅 Стандарт (3–7 дн.)',
            'urgent'   => '🔥 Срочно (1–2 дня)',
            'express'  => '⚡ Экспресс (24 ч)',
            'flexible' => '📆 Гибко',
            'superexp' => '🚀 Суперэкспресс',
        ];
        ?>
        <style>
        .remarka-meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .remarka-meta-row  { display: flex; flex-direction: column; gap: 4px; }
        .remarka-meta-label{ font-size: 11px; font-weight: 600; text-transform: uppercase; color: #888; letter-spacing:.05em }
        .remarka-meta-value{ font-size: 14px; color: #1e293b; font-weight: 500; }
        .remarka-total     { font-size: 22px; color: #059669; font-weight: 800; }
        .remarka-badge-tag { display:inline-block; padding:2px 8px; border-radius:4px; font-size:11px; font-weight:700; }
        .remarka-badge-mtpe    { background:#dbeafe; color:#1d4ed8; }
        .remarka-badge-human   { background:#ede9fe; color:#6d28d9; }
        .remarka-badge-premium { background:#fef9c3; color:#92400e; }
        .remarka-divider   { border: none; border-top: 1px solid #f1f5f9; margin: 16px 0; }
        </style>

        <div class="remarka-meta-grid" style="margin-top:12px">
            <div class="remarka-meta-row">
                <span class="remarka-meta-label">Тариф</span>
                <span class="remarka-meta-value">
                    <span class="remarka-badge-tag remarka-badge-<?= esc_attr($tariff) ?>">
                        <?= esc_html( $tariff_names[$tariff] ?? $tariff )  ?>
                    </span>
                </span>
            </div>
            <div class="remarka-meta-row">
                <span class="remarka-meta-label">Итого</span>
                <span class="remarka-total"><?= number_format( (int)$total, 0, '.', ' ' ) ?> ₽</span>
            </div>
            <div class="remarka-meta-row">
                <span class="remarka-meta-label">Контакт</span>
                <span class="remarka-meta-value">
                    <?= esc_html( $contact ) ?>
                    <span style="color:#888;font-size:11px">(<?= esc_html($contact_type) ?>)</span>
                </span>
            </div>
            <div class="remarka-meta-row">
                <span class="remarka-meta-label">Языковая пара</span>
                <span class="remarka-meta-value">
                    <?= esc_html( strtoupper( str_replace('-', ' → ', $slots['langPair'] ?? '—') ) )?>
                </span>
            </div>
            <div class="remarka-meta-row">
                <span class="remarka-meta-label">Тип документа</span>
                <span class="remarka-meta-value"><?= esc_html( $domain_names[$slots['domain'] ?? ''] ?? '—' ) ?></span>
            </div>
            <div class="remarka-meta-row">
                <span class="remarka-meta-label">Объём</span>
                <span class="remarka-meta-value">
                    <?php
                    if ( ! empty($slots['pages']) ) echo esc_html($slots['pages']) . ' стр.';
                    elseif ( ! empty($slots['chars']) ) echo number_format((int)$slots['chars'], 0, '.', ' ') . ' зн.';
                    else echo '—';
                    ?>
                </span>
            </div>
            <div class="remarka-meta-row">
                <span class="remarka-meta-label">Срочность</span>
                <span class="remarka-meta-value"><?= esc_html( $urgency_names[$slots['urgency'] ?? ''] ?? '—' ) ?></span>
            </div>
            <div class="remarka-meta-row">
                <span class="remarka-meta-label">SEO</span>
                <span class="remarka-meta-value"><?= esc_html( $slots['seo'] ?? '—' ) ?></span>
            </div>
        </div>

        <hr class="remarka-divider">

        <div class="remarka-meta-row">
            <span class="remarka-meta-label">Session ID</span>
            <code style="font-size:11px;color:#666"><?= esc_html($session_id) ?></code>
        </div>
        <?php
    }

    public function meta_box_actions( WP_Post $post ): void {
        $contact      = get_post_meta( $post->ID, '_remarka_contact',      true );
        $contact_type = get_post_meta( $post->ID, '_remarka_contact_type', true );
        $status       = get_post_meta( $post->ID, '_remarka_status',       true ) ?: 'new';

        $statuses = [
            'new'        => [ 'label' => '🆕 Новый',       'color' => '#dbeafe' ],
            'in_contact' => [ 'label' => '📞 Связались',   'color' => '#fef9c3' ],
            'in_work'    => [ 'label' => '⚙️ В работе',    'color' => '#dcfce7' ],
            'done'       => [ 'label' => '✅ Выполнен',     'color' => '#f0fdf4' ],
            'cancelled'  => [ 'label' => '❌ Отменён',      'color' => '#fef2f2' ],
        ];
        ?>
        <p><strong>Статус заказа:</strong></p>
        <select name="remarka_order_status" style="width:100%;margin-bottom:10px">
            <?php foreach ( $statuses as $val => $info ) : ?>
                <option value="<?= $val ?>" <?= selected($status,$val,false) ?>>
                    <?= $info['label'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <?php wp_nonce_field( 'remarka_order_meta', 'remarka_order_nonce' ); ?>

        <?php if ( $contact ) : ?>
        <hr>
        <p><strong>Связаться:</strong></p>
        <?php if ( $contact_type === 'email' ) : ?>
            <a href="mailto:<?= esc_attr($contact) ?>" class="button button-secondary" style="width:100%;text-align:center;margin-bottom:6px">
                📧 Написать email
            </a>
        <?php elseif ( $contact_type === 'phone' ) : ?>
            <a href="tel:<?= esc_attr($contact) ?>" class="button button-secondary" style="width:100%;text-align:center;margin-bottom:6px">
                📞 Позвонить
            </a>
        <?php endif; ?>
        <?php endif; ?>
        <?php
    }

    /* ══════════════════════════════════════════════════════
       SAVE META (статус заказа)
    ══════════════════════════════════════════════════════ */
    public function __construct_save() {
        add_action( 'save_post_remarka_order', [ $this, 'save_meta' ] );
    }

    public function save_meta( int $post_id ): void {
        if ( ! isset( $_POST['remarka_order_nonce'] ) ) return;
        if ( ! wp_verify_nonce( $_POST['remarka_order_nonce'], 'remarka_order_meta' ) ) return;
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;

        $status = sanitize_text_field( $_POST['remarka_order_status'] ?? 'new' );
        update_post_meta( $post_id, '_remarka_status', $status );
    }

    /* ══════════════════════════════════════════════════════
       ADMIN COLUMNS
    ══════════════════════════════════════════════════════ */
    public function admin_columns( array $columns ): array {
        return [
            'cb'              => $columns['cb'],
            'title'           => 'Заказ',
            'remarka_tariff'  => 'Тариф',
            'remarka_contact' => 'Контакт',
            'remarka_lang'    => 'Языки',
            'remarka_total'   => 'Сумма',
            'remarka_status'  => 'Статус',
            'date'            => 'Дата',
        ];
    }

    public function admin_column_content( string $column, int $post_id ): void {
        $tariff_names = [ 'mtpe' => '🤖 MTPE', 'human' => '👨‍💼 Проф.', 'premium' => '⭐ Premium' ];
        $status_labels = [
            'new'        => '<span style="background:#dbeafe;color:#1d4ed8;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:700">🆕 Новый</span>',
            'in_contact' => '<span style="background:#fef9c3;color:#92400e;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:700">📞 Связались</span>',
            'in_work'    => '<span style="background:#dcfce7;color:#166534;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:700">⚙️ В работе</span>',
            'done'       => '<span style="background:#f0fdf4;color:#166534;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:700">✅ Выполнен</span>',
            'cancelled'  => '<span style="background:#fef2f2;color:#991b1b;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:700">❌ Отменён</span>',
        ];

        switch ( $column ) {
            case 'remarka_tariff':
                $t = get_post_meta( $post_id, '_remarka_tariff', true );
                echo esc_html( $tariff_names[$t] ?? $t );
                break;
            case 'remarka_contact':
                $c  = get_post_meta( $post_id, '_remarka_contact',      true );
                $ct = get_post_meta( $post_id, '_remarka_contact_type', true );
                echo esc_html($c) . ' <span style="color:#888;font-size:11px">(' . esc_html($ct) . ')</span>';
                break;
            case 'remarka_lang':
                $lp = get_post_meta( $post_id, '_remarka_lang_pair', true );
                echo esc_html( strtoupper( str_replace('-', '→', $lp ?: '—') ) );
                break;
            case 'remarka_total':
                $total = (int) get_post_meta( $post_id, '_remarka_total', true );
                echo '<strong>' . number_format( $total, 0, '.', ' ' ) . ' ₽</strong>';
                break;
            case 'remarka_status':
                $s = get_post_meta( $post_id, '_remarka_status', true ) ?: 'new';
                echo $status_labels[$s] ?? esc_html($s);  // phpcs:ignore
                break;
        }
    }

    public function sortable_columns( array $cols ): array {
        $cols['remarka_total']  = 'remarka_total';
        $cols['remarka_status'] = 'remarka_status';
        return $cols;
    }

    /* ══════════════════════════════════════════════════════
       FILTER BAR — фильтр по тарифу/статусу
    ══════════════════════════════════════════════════════ */
    public function filter_bar( string $post_type ): void {
        if ( $post_type !== 'remarka_order' ) return;

        $tariff = sanitize_text_field( $_GET['remarka_tariff'] ?? '' );
        $status = sanitize_text_field( $_GET['remarka_status'] ?? '' );
        ?>
        <select name="remarka_tariff">
            <option value="">Все тарифы</option>
            <option value="mtpe"    <?= selected($tariff,'mtpe',false)    ?>>🤖 MTPE</option>
            <option value="human"   <?= selected($tariff,'human',false)   ?>>👨‍💼 Профессиональный</option>
            <option value="premium" <?= selected($tariff,'premium',false) ?>>⭐ Premium</option>
        </select>
        <select name="remarka_status">
            <option value="">Все статусы</option>
            <option value="new"        <?= selected($status,'new',false)        ?>>🆕 Новый</option>
            <option value="in_contact" <?= selected($status,'in_contact',false) ?>>📞 Связались</option>
            <option value="in_work"    <?= selected($status,'in_work',false)    ?>>⚙️ В работе</option>
            <option value="done"       <?= selected($status,'done',false)       ?>>✅ Выполнен</option>
            <option value="cancelled"  <?= selected($status,'cancelled',false)  ?>>❌ Отменён</option>
        </select>
        <?php
    }

    public function filter_query( WP_Query $query ): void {
        if ( ! is_admin() || ! $query->is_main_query() ) return;
        if ( $query->get('post_type') !== 'remarka_order' ) return;

        $meta_query = [];

        $tariff = sanitize_text_field( $_GET['remarka_tariff'] ?? '' );
        if ( $tariff ) {
            $meta_query[] = [ 'key' => '_remarka_tariff', 'value' => $tariff ];
        }

        $status = sanitize_text_field( $_GET['remarka_status'] ?? '' );
        if ( $status ) {
            $meta_query[] = [ 'key' => '_remarka_status', 'value' => $status ];
        }

        if ( ! empty($meta_query) ) {
            $query->set( 'meta_query', $meta_query );
        }
    }
}
