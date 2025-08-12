<?php
require_once __DIR__ . '/../../lib/helpers.php';
require_once __DIR__ . '/../../lib/db.php';
require_once __DIR__ . '/../../lib/auth.php';

ensure_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $company = trim($_POST['company_name'] ?? '');
    if ($email && $password) {
        $exists = find_user_by_email($email);
        if ($exists) {
            set_flash('error', 'Email already registered. Please login.');
            redirect('index.php?route=login');
        }
        run_prepared_query('INSERT INTO users (role, email, password_hash, name, status) VALUES ("distributor", :e, :ph, :n, :st)', [
            'e' => $email,
            'ph' => password_hash($password, PASSWORD_DEFAULT),
            'n' => $name,
            'st' => STATUS_ACTIVE,
        ]);
        $userId = (int)get_pdo_connection()->lastInsertId();
        run_prepared_query('INSERT INTO distributors (user_id, company_name) VALUES (:uid, :cn)', [
            'uid' => $userId,
            'cn' => $company,
        ]);
        $user = run_prepared_query('SELECT * FROM users WHERE id=:id', ['id' => $userId])->fetch();
        login_user($user);
        set_flash('success', 'Welcome! Your account has been created.');
        redirect('index.php?route=browse');
    }
}

include __DIR__ . '/../../partials/header.php';
?>
<h1>Register as Customer (Distributor)</h1>
<form method="post" class="card" style="max-width:520px;">
  <?= csrf_input_field() ?>
  <label>Name <input type="text" name="name" required></label>
  <label>Email <input type="email" name="email" required></label>
  <label>Password <input type="password" name="password" required></label>
  <label>Company Name (optional) <input type="text" name="company_name"></label>
  <button type="submit">Create Account</button>
</form>
<p>Already have an account? <a href="<?= APP_BASE_URL ?>/index.php?route=login">Login</a></p>
<?php include __DIR__ . '/../../partials/footer.php'; ?>