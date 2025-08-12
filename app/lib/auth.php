<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

function find_user_by_email(string $email): ?array {
    $stmt = run_prepared_query('SELECT * FROM users WHERE email = :email LIMIT 1', ['email' => $email]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function authenticate_user(string $email, string $password): ?array {
    $user = find_user_by_email($email);
    if (!$user) {
        return null;
    }
    if (!password_verify($password, $user['password_hash'])) {
        return null;
    }
    if (($user['status'] ?? '') !== STATUS_ACTIVE) {
        return null;
    }
    return $user;
}

function login_user(array $user): void {
    $_SESSION['user'] = [
        'id' => (int)$user['id'],
        'role' => $user['role'],
        'name' => $user['name'] ?? ($user['email'] ?? 'User'),
        'email' => $user['email'] ?? null,
    ];
}

function logout_user(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}