<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($data)) extract($data);

use App\Helpers\Session;

$currentUrl = $_SERVER['REQUEST_URI'] ?? '';
$adminName = Session::get('user_name') ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> - <?= SITE_NAME ?> Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= APP_URL ?>/assets/css/admin.css" rel="stylesheet">
    <style>
        :root {
            --admin-sidebar-width: 250px;
            --admin-sidebar-bg: #1a1d21;
            --admin-sidebar-hover: #2d3139;
            --admin-sidebar-active: #ff3838;
            --admin-topbar-height: 60px;
            --admin-primary: #ff3838;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f1f3f6; overflow-x: hidden; }

        /* ── Sidebar ── */
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--admin-sidebar-width);
            height: 100vh;
            background: var(--admin-sidebar-bg);
            color: #a0aec0;
            z-index: 1040;
            transition: transform 0.3s ease;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #333 transparent;
        }
        .admin-sidebar::-webkit-scrollbar { width: 4px; }
        .admin-sidebar::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .sidebar-logo i { font-size: 26px; color: var(--admin-primary); }
        .sidebar-logo span { font-size: 18px; font-weight: 700; color: #fff; letter-spacing: -0.5px; }
        .sidebar-logo em { font-style: normal; color: var(--admin-primary); }

        .sidebar-nav { padding: 12px 0; }
        .sidebar-nav .nav-section {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #4a5568;
            padding: 16px 20px 8px;
        }
        .sidebar-nav .nav-item { list-style: none; }
        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 20px;
            color: #a0aec0;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        .sidebar-nav .nav-link:hover {
            background: var(--admin-sidebar-hover);
            color: #e2e8f0;
        }
        .sidebar-nav .nav-link.active {
            background: rgba(255, 56, 56, 0.1);
            color: #fff;
            border-left-color: var(--admin-primary);
        }
        .sidebar-nav .nav-link i { font-size: 18px; width: 22px; text-align: center; }

        .sidebar-nav .submenu { list-style: none; padding: 0; }
        .sidebar-nav .submenu .nav-link {
            padding-left: 54px;
            font-size: 13px;
        }
        .sidebar-nav .submenu .nav-link::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #4a5568;
            flex-shrink: 0;
            transition: background 0.2s;
        }
        .sidebar-nav .submenu .nav-link.active::before,
        .sidebar-nav .submenu .nav-link:hover::before { background: var(--admin-primary); }

        .nav-toggle-icon { margin-left: auto; font-size: 12px; transition: transform 0.2s; }
        .nav-toggle-icon.rotated { transform: rotate(90deg); }

        /* ── Main Content ── */
        .admin-main {
            margin-left: var(--admin-sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        /* ── Top Bar ── */
        .admin-topbar {
            height: var(--admin-topbar-height);
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            padding: 0 24px;
            position: sticky;
            top: 0;
            z-index: 1030;
        }
        .admin-topbar .sidebar-toggle {
            display: none;
            border: none;
            background: none;
            font-size: 22px;
            color: #4a5568;
            cursor: pointer;
            padding: 6px;
        }
        .admin-topbar .topbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .admin-topbar .admin-info {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #2d3748;
        }
        .admin-topbar .admin-info .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--admin-primary);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
        }
        .admin-topbar .logout-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            color: #e53e3e;
            text-decoration: none;
            transition: background 0.2s;
        }
        .admin-topbar .logout-btn:hover { background: #fff5f5; }

        /* ── Flash Messages ── */
        .admin-flash { padding: 0 24px; margin-top: 16px; }
        .admin-flash .alert { border-radius: 8px; font-size: 14px; border: none; }

        /* ── Content ── */
        #content { padding: 24px; }

        /* ── Mobile Overlay ── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1035;
        }

        /* ── Responsive ── */
        @media (max-width: 991.98px) {
            .admin-sidebar { transform: translateX(-100%); }
            .admin-sidebar.show { transform: translateX(0); }
            .admin-main { margin-left: 0; }
            .admin-topbar .sidebar-toggle { display: block; }
            .sidebar-overlay.show { display: block; }
        }
    </style>
</head>
<body>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ═══ SIDEBAR ═══ -->
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-logo">
        <i class="bi bi-box-seam-fill"></i>
        <span><em>CM</em> Admin</span>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">Main</div>

        <ul>
            <li class="nav-item">
                <a href="<?= APP_URL ?>/admin/dashboard" class="nav-link <?= (strpos($currentUrl, '/admin/dashboard') !== false) ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a href="#submenuProducts" class="nav-link <?= (strpos($currentUrl, '/admin/products') !== false || strpos($currentUrl, '/admin/product') !== false) ? 'active' : '' ?>" data-bs-toggle="collapse" role="button" aria-expanded="<?= (strpos($currentUrl, '/admin/product') !== false) ? 'true' : 'false' ?>">
                    <i class="bi bi-box-seam"></i> Products
                    <i class="bi bi-chevron-right nav-toggle-icon <?= (strpos($currentUrl, '/admin/product') !== false) ? 'rotated' : '' ?>"></i>
                </a>
                <ul class="submenu collapse <?= (strpos($currentUrl, '/admin/product') !== false) ? 'show' : '' ?>" id="submenuProducts">
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/admin/products" class="nav-link <?= ($currentUrl === '/admin/products' || preg_match('#/admin/products(\?|$)#', $currentUrl)) ? 'active' : '' ?>">
                            All Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= APP_URL ?>/admin/products/create" class="nav-link <?= (strpos($currentUrl, '/admin/products/create') !== false) ? 'active' : '' ?>">
                            Add New
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="<?= APP_URL ?>/admin/categories" class="nav-link <?= (strpos($currentUrl, '/admin/categories') !== false || strpos($currentUrl, '/admin/category') !== false) ? 'active' : '' ?>">
                    <i class="bi bi-tags"></i> Categories
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= APP_URL ?>/admin/brands" class="nav-link <?= (strpos($currentUrl, '/admin/brands') !== false || strpos($currentUrl, '/admin/brand') !== false) ? 'active' : '' ?>">
                    <i class="bi bi-award"></i> Brands
                </a>
            </li>

            <div class="nav-section">Sales</div>

            <li class="nav-item">
                <a href="<?= APP_URL ?>/admin/orders" class="nav-link <?= (strpos($currentUrl, '/admin/orders') !== false || strpos($currentUrl, '/admin/order') !== false) ? 'active' : '' ?>">
                    <i class="bi bi-cart-check"></i> Orders
                </a>
            </li>

            <div class="nav-section">Content</div>

            <li class="nav-item">
                <a href="<?= APP_URL ?>/admin/banners" class="nav-link <?= (strpos($currentUrl, '/admin/banners') !== false || strpos($currentUrl, '/admin/banner') !== false) ? 'active' : '' ?>">
                    <i class="bi bi-image"></i> Banners
                </a>
            </li>

            <div class="nav-section">Users</div>

            <li class="nav-item">
                <a href="<?= APP_URL ?>/admin/users" class="nav-link <?= (strpos($currentUrl, '/admin/users') !== false) ? 'active' : '' ?>">
                    <i class="bi bi-people"></i> Customers
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= APP_URL ?>/admin/contacts" class="nav-link <?= (strpos($currentUrl, '/admin/contacts') !== false) ? 'active' : '' ?>">
                    <i class="bi bi-envelope"></i> Contacts
                </a>
            </li>

            <div class="nav-section">System</div>

            <li class="nav-item">
                <a href="<?= APP_URL ?>/admin/settings" class="nav-link <?= (strpos($currentUrl, '/admin/settings') !== false) ? 'active' : '' ?>">
                    <i class="bi bi-gear"></i> Settings
                </a>
            </li>
        </ul>
    </nav>
</aside>

<!-- ═══ MAIN CONTENT ═══ -->
<div class="admin-main" id="adminMain">

    <!-- Top Bar -->
    <header class="admin-topbar">
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
            <i class="bi bi-list"></i>
        </button>

        <div class="topbar-right">
            <div class="admin-info">
                <div class="avatar"><?= strtoupper(substr($adminName, 0, 1)) ?></div>
                <span><?= htmlspecialchars($adminName) ?></span>
            </div>
            <a href="<?= APP_URL ?>/admin/logout" class="logout-btn">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </header>

    <!-- Flash Messages -->
    <?php
    $flashSuccess = Session::getFlash('success');
    $flashError   = Session::getFlash('error');
    $flashInfo    = Session::getFlash('info');
    ?>
    <?php if ($flashSuccess): ?>
        <div class="admin-flash">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($flashSuccess) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($flashError): ?>
        <div class="admin-flash">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($flashError) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($flashInfo): ?>
        <div class="admin-flash">
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle me-2"></i><?= htmlspecialchars($flashInfo) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Content Wrapper -->
    <div id="content">
