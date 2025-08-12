<?php
require_once __DIR__ . '/../../lib/helpers.php';
require_once __DIR__ . '/../../lib/db.php';

ensure_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = (int)($_POST['product_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if ($productId && in_array($action, ['approve','reject'], true)) {
        $status = $action === 'approve' ? PRODUCT_APPROVED : PRODUCT_REJECTED;
        run_prepared_query('UPDATE products SET status = :s WHERE id = :id', ['s' => $status, 'id' => $productId]);
        set_flash('success', 'Product ' . $status);
        redirect('index.php?route=admin/products');
    }
}

$products = run_prepared_query("SELECT p.id, p.name, p.status, p.price, p.stock, u.name vendor_name FROM products p JOIN vendors v ON p.vendor_id = v.id JOIN users u ON v.user_id = u.id ORDER BY p.id DESC")->fetchAll();
?>
<h1>Product Approvals</h1>
<table class="table">
  <thead><tr><th>ID</th><th>Name</th><th>Vendor</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr></thead>
  <tbody>
  <?php foreach ($products as $p): ?>
    <tr>
      <td><?= (int)$p['id'] ?></td>
      <td><?= sanitize($p['name']) ?></td>
      <td><?= sanitize($p['vendor_name'] ?? '') ?></td>
      <td><?= number_format((float)$p['price'], 2) ?></td>
      <td><?= (int)$p['stock'] ?></td>
      <td><?= sanitize($p['status']) ?></td>
      <td>
        <form method="post" style="display:inline-block;"><?= csrf_input_field() ?><input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>"><input type="hidden" name="action" value="approve"><button>Approve</button></form>
        <form method="post" style="display:inline-block;"><?= csrf_input_field() ?><input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>"><input type="hidden" name="action" value="reject"><button class="danger">Reject</button></form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>