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
            --admin-sidebar-width: 260px;
            --admin-sidebar-bg: #ffffff;
            --admin-sidebar-hover: #f3f4f6;
            --admin-sidebar-active: #ff3838;
            --admin-topbar-height: 64px;
            --admin-primary: #ff3838;
            --admin-primary-dark: #e02020;
            --admin-sidebar-gradient: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f0f2f5; overflow-x: hidden; }

        /* ── Sidebar ── */
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--admin-sidebar-width);
            height: 100vh;
            background: #14201c;
            color: #fff;
            z-index: 1040;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #595a5b transparent;
            /* border-right: 1px solid #e5e7eb; */
        }
        .admin-sidebar::-webkit-scrollbar { width: 4px; }
        .admin-sidebar::-webkit-scrollbar-track { background: transparent; }
        .admin-sidebar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
        .admin-sidebar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 22px 22px;
            border-bottom: 1px solid #e5e7eb;
            text-decoration: none;
        }
        .sidebar-logo-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-primary-dark) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 18px;
            box-shadow: 0 4px 12px rgba(255, 56, 56, 0.3);
        }
        .sidebar-logo-text { font-size: 17px; font-weight: 700; color: #111827; letter-spacing: -0.5px; }
        .sidebar-logo-text em { font-style: normal; color: var(--admin-primary); }

        .sidebar-nav { padding: 16px 0; }
        .sidebar-nav .nav-section {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #9ca3af;
            padding: 20px 24px 10px;
        }
        .sidebar-nav .nav-item { list-style: none; padding: 0 12px; }
        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            color: #fff;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 10px;
            margin-bottom: 2px;
        }
        .sidebar-nav .nav-link:hover {
            background: var(--admin-sidebar-hover);
            color: #111827;
            transform: translateX(2px);
        }
        .sidebar-nav .nav-link.active {
            background: rgba(255, 56, 56, 0.08);
            color: #ff3838;
            font-weight: 600;
        }
        .sidebar-nav .nav-link.active i {
            color: var(--admin-primary);
        }
        .sidebar-nav .nav-link i { font-size: 18px; width: 22px; text-align: center; transition: all 0.25s; }

        .sidebar-nav .submenu { list-style: none; padding: 0; margin: 0 0 0 12px; }
        .sidebar-nav .submenu .nav-link {
            padding: 9px 14px 9px 44px;
            font-size: 13px;
            border-radius: 8px;
        }
        .sidebar-nav .submenu .nav-link::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #d1d5db;
            flex-shrink: 0;
            transition: all 0.25s;
        }
        .sidebar-nav .submenu .nav-link.active::before,
        .sidebar-nav .submenu .nav-link:hover::before { background: var(--admin-primary); }

        .nav-toggle-icon { margin-left: auto; font-size: 12px; transition: transform 0.25s; }
        .nav-toggle-icon.rotated { transform: rotate(90deg); }

        /* ── Main Content ── */
        .admin-main {
            margin-left: var(--admin-sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ── Top Bar ── */
        .admin-topbar {
            height: var(--admin-topbar-height);
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 1030;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .admin-topbar .sidebar-toggle {
            display: none;
            border: none;
            background: none;
            font-size: 22px;
            color: #4a5568;
            cursor: pointer;
            padding: 6px;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .admin-topbar .sidebar-toggle:hover { background: #f3f4f6; }
        .admin-topbar .topbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .admin-topbar .admin-info {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 500;
            color: #2d3748;
        }
        .admin-topbar .admin-info .avatar {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-primary-dark) 100%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(255, 56, 56, 0.25);
        }
        .admin-topbar .logout-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            color: #e53e3e;
            text-decoration: none;
            transition: all 0.2s;
            border: 1px solid transparent;
        }
        .admin-topbar .logout-btn:hover { background: #fff5f5; border-color: #fecaca; }

        /* ── Flash Messages ── */
        .admin-flash { padding: 0 28px; margin-top: 16px; }
        .admin-flash .alert { border-radius: 10px; font-size: 14px; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }

        /* ── Content ── */
        #content { padding: 28px; }

        /* ── Mobile Overlay ── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
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
        .card {
            background: #05516008 !important;
        }
    </style>

</head>
<body>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ═══ SIDEBAR ═══ -->
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon"><i class="bi bi-box-seam-fill"></i></div>
        <div class="sidebar-logo-text"><em>CM</em> Admin</div>
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

            <li class="nav-item">
                <a href="<?= APP_URL ?>/admin/support" class="nav-link <?= (strpos($currentUrl, '/admin/support') !== false) ? 'active' : '' ?>">
                    <i class="bi bi-headset"></i> Support Tickets
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
            <a href="<?= APP_URL ?>" class="logout-btn" target="_blank" style="background:var(--admin-sidebar-hover);color:var(--text-dark);margin-right:8px;">
                <i class="bi bi-globe"></i> View Website
            </a>
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
