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
    <title><?= htmlspecialchars($pageTitle ?? SITE_NAME) ?></title>
    <?= CSRF::meta() ?>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
            height: 0;
            overflow: hidden;
            line-height: 0;
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
            background: var(--cm-dark);
            color: #fff;
            padding: 16px 20px;
        }
        #cartDrawer .offcanvas-body { padding: 0; display: flex; flex-direction: column; }
        #cartDrawer .btn-close { filter: invert(1); }
        #cartItems { flex: 1; overflow-y: auto; padding: 16px 20px; }
        .cm-cart-item {
            display: flex;
            gap: 12px;
            padding: 14px 0;
            border-bottom: 1px solid var(--cm-gray-200);
        }
        .cm-cart-item img {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 6px;
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
            width: 26px;
            height: 26px;
            border: 1px solid var(--cm-gray-200);
            border-radius: 4px;
            background: var(--cm-gray-100);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 14px;
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
                <span>Customer Support</span>
                <span>|</span>
                <?php if (Auth::check()): ?>
                    <span>Welcome, <strong><?= Sanitizer::clean(Auth::name()) ?></strong></span>
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
                <i class="bi bi-box-seam" style="font-size:32px;color:var(--cm-primary)"></i>
                <span><em>Crokerses</em>Mart</span>
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
            </ul>

            <!-- Search (right) -->
            <div class="cm-search ms-auto d-none d-lg-flex" id="liveSearch">
                <form action="<?= APP_URL ?>/search" method="GET" class="d-flex w-100" autocomplete="off">
                    <input type="text" name="q" id="searchInput" placeholder="Search for crockery, dinnerware, kitchenware..." aria-label="Search">
                    <button type="submit" aria-label="Search"><i class="bi bi-search"></i></button>
                </form>
                <div id="searchResults"></div>
            </div>

            <!-- Header Icons -->
            <div class="cm-header-icons">
                <?php if (Auth::check()): ?>
                    <a href="<?= APP_URL ?>/wishlist" class="cm-icon-btn" title="Wishlist">
                        <i class="bi bi-heart"></i>
                        <?php if ($wishlistCount > 0): ?>
                            <span class="badge"><?= $wishlistCount ?></span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
                <a href="<?= APP_URL ?>/compare" class="cm-icon-btn d-none d-md-flex" title="Compare">
                    <i class="bi bi-arrow-left-right"></i>
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
                        <img src="<?= $img ?>" alt="<?= Sanitizer::clean($item['name']) ?>">
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
            <div class="subtotal">
                <span>Subtotal:</span>
                <span id="cartSubtotal"><?= Sanitizer::banglaPrice($cartTotal) ?></span>
            </div>
            <a href="<?= APP_URL ?>/checkout" class="btn-checkout">Proceed to Checkout</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Live Search ── */
    const searchInput   = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    let searchTimeout   = null;

    if (searchInput && searchResults) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            if (query.length < 2) {
                searchResults.style.display = 'none';
                searchResults.innerHTML = '';
                return;
            }
            searchTimeout = setTimeout(function () {
                fetch('<?= APP_URL ?>/ajax_handler.php?action=search&q=' + encodeURIComponent(query))
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (data.success && data.results && data.results.length > 0) {
                            let html = '';
                            data.results.forEach(function (item) {
                                html += '<a href="<?= APP_URL ?>/product/' + item.slug + '" class="search-item">' +
                                    '<img src="<?= APP_URL ?>/uploads/' + item.main_image + '" alt="">' +
                                    '<div>' +
                                        '<div class="item-name">' + item.name + '</div>' +
                                        '<div class="item-price">৳' + parseFloat(item.discount_price || item.price).toLocaleString() + '</div>' +
                                    '</div>' +
                                '</a>';
                            });
                            searchResults.innerHTML = html;
                            searchResults.style.display = 'block';
                        } else {
                            searchResults.innerHTML = '<div class="no-results">No products found</div>';
                            searchResults.style.display = 'block';
                        }
                    })
                    .catch(function () {
                        searchResults.style.display = 'none';
                    });
            }, 350);
        });

        searchInput.addEventListener('blur', function () {
            setTimeout(function () {
                searchResults.style.display = 'none';
            }, 200);
        });

        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                searchResults.style.display = 'none';
            }
        });
    }

    /* ── Offcanvas Cart: Load via AJAX ── */
    const cartDrawer = document.getElementById('cartDrawer');
    if (cartDrawer) {
        cartDrawer.addEventListener('show.bs.offcanvas', function () {
            refreshCartDrawer();
        });
    }

    function refreshCartDrawer() {
        fetch('<?= APP_URL ?>/ajax_handler.php?action=get_cart', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (!data.success) return;
            const items    = document.getElementById('cartItems');
            const footer   = document.getElementById('cartFooter');
            const countEl  = document.getElementById('cartCountText');
            const badge    = document.getElementById('cartCountBadge');
            const subtotal = document.getElementById('cartSubtotal');

            if (countEl) countEl.textContent = data.count;
            if (badge)   badge.textContent   = data.count;
            if (subtotal) subtotal.textContent = '৳' + parseFloat(data.total).toLocaleString();

            if (data.items.length === 0) {
                items.innerHTML = '<div class="cm-cart-empty">' +
                    '<i class="bi bi-bag-x"></i>' +
                    '<p>Your cart is empty</p>' +
                    '<a href="<?= APP_URL ?>/shop" class="btn btn-sm btn-outline-primary" style="border-color:var(--cm-primary);color:var(--cm-primary);border-radius:20px;">Start Shopping</a>' +
                '</div>';
                if (footer) footer.style.display = 'none';
            } else {
                let html = '';
                data.items.forEach(function (item) {
                    const price = item.discount_price || item.price;
                    html += '<div class="cm-cart-item" data-key="' + item.key + '">' +
                        '<img src="<?= APP_URL ?>/uploads/' + item.image + '" alt="">' +
                        '<div class="item-info flex-grow-1">' +
                            '<div class="d-flex justify-content-between">' +
                                '<div class="item-name">' + item.name + '</div>' +
                                '<button class="item-remove" data-key="' + item.key + '" title="Remove"><i class="bi bi-x-lg"></i></button>' +
                            '</div>' +
                            (item.variant_name ? '<small class="text-muted">' + item.variant_name + '</small>' : '') +
                            '<div class="item-price">৳' + parseFloat(price).toLocaleString() + '</div>' +
                            '<div class="qty-control">' +
                                '<button class="cart-qty-btn" data-key="' + item.key + '" data-action="decrease">-</button>' +
                                '<span>' + item.quantity + '</span>' +
                                '<button class="cart-qty-btn" data-key="' + item.key + '" data-action="increase">+</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>';
                });
                items.innerHTML = html;
                if (footer) footer.style.display = 'block';
                bindCartControls();
            }
        });
    }

    /* ── Cart Quantity & Remove ── */
    function bindCartControls() {
        document.querySelectorAll('.cart-qty-btn').forEach(function (btn) {
            btn.onclick = function () {
                const key    = this.getAttribute('data-key');
                const action = this.getAttribute('data-action');
                let current  = parseInt(this.closest('.qty-control').querySelector('span').textContent);
                let newQty   = action === 'increase' ? current + 1 : Math.max(1, current - 1);

                const formData = new FormData();
                formData.append('action', 'update_cart');
                formData.append('key', key);
                formData.append('quantity', newQty);

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
                        refreshCartDrawer();
                        if (typeof updateHeaderCartBadge === 'function') {
                            updateHeaderCartBadge(data.cart_count);
                        }
                    }
                });
            };
        });

        document.querySelectorAll('.item-remove').forEach(function (btn) {
            btn.onclick = function () {
                const key = this.getAttribute('data-key');
                const formData = new FormData();
                formData.append('action', 'remove_cart');
                formData.append('key', key);

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
                        refreshCartDrawer();
                        if (typeof updateHeaderCartBadge === 'function') {
                            updateHeaderCartBadge(data.cart_count);
                        }
                    }
                });
            };
        });
    }
    bindCartControls();

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
});
</script>
