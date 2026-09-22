<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($data)) extract($data);

use App\Helpers\Auth;
use App\Helpers\Session;
use App\Helpers\CSRF;
use App\Helpers\Sanitizer;

ob_start();
$cartCount = Session::getCartCount();
$cartTotal = Session::getCartTotal();
$wishlistCount = 0;
if (Auth::check()) {
    $wishlistDb = new App\Models\Wishlist();
    $wishlistCount = $wishlistDb->getCount(Auth::id());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?= APP_URL ?>">
    <title><?= htmlspecialchars($pageTitle ?? SITE_NAME) ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= APP_URL ?>/assets/images/favicon.svg">
    <?= CSRF::meta() ?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <link href="<?= APP_URL ?>/assets/css/style.css" rel="stylesheet">

    <style>
        :root {
            --cm-primary: #ff3838;
            --cm-primary-dark: #e02020;
            --cm-primary-light: #ff6b6b;
            --cm-dark: #1a1a2e;
            --cm-dark-light: #22223a;
            --cm-gray-100: #f8f9fa;
            --cm-gray-200: #e9ecef;
            --cm-gray-500: #6c757d;
            --cm-white: #ffffff;
            --cm-shadow: 0 2px 12px rgba(0,0,0,.08);
            --cm-shadow-lg: 0 8px 30px rgba(0,0,0,.12);
            --cm-radius: 8px;
            --cm-transition: .25s ease;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; }
        a { text-decoration: none; transition: color var(--cm-transition); }

        /* ── Top Bar ── */
        .cm-topbar {
            background: var(--cm-dark);
            color: #ccc;
            font-size: 12px;
            line-height: 36px;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }
        .cm-topbar.hidden {
            transform: translateY(-100%);
            opacity: 0;
            pointer-events: none;
        }
        .cm-topbar a { color: #ccc; }
        .cm-topbar a:hover { color: var(--cm-primary-light); }
        .cm-topbar .hotline i { color: var(--cm-primary); margin-right: 4px; }

        /* ── Main Header ── */
        .cm-header {
            background: var(--cm-white);
            box-shadow: var(--cm-shadow);
            position: sticky;
            top: 0;
            z-index: 1030;
        }
        .cm-logo img { max-height: 50px; }
        .cm-logo span {
            font-size: 22px;
            font-weight: 800;
            color: var(--cm-dark);
            letter-spacing: -.5px;
        }
        .cm-logo span em {
            font-style: normal;
            color: var(--cm-primary);
        }

        /* ── Search ── */
        .cm-search { position: relative; flex: 1; max-width: 560px; }
        .cm-search input {
            width: 100%;
            height: 44px;
            border: 2px solid var(--cm-gray-200);
            border-radius: 22px;
            padding: 0 52px 0 18px;
            font-size: 14px;
            transition: border-color var(--cm-transition);
            outline: none;
        }
        .cm-search input:focus { border-color: var(--cm-primary); }
        .cm-search button {
            position: absolute;
            right: 4px;
            top: 50%;
            transform: translateY(-50%);
            width: 38px;
            height: 38px;
            border: none;
            border-radius: 50%;
            background: var(--cm-primary);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background var(--cm-transition);
        }
        .cm-search button:hover { background: var(--cm-primary-dark); }
        #searchResults {
            display: none;
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            background: var(--cm-white);
            border-radius: var(--cm-radius);
            box-shadow: var(--cm-shadow-lg);
            max-height: 400px;
            overflow-y: auto;
            z-index: 999;
        }
        #searchResults .search-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            border-bottom: 1px solid var(--cm-gray-200);
            transition: background var(--cm-transition);
        }
        #searchResults .search-item:hover { background: var(--cm-gray-100); }
        #searchResults .search-item img {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 6px;
        }
        #searchResults .search-item .item-name { font-size: 14px; font-weight: 500; color: var(--cm-dark); }
        #searchResults .search-item .item-price { font-size: 13px; color: var(--cm-primary); font-weight: 600; }
        #searchResults .no-results { padding: 20px; text-align: center; color: var(--cm-gray-500); font-size: 14px; }
        #searchResults.show { display: block; }
        .search-dropdown__header { padding: 10px 16px; border-bottom: 1px solid var(--cm-gray-200); }
        .search-dropdown__list { list-style: none; padding: 0; margin: 0; }
        .search-dropdown__item { border-bottom: 1px solid var(--cm-gray-100); }
        .search-dropdown__item:last-child { border-bottom: none; }
        .search-dropdown__item.active, .search-dropdown__item:hover { background: var(--cm-gray-100); }
        .search-dropdown__link { display: flex; align-items: center; gap: 12px; padding: 10px 16px; text-decoration: none; color: inherit; }
        .search-dropdown__img { width: 48px; height: 48px; object-fit: cover; border-radius: 6px; }
        .search-dropdown__info { flex: 1; }
        .search-dropdown__name { font-size: 14px; font-weight: 500; color: var(--cm-dark); }
        .search-dropdown__name mark { background: #fff3cd; padding: 0 2px; border-radius: 2px; }
        .search-dropdown__category { font-size: 12px; }
        .search-dropdown__price { font-size: 13px; color: var(--cm-primary); font-weight: 600; }
        .search-dropdown__footer { padding: 10px 16px; border-top: 1px solid var(--cm-gray-200); text-align: center; }
        .search-dropdown__view-all { color: var(--cm-primary); font-size: 13px; font-weight: 600; text-decoration: none; }
        .search-dropdown__view-all:hover { text-decoration: underline; }

        /* ── Header Icons ── */
        .cm-header-icons { display: flex; align-items: center; gap: 6px; }
        .cm-icon-btn {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: none;
            background: transparent;
            color: var(--cm-dark);
            font-size: 22px;
            transition: all var(--cm-transition);
        }
        .cm-icon-btn:hover { background: var(--cm-gray-100); color: var(--cm-primary); }
        .cm-icon-btn .badge {
            position: absolute;
            top: 2px;
            right: 2px;
            min-width: 18px;
            height: 18px;
            font-size: 10px;
            font-weight: 700;
            line-height: 18px;
            padding: 0 5px;
            border-radius: 9px;
            background: var(--cm-primary);
            color: #fff;
        }

        /* ── Header Nav Links ── */
        .cm-header-nav {
            display: flex;
            align-items: center;
            gap: 4px;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .cm-header-nav .nav-link {
            color: var(--cm-dark);
            font-size: 14px;
            font-weight: 600;
            padding: 8px 14px;
            border-radius: 6px;
            transition: all var(--cm-transition);
            white-space: nowrap;
        }
        .cm-header-nav .nav-link:hover,
        .cm-header-nav .nav-link.active {
            color: var(--cm-primary);
            background: #fff5f5;
        }
        .cm-header-nav .offer-badge {
            background: #ffc107;
            color: #333;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            margin-left: 4px;
            animation: pulse-badge 2s infinite;
        }
        @keyframes pulse-badge {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        <?php if (($pageTitle ?? '') === 'Home'): ?>
        .cm-header .cm-header-nav { display: none !important; }
        .cm-header .cm-search { margin: 0 auto; max-width: 580px; width: 100%; }
        <?php endif; ?>

        /* ── Category Quick Icons ── */
        .cm-cat-bar {
            background: var(--cm-white);
            border-bottom: 1px solid var(--cm-gray-200);
            overflow-x: auto;
            scrollbar-width: none;
        }
        .cm-cat-bar::-webkit-scrollbar { display: none; }
        .cm-cat-bar .cat-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            padding: 12px 16px;
            font-size: 11px;
            font-weight: 500;
            color: var(--cm-gray-500);
            white-space: nowrap;
            transition: color var(--cm-transition);
            min-width: 80px;
        }
        .cm-cat-bar .cat-item:hover { color: var(--cm-primary); }
        .cm-cat-bar .cat-item .cat-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--cm-gray-100);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            transition: all var(--cm-transition);
        }
        .cm-cat-bar .cat-item:hover .cat-icon {
            background: #fff0f0;
            color: var(--cm-primary);
        }

        /* ── Offcanvas Cart ── */
        #cartDrawer .offcanvas-header {
            background: linear-gradient(135deg, var(--cm-dark) 0%, var(--cm-dark-light) 100%);
            color: #fff;
            padding: 16px 20px;
        }
        #cartDrawer .offcanvas-header .offcanvas-title { color: #fff; }
        #cartDrawer .offcanvas-body { padding: 0; display: flex; flex-direction: column; }
        #cartDrawer .btn-close { filter: invert(1); }
        #cartItems { flex: 1; overflow-y: auto; padding: 12px 16px; }
        .cm-cart-item {
            display: flex;
            gap: 12px;
            padding: 14px 8px;
            border-bottom: 1px solid var(--cm-gray-200);
            border-radius: 8px;
            transition: background var(--cm-transition);
            animation: cartItemIn 0.3s ease;
        }
        .cm-cart-item:hover { background: var(--cm-gray-100); }
        @keyframes cartItemIn {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .cm-cart-item img {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 8px;
            flex-shrink: 0;
        }
        .cm-cart-item .item-info { flex: 1; }
        .cm-cart-item .item-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--cm-dark);
            margin-bottom: 4px;
            line-height: 1.3;
        }
        .cm-cart-item .item-price { font-size: 14px; color: var(--cm-primary); font-weight: 700; }
        .cm-cart-item .item-remove {
            border: none;
            background: none;
            color: var(--cm-gray-500);
            cursor: pointer;
            padding: 4px;
            font-size: 16px;
            transition: color var(--cm-transition);
        }
        .cm-cart-item .item-remove:hover { color: var(--cm-primary); }
        .cm-cart-item .qty-control {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 6px;
        }
        .cm-cart-item .qty-control button {
            width: 28px;
            height: 28px;
            border: 1px solid var(--cm-gray-200);
            border-radius: 50%;
            background: var(--cm-gray-100);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all var(--cm-transition);
        }
        .cm-cart-item .qty-control button:hover { border-color: var(--cm-primary); color: var(--cm-primary); }
        .cm-cart-item .qty-control span {
            font-size: 13px;
            font-weight: 600;
            min-width: 20px;
            text-align: center;
        }
        #cartDrawer .cart-footer {
            border-top: 2px solid var(--cm-gray-200);
            padding: 16px 20px;
            background: var(--cm-gray-100);
        }
        #cartDrawer .cart-footer .subtotal {
            display: flex;
            justify-content: space-between;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 12px;
        }
        #cartDrawer .cart-footer .subtotal span:last-child { color: var(--cm-primary); }
        #cartDrawer .cart-footer .btn-checkout {
            display: block;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: var(--cm-radius);
            background: var(--cm-primary);
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            text-align: center;
            transition: background var(--cm-transition);
        }
        #cartDrawer .cart-footer .btn-checkout:hover { background: var(--cm-primary-dark); }
        .cm-continue-shopping {
            display: block;
            text-align: center;
            padding: 10px;
            font-size: 13px;
            font-weight: 600;
            color: var(--cm-gray-500);
            border-top: 1px solid var(--cm-gray-200);
            transition: color var(--cm-transition);
        }
        .cm-continue-shopping:hover { color: var(--cm-primary); }
        .cm-shipping-bar { margin-bottom: 12px; }
        .cm-shipping-bar .progress { height: 6px; border-radius: 3px; background: var(--cm-gray-200); }
        .cm-shipping-bar .progress-bar { background: var(--cm-primary); border-radius: 3px; transition: width 0.5s ease; }
        .cm-shipping-bar .shipping-text { font-size: 12px; color: var(--cm-gray-500); margin-top: 6px; }
        .cm-shipping-bar .shipping-text strong { color: var(--cm-primary); }
        .cm-cart-empty {
            text-align: center;
            padding: 40px 20px;
            color: var(--cm-gray-500);
        }
        .cm-cart-empty i { font-size: 48px; margin-bottom: 12px; display: block; }

        /* ── Responsive ── */
        @media (max-width: 991px) {
            .cm-topbar .support-info { display: none; }
            .cm-logo span { font-size: 18px; }
        }
        @media (max-width: 767px) {
            .cm-header-icons { gap: 2px; }
            .cm-icon-btn { width: 38px; height: 38px; font-size: 18px; }
            .cm-header-nav { display: none; }
        }
    </style>
