<?php
defined( 'ABSPATH' ) || exit;

class Remarka_Ajax {

    public function __construct() {
        // Сохранение сессии (авториз. и нет)
        add_action( 'wp_ajax_remarka_save_session',        [ $this, 'save_session' ] );
        add_action( 'wp_ajax_nopriv_remarka_save_session', [ $this, 'save_session' ] );

        // Загрузка сессии
        add_action( 'wp_ajax_remarka_load_session',        [ $this, 'load_session' ] );
        add_action( 'wp_ajax_nopriv_remarka_load_session', [ $this, 'load_session' ] );

        // Проксирование к api/gpt.php
        add_action( 'wp_ajax_remarka_chat',        [ $this, 'proxy_gpt' ] );
        add_action( 'wp_ajax_nopriv_remarka_chat', [ $this, 'proxy_gpt' ] );

        // Сохранение заказа (CPT + wp_options log)
        add_action( 'wp_ajax_remarka_save_order',        [ $this, 'save_order' ] );
        add_action( 'wp_ajax_nopriv_remarka_save_order', [ $this, 'save_order' ] );

        // Geo lookup
        add_action( 'wp_ajax_remarka_geo',        [ $this, 'get_geo' ] );
        add_action( 'wp_ajax_nopriv_remarka_geo', [ $this, 'get_geo' ] );
    }

    /* ══════════════════════════════════════════════════════
       PROXY → api/gpt.php
    ══════════════════════════════════════════════════════ */
    public function proxy_gpt(): void {
        check_ajax_referer( 'remarka_nonce', 'nonce' );

        $text   = sanitize_textarea_field( wp_unslash( $_POST['text']   ?? '' ) );
        $system = sanitize_textarea_field( wp_unslash( $_POST['system'] ?? '' ) );

        if ( empty( $text ) ) {
            wp_send_json_error( [ 'message' => 'Empty text' ], 400 );
        }

        // URL прокси из настроек
        $proxy_url = remarka_option( 'gpt_proxy_url', '/api/gpt.php' );

        // Если путь относительный — строим абсолютный
        if ( str_starts_with( $proxy_url, '/' ) ) {
            $proxy_url = home_url( $proxy_url );
        }

        $payload = wp_json_encode( [
            'text'   => $text,
            'system' => $system,
        ] );

        $response = wp_remote_post( $proxy_url, [
            'timeout'     => 30,
            'headers'     => [ 'Content-Type' => 'application/json' ],
            'body'        => $payload,
            'data_format' => 'body',
        ] );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( [ 'message' => $response->get_error_message() ], 502 );
        }

        $code = wp_remote_retrieve_response_code( $response );
        $body = wp_remote_retrieve_body( $response );

