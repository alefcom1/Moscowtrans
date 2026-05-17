<?php
defined( 'ABSPATH' ) || exit;

class Remarka_Translator {

    public function __construct() {
        add_action( 'init',                  [ $this, 'register_cpt' ] );
        add_action( 'admin_menu',            [ $this, 'register_menu' ] );
        add_action( 'add_meta_boxes',        [ $this, 'add_meta_boxes' ] );
        add_filter( 'manage_remarka_translator_posts_columns',       [ $this, 'admin_columns' ] );
        add_action( 'manage_remarka_translator_posts_custom_column', [ $this, 'admin_column_content' ], 10, 2 );

        // AJAX — сохранение анкеты
        add_action( 'wp_ajax_remarka_save_translator',        [ $this, 'save_translator' ] );
        add_action( 'wp_ajax_nopriv_remarka_save_translator', [ $this, 'save_translator' ] );
    }

    /* ══════════════════════════════════════════════════════
       REGISTER CPT
    ══════════════════════════════════════════════════════ */
    public function register_cpt(): void {
        register_post_type( 'remarka_translator', [
            'labels' => [
                'name'               => 'Анкеты переводчиков',
                'singular_name'      => 'Анкета переводчика',
                'add_new_item'       => 'Добавить анкету',
                'edit_item'          => 'Анкета переводчика',
                'not_found'          => 'Анкет не найдено',
                'not_found_in_trash' => 'В корзине анкет нет',
                'menu_name'          => 'Переводчики',
            ],
            'public'            => false,
            'show_ui'           => true,
            'show_in_menu'      => false,
            'show_in_rest'      => false,
            'capability_type'   => 'post',
            'capabilities'      => [ 'create_posts' => 'manage_options' ],
            'map_meta_cap'      => true,
            'hierarchical'      => false,
            'supports'          => [ 'title' ],
            'has_archive'       => false,
            'rewrite'           => false,
        ] );
    }

    /* ══════════════════════════════════════════════════════
       ADMIN MENU
    ══════════════════════════════════════════════════════ */
    public function register_menu(): void {
        add_submenu_page(
            'remarka-chat',
            'Анкеты переводчиков',
            'Переводчики',
            'manage_options',
            'edit.php?post_type=remarka_translator'
        );
    }

    /* ══════════════════════════════════════════════════════
       AJAX: SAVE TRANSLATOR APPLICATION
    ══════════════════════════════════════════════════════ */
    public function save_translator(): void {
        check_ajax_referer( 'remarka_nonce', 'nonce' );

        $raw_data   = wp_unslash( $_POST['data']   ?? '{}' );
        $raw_scores = wp_unslash( $_POST['scores'] ?? '{}' );

        $data   = json_decode( $raw_data,   true ) ?: [];
        $scores = json_decode( $raw_scores, true ) ?: [];

        // Базовая валидация
        $email = sanitize_email( $data['email'] ?? '' );
        $name  = sanitize_text_field( ($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '') );

        if ( empty( $name ) && empty( $email ) ) {
            wp_send_json_error( 'No name or email' );
        }

        // Создаём CPT-запись
        $title = sprintf(
            'Переводчик: %s — %s',
            trim( $name ) ?: 'Без имени',
            date_i18n( 'd.m.Y H:i' )
        );

        $post_id = wp_insert_post( [
            'post_type'   => 'remarka_translator',
            'post_title'  => $title,
            'post_status' => 'publish',
        ] );

        if ( ! $post_id || is_wp_error( $post_id ) ) {
            wp_send_json_error( 'Failed to create post' );
        }

        // Сохраняем все поля в post meta
        $fields = [
            'first_name', 'last_name', 'patronymic', 'birth_date',
            'phone', 'email', 'languages', 'emp_type', 'work_type',
            'workload', 'productivity', 'urgent_work', 'salary',
            'pc_skills', 'trados_skills', 'specialization',
            'test_scores', 'comment', 'submit_date', 'source',
        ];
        foreach ( $fields as $field ) {
            if ( isset( $data[ $field ] ) ) {
                update_post_meta( $post_id, '_tr_' . $field, sanitize_textarea_field( $data[ $field ] ) );
            }
        }

        // Сохраняем результаты тестов как отдельный массив
        if ( ! empty( $scores ) ) {
            update_post_meta( $post_id, '_tr_scores_detail', $scores );
        }

        // Лог в wp_options (последние 100 анкет)
        $log = get_option( 'remarka_translators_log', [] );
        array_unshift( $log, [
            'post_id'    => $post_id,
            'name'       => trim( $name ),
            'email'      => $email,
            'languages'  => $data['languages'] ?? '',
            'scores'     => $scores,
            'date'       => current_time( 'mysql' ),
        ] );
        update_option( 'remarka_translators_log', array_slice( $log, 0, 100 ) );

        // Email уведомление HR-менеджеру
        $this->notify_hr( $post_id, $data, $scores );

        wp_send_json_success( [ 'post_id' => $post_id ] );
    }

