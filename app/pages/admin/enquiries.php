<?php
require_once __DIR__ . '/../../lib/helpers.php';
require_once __DIR__ . '/../../lib/db.php';

$rows = run_prepared_query('SELECT e.id, e.status, e.created_at, dv.brand_name, du.name dist_name FROM enquiries e JOIN vendors dv ON e.vendor_id = dv.id JOIN users du ON e.distributor_id = du.id ORDER BY e.id DESC')->fetchAll();
?>
<h1>All Enquiries</h1>
<table class="table">
  <thead><tr><th>ID</th><th>Brand</th><th>Distributor</th><th>Status</th><th>Created</th></tr></thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr>
      <td><?= (int)$r['id'] ?></td>
      <td><?= sanitize($r['brand_name']) ?></td>
      <td><?= sanitize($r['dist_name']) ?></td>
      <td><?= sanitize($r['status']) ?></td>
      <td><?= sanitize($r['created_at']) ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>