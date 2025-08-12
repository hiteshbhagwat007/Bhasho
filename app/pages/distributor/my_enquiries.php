<?php
require_once __DIR__ . '/../../lib/helpers.php';
require_once __DIR__ . '/../../lib/db.php';

require_role(USER_ROLE_DISTRIBUTOR);
ensure_csrf();

$user = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enquiryId = (int)($_POST['enquiry_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if ($enquiryId && in_array($action, ['accept','reject','revision'], true)) {
        $statusMap = ['accept' => ENQUIRY_ACCEPTED, 'reject' => ENQUIRY_REJECTED, 'revision' => ENQUIRY_REVISION_REQUESTED];
        run_prepared_query('UPDATE enquiries SET status=:st WHERE id=:id AND distributor_id=:uid', [
            'st' => $statusMap[$action], 'id' => $enquiryId, 'uid' => $user['id']
        ]);
        if ($action === 'revision') {
            run_prepared_query('INSERT INTO enquiry_responses (enquiry_id, vendor_id, message, quote_total, status) VALUES (:e, (SELECT vendor_id FROM enquiries WHERE id=:e), :m, 0, :st)', [
                'e' => $enquiryId, 'm' => trim($_POST['message'] ?? 'Please revise the offer.'), 'st' => 'revision'
            ]);
        }
        set_flash('success', 'Action applied.');
        redirect('index.php?route=distributor/enquiries');
    }
}

$enquiries = run_prepared_query('SELECT e.*, v.brand_name FROM enquiries e JOIN vendors v ON e.vendor_id = v.id WHERE e.distributor_id = :uid ORDER BY e.id DESC', ['uid' => $user['id']])->fetchAll();
$responsesByEnquiry = [];
if ($enquiries) {
    $ids = implode(',', array_map('intval', array_column($enquiries, 'id')));
    $responses = run_prepared_query("SELECT * FROM enquiry_responses WHERE enquiry_id IN ($ids) ORDER BY id DESC")->fetchAll();
    foreach ($responses as $r) {
        $responsesByEnquiry[$r['enquiry_id']][] = $r;
    }
}
?>
<h1>My Enquiries</h1>
<?php foreach ($enquiries as $e): ?>
  <div class="card">
    <h3>Enquiry #<?= (int)$e['id'] ?> — Brand: <?= sanitize($e['brand_name']) ?> — Status: <?= sanitize($e['status']) ?></h3>
    <h4>Vendor Responses</h4>
    <ul>
      <?php foreach ($responsesByEnquiry[$e['id']] ?? [] as $r): ?>
        <li><?= sanitize($r['status']) ?>: <?= sanitize($r['message']) ?> — Quote: <?= number_format((float)$r['quote_total'], 2) ?></li>
      <?php endforeach; ?>
    </ul>
    <form method="post" class="inline">
      <?= csrf_input_field() ?>
      <input type="hidden" name="enquiry_id" value="<?= (int)$e['id'] ?>">
      <button name="action" value="accept">Accept</button>
      <button name="action" value="reject" class="danger">Reject</button>
      <input type="text" name="message" placeholder="Revision request">
      <button name="action" value="revision">Ask for Revision</button>
    </form>
  </div>
<?php endforeach; ?>