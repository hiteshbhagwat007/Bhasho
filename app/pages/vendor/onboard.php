<?php
require_once __DIR__ . '/../../lib/helpers.php';
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/auth.php';

ensure_csrf();

$step = max(1, min(5, (int)($_GET['step'] ?? 1)));

// If not logged in, show account creation step
if (!is_logged_in()) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 1) {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $name = trim($_POST['name'] ?? '');
        if ($email && $password) {
            $exists = find_user_by_email($email);
            if ($exists) {
                set_flash('error', 'Email already registered.');
                redirect('index.php?route=vendor/onboard&step=1');
            }
            run_prepared_query('INSERT INTO users (role, email, password_hash, name, status) VALUES ("vendor", :e, :ph, :n, :st)', [
                'e' => $email,
                'ph' => password_hash($password, PASSWORD_DEFAULT),
                'n' => $name,
                'st' => STATUS_PENDING,
            ]);
            $userId = (int)get_pdo_connection()->lastInsertId();
            $user = run_prepared_query('SELECT * FROM users WHERE id=:id', ['id' => $userId])->fetch();
            login_user($user);
            redirect('index.php?route=vendor/onboard&step=2');
        }
    }
    include __DIR__ . '/../../partials/header.php';
    ?>
    <h1>Vendor Onboarding</h1>
    <div class="stepper">
      <div class="steps">
        <span class="step <?= $step===1?'active':'' ?>">1. Account</span>
        <span class="step">2. Brand</span>
        <span class="step">3. KYC & Bank</span>
        <span class="step">4. Catalog</span>
        <span class="step">5. Agreement</span>
      </div>
      <form method="post">
        <?= csrf_input_field() ?>
        <label>Name <input type="text" name="name" required></label>
        <label>Email <input type="email" name="email" required></label>
        <label>Password <input type="password" name="password" required></label>
        <button type="submit">Create Account & Continue</button>
      </form>
    </div>
    <?php include __DIR__ . '/../../partials/footer.php';
    return;
}

// Logged-in vendor flow below
$user = current_user();
$vendor = run_prepared_query('SELECT * FROM vendors WHERE user_id = :uid', ['uid' => $user['id']])->fetch();
if (!$vendor) {
    run_prepared_query('INSERT INTO vendors (user_id, onboarding_status) VALUES (:uid, :st)', ['uid' => $user['id'], 'st' => ONBOARDING_DRAFT]);
    $vendor = run_prepared_query('SELECT * FROM vendors WHERE user_id = :uid', ['uid' => $user['id']])->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 2) {
        $brandName = trim($_POST['brand_name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        run_prepared_query('UPDATE vendors SET brand_name=:bn, description=:d WHERE id=:id', [
            'bn' => $brandName,
            'd' => $description,
            'id' => $vendor['id'],
        ]);
        set_flash('success', 'Brand details saved.');
        redirect('index.php?route=vendor/onboard&step=3');
    } elseif ($step === 3) {
        $gst = trim($_POST['gst'] ?? '');
        $pan = trim($_POST['pan'] ?? '');
        $bank = trim($_POST['bank_account'] ?? '');
        run_prepared_query('UPDATE vendors SET gst=:gst, pan=:pan, bank_account=:ba WHERE id=:id', [
            'gst' => $gst, 'pan' => $pan, 'ba' => $bank, 'id' => $vendor['id']
        ]);
        set_flash('success', 'KYC & Bank info saved.');
        redirect('index.php?route=vendor/onboard&step=4');
    } elseif ($step === 4) {
        set_flash('success', 'Initial catalog uploaded (placeholder).');
        redirect('index.php?route=vendor/onboard&step=5');
    } elseif ($step === 5) {
        run_prepared_query('UPDATE vendors SET onboarding_status=:st WHERE id=:id', ['st' => ONBOARDING_SUBMITTED, 'id' => $vendor['id']]);
        set_flash('success', 'Submitted for approval.');
        redirect('index.php?route=vendor/dashboard');
    }
}
?>
<h1>Vendor Onboarding</h1>
<div class="stepper">
  <div class="steps">
    <span class="step <?= $step===1?'active':'' ?>">1. Account</span>
    <span class="step <?= $step===2?'active':'' ?>">2. Brand</span>
    <span class="step <?= $step===3?'active':'' ?>">3. KYC & Bank</span>
    <span class="step <?= $step===4?'active':'' ?>">4. Catalog</span>
    <span class="step <?= $step===5?'active':'' ?>">5. Agreement</span>
  </div>

  <?php if ($step === 1): ?>
    <p>Your account is created. Proceed to add brand details.</p>
    <a class="btn" href="<?= APP_BASE_URL ?>/index.php?route=vendor/onboard&step=2">Next</a>
  <?php elseif ($step === 2): ?>
    <form method="post"><?= csrf_input_field() ?>
      <label>Brand Name <input type="text" name="brand_name" value="<?= sanitize($vendor['brand_name'] ?? '') ?>" required></label>
      <label>Description <textarea name="description" rows="4"><?= sanitize($vendor['description'] ?? '') ?></textarea></label>
      <button type="submit">Save & Continue</button>
    </form>
  <?php elseif ($step === 3): ?>
    <form method="post"><?= csrf_input_field() ?>
      <label>GST <input type="text" name="gst" value="<?= sanitize($vendor['gst'] ?? '') ?>"></label>
      <label>PAN <input type="text" name="pan" value="<?= sanitize($vendor['pan'] ?? '') ?>"></label>
      <label>Bank Account <input type="text" name="bank_account" value="<?= sanitize($vendor['bank_account'] ?? '') ?>"></label>
      <button type="submit">Save & Continue</button>
    </form>
  <?php elseif ($step === 4): ?>
    <p>Upload your initial products (placeholder).</p>
    <form method="post"><?= csrf_input_field() ?><button type="submit">Mark as Uploaded</button></form>
  <?php elseif ($step === 5): ?>
    <p>Agree to the platform terms and submit for approval.</p>
    <form method="post"><?= csrf_input_field() ?><label><input type="checkbox" required> I agree</label> <button type="submit">Submit for Approval</button></form>
  <?php endif; ?>
</div>