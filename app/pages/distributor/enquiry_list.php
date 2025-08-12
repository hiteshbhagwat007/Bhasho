<?php
require_once __DIR__ . '/../../lib/helpers.php';
require_once __DIR__ . '/../../lib/db.php';

ensure_csrf();

$items = $_SESSION['enquiry_list'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_role(USER_ROLE_DISTRIBUTOR);
    $user = current_user();

    // Group by vendor
    if (!$items) {
        set_flash('error', 'Your enquiry list is empty.');
        redirect('index.php?route=browse');
    }

    $productRows = [];
    if ($items) {
        $ids = implode(',', array_map('intval', array_keys($items)));
        $productRows = run_prepared_query("SELECT id, vendor_id FROM products WHERE id IN ($ids)")->fetchAll();
    }

    $productsByVendor = [];
    foreach ($productRows as $row) {
        $pid = (int)$row['id'];
        $vid = (int)$row['vendor_id'];
        $qty = (int)$items[$pid];
        $productsByVendor[$vid][] = ['product_id' => $pid, 'quantity' => $qty];
    }

    foreach ($productsByVendor as $vendorId => $list) {
        run_prepared_query('INSERT INTO enquiries (distributor_id, vendor_id, status, requirements) VALUES (:d, :v, :s, :r)', [
            'd' => $user['id'], 'v' => $vendorId, 's' => ENQUIRY_SUBMITTED, 'r' => trim($_POST['requirements'] ?? '')
        ]);
        $enquiryId = get_pdo_connection()->lastInsertId();
        foreach ($list as $it) {
            run_prepared_query('INSERT INTO enquiry_items (enquiry_id, product_id, quantity) VALUES (:e, :p, :q)', [
                'e' => $enquiryId, 'p' => $it['product_id'], 'q' => $it['quantity']
            ]);
        }
    }

    $_SESSION['enquiry_list'] = [];
    set_flash('success', 'Enquiry submitted. Vendors will respond soon.');
    redirect('index.php?route=distributor/enquiries');
}

?>
<h1>Enquiry List</h1>
<?php if (!$items): ?>
  <p>Your enquiry list is empty.</p>
  <a class="btn" href="<?= APP_BASE_URL ?>/index.php?route=browse">Browse Products</a>
<?php else: ?>
  <ul>
    <?php
      $ids = implode(',', array_map('intval', array_keys($items)));
      $rows = $ids ? run_prepared_query("SELECT id, name FROM products WHERE id IN ($ids)")->fetchAll() : [];
      $names = [];
      foreach ($rows as $r) { $names[$r['id']] = $r['name']; }
      foreach ($items as $pid => $qty):
    ?>
      <li><?= sanitize($names[$pid] ?? ('Product #' . (int)$pid)) ?> — Qty: <?= (int)$qty ?></li>
    <?php endforeach; ?>
  </ul>
  <form method="post">
    <?= csrf_input_field() ?>
    <label>Additional Requirements
      <textarea name="requirements" rows="4" placeholder="Quantity, customization, delivery needs..."></textarea>
    </label>
    <button type="submit">Submit Enquiry</button>
  </form>
<?php endif; ?>