    private function notify_hr( int $post_id, array $data, array $scores ): void {
        $to      = get_option( 'admin_email' );
        $name    = trim( ($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '') );
        $subject = '👤 Новая анкета переводчика: ' . $name;

        $body  = "Поступила анкета переводчика через AI-консультант\n";
        $body .= "==============================\n";
        $body .= "Имя:        " . $name . "\n";
        $body .= "Email:      " . ($data['email']    ?? '—') . "\n";
        $body .= "Телефон:    " . ($data['phone']    ?? '—') . "\n";
        $body .= "Языки:      " . ($data['languages'] ?? '—') . "\n";
        $body .= "Занятость:  " . ($data['emp_type']  ?? '—') . "\n";
        $body .= "Формат:     " . ($data['work_type'] ?? '—') . "\n";
        $body .= "Специализ.: " . ($data['specialization'] ?? '—') . "\n";

        if ( ! empty( $scores ) ) {
            $body .= "\nРезультаты тестов:\n";
            foreach ( $scores as $lang => $sc ) {
                $body .= "  {$lang}: {$sc['score']}/100 ({$sc['date']})\n";
            }
        }

        $body .= "\n" . $data['comment'] ?? '';
        $body .= "\n==============================\n";
        $body .= "Дата: " . date_i18n( 'd.m.Y H:i' ) . "\n";
        $body .= "WP Admin: " . admin_url( 'post.php?post=' . $post_id . '&action=edit' ) . "\n";

        wp_mail( $to, $subject, $body );
    }

    /* ══════════════════════════════════════════════════════
       META BOXES
    ══════════════════════════════════════════════════════ */
    public function add_meta_boxes(): void {
        add_meta_box(
            'remarka_tr_details',
            '👤 Данные переводчика',
            [ $this, 'meta_box_details' ],
            'remarka_translator',
            'normal',
            'high'
        );
        add_meta_box(
            'remarka_tr_tests',
            '🎯 Результаты тестов',
            [ $this, 'meta_box_tests' ],
            'remarka_translator',
            'side',
            'high'
        );
        add_meta_box(
            'remarka_tr_status',
            '⚡ Статус',
            [ $this, 'meta_box_status' ],
            'remarka_translator',
            'side',
            'default'
        );
    }

