<?php
require_once __DIR__ . '/../lib/helpers.php';
$user = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-Vendor Platform</title>
    <link rel="stylesheet" href="<?= APP_BASE_URL ?>/assets/css/app.css">
</head>
<body>
<?php include __DIR__ . '/nav.php'; ?>
<main class="container">
<?php if ($msg = get_flash('success')): ?>
    <div class="flash success"><?= sanitize($msg) ?></div>
<?php endif; ?>
<?php if ($msg = get_flash('error')): ?>
    <div class="flash error"><?= sanitize($msg) ?></div>
<?php endif; ?>