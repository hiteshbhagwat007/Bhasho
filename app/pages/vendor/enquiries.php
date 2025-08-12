<?php
require_once __DIR__ . '/../../lib/helpers.php';
require_once __DIR__ . '/../../lib/db.php';

require_role(USER_ROLE_VENDOR);
ensure_csrf();

$user = current_user();
$vendor = run_prepared_query('SELECT id FROM vendors WHERE user_id = :uid', ['uid' => $user['id']])->fetch();
$vendorId = $vendor['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enquiryId = (int)($_POST['enquiry_id'] ?? 0);
    $message = trim($_POST['message'] ?? '');
    $quote = (float)($_POST['quote_total'] ?? 0);
    if ($vendorId && $enquiryId && $message) {
        run_prepared_query('INSERT INTO enquiry_responses (enquiry_id, vendor_id, message, quote_total, status) VALUES (:eid, :vid, :m, :q, :st)', [
            'eid' => $enquiryId, 'vid' => $vendorId, 'm' => $message, 'q' => $quote, 'st' => 'offer'
        ]);
        run_prepared_query('UPDATE enquiries SET status=:st WHERE id=:id AND vendor_id=:vid', ['st' => ENQUIRY_RESPONDED, 'id' => $enquiryId, 'vid' => $vendorId]);
        set_flash('success', 'Response sent.');
        redirect('index.php?route=vendor/enquiries');
    }
}

$enquiries = $vendorId ? run_prepared_query('SELECT e.*, u.name distributor_name FROM enquiries e JOIN users u ON e.distributor_id = u.id WHERE e.vendor_id = :vid ORDER BY e.id DESC', ['vid' => $vendorId])->fetchAll() : [];
$itemsByEnquiry = [];
if ($enquiries) {
    $ids = implode(',', array_map('intval', array_column($enquiries, 'id')));
    $items = run_prepared_query("SELECT ei.*, p.name product_name FROM enquiry_items ei JOIN products p ON p.id = ei.product_id WHERE ei.enquiry_id IN ($ids)")->fetchAll();
    foreach ($items as $it) {
        $itemsByEnquiry[$it['enquiry_id']][] = $it;
    }
}
?>
<h1>Enquiries</h1>
<?php foreach ($enquiries as $e): ?>
  <div class="card">
    <h3>Enquiry #<?= (int)$e['id'] ?> from <?= sanitize($e['distributor_name']) ?> (Status: <?= sanitize($e['status']) ?>)</h3>
    <ul>
      <?php foreach ($itemsByEnquiry[$e['id']] ?? [] as $it): ?>
        <li><?= sanitize($it['product_name']) ?> — Qty: <?= (int)$it['quantity'] ?></li>
      <?php endforeach; ?>
    </ul>
    <p>Requirements: <?= nl2br(sanitize($e['requirements'])) ?></p>
    <form method="post" class="inline">
      <?= csrf_input_field() ?>
      <input type="hidden" name="enquiry_id" value="<?= (int)$e['id'] ?>">
      <input type="number" step="0.01" name="quote_total" placeholder="Quote total">
      <input type="text" name="message" placeholder="Message/terms" required>
      <button type="submit">Send Offer</button>
    </form>
  </div>
<?php endforeach; ?>