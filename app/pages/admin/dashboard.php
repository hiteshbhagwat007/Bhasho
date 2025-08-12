<?php
require_once __DIR__ . '/../../lib/helpers.php';
require_once __DIR__ . '/../../lib/db.php';

$stats = [
    'vendors' => (int)run_prepared_query("SELECT COUNT(*) c FROM users WHERE role='vendor'", [])->fetch()['c'],
    'distributors' => (int)run_prepared_query("SELECT COUNT(*) c FROM users WHERE role='distributor'", [])->fetch()['c'],
    'enquiries' => (int)run_prepared_query("SELECT COUNT(*) c FROM enquiries", [])->fetch()['c']
];
?>
<h1>Admin Dashboard</h1>
<div class="grid stats">
  <div class="card"><h3>Total Vendors</h3><p><?= $stats['vendors'] ?></p></div>
  <div class="card"><h3>Total Distributors</h3><p><?= $stats['distributors'] ?></p></div>
  <div class="card"><h3>Total Enquiries</h3><p><?= $stats['enquiries'] ?></p></div>
</div>