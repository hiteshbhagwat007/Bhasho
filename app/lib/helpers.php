<?php

require_once __DIR__ . '/../config/config.php';

function redirect(string $path): void {
    $url = APP_BASE_URL . '/' . ltrim($path, '/');
    header('Location: ' . $url);
    exit;
}

function sanitize(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function ensure_csrf(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(400);
            echo 'Invalid CSRF token.';
            exit;
        }
    }
}

function set_flash(string $key, string $message): void {
    $_SESSION['flash'][$key] = $message;
}

function get_flash(string $key): ?string {
    if (!empty($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool {
    return current_user() !== null;
}

function current_user_role(): ?string {
    return $_SESSION['user']['role'] ?? null;
}

function require_login(): void {
    if (!is_logged_in()) {
        set_flash('error', 'Please login first.');
        redirect('index.php?route=login');
    }
}

function require_role(string $role): void {
    require_login();
    if (current_user_role() !== $role) {
        http_response_code(403);
        echo 'Access denied.';
        exit;
    }
}

function require_any_role(array $roles): void {
    require_login();
    if (!in_array(current_user_role(), $roles, true)) {
        http_response_code(403);
        echo 'Access denied.';
        exit;
    }
}