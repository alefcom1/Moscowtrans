<?php
/**
 * calc-upload.php — приём файлов от калькулятора стоимости перевода.
 * Удаляет файлы старше 30 дней при каждом запросе (lazy cleanup).
 * Разместить в корне сайта. Папка uploads/calc/ создаётся автоматически.
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('X-Content-Type-Options: nosniff');

/* ── Конфигурация ── */
define('UPLOAD_SECRET',  'rem-msc-2026');          // должен совпадать с UPLOAD_TOKEN в JS
define('UPLOAD_DIR',     __DIR__ . '/uploads/calc/');
define('UPLOAD_URL',     '/uploads/calc/');
define('MAX_FILE_BYTES', 20 * 1024 * 1024);        // 20 МБ на файл
define('RETENTION_DAYS', 30);
define('ALLOWED_EXTS', [
    'pdf','doc','docx','txt','rtf','odt',
    'xls','xlsx','ppt','pptx','jpg','jpeg','png'
]);

function respond(array $data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/* ── Проверка токена ── */
if (($_POST['token'] ?? '') !== UPLOAD_SECRET) {
    respond(['files' => [], 'errors' => ['Unauthorized']], 403);
}

/* ── Создать папку, если нет ── */
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
    // Защита: запретить выполнение PHP и листинг директории
    file_put_contents(UPLOAD_DIR . '.htaccess',
        "Options -Indexes\nphp_flag engine off\nAddType text/plain .php .phtml\n");
}

/* ── Удалить файлы старше RETENTION_DAYS (lazy cleanup) ── */
$threshold = time() - RETENTION_DAYS * 86400;
foreach (glob(UPLOAD_DIR . '*') as $file) {
    if (is_file($file) && filemtime($file) < $threshold) {
        @unlink($file);
    }
}

/* ── Принять файлы ── */
$uploaded = [];
$errors   = [];

$fileNames  = $_FILES['files']['name']     ?? [];
$fileTmps   = $_FILES['files']['tmp_name'] ?? [];
$fileErrors = $_FILES['files']['error']    ?? [];
$fileSizes  = $_FILES['files']['size']     ?? [];

foreach ($fileTmps as $i => $tmp) {
    $origName = basename($fileNames[$i] ?? '');
    $errCode  = $fileErrors[$i] ?? UPLOAD_ERR_NO_FILE;
    $size     = (int)($fileSizes[$i] ?? 0);

    if ($errCode !== UPLOAD_ERR_OK) {
        $errors[] = "$origName: ошибка загрузки (код $errCode)";
        continue;
    }
    if ($size > MAX_FILE_BYTES) {
        $errors[] = "$origName: файл слишком большой (максимум 20 МБ)";
        continue;
    }
    $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXTS, true)) {
        $errors[] = "$origName: недопустимый формат .$ext";
        continue;
    }

    // Безопасное имя: дата + случайный суффикс + оригинальное расширение
    $safeName = date('Ymd_His') . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
    $dest     = UPLOAD_DIR . $safeName;

    if (move_uploaded_file($tmp, $dest)) {
        $uploaded[] = [
            'name'     => $origName,
            'url'      => UPLOAD_URL . $safeName,
            'size_fmt' => formatBytes($size),
        ];
    } else {
        $errors[] = "$origName: не удалось сохранить файл на сервере";
    }
}

respond(['files' => $uploaded, 'errors' => $errors]);

/* ── Вспомогательная функция ── */
function formatBytes(int $bytes): string {
    if ($bytes < 1024)             return $bytes . ' Б';
    if ($bytes < 1024 * 1024)      return round($bytes / 1024, 1) . ' КБ';
    return round($bytes / (1024 * 1024), 1) . ' МБ';
}
