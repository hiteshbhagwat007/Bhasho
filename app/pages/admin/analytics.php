<?php
require_once __DIR__ . '/../../lib/helpers.php';
require_once __DIR__ . '/../../lib/db.php';

$totalEnquiries = (int)run_prepared_query('SELECT COUNT(*) c FROM enquiries')->fetch()['c'];
$enquiriesByStatus = run_prepared_query('SELECT status, COUNT(*) c FROM enquiries GROUP BY status')->fetchAll();
?>
<h1>Analytics</h1>
<div class="grid stats">
  <div class="card"><h3>Total Enquiries</h3><p><?= $totalEnquiries ?></p></div>
</div>
<div class="card">
  <h3>Enquiries by Status</h3>
  <ul>
    <?php foreach ($enquiriesByStatus as $row): ?>
      <li><?= sanitize($row['status']) ?>: <?= (int)$row['c'] ?></li>
    <?php endforeach; ?>
  </ul>
</div>