        // Пробрасываем ответ как есть (JSON от gpt.php)
        status_header( $code );
        header( 'Content-Type: application/json; charset=utf-8' );
        echo $body;  // phpcs:ignore
        wp_die();
    }

    /* ══════════════════════════════════════════════════════
       SESSION: SAVE
    ══════════════════════════════════════════════════════ */
    public function save_session(): void {
        check_ajax_referer( 'remarka_nonce', 'nonce' );
        global $wpdb;

        $session_id   = sanitize_text_field( wp_unslash( $_POST['session_id']   ?? '' ) );
        $slots        = wp_unslash( $_POST['slots']        ?? '{}' );
        $intent       = sanitize_text_field( wp_unslash( $_POST['intent']       ?? '' ) );
        $messages     = wp_unslash( $_POST['messages']     ?? '[]' );
        $page_context = sanitize_text_field( wp_unslash( $_POST['page_context'] ?? 'general' ) );
        $geo_city     = sanitize_text_field( wp_unslash( $_POST['geo_city']     ?? '' ) );
        $geo_country  = sanitize_text_field( wp_unslash( $_POST['geo_country']  ?? '' ) );

        if ( empty( $session_id ) ) {
            wp_send_json_error( 'No session_id' );
        }

        // Валидируем JSON
        $slots_arr    = json_decode( $slots, true )    ?: [];
        $messages_arr = json_decode( $messages, true ) ?: [];

        $table = $wpdb->prefix . 'remarka_sessions';

        $exists = $wpdb->get_var(
            $wpdb->prepare( "SELECT id FROM {$table} WHERE session_id = %s", $session_id )
        );

        if ( $exists ) {
            $wpdb->update(
                $table,
                [
                    'slots'        => wp_json_encode( $slots_arr,    JSON_UNESCAPED_UNICODE ),
                    'intent'       => $intent,
                    'messages'     => wp_json_encode( $messages_arr, JSON_UNESCAPED_UNICODE ),
                    'page_context' => $page_context,
                    'geo_city'     => $geo_city,
                    'geo_country'  => $geo_country,
                    'user_id'      => get_current_user_id(),
                ],
                [ 'session_id' => $session_id ],
                [ '%s', '%s', '%s', '%s', '%s', '%s', '%d' ],
                [ '%s' ]
            );
        } else {
            $wpdb->insert(
                $table,
                [
                    'session_id'   => $session_id,
                    'user_id'      => get_current_user_id(),
                    'slots'        => wp_json_encode( $slots_arr,    JSON_UNESCAPED_UNICODE ),
                    'intent'       => $intent,
                    'messages'     => wp_json_encode( $messages_arr, JSON_UNESCAPED_UNICODE ),
                    'page_context' => $page_context,
                    'geo_city'     => $geo_city,
                    'geo_country'  => $geo_country,
                ],
                [ '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s' ]
            );
        }

        wp_send_json_success( [ 'saved' => true ] );
    }

    /* ══════════════════════════════════════════════════════
       SESSION: LOAD
    ══════════════════════════════════════════════════════ */
    public function load_session(): void {
        check_ajax_referer( 'remarka_nonce', 'nonce' );
        global $wpdb;

        $session_id = sanitize_text_field( wp_unslash( $_POST['session_id'] ?? '' ) );

        if ( empty( $session_id ) ) {
            wp_send_json_error( 'No session_id' );
        }

        $table = $wpdb->prefix . 'remarka_sessions';
        $row   = $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM {$table} WHERE session_id = %s", $session_id ),
            ARRAY_A
        );

        if ( ! $row ) {
            wp_send_json_success( null );  // Новая сессия
        }

        wp_send_json_success( [
            'session_id'   => $row['session_id'],
            'slots'        => json_decode( $row['slots'],    true ) ?: [],
            'intent'       => $row['intent'],
            'messages'     => json_decode( $row['messages'], true ) ?: [],
            'page_context' => $row['page_context'],
            'geo_city'     => $row['geo_city'],
            'geo_country'  => $row['geo_country'],
        ] );
    }

    /* ══════════════════════════════════════════════════════
       ORDER: SAVE → CPT + wp_options log
    ══════════════════════════════════════════════════════ */
    public function save_order(): void {
        check_ajax_referer( 'remarka_nonce', 'nonce' );

        $session_id = sanitize_text_field( wp_unslash( $_POST['session_id'] ?? '' ) );
        $tariff     = sanitize_text_field( wp_unslash( $_POST['tariff']     ?? '' ) );
        $contact    = sanitize_text_field( wp_unslash( $_POST['contact']    ?? '' ) );
        $contact_type = sanitize_text_field( wp_unslash( $_POST['contact_type'] ?? 'email' ) );
        $slots_raw  = wp_unslash( $_POST['slots'] ?? '{}' );
        $total      = absint( $_POST['total'] ?? 0 );

        $slots = json_decode( $slots_raw, true ) ?: [];

        $tariff_names = [
            'mtpe'    => remarka_option( 'tariff_mtpe_name',    'MTPE' ),
            'human'   => remarka_option( 'tariff_human_name',   'Профессиональный' ),
            'premium' => remarka_option( 'tariff_premium_name', 'Premium Expert' ),
        ];

        // 1. Сохранить в CPT remarka_order
        $post_title = sprintf(
            'Заказ #%s — %s — %s',
            strtoupper( substr( $session_id, 0, 8 ) ),
            $tariff_names[ $tariff ] ?? $tariff,
            date_i18n( 'd.m.Y H:i' )
        );

        $post_id = wp_insert_post( [
            'post_type'   => 'remarka_order',
            'post_title'  => $post_title,
            'post_status' => 'publish',
        ] );

        if ( $post_id && ! is_wp_error( $post_id ) ) {
            update_post_meta( $post_id, '_remarka_session_id',   $session_id );
            update_post_meta( $post_id, '_remarka_tariff',       $tariff );
            update_post_meta( $post_id, '_remarka_contact',      $contact );
            update_post_meta( $post_id, '_remarka_contact_type', $contact_type );
            update_post_meta( $post_id, '_remarka_total',        $total );
            update_post_meta( $post_id, '_remarka_slots',        $slots );
            update_post_meta( $post_id, '_remarka_lang_pair',    $slots['langPair'] ?? '' );
            update_post_meta( $post_id, '_remarka_domain',       $slots['domain']   ?? '' );
            update_post_meta( $post_id, '_remarka_pages',        $slots['pages']    ?? '' );
            update_post_meta( $post_id, '_remarka_urgency',      $slots['urgency']  ?? '' );
        }

        // 2. Лог в wp_options (последние 200 заказов)
        $log = get_option( 'remarka_orders_log', [] );
        array_unshift( $log, [
            'id'           => $post_id ?: 0,
            'session_id'   => $session_id,
            'tariff'       => $tariff,
            'contact'      => $contact,
            'contact_type' => $contact_type,
            'total'        => $total,
            'slots'        => $slots,
            'date'         => current_time( 'mysql' ),
        ] );
        $log = array_slice( $log, 0, 200 );
        update_option( 'remarka_orders_log', $log );

        // 3. Email уведомление менеджеру (через wp_mail)
        $this->notify_manager( $post_title, $contact, $contact_type, $slots, $total, $tariff );

        wp_send_json_success( [ 'post_id' => $post_id ] );
    }

    private function notify_manager( string $title, string $contact, string $type, array $slots, int $total, string $tariff ): void {
        $to      = get_option( 'admin_email' );
        $subject = '📥 Новый заказ: ' . $title;

        $domain_names = [
            'general' => 'Общий', 'technical' => 'Технический', 'legal' => 'Юридический',
            'medical' => 'Медицинский', 'it' => 'IT', 'finance' => 'Финансовый',
            'marketing' => 'Маркетинговый',
        ];
        $urgency_names = [
            'standard' => 'Стандарт', 'urgent' => 'Срочно',
            'express' => 'Экспресс', 'flexible' => 'Гибко',
        ];

        $body  = "Новый заказ через чат Remarka\n";
        $body .= "================================\n";
        $body .= "Тариф:     " . $tariff . "\n";
        $body .= "Контакт:   " . $contact . " (" . $type . ")\n";
        $body .= "Языки:     " . ( $slots['langPair'] ?? '—' ) . "\n";
        $body .= "Тип:       " . ( $domain_names[ $slots['domain'] ?? '' ] ?? '—' ) . "\n";
        $body .= "Объём:     " . ( $slots['pages'] ?? '—' ) . " стр.\n";
        $body .= "Срочность: " . ( $urgency_names[ $slots['urgency'] ?? '' ] ?? '—' ) . "\n";
        $body .= "Итого:     " . number_format( $total, 0, '.', ' ' ) . " ₽\n";
        $body .= "================================\n";
        $body .= "Дата: " . date_i18n( 'd.m.Y H:i' ) . "\n";

        wp_mail( $to, $subject, $body );
    }

    /* ══════════════════════════════════════════════════════
       GEO: server-side lookup (ipapi.co)
    ══════════════════════════════════════════════════════ */
    public function get_geo(): void {
        check_ajax_referer( 'remarka_nonce', 'nonce' );

        $ip = sanitize_text_field( $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '' );
        // Берём первый IP из цепочки прокси
        $ip = trim( explode( ',', $ip )[0] );

        // Локальные IP → пропускаем
        if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) === false ) {
            wp_send_json_success( null );
        }

        // Кеш на 24 часа
        $cache_key = 'remarka_geo_' . md5( $ip );
        $cached    = get_transient( $cache_key );
        if ( $cached ) {
            wp_send_json_success( $cached );
        }

        $response = wp_remote_get( "https://ipapi.co/{$ip}/json/", [ 'timeout' => 5 ] );

        if ( is_wp_error( $response ) ) {
            wp_send_json_success( null );
        }

        $data = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( ! empty( $data['city'] ) ) {
            $geo = [
                'city'         => $data['city']         ?? '',
                'country'      => $data['country_name'] ?? '',
                'country_code' => $data['country_code'] ?? '',
            ];
            set_transient( $cache_key, $geo, DAY_IN_SECONDS );
            wp_send_json_success( $geo );
        }

        wp_send_json_success( null );
    }
}
