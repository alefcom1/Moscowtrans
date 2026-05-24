<?php defined('ABSPATH') || exit;

// Handle import
if (!empty($_FILES['import_file']['tmp_name']) && check_admin_referer('rtap_import')) {
    $ext  = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);
    $data = file_get_contents($_FILES['import_file']['tmp_name']);
    $res  = ($ext === 'csv') ? RTAP_Importer::from_csv($data) : RTAP_Importer::from_json($data);
    echo '<div class="notice notice-success"><p>Импортировано: ' . (int)$res['imported'] . ', ошибок: ' . (int)$res['errors'] . '</p></div>';
}

// Handle deactivate
if (!empty($_GET['deactivate']) && check_admin_referer('rtap_deactivate_' . $_GET['deactivate'])) {
    global $wpdb;
    $wpdb->update($wpdb->prefix . 'rtap_questions', ['active' => 0], ['id' => absint($_GET['deactivate'])]);
    echo '<div class="notice notice-success"><p>Вопрос деактивирован.</p></div>';
}

$topic  = sanitize_key($_GET['filter_topic'] ?? '');
$level  = sanitize_key($_GET['filter_level'] ?? '');
$lang   = sanitize_key($_GET['filter_lang']  ?? 'en');
$page   = max(1, (int)($_GET['paged'] ?? 1));
$per    = 30;

global $wpdb;
$where = $wpdb->prepare('WHERE lang=%s', $lang);
if ($topic) $where .= $wpdb->prepare(' AND topic=%s', $topic);
if ($level) $where .= $wpdb->prepare(' AND level=%s', $level);

$total = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}rtap_questions $where");
$rows  = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rtap_questions $where ORDER BY id DESC LIMIT $per OFFSET " . (($page-1)*$per), ARRAY_A);
?>
<div class="wrap">
  <h1>RTAP — Банк вопросов <span class="title-count">(<?= $total ?>)</span></h1>

  <div style="display:flex;gap:16px;align-items:flex-start;flex-wrap:wrap;margin:16px 0;">
    <!-- Filters -->
    <form method="get" style="display:flex;gap:8px;flex-wrap:wrap">
      <input type="hidden" name="page" value="rtap-questions">
      <select name="filter_lang" onchange="this.form.submit()">
        <option value="en" <?= selected($lang,'en',false) ?>>EN</option>
        <option value="de" <?= selected($lang,'de',false) ?>>DE</option>
        <option value="fr" <?= selected($lang,'fr',false) ?>>FR</option>
      </select>
      <select name="filter_topic" onchange="this.form.submit()">
        <option value="">Все тематики</option>
        <?php foreach(['technical','legal','medical','it'] as $t): ?>
          <option value="<?= $t ?>" <?= selected($topic,$t,false) ?>><?= ucfirst($t) ?></option>
        <?php endforeach; ?>
      </select>
      <select name="filter_level" onchange="this.form.submit()">
        <option value="">Все уровни</option>
        <?php foreach(['beginner','intermediate','advanced'] as $l): ?>
          <option value="<?= $l ?>" <?= selected($level,$l,false) ?>><?= ucfirst($l) ?></option>
        <?php endforeach; ?>
      </select>
    </form>

    <!-- Import form -->
    <form method="post" enctype="multipart/form-data" style="display:flex;gap:8px;align-items:center">
      <?php wp_nonce_field('rtap_import'); ?>
      <input type="file" name="import_file" accept=".json,.csv" required>
      <button type="submit" class="button button-primary">Импорт JSON/CSV</button>
    </form>
  </div>

  <table class="widefat striped">
    <thead>
      <tr>
        <th>ID</th><th>Тема</th><th>Уровень</th><th>Тип</th>
        <th>Вопрос</th><th>Сложность</th><th>Статус</th><th>Действия</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($rows as $q): ?>
      <tr>
        <td><?= (int)$q['id'] ?></td>
        <td><?= esc_html($q['topic']) ?></td>
        <td><?= esc_html($q['level']) ?></td>
        <td><code><?= esc_html($q['type']) ?></code></td>
        <td><?= esc_html(mb_substr($q['question'], 0, 80)) ?>…</td>
        <td><?= (int)$q['difficulty'] ?>/5</td>
        <td><?= $q['active'] ? '✅' : '⛔' ?></td>
        <td>
          <?php if ($q['active']): ?>
            <a href="<?= wp_nonce_url(add_query_arg(['page'=>'rtap-questions','deactivate'=>$q['id']], admin_url('admin.php')), 'rtap_deactivate_'.$q['id']) ?>"
               class="button button-small">Деактивировать</a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <?php if ($total > $per): ?>
    <div class="tablenav bottom">
      <?php
      echo paginate_links(['total' => ceil($total/$per), 'current' => $page,
        'base' => add_query_arg('paged','%#%'), 'format' => '']);
      ?>
    </div>
  <?php endif; ?>
</div>