    public function meta_box_details( WP_Post $post ): void {
        $get = fn( $key ) => get_post_meta( $post->ID, '_tr_' . $key, true );

        $sections = [
            'Личные данные' => [
                ['Имя',         $get('first_name') . ' ' . $get('last_name')],
                ['Отчество',    $get('patronymic') ?: '—'],
                ['Дата рожд.',  $get('birth_date')  ?: '—'],
                ['Email',       '<a href="mailto:' . esc_attr($get('email')) . '">' . esc_html($get('email')) . '</a>'],
                ['Телефон',     $get('phone') ? '<a href="tel:' . esc_attr($get('phone')) . '">' . esc_html($get('phone')) . '</a>' : '—'],
            ],
            'Языки' => [
                ['Языки', $get('languages') ?: '—'],
            ],
            'Условия работы' => [
                ['Занятость',        $get('emp_type')    ?: '—'],
                ['Формат',           $get('work_type')   ?: '—'],
                ['Загруженность',    $get('workload')    ?: '—'],
                ['Производит.',      $get('productivity') ?: '—'],
                ['Срочные',          $get('urgent_work') ?: '—'],
                ['Желаемый доход',   $get('salary')      ?: '—'],
            ],
            'Навыки' => [
                ['Программы',        $get('pc_skills')      ?: '—'],
                ['CAT-инструменты',  $get('trados_skills')  ?: '—'],
                ['Специализация',    $get('specialization') ?: '—'],
            ],
        ];
        ?>
        <style>
        .tr-sec { margin-bottom:18px; }
        .tr-sec-title { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#7a7a7a; margin-bottom:8px; padding-bottom:5px; border-bottom:1px solid #f0f0f0; }
        .tr-grid { display:grid; grid-template-columns:140px 1fr; gap:4px 12px; }
        .tr-label { font-size:12px; color:#888; font-weight:500; padding:3px 0; }
        .tr-value { font-size:13px; color:#1e293b; font-weight:500; padding:3px 0; }
        .tr-comment { background:#fafaf8; border:1px solid #eee; border-radius:6px; padding:10px 12px; font-size:13px; color:#374151; line-height:1.55; margin-top:4px; }
        </style>

        <div style="padding:4px 0">
            <?php foreach ( $sections as $title => $rows ) : ?>
            <div class="tr-sec">
                <div class="tr-sec-title"><?= esc_html($title) ?></div>
                <div class="tr-grid">
                    <?php foreach ( $rows as [$label, $value] ) : ?>
                        <div class="tr-label"><?= esc_html($label) ?></div>
                        <div class="tr-value"><?= wp_kses_post($value) ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>

            <?php $comment = $get('comment'); if ( $comment && $comment !== '—' ) : ?>
            <div class="tr-sec">
                <div class="tr-sec-title">Комментарий</div>
                <div class="tr-comment"><?= esc_html($comment) ?></div>
            </div>
            <?php endif; ?>

            <div class="tr-sec">
                <div class="tr-sec-title">Источник</div>
                <div class="tr-grid">
                    <div class="tr-label">Источник</div>
                    <div class="tr-value"><?= esc_html($get('source') ?: 'AI-консультант') ?></div>
                    <div class="tr-label">Дата подачи</div>
                    <div class="tr-value"><?= esc_html($get('submit_date') ?: '—') ?></div>
                </div>
            </div>
        </div>
        <?php
    }

    public function meta_box_tests( WP_Post $post ): void {
        $scores = get_post_meta( $post->ID, '_tr_scores_detail', true );
        $scores_text = get_post_meta( $post->ID, '_tr_test_scores', true );

        if ( empty( $scores ) && empty( $scores_text ) ) {
            echo '<p style="color:#888;font-size:13px">Тесты не пройдены</p>';
            return;
        }
        ?>
        <div style="display:flex;flex-direction:column;gap:8px">
            <?php if ( is_array($scores) ) :
                foreach ( $scores as $lang => $sc ) :
                    $score = (int)($sc['score'] ?? 0);
                    $color = $score >= 85 ? '#16a34a' : ($score >= 70 ? '#c4922a' : ($score >= 50 ? '#2563eb' : '#dc2626'));
                    $bg    = $score >= 85 ? '#f0fdf4' : ($score >= 70 ? '#fefce8' : ($score >= 50 ? '#eff6ff' : '#fef2f2'));
                    $label = $score >= 85 ? 'Отлично' : ($score >= 70 ? 'Хорошо' : ($score >= 50 ? 'Средне' : 'Слабо'));
            ?>
            <div style="background:<?= $bg ?>;border-radius:8px;padding:10px 12px">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px">
                    <span style="font-size:13px;font-weight:600;color:#1e293b"><?= esc_html($lang) ?></span>
                    <span style="font-size:16px;font-weight:800;color:<?= $color ?>"><?= $score ?>/100</span>
                </div>
                <div style="height:5px;background:rgba(0,0,0,0.08);border-radius:3px;overflow:hidden;margin-bottom:5px">
                    <div style="height:100%;width:<?= $score ?>%;background:<?= $color ?>;border-radius:3px"></div>
                </div>
                <div style="display:flex;justify-content:space-between">
                    <span style="font-size:11px;color:<?= $color ?>;font-weight:700"><?= $label ?></span>
                    <span style="font-size:11px;color:#888"><?= esc_html($sc['date'] ?? '') ?></span>
                </div>
                <?php if ( ! empty( $sc['feedback'] ) ) : ?>
                <div style="font-size:11.5px;color:#555;margin-top:6px;line-height:1.5;border-top:1px solid rgba(0,0,0,0.06);padding-top:6px">
                    <?= esc_html(wp_trim_words($sc['feedback'], 20)) ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; else : ?>
            <div style="font-size:12px;color:#888"><?= esc_html($scores_text) ?></div>
            <?php endif; ?>
        </div>
        <?php
    }

    public function meta_box_status( WP_Post $post ): void {
        $status = get_post_meta( $post->ID, '_tr_status', true ) ?: 'new';
        $statuses = [
            'new'       => '🆕 Новая',
            'review'    => '👀 На рассмотрении',
            'test'      => '🎯 На тестировании',
            'interview' => '💼 На собеседовании',
            'accepted'  => '✅ Принят',
            'declined'  => '❌ Отклонён',
            'reserve'   => '📋 В резерве',
        ];
        ?>
        <select name="remarka_tr_status" style="width:100%;margin-bottom:10px;padding:6px">
            <?php foreach ( $statuses as $val => $label ) : ?>
                <option value="<?= $val ?>" <?= selected($status, $val, false) ?>><?= $label ?></option>
            <?php endforeach; ?>
        </select>
        <?php wp_nonce_field('remarka_tr_meta', 'remarka_tr_nonce'); ?>

        <?php
        $email = get_post_meta( $post->ID, '_tr_email', true );
        $phone = get_post_meta( $post->ID, '_tr_phone', true );
        if ( $email || $phone ) : ?>
        <hr style="border:none;border-top:1px solid #eee;margin:10px 0">
        <p style="font-size:12px;font-weight:600;color:#555;margin:0 0 6px">Связаться:</p>
        <?php if ( $email ) : ?>
            <a href="mailto:<?= esc_attr($email) ?>" class="button button-secondary" style="width:100%;text-align:center;display:block;margin-bottom:5px">📧 Email</a>
        <?php endif; ?>
        <?php if ( $phone ) : ?>
            <a href="tel:<?= esc_attr($phone) ?>" class="button button-secondary" style="width:100%;text-align:center;display:block">📞 Звонок</a>
        <?php endif; ?>
        <?php endif; ?>
        <?php
    }

    /* ══════════════════════════════════════════════════════
       SAVE META
    ══════════════════════════════════════════════════════ */
    public function __construct_save_hook(): void {
        add_action( 'save_post_remarka_translator', [ $this, 'save_meta' ] );
    }

    public function save_meta( int $post_id ): void {
        if ( ! isset($_POST['remarka_tr_nonce']) ) return;
        if ( ! wp_verify_nonce($_POST['remarka_tr_nonce'], 'remarka_tr_meta') ) return;
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
        $status = sanitize_text_field( $_POST['remarka_tr_status'] ?? 'new' );
        update_post_meta( $post_id, '_tr_status', $status );
    }

    /* ══════════════════════════════════════════════════════
       ADMIN COLUMNS
    ══════════════════════════════════════════════════════ */
    public function admin_columns( array $cols ): array {
        return [
            'cb'           => $cols['cb'],
            'title'        => 'Переводчик',
            'tr_langs'     => 'Языки',
            'tr_spec'      => 'Специализация',
            'tr_scores'    => 'Тесты',
            'tr_status'    => 'Статус',
            'date'         => 'Дата',
        ];
    }

    public function admin_column_content( string $column, int $post_id ): void {
        $get = fn($k) => get_post_meta($post_id, '_tr_' . $k, true);

        $status_map = [
            'new'       => ['🆕 Новая',         '#dbeafe','#1d4ed8'],
            'review'    => ['👀 На рассмотрении','#fef9c3','#92400e'],
            'test'      => ['🎯 Тестирование',   '#ede9fe','#6d28d9'],
            'interview' => ['💼 Собеседование',  '#fce7f3','#9d174d'],
            'accepted'  => ['✅ Принят',          '#dcfce7','#166534'],
            'declined'  => ['❌ Отклонён',        '#fee2e2','#991b1b'],
            'reserve'   => ['📋 Резерв',          '#f0fdf4','#15803d'],
        ];

        switch ( $column ) {
            case 'tr_langs':
                $langs = $get('languages');
                echo '<span style="font-size:12px">' . esc_html(wp_trim_words($langs, 6, '…')) . '</span>';
                break;
            case 'tr_spec':
                echo '<span style="font-size:12px">' . esc_html(wp_trim_words($get('specialization') ?: '—', 4, '…')) . '</span>';
                break;
            case 'tr_scores':
                $scores = get_post_meta($post_id, '_tr_scores_detail', true);
                if ( is_array($scores) && ! empty($scores) ) {
                    foreach ( $scores as $lang => $sc ) {
                        $score = (int)($sc['score'] ?? 0);
                        $color = $score >= 85 ? '#16a34a' : ($score >= 70 ? '#c4922a' : '#dc2626');
                        echo '<span style="display:inline-block;background:rgba(0,0,0,.05);border-radius:4px;padding:1px 6px;font-size:11px;color:' . $color . ';font-weight:700;margin:1px">';
                        echo esc_html(mb_substr($lang, 0, 3)) . ' ' . $score . '</span>';
                    }
                } else {
                    echo '<span style="color:#999;font-size:12px">—</span>';
                }
                break;
            case 'tr_status':
                $st = $get('status') ?: 'new';
                [$label, $bg, $color] = $status_map[$st] ?? ['—','#f1f5f9','#64748b'];
                echo '<span style="background:' . $bg . ';color:' . $color . ';padding:2px 8px;border-radius:4px;font-size:11px;font-weight:700">' . $label . '</span>';
                break;
        }
    }
}
