<?php
require_once __DIR__ . '/../../lib/helpers.php';
require_once __DIR__ . '/../../lib/db.php';

$rows = run_prepared_query("SELECT id, email, name, status FROM users WHERE role='distributor' ORDER BY id DESC")->fetchAll();
?>
<h1>Distributors</h1>
<table class="table">
  <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Status</th></tr></thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr>
      <td><?= (int)$r['id'] ?></td>
      <td><?= sanitize($r['name'] ?? '-') ?></td>
      <td><?= sanitize($r['email']) ?></td>
      <td><?= sanitize($r['status']) ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>