<?php
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/db.php';

ensure_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $user = authenticate_user($email, $password);
    if ($user) {
        login_user($user);
        if ($user['role'] === USER_ROLE_ADMIN) {
            redirect('index.php?route=admin/dashboard');
        } elseif ($user['role'] === USER_ROLE_VENDOR) {
            redirect('index.php?route=vendor/dashboard');
        } elseif ($user['role'] === USER_ROLE_DISTRIBUTOR) {
            redirect('index.php');
        } else {
            redirect('index.php');
        }
    } else {
        set_flash('error', 'Invalid credentials or inactive account.');
        redirect('index.php?route=login');
    }
}

?>
<?php include __DIR__ . '/../partials/header.php'; ?>
<section class="auth">
  <h1>Login</h1>
  <form method="post" action="">
    <?= csrf_input_field() ?>
    <label>Email
      <input type="email" name="email" required>
    </label>
    <label>Password
      <input type="password" name="password" required>
    </label>
    <button type="submit">Login</button>
  </form>
  <div class="auth-links">
    <p>New Vendor? <a href="<?= APP_BASE_URL ?>/index.php?route=vendor/onboard">Start Onboarding</a></p>
    <p>New Distributor? <a href="<?= APP_BASE_URL ?>/index.php?route=distributor/register">Register</a></p>
  </div>
</section>
<?php include __DIR__ . '/../partials/footer.php'; ?>