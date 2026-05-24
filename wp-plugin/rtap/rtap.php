<?php
/**
 * Plugin Name: RTAP — Remarka Translator Assessment Platform
 * Plugin URI:  https://moscowtrans.ru
 * Description: Система тестирования и сертификации переводчиков
 * Version:     1.0.0
 * Author:      Бюро переводов Ремарка
 * Text Domain: rtap
 */

defined('ABSPATH') || exit;

define('RTAP_VERSION', '1.0.0');
define('RTAP_DIR',     plugin_dir_path(__FILE__));
define('RTAP_URL',     plugin_dir_url(__FILE__));
define('RTAP_PREFIX',  'rtap_');

require_once RTAP_DIR . 'includes/class-rtap-db.php';
require_once RTAP_DIR . 'includes/class-rtap-api.php';
require_once RTAP_DIR . 'includes/class-rtap-candidate.php';
require_once RTAP_DIR . 'includes/class-rtap-certificate.php';
require_once RTAP_DIR . 'includes/class-rtap-stats.php';
require_once RTAP_DIR . 'includes/class-rtap-qow.php';
require_once RTAP_DIR . 'includes/class-rtap-importer.php';
require_once RTAP_DIR . 'includes/class-rtap-seo.php';

register_activation_hook(__FILE__,   ['RTAP_DB', 'install']);
register_deactivation_hook(__FILE__, ['RTAP_DB', 'deactivate']);

add_action('init',            'rtap_register_cpt');
add_action('rest_api_init',   ['RTAP_API', 'register_routes']);
add_action('admin_menu',      'rtap_admin_menu');
add_action('wp_enqueue_scripts', 'rtap_enqueue_frontend');
add_action('admin_enqueue_scripts', 'rtap_enqueue_admin');
add_action('rtap_sync_pending', ['RTAP_Candidate', 'sync_pending']);

add_shortcode('rtap_quiz',    'rtap_quiz_shortcode');
add_shortcode('rtap_qow',     'rtap_qow_shortcode');
add_shortcode('rtap_verify',  'rtap_verify_shortcode');

if (!wp_next_scheduled('rtap_sync_pending')) {
    wp_schedule_event(time(), 'hourly', 'rtap_sync_pending');
}

function rtap_register_cpt(): void {
    $topics = [
        'tekhnicheskiy' => ['Технический перевод', 'technical'],
        'yuridicheskiy' => ['Юридический перевод', 'legal'],
        'meditsinskiy'  => ['Медицинский перевод', 'medical'],
        'it'            => ['IT-перевод',           'it'],
    ];

    foreach ($topics as $slug => $data) {
        register_post_type("rtap_{$slug}", [
            'labels'      => ['name' => $data[0], 'singular_name' => $data[0]],
            'public'      => true,
            'has_archive' => false,
            'rewrite'     => ['slug' => "test-perevodchika/{$slug}"],
            'supports'    => ['title', 'editor', 'custom-fields'],
            'show_in_rest' => true,
        ]);
    }

    flush_rewrite_rules();
}

function rtap_admin_menu(): void {
    add_menu_page('RTAP', 'RTAP Тесты', 'manage_options', 'rtap', 'rtap_page_dashboard',
        'dashicons-awards', 30);
    add_submenu_page('rtap', 'Дашборд',      'Дашборд',      'manage_options', 'rtap',           'rtap_page_dashboard');
    add_submenu_page('rtap', 'Вопросы',      'Вопросы',      'manage_options', 'rtap-questions', 'rtap_page_questions');
    add_submenu_page('rtap', 'Кандидаты',    'Кандидаты',    'manage_options', 'rtap-candidates','rtap_page_candidates');
    add_submenu_page('rtap', 'Сертификаты',  'Сертификаты',  'manage_options', 'rtap-certs',     'rtap_page_certificates');
    add_submenu_page('rtap', 'Вопрос недели','Вопрос недели','manage_options', 'rtap-qow',       'rtap_page_qow');
    add_submenu_page('rtap', 'Настройки',    'Настройки',    'manage_options', 'rtap-settings',  'rtap_page_settings');
}

