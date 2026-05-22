<?php
/**
 * WordPress AJAX handler for the calc-widget file upload.
 * Replaces the standalone calc-upload.php from the prototype.
 *
 * Token: 'rem-msc-2026' (must match UPLOAD_TOKEN in calc-widget.js)
 */

define('REMARKA_UPLOAD_TOKEN', 'rem-msc-2026');

function remarka_handle_upload() {
    // CSRF nonce check (passed from wp_localize_script)
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'remarka_upload_nonce')) {
        wp_send_json_error(['message' => 'Нарушение безопасности'], 403);
    }

    // Token check (matches prototype behaviour)
    if (empty($_POST['token']) || $_POST['token'] !== REMARKA_UPLOAD_TOKEN) {
        wp_send_json_error(['message' => 'Неверный токен'], 403);
    }

    if (empty($_FILES['files'])) {
        wp_send_json(['files' => [], 'errors' => ['Файлы не переданы']]);
    }

    $allowed_ext = ['pdf','doc','docx','txt','rtf','odt','xls','xlsx','ppt','pptx','jpg','jpeg','png'];
    $max_size    = 20 * 1024 * 1024; // 20 MB

    $upload_dir  = wp_upload_dir();
    $calc_dir    = trailingslashit($upload_dir['basedir']) . 'calc/';
    $calc_url    = trailingslashit($upload_dir['baseurl']) . 'calc/';

    if (!file_exists($calc_dir)) {
        wp_mkdir_p($calc_dir);
        // Prevent direct PHP execution in upload dir
        file_put_contents($calc_dir . '.htaccess', "php_flag engine off\nOptions -ExecCGI\n");
        file_put_contents($calc_dir . 'index.php', '<?php // Silence');
    }

    // Lazy cleanup: remove files older than 30 days
    foreach (glob($calc_dir . '*') as $old_file) {
        if (is_file($old_file) && (time() - filemtime($old_file)) > 30 * 86400) {
            @unlink($old_file);
        }
    }

    // Re-index the FILES array (browser may send multiple files)
    $files_count = count($_FILES['files']['name']);
    $uploaded    = [];
    $errors      = [];

    for ($i = 0; $i < $files_count; $i++) {
        $orig_name = $_FILES['files']['name'][$i];
        $tmp_path  = $_FILES['files']['tmp_name'][$i];
        $size      = $_FILES['files']['size'][$i];
        $err_code  = $_FILES['files']['error'][$i];

        if ($err_code !== UPLOAD_ERR_OK) {
            $errors[] = "Ошибка загрузки «{$orig_name}» (код {$err_code})";
            continue;
        }
        if ($size > $max_size) {
            $errors[] = "«{$orig_name}»: файл больше 20 МБ";
            continue;
        }

        $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_ext, true)) {
            $errors[] = "«{$orig_name}»: недопустимый формат";
            continue;
        }

        // Safe filename: date_hex_rand.ext
        $safe_name = date('Ymd') . '_' . substr(md5(uniqid('', true)), 0, 8) . '_' . mt_rand(100, 999) . '.' . $ext;
        $dest      = $calc_dir . $safe_name;

        if (!move_uploaded_file($tmp_path, $dest)) {
            $errors[] = "Не удалось сохранить «{$orig_name}»";
            continue;
        }

        $size_fmt = $size < 1024 * 1024
            ? round($size / 1024, 1) . ' КБ'
            : round($size / (1024 * 1024), 1) . ' МБ';

        $uploaded[] = [
            'name'     => $orig_name,
            'url'      => $calc_url . $safe_name,
            'size_fmt' => $size_fmt,
        ];
    }

    wp_send_json(['files' => $uploaded, 'errors' => $errors]);
}

add_action('wp_ajax_remarka_upload',        'remarka_handle_upload');
add_action('wp_ajax_nopriv_remarka_upload', 'remarka_handle_upload');
