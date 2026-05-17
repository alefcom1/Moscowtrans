<?php
defined( 'ABSPATH' ) || exit;

/**
 * AJAX-обработчики для всех дополнительных модулей:
 * B2BFlow, MarketEntry, PartnerFlow → remarka_save_b2b
 * FeedbackCollector               → remarka_save_review
 * OrderTracker                    → remarka_track_order
 * LoyaltyProgram (sync)           → remarka_sync_loyalty
 */
class Remarka_Modules_Ajax {

    public function __construct() {
        $actions = [
            'remarka_save_b2b',
            'remarka_save_review',
            'remarka_track_order',
            'remarka_sync_loyalty',
        ];
        foreach ( $actions as $action ) {
            add_action( 'wp_ajax_' . $action,        [ $this, str_replace( 'remarka_', '', $action ) ] );
            add_action( 'wp_ajax_nopriv_' . $action, [ $this, str_replace( 'remarka_', '', $action ) ] );
        }
    }

    /* ══════════════════════════════════════════════════════
       B2B / MARKET ENTRY / PARTNER → единый CPT + wp_options
    ══════════════════════════════════════════════════════ */
    public function save_b2b(): void {
        check_ajax_referer( 'remarka_nonce', 'nonce' );

        $raw  = wp_unslash( $_POST['data'] ?? '{}' );
        $data = json_decode( $raw, true ) ?: [];
        $type = sanitize_text_field( $data['type'] ?? 'b2b' );

        $labels = [
            'b2b'          => '🏢 B2B-клиент',
            'market_entry' => '🌍 Выход на рынок',
            'partner'      => '🤝 Партнёр',
        ];

        $company = sanitize_text_field(
            $data['company'] ?? $data['name'] ?? 'Без названия'
        );
        $email   = sanitize_email( $data['contact_email'] ?? $data['email'] ?? '' );

        // CPT remarka_order используем для хранения B2B-лидов
        $title = sprintf(
            '%s: %s — %s',
            $labels[ $type ] ?? 'Лид',
            $company,
            date_i18n( 'd.m.Y H:i' )
        );

        $post_id = wp_insert_post( [
            'post_type'   => 'remarka_order',
            'post_title'  => $title,
            'post_status' => 'publish',
        ] );

        if ( $post_id && ! is_wp_error( $post_id ) ) {
            update_post_meta( $post_id, '_remarka_tariff',  $type );
            update_post_meta( $post_id, '_remarka_contact', $email );
            update_post_meta( $post_id, '_remarka_contact_type', 'email' );
            update_post_meta( $post_id, '_remarka_slots',   $data );
            update_post_meta( $post_id, '_remarka_status',  'new' );

            // Специфичные поля
            foreach ( $data as $key => $val ) {
                if ( is_string( $val ) || is_numeric( $val ) ) {
                    update_post_meta( $post_id, '_b2b_' . sanitize_key( $key ), sanitize_textarea_field( $val ) );
                }
            }
        }

        // Лог в wp_options
        $log = get_option( 'remarka_b2b_log', [] );
        array_unshift( $log, array_merge( $data, [
            'post_id' => $post_id ?: 0,
            'date'    => current_time( 'mysql' ),
        ] ) );
        update_option( 'remarka_b2b_log', array_slice( $log, 0, 100 ) );

        // Email HR
        $this->_notify(
            $title,
            "Тип: {$type}\nКомпания: {$company}\nEmail: {$email}\n\n" .
            implode( "\n", array_map(
                fn($k,$v) => "{$k}: {$v}",
                array_keys($data), array_values($data)
            ) )
        );

        wp_send_json_success( [ 'post_id' => $post_id ] );
    }

    /* ══════════════════════════════════════════════════════
       REVIEWS → CPT remarka_review
    ══════════════════════════════════════════════════════ */
    public function save_review(): void {
        check_ajax_referer( 'remarka_nonce', 'nonce' );

        $rating    = absint( $_POST['rating']    ?? 0 );
        $comment   = sanitize_textarea_field( wp_unslash( $_POST['comment']   ?? '' ) );
        $publish   = ( $_POST['publish']   ?? '0' ) === '1';
        $anonymous = ( $_POST['anonymous'] ?? '0' ) === '1';
        $order_id  = absint( $_POST['order_id'] ?? 0 );

        if ( $rating < 1 || $rating > 5 ) {
            wp_send_json_error( 'Invalid rating' );
        }

        // Регистрируем CPT если ещё нет
        if ( ! post_type_exists( 'remarka_review' ) ) {
            register_post_type( 'remarka_review', [
                'public'   => false,
                'show_ui'  => true,
                'label'    => 'Отзывы',
                'supports' => [ 'title', 'editor' ],
            ] );
        }

        $stars   = str_repeat( '⭐', $rating );
        $user_id = get_current_user_id();
        $name    = $anonymous ? 'Аноним'
            : ( $user_id ? get_userdata($user_id)->display_name : 'Клиент' );

        $post_id = wp_insert_post( [
            'post_type'    => 'remarka_review',
            'post_title'   => "{$stars} — {$name} · " . date_i18n('d.m.Y'),
            'post_content' => $comment,
            'post_status'  => $publish ? 'publish' : 'private',
        ] );

        if ( $post_id && ! is_wp_error( $post_id ) ) {
            update_post_meta( $post_id, '_review_rating',    $rating );
            update_post_meta( $post_id, '_review_anonymous', $anonymous );
            update_post_meta( $post_id, '_review_order_id',  $order_id );
            update_post_meta( $post_id, '_review_user_id',   $user_id );
        }

        // Обновляем средний рейтинг в wp_options
        $stats = get_option( 'remarka_review_stats', [ 'count' => 0, 'total' => 0 ] );
        $stats['count']++;
        $stats['total'] += $rating;
        $stats['avg']    = round( $stats['total'] / $stats['count'], 2 );
        update_option( 'remarka_review_stats', $stats );

        wp_send_json_success( [ 'post_id' => $post_id, 'avg' => $stats['avg'] ] );
    }

