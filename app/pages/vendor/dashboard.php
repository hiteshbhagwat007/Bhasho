<?php
require_once __DIR__ . '/../../lib/helpers.php';
require_once __DIR__ . '/../../lib/db.php';

$user = current_user();
$vendor = run_prepared_query('SELECT id FROM vendors WHERE user_id = :uid', ['uid' => $user['id']])->fetch();
$vendorId = $vendor['id'] ?? 0;

$stats = [
    'products' => $vendorId ? (int)run_prepared_query('SELECT COUNT(*) c FROM products WHERE vendor_id = :vid', ['vid' => $vendorId])->fetch()['c'] : 0,
    'enquiries' => $vendorId ? (int)run_prepared_query('SELECT COUNT(*) c FROM enquiries WHERE vendor_id = :vid', ['vid' => $vendorId])->fetch()['c'] : 0,
];
?>
<h1>Vendor Dashboard</h1>
<div class="grid stats">
  <div class="card"><h3>Your Products</h3><p><?= $stats['products'] ?></p></div>
  <div class="card"><h3>Your Enquiries</h3><p><?= $stats['enquiries'] ?></p></div>
</div>
<p>Complete your <a href="<?= APP_BASE_URL ?>/index.php?route=vendor/onboard">onboarding</a> if not done.</p>