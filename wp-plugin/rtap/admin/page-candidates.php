<?php defined('ABSPATH') || exit;

// Manual TMS sync
if (!empty($_GET['sync']) && check_admin_referer('rtap_sync_' . $_GET['sync'])) {
    global $wpdb;
    $c = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}rtap_candidates WHERE id=%d", absint($_GET['sync'])
    ), ARRAY_A);
    if ($c) {
        RTAP_Candidate::try_tms_sync($c['id'], $c);
        echo '<div class="notice notice-success"><p>Синхронизация запущена.</p></div>';
    }
}

$page = max(1, (int)($_GET['paged'] ?? 1));
$per  = 25;
global $wpdb;
$total = (int)$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}rtap_candidates");
$rows  = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rtap_candidates ORDER BY id DESC LIMIT $per OFFSET " . (($page-1)*$per), ARRAY_A);
?>
<div class="wrap">
  <h1>RTAP — Кандидаты <span class="title-count">(<?= $total ?>)</span></h1>

  <table class="widefat striped">
    <thead>
      <tr>
        <th>ID</th><th>Имя</th><th>Email</th><th>Телефон</th>
        <th>Тематики</th><th>TMS ID</th><th>Синхр.</th><th>Дата</th><th>Действия</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($rows as $c): ?>
      <tr>
        <td><?= (int)$c['id'] ?></td>
        <td><?= esc_html($c['name']) ?></td>
        <td><a href="mailto:<?= esc_attr($c['email']) ?>"><?= esc_html($c['email']) ?></a></td>
        <td><?= esc_html($c['phone']) ?></td>
        <td><?= esc_html(implode(', ', json_decode($c['topics'] ?? '[]', true) ?: [])) ?></td>
        <td><?= esc_html($c['tms_id'] ?: '—') ?></td>
        <td><?= $c['tms_synced'] ? '✅' : '⏳' ?></td>
        <td><?= esc_html(date('d.m.Y', strtotime($c['created_at']))) ?></td>
        <td>
          <?php if (!$c['tms_synced']): ?>
            <a href="<?= wp_nonce_url(add_query_arg(['page'=>'rtap-candidates','sync'=>$c['id']], admin_url('admin.php')), 'rtap_sync_'.$c['id']) ?>"
               class="button button-small">Sync TMS</a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <?php if ($total > $per): ?>
    <div class="tablenav bottom">
      <?php echo paginate_links(['total'=>ceil($total/$per),'current'=>$page,'base'=>add_query_arg('paged','%#%'),'format'=>'']); ?>
    </div>
  <?php endif; ?>
</div>