</head>
<body>

<!-- ═══ TOP BAR ═══ -->
<div class="cm-topbar">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div class="hotline">
                <i class="bi bi-telephone-fill"></i>
                Hotline: <a href="tel:<?= SITE_PHONE ?>"><?= SITE_PHONE ?></a>
            </div>
            <div class="d-flex align-items-center gap-3 support-info">
                <a href="<?= APP_URL ?>/support" style="text-decoration:none;color:inherit;"><i class="bi bi-headset me-1"></i>Customer Support</a>
                <span>|</span>
                <?php if (Auth::check()): ?>
                    <a href="<?= APP_URL ?>/account/profile" id="headerUserName" style="text-decoration:none;color:inherit;">Welcome, <strong><?= Sanitizer::clean(Auth::name()) ?></strong></a>
                    <?php if (Auth::admin()): ?>
                        <span>|</span>
                        <a href="<?= APP_URL ?>/admin/dashboard"><i class="bi bi-speedometer2 me-1"></i>Admin Panel</a>
                    <?php endif; ?>
                    <span>|</span>
                    <a href="<?= APP_URL ?>/logout">Logout</a>
                <?php else: ?>
                    <a href="<?= APP_URL ?>/login">Login</a>
                    <span>|</span>
                    <a href="<?= APP_URL ?>/register">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ═══ MAIN HEADER ═══ -->
