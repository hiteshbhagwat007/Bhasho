<?php
require_once __DIR__ . '/../../lib/helpers.php';
require_once __DIR__ . '/../../lib/db.php';

ensure_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'site_name' => trim($_POST['site_name'] ?? 'Multi-Vendor Platform'),
        'homepage_banner' => trim($_POST['homepage_banner'] ?? ''),
    ];
    foreach ($settings as $key => $value) {
        run_prepared_query('INSERT INTO site_settings(`key`,`value`) VALUES (:k,:v) ON DUPLICATE KEY UPDATE `value`=:v', ['k' => $key, 'v' => $value]);
    }
    set_flash('success', 'Settings saved.');
    redirect('index.php?route=admin/settings');
}

$settingsRows = run_prepared_query('SELECT `key`,`value` FROM site_settings')->fetchAll();
$settings = [];
foreach ($settingsRows as $row) { $settings[$row['key']] = $row['value']; }
?>
<h1>Settings</h1>
<form method="post">
  <?= csrf_input_field() ?>
  <label>Site Name <input type="text" name="site_name" value="<?= sanitize($settings['site_name'] ?? 'Multi-Vendor Platform') ?>"></label>
  <label>Homepage Banner Text <input type="text" name="homepage_banner" value="<?= sanitize($settings['homepage_banner'] ?? '') ?>"></label>
  <button type="submit">Save</button>
</form>