<?php
require_once __DIR__ . '/../lib/helpers.php';
$role = current_user_role();
?>
<header class="header">
  <div class="container header-inner">
    <a class="brand" href="<?= APP_BASE_URL ?>/index.php">MV</a>
    <nav class="nav">
      <ul>
        <?php if (!$role): ?>
          <li><a href="<?= APP_BASE_URL ?>/index.php">Home</a></li>
          <li><a href="<?= APP_BASE_URL ?>/index.php?route=browse">Categories</a></li>
          <li><a href="#">About</a></li>
          <li><a href="#">Contact</a></li>
          <li><a class="btn" href="<?= APP_BASE_URL ?>/index.php?route=login">Login/Register</a></li>
        <?php elseif ($role === USER_ROLE_ADMIN): ?>
          <li><a href="<?= APP_BASE_URL ?>/index.php?route=admin/dashboard">Dashboard</a></li>
          <li><a href="<?= APP_BASE_URL ?>/index.php?route=admin/vendors">Vendors</a></li>
          <li><a href="<?= APP_BASE_URL ?>/index.php?route=admin/distributors">Distributors</a></li>
          <li><a href="<?= APP_BASE_URL ?>/index.php?route=admin/products">Products</a></li>
          <li><a href="<?= APP_BASE_URL ?>/index.php?route=admin/enquiries">Enquiries</a></li>
          <li><a href="<?= APP_BASE_URL ?>/index.php?route=admin/analytics">Analytics</a></li>
          <li><a href="<?= APP_BASE_URL ?>/index.php?route=admin/settings">Settings</a></li>
          <li><a class="btn" href="<?= APP_BASE_URL ?>/index.php?route=logout">Logout</a></li>
        <?php elseif ($role === USER_ROLE_VENDOR): ?>
          <li><a href="<?= APP_BASE_URL ?>/index.php?route=vendor/dashboard">Dashboard</a></li>
          <li><a href="<?= APP_BASE_URL ?>/index.php?route=vendor/onboard">Profile</a></li>
          <li><a href="<?= APP_BASE_URL ?>/index.php?route=vendor/products">Products</a></li>
          <li><a href="<?= APP_BASE_URL ?>/index.php?route=vendor/enquiries">Enquiries</a></li>
          <li><a href="<?= APP_BASE_URL ?>/index.php?route=vendor/reports">Reports</a></li>
          <li><a class="btn" href="<?= APP_BASE_URL ?>/index.php?route=logout">Logout</a></li>
        <?php elseif ($role === USER_ROLE_DISTRIBUTOR): ?>
          <li><a href="<?= APP_BASE_URL ?>/index.php">Home</a></li>
          <li><a href="<?= APP_BASE_URL ?>/index.php?route=browse">Categories</a></li>
          <li><a href="<?= APP_BASE_URL ?>/index.php?route=enquiry/list">Enquiry List</a></li>
          <li><a href="<?= APP_BASE_URL ?>/index.php?route=distributor/enquiries">My Enquiries</a></li>
          <li><a href="<?= APP_BASE_URL ?>/index.php?route=distributor/profile">Profile</a></li>
          <li><a class="btn" href="<?= APP_BASE_URL ?>/index.php?route=logout">Logout</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</header>