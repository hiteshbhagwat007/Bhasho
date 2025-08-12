<?php
require_once __DIR__ . '/../../lib/helpers.php';
require_once __DIR__ . '/../../lib/db.php';

require_role(USER_ROLE_VENDOR);
ensure_csrf();

$user = current_user();
$vendor = run_prepared_query('SELECT id FROM vendors WHERE user_id = :uid', ['uid' => $user['id']])->fetch();
$vendorId = $vendor['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    if ($vendorId && $name) {
        run_prepared_query('INSERT INTO products (vendor_id, name, description, price, stock, status) VALUES (:vid, :n, :d, :p, :s, :st)', [
            'vid' => $vendorId, 'n' => $name, 'd' => $description, 'p' => $price, 's' => $stock, 'st' => PRODUCT_PENDING
        ]);
        set_flash('success', 'Product submitted for approval.');
        redirect('index.php?route=vendor/products');
    }
}

$products = $vendorId ? run_prepared_query('SELECT * FROM products WHERE vendor_id = :vid ORDER BY id DESC', ['vid' => $vendorId])->fetchAll() : [];
?>
<h1>Your Products</h1>
<section class="card">
  <h3>Add Product</h3>
  <form method="post">
    <?= csrf_input_field() ?>
    <label>Name <input type="text" name="name" required></label>
    <label>Description <textarea name="description" rows="3"></textarea></label>
    <label>Price <input type="number" step="0.01" name="price" required></label>
    <label>Stock <input type="number" name="stock" required></label>
    <button type="submit">Submit for Approval</button>
  </form>
</section>
<section>
  <h3>Product List</h3>
  <table class="table">
    <thead><tr><th>ID</th><th>Name</th><th>Price</th><th>Stock</th><th>Status</th></tr></thead>
    <tbody>
      <?php foreach ($products as $p): ?>
        <tr>
          <td><?= (int)$p['id'] ?></td>
          <td><?= sanitize($p['name']) ?></td>
          <td><?= number_format((float)$p['price'], 2) ?></td>
          <td><?= (int)$p['stock'] ?></td>
          <td><?= sanitize($p['status']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>