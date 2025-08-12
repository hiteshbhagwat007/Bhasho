<?php
require_once __DIR__ . '/../../lib/helpers.php';
require_once __DIR__ . '/../../lib/db.php';

ensure_csrf();

if (!isset($_SESSION['enquiry_list'])) {
    $_SESSION['enquiry_list'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = (int)($_POST['product_id'] ?? 0);
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));
    if ($productId) {
        $_SESSION['enquiry_list'][$productId] = ($_SESSION['enquiry_list'][$productId] ?? 0) + $quantity;
        set_flash('success', 'Added to enquiry list.');
        redirect('index.php?route=browse');
    }
}

$products = run_prepared_query("SELECT p.*, v.brand_name FROM products p JOIN vendors v ON p.vendor_id = v.id WHERE p.status = 'approved' ORDER BY p.id DESC")->fetchAll();
?>
<h1>Browse Products</h1>
<div class="grid products">
  <?php foreach ($products as $p): ?>
    <div class="card product">
      <h3><?= sanitize($p['name']) ?></h3>
      <p class="muted">Brand: <?= sanitize($p['brand_name'] ?? '') ?></p>
      <p><?= nl2br(sanitize($p['description'])) ?></p>
      <p>Price: <?= number_format((float)$p['price'], 2) ?></p>
      <form method="post">
        <?= csrf_input_field() ?>
        <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
        <input type="number" name="quantity" value="1" min="1">
        <button type="submit">Add to Enquiry</button>
      </form>
    </div>
  <?php endforeach; ?>
</div>
<a class="btn" href="<?= APP_BASE_URL ?>/index.php?route=enquiry/list">Go to Enquiry List (<?= array_sum($_SESSION['enquiry_list']) ?>)</a>