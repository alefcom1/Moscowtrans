<?php
defined( 'ABSPATH' ) || exit;

class Remarka_Shortcode {

    public function __construct() {
        add_shortcode( 'remarka_chat',        [ $this, 'render_fullscreen' ] );
        add_shortcode( 'remarka_chat_widget', [ $this, 'render_widget' ] );

        // Авто-вставка на нужных страницах (без шорткода)
        add_action( 'wp_footer', [ $this, 'maybe_auto_inject' ] );

        // Регистрация ассетов
        add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );
    }

    /* ══════════════════════════════════════════════════════
       REGISTER ASSETS (только регистрация, не подключение)
    ══════════════════════════════════════════════════════ */
    public function register_assets(): void {
        $v = REMARKA_VERSION;
        $u = REMARKA_PLUGIN_URL;

        // CSS
        wp_register_style(
            'remarka-chat',
            $u . 'assets/css/chat.css',
            [],
            $v
        );

        // JS модули (в правильном порядке зависимостей)
        wp_register_script( 'remarka-context',  $u . 'assets/js/context.js',  [],            $v, true );
        wp_register_script( 'remarka-pricing',  $u . 'assets/js/pricing.js',  [],            $v, true );
        wp_register_script( 'remarka-ai',       $u . 'assets/js/ai.js',       [ 'remarka-context', 'remarka-pricing' ], $v, true );
        wp_register_script( 'remarka-chat',     $u . 'assets/js/chat.js',     [ 'remarka-ai' ],     $v, true );

        // EmailJS CDN
        wp_register_script(
            'emailjs',
            'https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js',
            [],
            '4',
            true
        );
    }

    /* ══════════════════════════════════════════════════════
       ENQUEUE — подключить все ассеты + передать конфиг в JS
    ══════════════════════════════════════════════════════ */
    private function enqueue_assets( string $context = 'general' ): void {
        // Уже подключали на этой странице?
        if ( did_action( 'remarka_assets_enqueued' ) ) return;
        do_action( 'remarka_assets_enqueued' );

        wp_enqueue_style( 'remarka-chat' );

        if ( remarka_option( 'emailjs_public_key' ) ) {
            wp_enqueue_script( 'emailjs' );
        }

        wp_enqueue_script( 'remarka-context' );
        wp_enqueue_script( 'remarka-pricing' );
        wp_enqueue_script( 'remarka-ai' );
        wp_enqueue_script( 'remarka-chat' );
        wp_enqueue_script( 'remarka-wp-adapter' );
        wp_enqueue_script( 'remarka-translator-survey' );
        wp_enqueue_script( 'remarka-document-checker' );
        wp_enqueue_script( 'remarka-quality-checker' );
        wp_enqueue_script( 'remarka-complexity-meter' );
        wp_enqueue_script( 'remarka-flows-bundle' );
        wp_enqueue_script( 'remarka-feedback' );
        wp_enqueue_script( 'remarka-modules-bundle-2' );
        wp_enqueue_script( 'remarka-live-quote' );
        wp_enqueue_script( 'remarka-proactive-discount' );
        wp_enqueue_script( 'remarka-deadline-calendar' );
        wp_enqueue_script( 'remarka-modules-bundle-3' );
        wp_enqueue_script( 'remarka-orchestrator' );

        // ── Передаём конфиг из WP в JavaScript ──
        $tariffs = remarka_get_tariffs();

        wp_localize_script( 'remarka-chat', 'RemarkaConfig', [
            // WordPress AJAX
            'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
            'nonce'     => wp_create_nonce( 'remarka_nonce' ),

            // Контекст текущей страницы
            'pageContext' => $context,

            // Настройки агента
            'agentName'   => remarka_option( 'agent_name', 'Ольга' ),
            'languages'   => remarka_option( 'active_languages', [ 'ru', 'en', 'it' ] ),

            // Тарифы из БД/wp_options
            'tariffs'     => $tariffs,

            // Proactive trigger задержка
            'proactiveDelay' => (int) remarka_option( 'proactive_delay', 8000 ),

            // EmailJS
            'emailjs' => [
                'publicKey'        => remarka_option( 'emailjs_public_key', '' ),
                'serviceId'        => remarka_option( 'emailjs_service_id', 'remarka_service' ),
                'templateOrder'    => remarka_option( 'emailjs_template_order',    'order_template' ),
                'templateCallback' => remarka_option( 'emailjs_template_callback', 'callback_template' ),
            ],

            // Сайт
            'siteUrl'     => home_url(),
            'pluginUrl'   => REMARKA_PLUGIN_URL,
            'isLoggedIn'  => is_user_logged_in(),
            'userId'      => get_current_user_id(),
        ] );

        // Инициализация EmailJS если ключ задан
        $ejs_key = remarka_option( 'emailjs_public_key' );
        if ( $ejs_key ) {
            wp_add_inline_script(
                'emailjs',
                'window.addEventListener("load", function() { if(typeof emailjs !== "undefined") emailjs.init("' . esc_js( $ejs_key ) . '"); });'
            );
        }

        // Инициализация ChatEngine после загрузки DOM
        wp_add_inline_script(
            'remarka-chat',
            'document.addEventListener("DOMContentLoaded", function() {
                if (typeof ChatEngine !== "undefined") {
                    ChatEngine.init();
                }
            });'
        );
    }

    /* ══════════════════════════════════════════════════════
       ОПРЕДЕЛИТЬ КОНТЕКСТ: shortcode attr > data-attr > PageContext
    ══════════════════════════════════════════════════════ */
    private function resolve_context( array $atts ): string {
        // 1. Явный атрибут шорткода [remarka_chat context="legal"]
        if ( ! empty( $atts['context'] ) ) {
            $valid = [ 'general', 'technical', 'legal', 'medical', 'it', 'website', 'finance' ];
            $ctx   = sanitize_text_field( $atts['context'] );
            if ( in_array( $ctx, $valid, true ) ) return $ctx;
        }

        // 2. Мета-поле страницы _remarka_context
        if ( is_singular() ) {
            $meta = get_post_meta( get_the_ID(), '_remarka_context', true );
            if ( $meta ) return sanitize_text_field( $meta );
        }

        // 3. Автодетект по URL — делается на JS-стороне через PageContext.detect()
        //    Здесь возвращаем 'auto' → JS подберёт сам
        return 'auto';
    }

    /* ══════════════════════════════════════════════════════
       SHORTCODE: [remarka_chat] — полноэкранный
    ══════════════════════════════════════════════════════ */
    public function render_fullscreen( $atts ): string {
        $atts = shortcode_atts( [
            'context' => '',
            'height'  => '100vh',
            'class'   => '',
        ], $atts, 'remarka_chat' );

        $context = $this->resolve_context( $atts );
        $this->enqueue_assets( $context );

        ob_start();
        include REMARKA_PLUGIN_DIR . 'templates/chat-widget.php';
        return ob_get_clean();
    }

    /* ══════════════════════════════════════════════════════
       SHORTCODE: [remarka_chat_widget] — кнопка-виджет
    ══════════════════════════════════════════════════════ */
    public function render_widget( $atts ): string {
        $atts = shortcode_atts( [
            'context'  => '',
            'position' => 'bottom-right',  // bottom-right | bottom-left
        ], $atts, 'remarka_chat_widget' );

        $context = $this->resolve_context( $atts );
        $this->enqueue_assets( $context );

        ob_start();
        include REMARKA_PLUGIN_DIR . 'templates/chat-widget.php';
        // Добавляем launcher кнопку
        ?>
        <button
            class="remarka-launcher"
            onclick="document.getElementById('remarka-app').classList.toggle('remarka-open')"
            aria-label="Открыть консультант"
            data-position="<?= esc_attr( $atts['position'] ) ?>"
        >
            <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
            <span class="remarka-launcher-badge">1</span>
        </button>
        <?php
        return ob_get_clean();
    }

    /* ══════════════════════════════════════════════════════
       AUTO INJECT: вставка без шорткода по настройкам
    ══════════════════════════════════════════════════════ */
    public function maybe_auto_inject(): void {
        // Если шорткод уже был использован — не дублируем
        if ( did_action( 'remarka_assets_enqueued' ) ) return;

        $show_on   = remarka_option( 'show_on', 'all' );
        $current   = get_the_ID();

        if ( $show_on === 'all' ) {
            $this->auto_render();
            return;
        }

        if ( $show_on === 'selected' ) {
            $pages = (array) remarka_option( 'show_on_pages', [] );
            if ( in_array( $current, $pages, false ) ) {
                $this->auto_render();
            }
            return;
        }

        if ( $show_on === 'except' ) {
            $pages = (array) remarka_option( 'hide_on_pages', [] );
            if ( ! in_array( $current, $pages, false ) ) {
                $this->auto_render();
            }
        }
    }

    private function auto_render(): void {
        $this->enqueue_assets( 'auto' );
        include REMARKA_PLUGIN_DIR . 'templates/chat-widget.php';
    }
}
