<?php defined('ABSPATH') || exit;
$stats = RTAP_Stats::dashboard();
?>
<div class="wrap">
  <h1>RTAP — Дашборд</h1>

  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin:20px 0;">
    <?php
    $cards = [
        ['Попыток за неделю', $stats['attempts_week'], '📊'],
        ['Средний балл', $stats['avg_score'] . '%',    '🎯'],
        ['Новых кандидатов', $stats['new_candidates'],  '👤'],
        ['Топ тематика', $stats['by_topic'][0]['topic'] ?? '—', '🏆'],
    ];
    foreach ($cards as [$label, $val, $icon]): ?>
    <div style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:20px;text-align:center;box-shadow:0 1px 4px rgba(0,0,0,.06)">
      <div style="font-size:32px"><?= $icon ?></div>
      <div style="font-size:28px;font-weight:700;margin:6px 0"><?= esc_html($val) ?></div>
      <div style="font-size:13px;color:#666"><?= esc_html($label) ?></div>
    </div>
    <?php endforeach; ?>
  </div>

  <h2>По тематикам</h2>
  <table class="widefat striped">
    <thead><tr><th>Тематика</th><th>Попыток</th></tr></thead>
    <tbody>
    <?php foreach ($stats['by_topic'] as $row): ?>
      <tr><td><?= esc_html($row['topic']) ?></td><td><?= esc_html($row['cnt']) ?></td></tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
