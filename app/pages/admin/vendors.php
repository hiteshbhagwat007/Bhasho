<?php
require_once __DIR__ . '/../../lib/helpers.php';
require_once __DIR__ . '/../../lib/db.php';

ensure_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vendorUserId = (int)($_POST['user_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if ($vendorUserId && in_array($action, ['approve','suspend','delete'], true)) {
        if ($action === 'approve') {
            run_prepared_query("UPDATE users SET status = :s WHERE id = :id AND role='vendor'", ['s' => STATUS_ACTIVE, 'id' => $vendorUserId]);
        } elseif ($action === 'suspend') {
            run_prepared_query("UPDATE users SET status = :s WHERE id = :id AND role='vendor'", ['s' => STATUS_SUSPENDED, 'id' => $vendorUserId]);
        } elseif ($action === 'delete') {
            run_prepared_query("DELETE FROM users WHERE id = :id AND role='vendor'", ['id' => $vendorUserId]);
        }
        set_flash('success', 'Action completed.');
        redirect('index.php?route=admin/vendors');
    }
}

$vendors = run_prepared_query("SELECT u.id, u.email, u.name, u.status, v.brand_name, v.onboarding_status FROM users u LEFT JOIN vendors v ON v.user_id = u.id WHERE u.role='vendor' ORDER BY u.id DESC")->fetchAll();
?>
<h1>Vendors</h1>
<table class="table">
  <thead>
    <tr><th>ID</th><th>Brand</th><th>Email</th><th>Status</th><th>Onboarding</th><th>Actions</th></tr>
  </thead>
  <tbody>
  <?php foreach ($vendors as $v): ?>
    <tr>
      <td><?= (int)$v['id'] ?></td>
      <td><?= sanitize($v['brand_name'] ?? '-') ?></td>
      <td><?= sanitize($v['email']) ?></td>
      <td><?= sanitize($v['status']) ?></td>
      <td><?= sanitize($v['onboarding_status'] ?? '-') ?></td>
      <td>
        <form method="post" style="display:inline-block;"><?= csrf_input_field() ?><input type="hidden" name="user_id" value="<?= (int)$v['id'] ?>"><input type="hidden" name="action" value="approve"><button>Approve</button></form>
        <form method="post" style="display:inline-block;"><?= csrf_input_field() ?><input type="hidden" name="user_id" value="<?= (int)$v['id'] ?>"><input type="hidden" name="action" value="suspend"><button> Suspend </button></form>
        <form method="post" style="display:inline-block;" onsubmit="return confirm('Delete vendor?');"><?php echo csrf_input_field(); ?><input type="hidden" name="user_id" value="<?= (int)$v['id'] ?>"><input type="hidden" name="action" value="delete"><button class="danger"> Delete </button></form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>