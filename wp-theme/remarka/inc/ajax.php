<?php
/**
 * WordPress AJAX handlers.
 *
 * Telegram notifications require two constants in wp-config.php:
 *   define('REMARKA_TG_TOKEN',   '...');
 *   define('REMARKA_TG_CHAT_ID', '...');
 */

define('REMARKA_UPLOAD_TOKEN', 'rem-msc-2026');

/**
 * Send a Telegram message to the configured chat.
 * Silently returns false if constants are not set.
 */
function remarka_tg(string $text): bool {
    if (!defined('REMARKA_TG_TOKEN') || !defined('REMARKA_TG_CHAT_ID')) {
        return false;
    }
    $url  = 'https://api.telegram.org/bot' . REMARKA_TG_TOKEN . '/sendMessage';
    $args = [
        'timeout'  => 5,
        'blocking' => false, // fire-and-forget, не тормозим ответ
        'body'     => [
            'chat_id'                  => REMARKA_TG_CHAT_ID,
            'text'                     => $text,
            'parse_mode'               => 'HTML',
            'disable_web_page_preview' => true,
        ],
    ];
    wp_remote_post($url, $args);
    return true;
}

/* ── Калькулятор: загрузка файла ───────────────────────── */

function remarka_handle_upload() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'remarka_upload_nonce')) {
        wp_send_json_error(['message' => 'Нарушение безопасности'], 403);
    }
    if (empty($_POST['token']) || $_POST['token'] !== REMARKA_UPLOAD_TOKEN) {
        wp_send_json_error(['message' => 'Неверный токен'], 403);
    }
    if (empty($_FILES['files'])) {
        wp_send_json(['files' => [], 'errors' => ['Файлы не переданы']]);
    }

    $allowed_ext = ['pdf','doc','docx','txt','rtf','odt','xls','xlsx','ppt','pptx','jpg','jpeg','png'];
    $max_size    = 20 * 1024 * 1024;

    $upload_dir = wp_upload_dir();
    $calc_dir   = trailingslashit($upload_dir['basedir']) . 'calc/';
    $calc_url   = trailingslashit($upload_dir['baseurl']) . 'calc/';

    if (!file_exists($calc_dir)) {
        wp_mkdir_p($calc_dir);
        file_put_contents($calc_dir . '.htaccess', "php_flag engine off\nOptions -ExecCGI\n");
        file_put_contents($calc_dir . 'index.php', '<?php // Silence');
    }

    foreach (glob($calc_dir . '*') as $old_file) {
        if (is_file($old_file) && (time() - filemtime($old_file)) > 30 * 86400) {
            @unlink($old_file);
        }
    }

    $files_count = count($_FILES['files']['name']);
    $uploaded    = [];
    $errors      = [];

    for ($i = 0; $i < $files_count; $i++) {
        $orig_name = $_FILES['files']['name'][$i];
        $tmp_path  = $_FILES['files']['tmp_name'][$i];
        $size      = $_FILES['files']['size'][$i];
        $err_code  = $_FILES['files']['error'][$i];

        if ($err_code !== UPLOAD_ERR_OK) { $errors[] = "Ошибка загрузки «{$orig_name}» (код {$err_code})"; continue; }
        if ($size > $max_size)           { $errors[] = "«{$orig_name}»: файл больше 20 МБ"; continue; }

        $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_ext, true)) { $errors[] = "«{$orig_name}»: недопустимый формат"; continue; }

        $safe_name = date('Ymd') . '_' . substr(md5(uniqid('', true)), 0, 8) . '_' . mt_rand(100, 999) . '.' . $ext;
        $dest      = $calc_dir . $safe_name;

        if (!move_uploaded_file($tmp_path, $dest)) { $errors[] = "Не удалось сохранить «{$orig_name}»"; continue; }

        $size_fmt = $size < 1024 * 1024
            ? round($size / 1024, 1) . ' КБ'
            : round($size / (1024 * 1024), 1) . ' МБ';

        $uploaded[] = ['name' => $orig_name, 'url' => $calc_url . $safe_name, 'size_fmt' => $size_fmt];
    }

    // Telegram: уведомление о файле из калькулятора
    if (!empty($uploaded)) {
        $names    = implode(', ', array_column($uploaded, 'name'));
        $referer  = isset($_SERVER['HTTP_REFERER']) ? esc_url_raw($_SERVER['HTTP_REFERER']) : site_url();
        $site     = parse_url(site_url(), PHP_URL_HOST);
        remarka_tg(
            "📎 <b>Новый файл из калькулятора</b>\n" .
            "🌐 <b>Сайт:</b> {$site}\n" .
            "🔗 <b>Страница:</b> {$referer}\n" .
            "📄 Файл(ы): <code>{$names}</code>"
        );
    }

    wp_send_json(['files' => $uploaded, 'errors' => $errors]);
}

add_action('wp_ajax_remarka_upload',        'remarka_handle_upload');
add_action('wp_ajax_nopriv_remarka_upload', 'remarka_handle_upload');

/* ── Контактная форма ──────────────────────────────────── */

function remarka_handle_contact() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'remarka_contact_nonce')) {
        wp_send_json_error(['message' => 'Ошибка безопасности'], 403);
    }

    $name     = sanitize_text_field($_POST['name']         ?? '');
    $phone    = sanitize_text_field($_POST['phone']        ?? '');
    $email    = sanitize_email($_POST['email']             ?? '');
    $message  = sanitize_textarea_field($_POST['message']  ?? '');
    $page_url = esc_url_raw($_POST['page_url']             ?? '');

    if (!$name || !$phone || !is_email($email)) {
        wp_send_json_error(['message' => 'Заполните обязательные поля']);
    }

    $site = parse_url(site_url(), PHP_URL_HOST);
    if (!$page_url) {
        $page_url = site_url('/kontakty/');
    }

    // Email
    $to      = 'info@moscowtrans.ru';
    $subject = 'Новая заявка с сайта — ' . $name;
    $body    = "Сайт: {$site}\nСтраница: {$page_url}\n\nИмя: {$name}\nТелефон: {$phone}\nE-mail: {$email}\n\nСообщение:\n{$message}";
    $headers = ['Content-Type: text/plain; charset=UTF-8', "Reply-To: {$name} <{$email}>"];
    wp_mail($to, $subject, $body, $headers);

    // Telegram
    $msg_line = $message
        ? "\n💬 <i>" . htmlspecialchars($message, ENT_QUOTES) . "</i>"
        : '';

    remarka_tg(
        "📩 <b>Новая заявка с сайта</b>\n" .
        "🌐 <b>Сайт:</b> {$site}\n" .
        "🔗 <b>Страница:</b> {$page_url}\n" .
        "━━━━━━━━━━━━\n" .
        "👤 <b>{$name}</b>\n" .
        "📞 {$phone}\n" .
        "✉️ {$email}" .
        $msg_line
    );

    wp_send_json_success(['message' => 'Отправлено']);
}

add_action('wp_ajax_remarka_contact',        'remarka_handle_contact');
add_action('wp_ajax_nopriv_remarka_contact', 'remarka_handle_contact');
