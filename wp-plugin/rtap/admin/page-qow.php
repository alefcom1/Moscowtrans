<?php defined('ABSPATH') || exit;

if (!empty($_POST['set_question']) && check_admin_referer('rtap_set_qow')) {
    $q_id = absint($_POST['question_id']);
    if ($q_id && RTAP_QOW::set_question($q_id)) {
        echo '<div class="notice notice-success"><p>Вопрос недели установлен.</p></div>';
    }
}

global $wpdb;
$current = RTAP_DB::get_active_qow();
$pool    = $wpdb->get_results(
    "SELECT id, topic, level, type, question FROM {$wpdb->prefix}rtap_questions WHERE type IN ('mc','bt','fb') AND active=1 ORDER BY RAND() LIMIT 50",
    ARRAY_A
);
$history = $wpdb->get_results(
    "SELECT qow.*, q.question FROM {$wpdb->prefix}rtap_question_of_week qow JOIN {$wpdb->prefix}rtap_questions q ON q.id=qow.question_id ORDER BY qow.week_start DESC LIMIT 20",
    ARRAY_A
);
?>
<div class="wrap">
  <h1>RTAP — Вопрос недели</h1>

  <?php if ($current): ?>
  <div style="background:#fff;border:1px solid #ddd;border-radius:8px;padding:20px;margin:16px 0">
    <h3>Текущий вопрос</h3>
    <p><strong><?= esc_html($current['question']) ?></strong></p>
    <p>
      <span class="button"><?= esc_html($current['topic']) ?></span>
      <span class="button"><?= esc_html($current['level']) ?></span>
      <span class="button"><?= esc_html($current['type']) ?></span>
    </p>
    <?php $stats = json_decode($current['stats_json'] ?? '{}', true) ?: []; ?>
    <p>Ответов: <strong><?= (int)($stats['total_answers'] ?? 0) ?></strong>,
       верных: <strong><?= (int)($stats['correct_pct'] ?? 0) ?>%</strong></p>
  </div>
  <?php endif; ?>

  <h2>Установить вопрос недели</h2>
  <form method="post">
    <?php wp_nonce_field('rtap_set_qow'); ?>
    <select name="question_id" style="min-width:400px">
      <option value="">Выберите вопрос</option>
      <?php foreach ($pool as $q): ?>
        <option value="<?= (int)$q['id'] ?>">
          [<?= esc_html($q['topic']) ?>/<?= esc_html($q['level']) ?>/<?= esc_html($q['type']) ?>]
          <?= esc_html(mb_substr($q['question'], 0, 60)) ?>…
        </option>
      <?php endforeach; ?>
    </select>
    <button type="submit" name="set_question" class="button button-primary" style="margin-left:8px">Установить</button>
    <button type="submit" name="set_question" value="random" class="button" style="margin-left:8px"
      onclick="document.querySelector('[name=question_id]').value='<?= esc_js($pool[0]['id'] ?? 0) ?>'">
      Случайный
    </button>
  </form>

  <h2 style="margin-top:30px">История</h2>
  <table class="widefat striped">
    <thead><tr><th>Неделя</th><th>Вопрос</th><th>Ответов</th><th>Верных %</th></tr></thead>
    <tbody>
    <?php foreach ($history as $h):
      $st = json_decode($h['stats_json'] ?? '{}', true) ?: [];
    ?>
      <tr>
        <td><?= esc_html($h['week_start']) ?></td>
        <td><?= esc_html(mb_substr($h['question'], 0, 70)) ?>…</td>
        <td><?= (int)($st['total_answers'] ?? 0) ?></td>
        <td><?= (int)($st['correct_pct'] ?? 0) ?>%</td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