function rtap_page_dashboard():    void { require RTAP_DIR . 'admin/page-dashboard.php';    }
function rtap_page_questions():    void { require RTAP_DIR . 'admin/page-questions.php';    }
function rtap_page_candidates():   void { require RTAP_DIR . 'admin/page-candidates.php';   }
function rtap_page_certificates(): void { require RTAP_DIR . 'admin/page-certificates.php'; }
function rtap_page_qow():          void { require RTAP_DIR . 'admin/page-qow.php';          }
function rtap_page_settings():     void { require RTAP_DIR . 'admin/page-settings.php';     }

function rtap_enqueue_frontend(): void {
    if (!is_page() && !is_singular()) return;

    global $post;
    $content = $post ? $post->post_content . ($post->post_status ?? '') : '';
    $has_rtap = has_shortcode($content, 'rtap_quiz')
             || has_shortcode($content, 'rtap_qow')
             || has_shortcode($content, 'rtap_verify');

    if (!$has_rtap) return;

    $dist     = RTAP_DIR . 'frontend/dist/';
    $dist_url = RTAP_URL . 'frontend/dist/';

    // Load all assets via Vite manifest
    $manifest_path = $dist . '.vite/manifest.json';
    if (file_exists($manifest_path)) {
        $manifest = json_decode(file_get_contents($manifest_path), true);
        foreach ($manifest as $entry) {
            $ver = filemtime($dist . ($entry['file'] ?? '')) ?: RTAP_VERSION;
            if (!empty($entry['isEntry'])) {
                wp_enqueue_script('rtap-app', $dist_url . $entry['file'], [], $ver, true);
            }
            foreach ($entry['css'] ?? [] as $css) {
                wp_enqueue_style('rtap-' . md5($css), $dist_url . $css, [], $ver);
            }
            foreach ($entry['imports'] ?? [] as $chunk_key) {
                if (!empty($manifest[$chunk_key]['file'])) {
                    $cv = filemtime($dist . $manifest[$chunk_key]['file']) ?: RTAP_VERSION;
                    wp_enqueue_script('rtap-chunk-' . md5($chunk_key), $dist_url . $manifest[$chunk_key]['file'], ['rtap-app'], $cv, true);
                }
            }
        }
    } else {
        // Fallback: load known files directly
        $ver = filemtime($dist . 'assets/index.js') ?: RTAP_VERSION;
        wp_enqueue_style('rtap-app',  $dist_url . 'assets/main.css',  [], $ver);
        wp_enqueue_script('rtap-app', $dist_url . 'assets/index.js',  [], $ver, true);
        foreach (['index.es.js','html2canvas.esm.js','jspdf.es.min.js','purify.es.js'] as $chunk) {
            if (file_exists($dist . "assets/$chunk")) {
                wp_enqueue_script('rtap-' . $chunk, $dist_url . "assets/$chunk", ['rtap-app'], $ver, true);
            }
        }
    }

    wp_localize_script('rtap-app', 'rtapConfig', [
        'apiBase'    => rest_url('rtap/v1'),
        'nonce'      => wp_create_nonce('wp_rest'),
        'siteUrl'    => get_site_url(),
        'version'    => RTAP_VERSION,
        'certUrlBase'=> get_site_url() . '/verify/',
    ]);
}

function rtap_enqueue_admin(string $hook): void {
    if (strpos($hook, 'rtap') === false) return;
    wp_enqueue_style('rtap-admin',  RTAP_URL . 'assets/admin.css', [], RTAP_VERSION);
    wp_enqueue_script('rtap-admin', RTAP_URL . 'assets/admin.js',  [], RTAP_VERSION, true);
}

function rtap_quiz_shortcode(array $atts): string {
    $atts = shortcode_atts(['topic' => '', 'lang' => 'en'], $atts);
    $topic = sanitize_key($atts['topic']);
    $lang  = sanitize_key($atts['lang']);
    return sprintf(
        '<div id="rtap-root" data-topic="%s" data-lang="%s"></div>',
        esc_attr($topic), esc_attr($lang)
    );
}

function rtap_qow_shortcode(): string {
    return '<div id="rtap-qow-root"></div>';
}

function rtap_verify_shortcode(array $atts): string {
    $atts  = shortcode_atts(['id' => ''], $atts);
    $cert_id = sanitize_text_field($atts['id']);
    if (!$cert_id && isset($_GET['cert_id'])) {
        $cert_id = sanitize_text_field($_GET['cert_id']);
    }
    return sprintf('<div id="rtap-verify-root" data-cert-id="%s"></div>', esc_attr($cert_id));
}
