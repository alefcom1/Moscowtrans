<?php
/**
 * Plugin Name:       Remarka Chat — AI Консультант
 * Plugin URI:        https://remarka-bureau.ru
 * Description:       Полноэкранный AI-консультант бюро переводов с калькулятором стоимости, распознаванием файлов и голосовым вводом.
 * Version:           2.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            Remarka Bureau
 * Author URI:        https://remarka-bureau.ru
 * License:           GPL v2 or later
 * Text Domain:       remarka-chat
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) || exit;

/* ═══════════════════════════════════════════════════════════
   КОНСТАНТЫ
═══════════════════════════════════════════════════════════ */
define( 'REMARKA_VERSION',    '2.0.0' );
define( 'REMARKA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'REMARKA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'REMARKA_PLUGIN_FILE', __FILE__ );

/* ═══════════════════════════════════════════════════════════
   АВТОЗАГРУЗКА КЛАССОВ
═══════════════════════════════════════════════════════════ */
spl_autoload_register( function ( string $class ) {
    $map = [
        'Remarka_Admin'     => 'includes/class-admin.php',
        'Remarka_Ajax'      => 'includes/class-ajax.php',
        'Remarka_Shortcode' => 'includes/class-shortcode.php',
        'Remarka_Orders'    => 'includes/class-orders-cpt.php',
        'Remarka_Translator'    => 'includes/class-translator.php',
        'Remarka_Modules_Ajax'  => 'includes/class-modules-ajax.php',
    ];
    if ( isset( $map[ $class ] ) ) {
        require_once REMARKA_PLUGIN_DIR . $map[ $class ];
    }
} );

/* ═══════════════════════════════════════════════════════════
   ХЕЛПЕР: получить настройку плагина
═══════════════════════════════════════════════════════════ */
function remarka_option( string $key, $default = '' ) {
    $opts = get_option( 'remarka_settings', [] );
    return $opts[ $key ] ?? $default;
}

/* ═══════════════════════════════════════════════════════════
   ХЕЛПЕР: получить все тарифы
═══════════════════════════════════════════════════════════ */
function remarka_get_tariffs(): array {
    return [
        'mtpe'    => [
            'name'  => remarka_option( 'tariff_mtpe_name',    'MTPE (Вычитка AI)' ),
            'price' => (int) remarka_option( 'tariff_mtpe_price',    350 ),
            'desc'  => remarka_option( 'tariff_mtpe_desc',    'Постредактура машинного перевода специалистом' ),
        ],
        'human'   => [
            'name'  => remarka_option( 'tariff_human_name',   'Профессиональный' ),
            'price' => (int) remarka_option( 'tariff_human_price',   750 ),
            'desc'  => remarka_option( 'tariff_human_desc',   'Отраслевой переводчик-специалист' ),
        ],
        'premium' => [
            'name'  => remarka_option( 'tariff_premium_name', 'Premium Expert' ),
            'price' => (int) remarka_option( 'tariff_premium_price', 1350 ),
            'desc'  => remarka_option( 'tariff_premium_desc', 'Переводчик + редактор-носитель языка' ),
        ],
    ];
}

/* ═══════════════════════════════════════════════════════════
   ИНИЦИАЛИЗАЦИЯ
═══════════════════════════════════════════════════════════ */
function remarka_init(): void {
    // Admin
    if ( is_admin() ) {
        new Remarka_Admin();
    }

    // AJAX (авторизован и нет)
    new Remarka_Ajax();

    // Шорткоды
    new Remarka_Shortcode();

    // Custom Post Type заказов
    new Remarka_Orders();

    // Модуль анкет переводчиков
    new Remarka_Translator();

    // AJAX-обработчики всех модулей
    new Remarka_Modules_Ajax();
}
add_action( 'plugins_loaded', 'remarka_init' );

/* ═══════════════════════════════════════════════════════════
   АКТИВАЦИЯ — создать таблицу сессий
═══════════════════════════════════════════════════════════ */
register_activation_hook( __FILE__, 'remarka_activate' );
function remarka_activate(): void {
    global $wpdb;
    $table   = $wpdb->prefix . 'remarka_sessions';
    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS {$table} (
        id          BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        session_id  VARCHAR(64)  NOT NULL,
        user_id     BIGINT(20)   DEFAULT 0,
        geo_city    VARCHAR(100) DEFAULT '',
        geo_country VARCHAR(100) DEFAULT '',
        page_context VARCHAR(50) DEFAULT 'general',
        slots       LONGTEXT     DEFAULT '',
        intent      VARCHAR(100) DEFAULT '',
        messages    LONGTEXT     DEFAULT '',
        created_at  DATETIME     DEFAULT CURRENT_TIMESTAMP,
        updated_at  DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY session_id (session_id)
    ) {$charset};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );

    // Дефолтные настройки
    $defaults = [
        'gpt_proxy_url'       => '/api/gpt.php',
        'emailjs_public_key'  => '',
        'emailjs_service_id'  => 'remarka_service',
        'emailjs_template_order'    => 'order_template',
        'emailjs_template_callback' => 'callback_template',
        'agent_name'          => 'Ольга',
        'active_languages'    => ['ru', 'en', 'it'],
        'tariff_mtpe_price'   => 350,
        'tariff_human_price'  => 750,
        'tariff_premium_price'=> 1350,
        'tariff_mtpe_name'    => 'MTPE (Вычитка AI)',
        'tariff_human_name'   => 'Профессиональный',
        'tariff_premium_name' => 'Premium Expert',
        'tariff_mtpe_desc'    => 'Постредактура машинного перевода специалистом',
        'tariff_human_desc'   => 'Отраслевой переводчик-специалист',
        'tariff_premium_desc' => 'Переводчик + редактор-носитель языка',
        'show_on'             => 'all',
        'show_on_pages'       => [],
        'hide_on_pages'       => [],
        'proactive_delay'     => 8000,
        'chat_position'       => 'fullscreen',
    ];
    if ( ! get_option( 'remarka_settings' ) ) {
        update_option( 'remarka_settings', $defaults );
    }
}

/* ═══════════════════════════════════════════════════════════
   ДЕАКТИВАЦИЯ
═══════════════════════════════════════════════════════════ */
register_deactivation_hook( __FILE__, 'remarka_deactivate' );
function remarka_deactivate(): void {
    flush_rewrite_rules();
}

/* ═══════════════════════════════════════════════════════════
   УДАЛЕНИЕ
═══════════════════════════════════════════════════════════ */
register_uninstall_hook( __FILE__, 'remarka_uninstall' );
function remarka_uninstall(): void {
    global $wpdb;
    $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}remarka_sessions" );
    delete_option( 'remarka_settings' );
}
