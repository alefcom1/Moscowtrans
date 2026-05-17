<?php
defined( 'ABSPATH' ) || exit;

class Remarka_Admin {

    public function __construct() {
        add_action( 'admin_menu',    [ $this, 'register_menu' ] );
        add_action( 'admin_init',    [ $this, 'register_settings' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
        // AJAX для сброса сессий
        add_action( 'wp_ajax_remarka_clear_sessions', [ $this, 'ajax_clear_sessions' ] );
    }

    /* ── МЕНЮ ─────────────────────────────────────────────── */
    public function register_menu(): void {
        add_menu_page(
            'Remarka Chat',
            'Remarka Chat',
            'manage_options',
            'remarka-chat',
            [ $this, 'page_dashboard' ],
            'dashicons-format-chat',
            30
        );
        add_submenu_page( 'remarka-chat', 'Настройки',    'Настройки',    'manage_options', 'remarka-chat',          [ $this, 'page_dashboard' ] );
        add_submenu_page( 'remarka-chat', 'Тарифы',       'Тарифы',       'manage_options', 'remarka-chat-tariffs',  [ $this, 'page_tariffs' ] );
        add_submenu_page( 'remarka-chat', 'Заказы (CPT)', 'Заказы',       'manage_options', 'edit.php?post_type=remarka_order', null );
        add_submenu_page( 'remarka-chat', 'Сессии',       'Сессии',       'manage_options', 'remarka-chat-sessions', [ $this, 'page_sessions' ] );
        add_submenu_page( 'remarka-chat', 'Помощь',       'Помощь / Docs','manage_options', 'remarka-chat-help',     [ $this, 'page_help' ] );
    }

    /* ── ASSETS ───────────────────────────────────────────── */
    public function enqueue_admin_assets( string $hook ): void {
        if ( strpos( $hook, 'remarka' ) === false ) return;
        wp_enqueue_style(
            'remarka-admin',
            REMARKA_PLUGIN_URL . 'assets/css/admin.css',
            [],
            REMARKA_VERSION
        );
    }

    /* ── REGISTER SETTINGS ────────────────────────────────── */
    public function register_settings(): void {
        register_setting( 'remarka_settings_group', 'remarka_settings', [
            'sanitize_callback' => [ $this, 'sanitize_settings' ],
        ] );
    }

    public function sanitize_settings( array $input ): array {
        $clean = [];

        $clean['gpt_proxy_url']      = sanitize_text_field( $input['gpt_proxy_url'] ?? '/api/gpt.php' );
        $clean['agent_name']         = sanitize_text_field( $input['agent_name'] ?? 'Ольга' );
        $clean['emailjs_public_key'] = sanitize_text_field( $input['emailjs_public_key'] ?? '' );
        $clean['emailjs_service_id'] = sanitize_text_field( $input['emailjs_service_id'] ?? 'remarka_service' );
        $clean['emailjs_template_order']    = sanitize_text_field( $input['emailjs_template_order'] ?? 'order_template' );
        $clean['emailjs_template_callback'] = sanitize_text_field( $input['emailjs_template_callback'] ?? 'callback_template' );
        $clean['show_on']            = sanitize_text_field( $input['show_on'] ?? 'all' );
        $clean['proactive_delay']    = absint( $input['proactive_delay'] ?? 8000 );
        $clean['chat_position']      = sanitize_text_field( $input['chat_position'] ?? 'fullscreen' );

        // Массивы
        $clean['active_languages'] = array_map( 'sanitize_text_field',
            (array) ( $input['active_languages'] ?? ['ru', 'en', 'it'] )
        );
        $clean['show_on_pages'] = array_map( 'absint',
            (array) ( $input['show_on_pages'] ?? [] )
        );
        $clean['hide_on_pages'] = array_map( 'absint',
            (array) ( $input['hide_on_pages'] ?? [] )
        );

        // Тарифы
        foreach ( ['mtpe', 'human', 'premium'] as $t ) {
            $clean["tariff_{$t}_name"]  = sanitize_text_field( $input["tariff_{$t}_name"] ?? '' );
            $clean["tariff_{$t}_price"] = absint( $input["tariff_{$t}_price"] ?? 0 );
            $clean["tariff_{$t}_desc"]  = sanitize_text_field( $input["tariff_{$t}_desc"] ?? '' );
        }

        return $clean;
    }

    /* ══════════════════════════════════════════════════════
       СТРАНИЦА: Настройки (Dashboard)
    ══════════════════════════════════════════════════════ */
    public function page_dashboard(): void {
        $opts = get_option( 'remarka_settings', [] );
        ?>
        <div class="wrap remarka-admin">
            <h1>⚙️ Remarka Chat — Настройки</h1>

            <?php settings_errors(); ?>

            <form method="post" action="options.php">
                <?php settings_fields( 'remarka_settings_group' ); ?>

                <div class="remarka-admin-grid">

                    <!-- Блок: API -->
                    <div class="remarka-card">
                        <h2>🔗 API & Интеграции</h2>

                        <table class="form-table">
                            <tr>
                                <th><label for="gpt_proxy_url">URL прокси GPT</label></th>
                                <td>
                                    <input type="text" id="gpt_proxy_url" name="remarka_settings[gpt_proxy_url]"
                                        value="<?= esc_attr( $opts['gpt_proxy_url'] ?? '/api/gpt.php' ) ?>"
                                        class="regular-text">
                                    <p class="description">Путь к PHP-прокси. Обычно <code>/api/gpt.php</code></p>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="emailjs_public_key">EmailJS Public Key</label></th>
                                <td>
                                    <input type="text" id="emailjs_public_key" name="remarka_settings[emailjs_public_key]"
                                        value="<?= esc_attr( $opts['emailjs_public_key'] ?? '' ) ?>"
                                        class="regular-text" placeholder="user_xxxxxxxxxxxx">
                                    <p class="description">Получить на <a href="https://emailjs.com" target="_blank">emailjs.com</a></p>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="emailjs_service_id">EmailJS Service ID</label></th>
                                <td>
                                    <input type="text" id="emailjs_service_id" name="remarka_settings[emailjs_service_id]"
                                        value="<?= esc_attr( $opts['emailjs_service_id'] ?? 'remarka_service' ) ?>"
                                        class="regular-text">
                                </td>
                            </tr>
                            <tr>
                                <th><label>EmailJS Templates</label></th>
                                <td>
                                    <input type="text" name="remarka_settings[emailjs_template_order]"
                                        value="<?= esc_attr( $opts['emailjs_template_order'] ?? 'order_template' ) ?>"
                                        class="regular-text" placeholder="order_template">
                                    <p class="description">ID шаблона заказа</p>
                                    <input type="text" name="remarka_settings[emailjs_template_callback]"
                                        value="<?= esc_attr( $opts['emailjs_template_callback'] ?? 'callback_template' ) ?>"
                                        class="regular-text" placeholder="callback_template" style="margin-top:6px">
                                    <p class="description">ID шаблона обратного звонка</p>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Блок: Агент -->
                    <div class="remarka-card">
                        <h2>👩‍💼 Настройки агента</h2>
                        <table class="form-table">
                            <tr>
                                <th><label for="agent_name">Имя менеджера</label></th>
                                <td>
                                    <input type="text" id="agent_name" name="remarka_settings[agent_name]"
                                        value="<?= esc_attr( $opts['agent_name'] ?? 'Ольга' ) ?>"
                                        class="regular-text">
                                </td>
                            </tr>
                            <tr>
                                <th>Языки интерфейса</th>
                                <td>
                                    <?php
                                    $langs    = $opts['active_languages'] ?? ['ru', 'en', 'it'];
                                    $langList = [ 'ru' => '🇷🇺 Русский', 'en' => '🇬🇧 English', 'it' => '🇮🇹 Italiano' ];
                                    foreach ( $langList as $code => $label ) : ?>
                                        <label style="margin-right:16px">
                                            <input type="checkbox" name="remarka_settings[active_languages][]"
                                                value="<?= $code ?>"
                                                <?= in_array( $code, $langs, true ) ? 'checked' : '' ?>>
                                            <?= $label ?>
                                        </label>
                                    <?php endforeach; ?>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="proactive_delay">Proactive trigger (мс)</label></th>
                                <td>
                                    <input type="number" id="proactive_delay" name="remarka_settings[proactive_delay]"
                                        value="<?= absint( $opts['proactive_delay'] ?? 8000 ) ?>"
                                        min="3000" max="60000" step="1000" class="small-text">
                                    <p class="description">Через сколько мс предложить помощь (если пользователь молчит)</p>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Блок: Отображение -->
                    <div class="remarka-card">
                        <h2>📍 Где показывать чат</h2>
                        <table class="form-table">
                            <tr>
                                <th>Показывать на</th>
                                <td>
                                    <?php
                                    $showOn = $opts['show_on'] ?? 'all';
                                    $modes  = [
                                        'all'       => 'Всех страницах',
                                        'selected'  => 'Только выбранных страницах',
                                        'except'    => 'Всех, кроме выбранных',
                                    ];
                                    foreach ( $modes as $val => $label ) : ?>
                                        <label style="display:block;margin-bottom:6px">
                                            <input type="radio" name="remarka_settings[show_on]"
                                                value="<?= $val ?>"
                                                <?= $showOn === $val ? 'checked' : '' ?>>
                                            <?= $label ?>
                                        </label>
                                    <?php endforeach; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Конкретные страницы</th>
                                <td>
                                    <?php
                                    $pages = get_pages();
                                    $showPages = $opts['show_on_pages'] ?? [];
                                    $hidePages = $opts['hide_on_pages'] ?? [];
                                    ?>
                                    <div style="max-height:180px;overflow-y:auto;border:1px solid #ddd;padding:8px;border-radius:4px">
                                        <?php foreach ( $pages as $page ) : ?>
                                            <label style="display:block;margin-bottom:4px">
                                                <input type="checkbox"
                                                    name="remarka_settings[show_on_pages][]"
                                                    value="<?= $page->ID ?>"
                                                    <?= in_array( $page->ID, $showPages ) ? 'checked' : '' ?>>
                                                <?= esc_html( $page->post_title ) ?>
                                                <span style="color:#999;font-size:11px">(ID: <?= $page->ID ?>)</span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                    <p class="description">Выбранные страницы используются для режимов выше</p>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Блок: Шорткод -->
                    <div class="remarka-card">
                        <h2>📋 Использование шорткода</h2>
                        <p>Вставьте в нужное место страницы:</p>
                        <code style="display:block;padding:10px;background:#f0f0f0;border-radius:4px;margin-bottom:10px">
                            [remarka_chat]
                        </code>
                        <p>С явным контекстом страницы:</p>
                        <code style="display:block;padding:10px;background:#f0f0f0;border-radius:4px;margin-bottom:10px">
                            [remarka_chat context="technical"]
                        </code>
                        <p>Доступные контексты:</p>
                        <code>general · technical · legal · medical · it · website · finance</code>
                        <hr>
                        <p>Только виджет-кнопка (чат открывается по клику):</p>
                        <code style="display:block;padding:10px;background:#f0f0f0;border-radius:4px">
                            [remarka_chat_widget]
                        </code>
                    </div>

                </div><!-- /.remarka-admin-grid -->

                <?php submit_button( 'Сохранить настройки', 'primary large' ); ?>
            </form>
        </div>
        <?php
    }

    /* ══════════════════════════════════════════════════════
       СТРАНИЦА: Тарифы
    ══════════════════════════════════════════════════════ */
    public function page_tariffs(): void {
        $opts    = get_option( 'remarka_settings', [] );
        $tariffs = remarka_get_tariffs();
        ?>
        <div class="wrap remarka-admin">
            <h1>💰 Тарифы переводов</h1>
            <p>Изменения сразу отражаются в калькуляторе и ответах AI-консультанта.</p>

            <?php settings_errors(); ?>

            <form method="post" action="options.php">
                <?php settings_fields( 'remarka_settings_group' ); ?>

                <div class="remarka-tariffs-grid">
                    <?php
                    $tariffDefs = [
                        'mtpe'    => [ 'icon' => '🤖', 'label' => 'MTPE (Вычитка AI)' ],
                        'human'   => [ 'icon' => '👨‍💼', 'label' => 'Профессиональный' ],
                        'premium' => [ 'icon' => '⭐', 'label' => 'Premium Expert' ],
                    ];
                    foreach ( $tariffDefs as $key => $def ) :
                        $name  = $opts["tariff_{$key}_name"]  ?? $def['label'];
                        $price = $opts["tariff_{$key}_price"] ?? 350;
                        $desc  = $opts["tariff_{$key}_desc"]  ?? '';
                    ?>
                    <div class="remarka-card remarka-tariff-card">
                        <h2><?= $def['icon'] ?> <?= $def['label'] ?></h2>
                        <table class="form-table">
                            <tr>
                                <th><label>Название тарифа</label></th>
                                <td>
                                    <input type="text"
                                        name="remarka_settings[tariff_<?= $key ?>_name]"
                                        value="<?= esc_attr( $name ) ?>"
                                        class="regular-text">
                                </td>
                            </tr>
                            <tr>
                                <th><label>Базовая цена (₽/стр.)</label></th>
                                <td>
                                    <input type="number"
                                        name="remarka_settings[tariff_<?= $key ?>_price]"
                                        value="<?= absint( $price ) ?>"
                                        min="0" step="50" class="small-text">
                                    <span> ₽ за стандартную страницу (1800 зн.)</span>
                                </td>
                            </tr>
                            <tr>
                                <th><label>Описание</label></th>
                                <td>
                                    <input type="text"
                                        name="remarka_settings[tariff_<?= $key ?>_desc]"
                                        value="<?= esc_attr( $desc ) ?>"
                                        class="large-text">
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php submit_button( 'Сохранить тарифы', 'primary large' ); ?>
            </form>
        </div>
        <?php
    }

    /* ══════════════════════════════════════════════════════
       СТРАНИЦА: Сессии
    ══════════════════════════════════════════════════════ */
    public function page_sessions(): void {
        global $wpdb;
        $table    = $wpdb->prefix . 'remarka_sessions';
        $sessions = $wpdb->get_results(
            "SELECT * FROM {$table} ORDER BY updated_at DESC LIMIT 100"
        );
        ?>
        <div class="wrap remarka-admin">
            <h1>📊 Сессии пользователей</h1>
            <p>Последние 100 сессий. <button class="button" id="remarka-clear-sessions">Очистить все сессии</button></p>

            <table class="widefat fixed striped remarka-sessions-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Session</th>
                        <th>Гео</th>
                        <th>Контекст</th>
                        <th>Intent</th>
                        <th>Слоты</th>
                        <th>Сообщений</th>
                        <th>Обновлено</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( empty( $sessions ) ) : ?>
                        <tr><td colspan="8" style="text-align:center;padding:20px">Сессий пока нет</td></tr>
                    <?php else : foreach ( $sessions as $s ) :
                        $slots    = json_decode( $s->slots,    true ) ?: [];
                        $messages = json_decode( $s->messages, true ) ?: [];
                    ?>
                    <tr>
                        <td><?= $s->id ?></td>
                        <td><code style="font-size:10px"><?= esc_html( substr( $s->session_id, 0, 12 ) ) ?>…</code></td>
                        <td><?= esc_html( $s->geo_city ) ?> <?= esc_html( $s->geo_country ) ?></td>
                        <td><span class="remarka-badge remarka-badge--<?= esc_attr( $s->page_context ) ?>"><?= esc_html( $s->page_context ) ?></span></td>
                        <td><?= esc_html( $s->intent ?: '—' ) ?></td>
                        <td>
                            <?php foreach ( $slots as $k => $v ) : ?>
                                <span class="remarka-slot"><?= esc_html($k) ?>: <?= esc_html($v) ?></span>
                            <?php endforeach; ?>
                        </td>
                        <td><?= count( $messages ) ?></td>
                        <td><?= esc_html( date_i18n( 'd.m.Y H:i', strtotime( $s->updated_at ) ) ) ?></td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <script>
        document.getElementById('remarka-clear-sessions')?.addEventListener('click', function() {
            if (!confirm('Удалить все сессии?')) return;
            fetch(ajaxurl, {
                method: 'POST',
                headers: {'Content-Type':'application/x-www-form-urlencoded'},
                body: 'action=remarka_clear_sessions&_wpnonce=<?= wp_create_nonce('remarka_clear') ?>'
            }).then(r => r.json()).then(d => {
                if (d.success) location.reload();
            });
        });
        </script>
        <?php
    }

    public function ajax_clear_sessions(): void {
        check_ajax_referer( 'remarka_clear' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die();
        global $wpdb;
        $wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}remarka_sessions" );
        wp_send_json_success();
    }

    /* ══════════════════════════════════════════════════════
       СТРАНИЦА: Помощь
    ══════════════════════════════════════════════════════ */
    public function page_help(): void {
        ?>
        <div class="wrap remarka-admin">
            <h1>📖 Помощь и документация</h1>
            <div class="remarka-admin-grid">
                <div class="remarka-card">
                    <h2>Быстрый старт</h2>
                    <ol>
                        <li>Установите URL прокси GPT в настройках</li>
                        <li>Задайте ключ EmailJS (необязательно)</li>
                        <li>Настройте тарифы под ваши цены</li>
                        <li>Добавьте <code>[remarka_chat]</code> на нужные страницы</li>
                    </ol>
                </div>
                <div class="remarka-card">
                    <h2>Контексты страниц</h2>
                    <p>Чат автоматически определяет тип страницы по URL и адаптирует приветствие. Для ручного управления:</p>
                    <code>[remarka_chat context="technical"]</code><br><br>
                    <strong>Доступные контексты:</strong>
                    <ul style="margin-top:8px">
                        <li><code>technical</code> — Технический перевод</li>
                        <li><code>legal</code> — Юридический</li>
                        <li><code>medical</code> — Медицинский</li>
                        <li><code>it</code> — IT и локализация</li>
                        <li><code>website</code> — Перевод сайтов</li>
                        <li><code>finance</code> — Финансовый</li>
                        <li><code>general</code> — Общий (по умолчанию)</li>
                    </ul>
                </div>
            </div>
        </div>
        <?php
    }
}
