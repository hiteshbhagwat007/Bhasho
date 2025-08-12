<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/helpers.php';

logout_user();
set_flash('success', 'You have been logged out.');
redirect('index.php');