<header class="cm-header py-2">
    <div class="container">
        <div class="d-flex align-items-center gap-3">

            <!-- Logo -->
            <a href="<?= APP_URL ?>" class="cm-logo text-decoration-none d-flex align-items-center gap-2 flex-shrink-0">
                <img src="<?= APP_URL ?>/assets/images/logo-icon.svg" alt="" style="width:36px;height:36px;color:var(--cm-primary)">
                <span><em>Crokerses </em>Mart</span>
            </a>

            <!-- Nav Links (desktop) -->
            <ul class="cm-header-nav d-none d-lg-flex">
                <li class="nav-item">
                    <a class="nav-link" href="<?= APP_URL ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= APP_URL ?>/shop">Shop</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= APP_URL ?>/offer">
                        Offer
                        <span class="offer-badge">HOT</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= APP_URL ?>/about">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= APP_URL ?>/support"><i class="bi bi-headset me-1"></i>Support</a>
                </li>
            </ul>

            <!-- Search (right) -->
            <div class="cm-search ms-auto d-none d-lg-flex" id="liveSearch" data-base-url="<?= APP_URL ?>">
                <form action="<?= APP_URL ?>/search" method="GET" class="d-flex w-100" autocomplete="off">
                    <input type="text" name="q" id="searchInput" placeholder="Search for crockery, dinnerware, kitchenware..." aria-label="Search">
                    <button type="submit" aria-label="Search"><i class="bi bi-search"></i></button>
                </form>
                <div id="searchResults"></div>
            </div>

            <!-- Header Icons -->
            <div class="cm-header-icons">
                <?php if (Auth::check()): ?>
                    <?php
                    $pendingOrderCount = 0;
                    try {
                        $orderModel = new App\Models\Order();
                        $userOrders = $orderModel->getByUserId(Auth::id(), 100);
                        $pendingOrderCount = count(array_filter($userOrders, fn($o) => in_array($o['order_status'] ?? '', ['pending', 'processing'])));
                    } catch (\Throwable $e) {}
                    ?>
                    <a href="<?= APP_URL ?>/my-orders" class="cm-icon-btn" title="My Orders">
                        <i class="bi bi-bag-check"></i>
                        <?php if ($pendingOrderCount > 0): ?>
                            <span class="badge"><?= $pendingOrderCount ?></span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
                <a href="<?= APP_URL ?><?= Auth::check() ? '/wishlist' : '/login' ?>" class="cm-icon-btn" title="Wishlist">
                    <i class="bi bi-heart"></i>
                    <span class="badge" id="wishlistCountBadge" style="<?= $wishlistCount > 0 ? '' : 'display:none;' ?>"><?= $wishlistCount ?></span>
                </a>
                <button type="button" class="cm-icon-btn" data-bs-toggle="offcanvas" data-bs-target="#cartDrawer" title="Cart">
                    <i class="bi bi-bag"></i>
                    <?php if ($cartCount > 0): ?>
                        <span class="badge" id="cartCountBadge"><?= $cartCount ?></span>
                    <?php endif; ?>
                </button>
            </div>
        </div>

        <!-- Mobile Search -->
        <div class="d-lg-none mt-3" id="liveSearchMobile">
            <form action="<?= APP_URL ?>/search" method="GET" autocomplete="off">
                <input type="text" name="q" class="form-control" placeholder="Search products..." style="border-radius:22px;padding-right:48px;">
            </form>
        </div>
    </div>
