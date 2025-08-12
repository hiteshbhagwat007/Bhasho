<?php

// Application configuration

// Set default timezone
ini_set('date.timezone', 'UTC');

// Sessions
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Base URL detection (adjust if behind a reverse proxy)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$basePath = rtrim(str_replace('index.php', '', $scriptName), '/');

define('APP_BASE_URL', getenv('APP_BASE_URL') ?: $protocol . '://' . $host . $basePath);

// Database configuration - use environment variables when available
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'multivendor');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

// File upload directories
define('UPLOAD_DIR_PRODUCTS', __DIR__ . '/../../uploads/products');
define('UPLOAD_DIR_BRANDS', __DIR__ . '/../../uploads/brands');

// Application constants
const USER_ROLE_ADMIN = 'admin';
const USER_ROLE_VENDOR = 'vendor';
const USER_ROLE_DISTRIBUTOR = 'distributor';

const STATUS_ACTIVE = 'active';
const STATUS_SUSPENDED = 'suspended';
const STATUS_PENDING = 'pending';

const ONBOARDING_DRAFT = 'draft';
const ONBOARDING_SUBMITTED = 'submitted';
const ONBOARDING_APPROVED = 'approved';

const PRODUCT_PENDING = 'pending';
const PRODUCT_APPROVED = 'approved';
const PRODUCT_REJECTED = 'rejected';

const ENQUIRY_SUBMITTED = 'submitted';
const ENQUIRY_RESPONDED = 'responded';
const ENQUIRY_ACCEPTED = 'accepted';
const ENQUIRY_REJECTED = 'rejected';
const ENQUIRY_REVISION_REQUESTED = 'revision_requested';

// CSRF token helper
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function csrf_input_field(): string {
    $token = htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}