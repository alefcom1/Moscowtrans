<?php defined('ABSPATH') || exit;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer('rtap_settings')) {
    update_option('rtap_tms_url', esc_url_raw($_POST['tms_url'] ?? ''));
    update_option('rtap_tms_key', sanitize_text_field($_POST['tms_key'] ?? ''));
    update_option('rtap_min_cert', absint($_POST['min_cert'] ?? 70));
    update_option('rtap_min_inter', absint($_POST['min_inter'] ?? 60));
    update_option('rtap_min_adv',   absint($_POST['min_adv']   ?? 70));
    echo '<div class="notice notice-success"><p>Настройки сохранены.</p></div>';
}

$tms_url  = get_option('rtap_tms_url',  'https://tms.perevod4.ru/api/v1/public/translators');
$tms_key  = get_option('rtap_tms_key',  '');
$min_cert = get_option('rtap_min_cert', 70);
$min_inter= get_option('rtap_min_inter',60);
$min_adv  = get_option('rtap_min_adv',  70);
?>
<div class="wrap">
  <h1>RTAP — Настройки</h1>
  <form method="post">
    <?php wp_nonce_field('rtap_settings'); ?>

    <h2>TMS Интеграция (tms.perevod4.ru)</h2>
    <table class="form-table">
      <tr>
        <th><label for="tms_url">TMS API URL</label></th>
        <td><input type="url" id="tms_url" name="tms_url" value="<?= esc_attr($tms_url) ?>"
                   class="regular-text" placeholder="https://tms.perevod4.ru/api/v1/public/translators" /></td>
      </tr>
      <tr>
        <th><label for="tms_key">X-API-Key</label></th>
        <td><input type="password" id="tms_key" name="tms_key" value="<?= esc_attr($tms_key) ?>"
                   class="regular-text" autocomplete="off" /></td>
      </tr>
    </table>

    <h2>Пороги баллов</h2>
    <table class="form-table">
      <tr>
        <th>Минимум для сертификата (%)</th>
        <td><input type="number" name="min_cert" value="<?= esc_attr($min_cert) ?>" min="50" max="100" /></td>
      </tr>
      <tr>
        <th>Beginner → Intermediate (%)</th>
        <td><input type="number" name="min_inter" value="<?= esc_attr($min_inter) ?>" min="50" max="100" /></td>
      </tr>
      <tr>
        <th>Intermediate → Advanced (%)</th>
        <td><input type="number" name="min_adv" value="<?= esc_attr($min_adv) ?>" min="50" max="100" /></td>
      </tr>
    </table>

    <?php submit_button('Сохранить настройки'); ?>
  </form>
</div>