</header>

<!-- ═══ OFFCANVAS CART ═══ -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="cartDrawer" aria-labelledby="cartDrawerLabel" style="width:380px;">
    <div class="offcanvas-header">
        <h6 class="offcanvas-title fw-bold" id="cartDrawerLabel"><i class="bi bi-bag me-2"></i>Shopping Cart (<span id="cartCountText"><?= $cartCount ?></span>)</h6>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div id="cartItems">
            <?php
            $cart = Session::getCart();
            if (empty($cart)):
            ?>
                <div class="cm-cart-empty">
                    <i class="bi bi-bag-x"></i>
                    <p>Your cart is empty</p>
                    <a href="<?= APP_URL ?>/shop" class="btn btn-sm btn-outline-primary" style="border-color:var(--cm-primary);color:var(--cm-primary);border-radius:20px;">Start Shopping</a>
                </div>
            <?php else: ?>
                <?php foreach ($cart as $key => $item):
                    $img = Sanitizer::image($item['image']);
                    $itemPrice = $item['discount_price'] ?? $item['price'];
                ?>
                    <div class="cm-cart-item" data-key="<?= htmlspecialchars($key) ?>">
                        <img src="<?= $img ?>" alt="<?= Sanitizer::clean($item['name']) ?>" width="64" height="64" loading="lazy">
                        <div class="item-info flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <div class="item-name"><?= Sanitizer::clean($item['name']) ?></div>
                                <button class="item-remove" data-key="<?= htmlspecialchars($key) ?>" title="Remove"><i class="bi bi-x-lg"></i></button>
                            </div>
                            <?php if (!empty($item['variant_name'])): ?>
                                <small class="text-muted"><?= Sanitizer::clean($item['variant_name']) ?></small>
                            <?php endif; ?>
                            <div class="item-price"><?= Sanitizer::banglaPrice($itemPrice) ?></div>
                            <div class="qty-control">
                                <button class="cart-qty-btn" data-key="<?= htmlspecialchars($key) ?>" data-action="decrease" aria-label="Decrease quantity">-</button>
                                <span><?= (int) $item['quantity'] ?></span>
                                <button class="cart-qty-btn" data-key="<?= htmlspecialchars($key) ?>" data-action="increase" aria-label="Increase quantity">+</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="cart-footer" id="cartFooter" <?= empty($cart) ? 'style="display:none;"' : '' ?>>
            <div class="cm-shipping-bar" id="shippingBar">
                <?php
                $freeShippingMin = 3000;
                $shippingProgress = min(($cartTotal / $freeShippingMin) * 100, 100);
                $remaining = max(0, $freeShippingMin - $cartTotal);
                ?>
                <div class="progress">
                    <div class="progress-bar" style="width: <?= $shippingProgress ?>%"></div>
                </div>
                <div class="shipping-text" id="shippingText">
                    <?php if ($remaining > 0): ?>
                        Add <strong><?= Sanitizer::banglaPrice($remaining) ?></strong> more for FREE shipping!
                    <?php else: ?>
                        <strong>You've unlocked FREE shipping!</strong>
                    <?php endif; ?>
                </div>
            </div>
            <div class="subtotal">
                <span>Subtotal:</span>
                <span id="cartSubtotal"><?= Sanitizer::banglaPrice($cartTotal) ?></span>
            </div>
            <a href="<?= APP_URL ?>/checkout" class="btn-checkout">Proceed to Checkout</a>
            <a href="<?= APP_URL ?>/shop" class="cm-continue-shopping">
                <i class="bi bi-arrow-left me-1"></i> Continue Shopping
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Offcanvas Cart: Load via AJAX ── */
    const cartDrawer = document.getElementById('cartDrawer');
    if (cartDrawer) {
        cartDrawer.addEventListener('show.bs.offcanvas', function () {
            if (typeof Cart !== 'undefined') Cart.loadCart();
        });
    }

    /* ── Shipping Progress Bar ── */
    window.updateShippingBar = function (total) {
        const freeMin = 3000;
        const pct = Math.min((total / freeMin) * 100, 100);
        const remaining = Math.max(0, freeMin - total);
        const bar = document.querySelector('#shippingBar .progress-bar');
        const text = document.getElementById('shippingText');
        if (bar) bar.style.width = pct + '%';
        if (text) {
            text.innerHTML = remaining > 0
                ? 'Add <strong>৳' + remaining.toLocaleString() + '</strong> more for FREE shipping!'
                : '<strong>You\'ve unlocked FREE shipping!</strong>';
        }
    }

    /* ── Global badge updater (used by product pages) ── */
    window.updateHeaderCartBadge = function (count) {
        const badge = document.getElementById('cartCountBadge');
        const text  = document.getElementById('cartCountText');
        if (badge) badge.textContent = count;
        if (text)  text.textContent  = count;
        if (count === 0 && badge) badge.style.display = 'none';
    };

    /* ── Header Hide on Scroll ── */
    var lastScroll = 0;
    var topbar = document.querySelector('.cm-topbar');

    window.addEventListener('scroll', function () {
        var currentScroll = window.pageYOffset;

        if (currentScroll > 100) {
            if (currentScroll > lastScroll) {
                topbar && topbar.classList.add('hidden');
            } else {
                topbar && topbar.classList.remove('hidden');
            }
        } else {
            topbar && topbar.classList.remove('hidden');
        }

        lastScroll = currentScroll;
    }, { passive: true });

    /* ── Fetch user profile for navbar ── */
    var headerUserName = document.getElementById('headerUserName');
    if (headerUserName) {
        fetch('<?= APP_URL ?>/api/user/profile', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success && data.user) {
                headerUserName.querySelector('strong').textContent = data.user.name;
            }
        })
        .catch(function () {});
    }

    /* ── Wishlist toggle on product cards ── */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.wishlist-btn');
        if (!btn) return;
        e.preventDefault();

        var productId = btn.dataset.productId;
        if (!productId) return;

        var icon = btn.querySelector('i');
        var formData = new FormData();
        formData.append('action', 'toggle_wishlist');
        formData.append('product_id', productId);

        fetch('<?= APP_URL ?>/ajax_handler.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                if (data.added) {
                    icon.classList.remove('bi-heart');
                    icon.classList.add('bi-heart-fill');
                    btn.style.color = '#ff3838';
                } else {
                    icon.classList.remove('bi-heart-fill');
                    icon.classList.add('bi-heart');
                    btn.style.color = '';
                }
                var wBadge = document.getElementById('wishlistCountBadge');
                if (wBadge) {
                    var count = data.count || 0;
                    wBadge.textContent = count;
                    wBadge.style.display = count > 0 ? '' : 'none';
                }
            } else {
                alert(data.message || 'Please login first');
            }
        })
        .catch(function () {});
    });
});
</script>