    /* ══════════════════════════════════════════════════════
       ORDER TRACKER — поиск заказа по ID или email
    ══════════════════════════════════════════════════════ */
    public function track_order(): void {
        check_ajax_referer( 'remarka_nonce', 'nonce' );

        $query = sanitize_text_field( wp_unslash( $_POST['query'] ?? '' ) );
        if ( empty( $query ) ) {
            wp_send_json_error( 'Empty query' );
        }

        // Поиск по post_id (если передан #RM-... или числовой ID)
        $post_id = 0;
        if ( preg_match( '/\d+/', $query, $m ) ) {
            $post_id = absint( $m[0] );
        }

        // Поиск по email через meta
        $order = null;
        if ( $post_id > 0 ) {
            $post = get_post( $post_id );
            if ( $post && $post->post_type === 'remarka_order' ) {
                $order = $this->_format_order( $post );
            }
        }

        // Поиск по email
        if ( ! $order && is_email( $query ) ) {
            $posts = get_posts( [
                'post_type'   => 'remarka_order',
                'numberposts' => 1,
                'orderby'     => 'date',
                'order'       => 'DESC',
                'meta_query'  => [ [
                    'key'     => '_remarka_contact',
                    'value'   => $query,
                    'compare' => '=',
                ] ],
            ] );
            if ( ! empty( $posts ) ) {
                $order = $this->_format_order( $posts[0] );
            }
        }

        if ( $order ) {
            wp_send_json_success( $order );
        } else {
            wp_send_json_error( 'Not found' );
        }
    }

    private function _format_order( WP_Post $post ): array {
        $get     = fn($k) => get_post_meta( $post->ID, $k, true );
        $slots   = $get('_remarka_slots') ?: [];
        $status  = $get('_remarka_status') ?: 'new';

        // Маппинг статусов в читаемые
        $statusMap = [
            'new'        => 'new',
            'in_contact' => 'in_work',
            'in_work'    => 'in_work',
            'done'       => 'ready',
            'cancelled'  => 'cancelled',
        ];

        $tariffNames = [
            'mtpe'    => 'MTPE (Вычитка AI)',
            'human'   => 'Профессиональный',
            'premium' => 'Premium Expert',
        ];

        return [
            'id'        => '#RM-' . str_pad( $post->ID, 4, '0', STR_PAD_LEFT ),
            'status'    => $statusMap[ $status ] ?? 'new',
            'tariff'    => $tariffNames[ $get('_remarka_tariff') ] ?? $get('_remarka_tariff'),
            'lang_pair' => strtoupper( str_replace('-', ' → ', is_array($slots) ? ($slots['langPair'] ?? '') : '' ) ),
            'pages'     => is_array($slots) ? ( $slots['pages'] ?? '' ) : '',
            'deadline'  => $get('_remarka_deadline') ?: '',
            'manager'   => remarka_option( 'agent_name', 'Ольга' ),
            'progress'  => $this->_guessProgress( $status ),
            'date'      => date_i18n( 'd.m.Y', strtotime( $post->post_date ) ),
        ];
    }

    private function _guessProgress( string $status ): int {
        return match ( $status ) {
            'new'        => 5,
            'in_contact' => 20,
            'in_work'    => 60,
            'done'       => 100,
            'cancelled'  => 0,
            default      => 10,
        };
    }

    /* ══════════════════════════════════════════════════════
       LOYALTY SYNC — синхронизация с сервером
    ══════════════════════════════════════════════════════ */
    public function sync_loyalty(): void {
        check_ajax_referer( 'remarka_nonce', 'nonce' );

        $user_id = get_current_user_id();
        if ( ! $user_id ) {
            wp_send_json_error( 'Not logged in' );
        }

        $raw  = wp_unslash( $_POST['loyalty'] ?? '{}' );
        $data = json_decode( $raw, true ) ?: [];

        // Берём максимум из клиента и сервера
        $server = [
            'orders'     => (int) get_user_meta( $user_id, 'remarka_loyalty_orders',      true ),
            'totalSpent' => (int) get_user_meta( $user_id, 'remarka_loyalty_total_spent', true ),
        ];

        $merged = [
            'orders'     => max( $server['orders'],     (int)($data['orders']     ?? 0) ),
            'totalSpent' => max( $server['totalSpent'], (int)($data['totalSpent'] ?? 0) ),
        ];

        update_user_meta( $user_id, 'remarka_loyalty_orders',      $merged['orders'] );
        update_user_meta( $user_id, 'remarka_loyalty_total_spent', $merged['totalSpent'] );

        wp_send_json_success( $merged );
    }

    /* ══════════════════════════════════════════════════════
       HELPER: Email уведомление
    ══════════════════════════════════════════════════════ */
    private function _notify( string $subject, string $body ): void {
        wp_mail(
            get_option('admin_email'),
            $subject,
            $body . "\n\nДата: " . date_i18n('d.m.Y H:i') . "\nАдмин: " . admin_url('edit.php?post_type=remarka_order')
        );
    }
}
