<?php defined('ABSPATH') || exit;
global $wpdb;
$search = sanitize_text_field($_GET['s'] ?? '');
$where  = $search ? $wpdb->prepare("WHERE id LIKE %s OR candidate_name LIKE %s", "%$search%", "%$search%") : '';
$rows   = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rtap_certificates $where ORDER BY issued_at DESC LIMIT 100", ARRAY_A);
$total  = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}rtap_certificates");
?>
<div class="wrap">
  <h1>RTAP — Сертификаты <span class="title-count">(<?= $total ?>)</span></h1>

  <form method="get" style="margin:12px 0">
    <input type="hidden" name="page" value="rtap-certs">
    <input type="search" name="s" value="<?= esc_attr($search) ?>" placeholder="Поиск по номеру или имени">
    <button type="submit" class="button">Найти</button>
  </form>

  <table class="widefat striped">
    <thead>
      <tr><th>Номер</th><th>Имя</th><th>Тема</th><th>Уровень</th><th>Балл</th><th>Дата</th><th>Верификация</th></tr>
    </thead>
    <tbody>
    <?php foreach ($rows as $c): ?>
      <tr>
        <td><code><?= esc_html($c['id']) ?></code></td>
        <td><?= esc_html($c['candidate_name']) ?></td>
        <td><?= esc_html($c['topic']) ?></td>
        <td><?= esc_html($c['level']) ?></td>
        <td><?= (int)$c['score_pct'] ?>%</td>
        <td><?= esc_html(date('d.m.Y', strtotime($c['issued_at']))) ?></td>
        <td>
          <a href="<?= esc_url(get_site_url() . '/verify/' . $c['id']) ?>" target="_blank" class="button button-small">Открыть</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
