<?php
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/lib/helpers.php';
require_once __DIR__ . '/../app/lib/auth.php';
require_once __DIR__ . '/../app/lib/db.php';

$route = $_GET['route'] ?? '';

function render(string $pagePath, array $vars = []): void {
    extract($vars);
    include __DIR__ . '/../app/partials/header.php';
    include $pagePath;
    include __DIR__ . '/../app/partials/footer.php';
}

switch ($route) {
    case 'login':
        include __DIR__ . '/../app/pages/login.php';
        break;
    case 'logout':
        include __DIR__ . '/../app/pages/logout.php';
        break;

    // Admin routes
    case 'admin/dashboard':
        require_role(USER_ROLE_ADMIN);
        render(__DIR__ . '/../app/pages/admin/dashboard.php');
        break;
    case 'admin/vendors':
        require_role(USER_ROLE_ADMIN);
        render(__DIR__ . '/../app/pages/admin/vendors.php');
        break;
    case 'admin/products':
        require_role(USER_ROLE_ADMIN);
        render(__DIR__ . '/../app/pages/admin/products.php');
        break;
    case 'admin/enquiries':
        require_role(USER_ROLE_ADMIN);
        render(__DIR__ . '/../app/pages/admin/enquiries.php');
        break;
    case 'admin/distributors':
        require_role(USER_ROLE_ADMIN);
        render(__DIR__ . '/../app/pages/admin/distributors.php');
        break;
    case 'admin/analytics':
        require_role(USER_ROLE_ADMIN);
        render(__DIR__ . '/../app/pages/admin/analytics.php');
        break;
    case 'admin/settings':
        require_role(USER_ROLE_ADMIN);
        render(__DIR__ . '/../app/pages/admin/settings.php');
        break;

    // Vendor routes
    case 'vendor/dashboard':
        require_role(USER_ROLE_VENDOR);
        render(__DIR__ . '/../app/pages/vendor/dashboard.php');
        break;
    case 'vendor/onboard':
        render(__DIR__ . '/../app/pages/vendor/onboard.php');
        break;
    case 'vendor/products':
        require_role(USER_ROLE_VENDOR);
        render(__DIR__ . '/../app/pages/vendor/products.php');
        break;
    case 'vendor/enquiries':
        require_role(USER_ROLE_VENDOR);
        render(__DIR__ . '/../app/pages/vendor/enquiries.php');
        break;
    case 'vendor/reports':
        require_role(USER_ROLE_VENDOR);
        render(__DIR__ . '/../app/pages/vendor/reports.php');
        break;

    // Distributor routes
    case 'distributor/dashboard':
        require_role(USER_ROLE_DISTRIBUTOR);
        render(__DIR__ . '/../app/pages/distributor/dashboard.php');
        break;
    case 'distributor/enquiries':
        require_role(USER_ROLE_DISTRIBUTOR);
        render(__DIR__ . '/../app/pages/distributor/my_enquiries.php');
        break;
    case 'distributor/profile':
        require_role(USER_ROLE_DISTRIBUTOR);
        render(__DIR__ . '/../app/pages/distributor/profile.php');
        break;

    // Common routes
    case 'browse':
        render(__DIR__ . '/../app/pages/distributor/browse.php');
        break;
    case 'enquiry/list':
        render(__DIR__ . '/../app/pages/distributor/enquiry_list.php');
        break;

    default:
        // Landing/Home
        render(__DIR__ . '/../app/pages/distributor/browse.php');
        break;
}