<?php
use App\Helpers\Sanitizer;
/**
 * Home Page - CrokersesMart
 *
 * @var array $heroSliders       Hero slider banners
 * @var array $middleBanners     Middle/promo banners
 * @var array $sidebarBanners    Sidebar banners
 * @var array $categories        Featured categories
 * @var array $categoryProducts  Products grouped by category
 * @var array $brands            Brand list
 */

$heroSliders      = $heroSliders ?? [];
$middleBanners    = $middleBanners ?? [];
$sidebarBanners   = $sidebarBanners ?? [];
$categories       = $categories ?? [];
$categoryProducts = $categoryProducts ?? [];
$brands           = $brands ?? [];

$placeholderBanners = [
    [
        'title' => 'Premium Collection',
        'subtitle' => 'Up to 40% Off',
        'description' => 'Explore our curated selection of premium dinnerware.',
        'link' => APP_URL . '/shop?category=premium',
        'button_text' => 'Shop Now',
        'bg_color' => '#ff3838',
    ],
    [
        'title' => 'Kitchen Essentials',
        'subtitle' => 'Starting ৳499',
        'description' => 'Everything you need for your kitchen at unbeatable prices.',
        'link' => APP_URL . '/shop?category=kitchen',
        'button_text' => 'Explore',
        'bg_color' => '#1a1a2e',
    ],
    [
        'title' => 'Combo Offers',
        'subtitle' => 'Save More Together',
        'description' => 'Buy complete sets and enjoy exclusive combo discounts.',
        'link' => APP_URL . '/shop?category=combo',
        'button_text' => 'View Deals',
        'bg_color' => '#28a745',
    ],
];

$promoFeatures = [
    ['icon' => 'bi-truck', 'title' => 'Free Delivery', 'text' => 'On orders over ৳1,500 within Dhaka'],
    ['icon' => 'bi-arrow-return-left', 'title' => 'Easy Returns', 'text' => '7-day hassle-free return policy'],
    ['icon' => 'bi-shield-check', 'title' => 'Secure Payment', 'text' => '100% secure checkout with SSL'],
    ['icon' => 'bi-headset', 'title' => '24/7 Support', 'text' => 'Dedicated customer support team'],
];
?>

<style>
/* ── Home Page Styles ── */
.cm-section { padding: 48px 0; }
.cm-section:nth-child(even) { background: var(--cm-gray-100); }

.cm-section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
}
.cm-section-header h2 {
    font-size: 26px;
    font-weight: 800;
    color: var(--cm-dark);
    position: relative;
}
.cm-section-header h2::after {
    content: '';
    display: block;
    width: 50px;
    height: 3px;
    background: var(--cm-primary);
    border-radius: 2px;
    margin-top: 8px;
}
.cm-section-header .view-all {
    font-size: 14px;
    font-weight: 600;
    color: var(--cm-primary);
    display: flex;
    align-items: center;
    gap: 4px;
    transition: gap var(--cm-transition);
}
.cm-section-header .view-all:hover { gap: 8px; }

/* ── Hero Wrapper (Sidebar + Slider) ── */
.cm-hero-wrapper {
    display: flex;
    background: var(--cm-white);
    border-bottom: 3px solid var(--cm-primary);
}
.cm-categories-sidebar {
    width: 270px;
    flex-shrink: 0;
    background: var(--cm-white);
    border-right: 1px solid var(--cm-gray-200);
    position: relative;
    z-index: 20;
}
.cm-sidebar-header {
    background: linear-gradient(135deg, var(--cm-primary), var(--cm-primary-dark));
    color: #fff;
    font-size: 15px;
    font-weight: 700;
    padding: 14px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    letter-spacing: 0.3px;
}
.cm-sidebar-header i { font-size: 18px; }
.cm-sidebar-list {
    list-style: none;
    margin: 0;
    padding: 6px 0;
    max-height: 440px;
    overflow-y: auto;
    scrollbar-width: thin;
}
.cm-sidebar-list::-webkit-scrollbar { width: 4px; }
.cm-sidebar-list::-webkit-scrollbar-thumb { background: var(--cm-gray-200); border-radius: 4px; }

/* ── Sidebar Item (with submenu) ── */
.cm-sidebar-item {
    position: relative;
}
.cm-sidebar-item > a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 11px 20px;
    font-size: 13.5px;
    font-weight: 500;
    color: var(--cm-dark);
    text-decoration: none;
    border-left: 3px solid transparent;
    transition: all var(--cm-transition);
}
.cm-sidebar-item > a:hover,
.cm-sidebar-item:hover > a {
    background: #fff5f5;
    color: var(--cm-primary);
    border-left-color: var(--cm-primary);
}
.cm-sidebar-item > a .sidebar-cat-icon {
    width: 26px;
    height: 26px;
    border-radius: 6px;
    background: #fff0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    color: var(--cm-primary);
    flex-shrink: 0;
    transition: all var(--cm-transition);
}
.cm-sidebar-item:hover > a .sidebar-cat-icon {
    background: var(--cm-primary);
    color: #fff;
}
.cm-sidebar-item > a .sidebar-cat-name { flex: 1; }
.cm-sidebar-item > a .sidebar-arrow {
    font-size: 10px;
    color: var(--cm-gray-500);
    transition: all var(--cm-transition);
}
.cm-sidebar-item:hover > a .sidebar-arrow {
    color: var(--cm-primary);
    transform: translateX(3px);
}

/* ── Sidebar Submenu ── */
.cm-sidebar-submenu {
    display: none;
    position: absolute;
    top: -6px;
    left: 100%;
    width: 260px;
    background: var(--cm-white);
    border: 1px solid var(--cm-gray-200);
    border-radius: 0 10px 10px 0;
    border-left: 3px solid var(--cm-primary);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    z-index: 9999;
    list-style: none;
    margin: 0;
    padding: 8px 0;
    max-height: 440px;
    overflow-y: auto;
    scrollbar-width: thin;
    animation: fadeInSubmenu 0.15s ease;
}
@keyframes fadeInSubmenu {
    from { opacity: 0; transform: translateX(-6px); }
    to { opacity: 1; transform: translateX(0); }
}
.cm-sidebar-item:hover > .cm-sidebar-submenu {
    display: block;
}
.cm-sidebar-submenu li a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 20px;
    font-size: 13px;
    font-weight: 500;
    color: var(--cm-dark);
    text-decoration: none;
    transition: all var(--cm-transition);
}
.cm-sidebar-submenu li a:hover {
    background: #fff5f5;
    color: var(--cm-primary);
    padding-left: 26px;
}
.cm-sidebar-submenu li a::before {
    content: '';
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: var(--cm-gray-200);
    flex-shrink: 0;
    transition: background var(--cm-transition);
}
.cm-sidebar-submenu li a:hover::before {
    background: var(--cm-primary);
}

.cm-sidebar-footer {
    display: block;
    text-align: center;
    margin: 8px 12px;
    padding: 10px 16px;
    font-size: 13px;
    font-weight: 700;
    color: var(--cm-primary);
    border: 1px solid var(--cm-gray-200);
    border-radius: 8px;
    text-decoration: none;
    transition: all var(--cm-transition);
}
.cm-sidebar-footer:hover {
    background: var(--cm-primary);
    color: #fff;
    border-color: var(--cm-primary);
}
.cm-hero-slider-wrap {
    flex: 1;
    min-width: 0;
    overflow: hidden;
}
.cm-hero { position: relative; overflow: hidden; }
.cm-hero-slider-wrap .cm-hero { height: 100%; }
.cm-hero .carousel-item { min-height: 480px; }
.cm-hero .carousel-item .slide-bg {
    position: absolute; inset: 0;
    background-size: cover; background-position: center;
    transition: transform 6s ease;
}
.cm-hero .carousel-item.active .slide-bg { transform: scale(1.05); }
.cm-hero .carousel-caption {
    position: relative; z-index: 2;
    text-align: left; bottom: 80px; left: 6%; right: 6%;
    padding: 0;
}
.cm-hero .slide-title {
    font-size: 44px; font-weight: 800; color: #fff;
    line-height: 1.15; margin-bottom: 12px;
    text-shadow: 0 2px 8px rgba(0,0,0,0.3);
}
.cm-hero .slide-desc {
    font-size: 18px; color: rgba(255,255,255,0.9);
    margin-bottom: 24px; max-width: 520px;
}
.cm-hero .carousel-indicators { bottom: 24px; }
.cm-hero .carousel-indicators button {
    width: 32px; height: 5px; border-radius: 3px;
    background: rgba(255,255,255,0.5); border: none;
    transition: all var(--cm-transition);
}
.cm-hero .carousel-indicators .active {
    background: var(--cm-primary); width: 50px;
}
.cm-hero .carousel-control-prev,
.cm-hero .carousel-control-next {
    width: 44px; height: 44px; top: 50%;
    background: rgba(0,0,0,0.35); border-radius: 50%;
    transform: translateY(-50%); opacity: 1;
    transition: background var(--cm-transition);
}
.cm-hero .carousel-control-prev:hover,
.cm-hero .carousel-control-next:hover { background: var(--cm-primary); }
.cm-hero .carousel-control-prev { left: 20px; }
.cm-hero .carousel-control-next { right: 20px; }

/* ── Middle Banners ── */
.cm-mid-banners { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
.cm-mid-banner {
    position: relative; border-radius: 12px; overflow: hidden;
    min-height: 180px; display: flex; align-items: center;
    padding: 28px; text-decoration: none; transition: transform var(--cm-transition);
}
.cm-mid-banner:hover { transform: translateY(-4px); }
.cm-mid-banner::before {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(135deg, rgba(0,0,0,0.65), rgba(0,0,0,0.25));
    z-index: 1;
}
.cm-mid-banner .banner-bg {
    position: absolute; inset: 0; background-size: cover;
    background-position: center; z-index: 0;
    transition: transform 0.5s ease;
}
.cm-mid-banner:hover .banner-bg { transform: scale(1.06); }
.cm-mid-banner .banner-content { position: relative; z-index: 2; color: #fff; }
.cm-mid-banner .banner-content .b-subtitle {
    font-size: 13px; font-weight: 600; color: var(--cm-primary-light);
    text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;
}
.cm-mid-banner .banner-content h4 {
    color: #fff; font-size: 20px; font-weight: 800; margin-bottom: 6px;
}
.cm-mid-banner .banner-content p {
    color: rgba(255,255,255,0.8); font-size: 13px; margin-bottom: 12px;
}
.cm-mid-banner .banner-content .b-btn {
    font-size: 12px; font-weight: 700; color: #fff;
    border: 1px solid rgba(255,255,255,0.4); padding: 6px 18px;
    border-radius: 20px; text-decoration: none;
    transition: all var(--cm-transition); display: inline-flex;
    align-items: center; gap: 6px;
}
.cm-mid-banner:hover .banner-content .b-btn {
    background: var(--cm-primary); border-color: var(--cm-primary);
}

/* ── Categories Scroll (Lifestyle Cards) ── */
.cm-cat-scroll {
    display: flex; gap: 20px; overflow-x: auto;
    scrollbar-width: none; padding: 8px 0 16px;
}
.cm-cat-scroll::-webkit-scrollbar { display: none; }
.cm-lifestyle-card {
    flex-shrink: 0; width: 180px; height: 240px;
    position: relative; border-radius: 14px;
    overflow: hidden; text-decoration: none;
    cursor: pointer; box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    transition: all 0.35s ease;
}
.cm-lifestyle-card:hover {
    box-shadow: 0 12px 32px rgba(0,0,0,0.16);
    transform: translateY(-6px);
}
.cm-lifestyle-card .lifestyle-bg {
    position: absolute; inset: 0;
    background-size: cover; background-position: center;
    transition: transform 0.5s ease;
}
.cm-lifestyle-card:hover .lifestyle-bg {
    transform: scale(1.08);
}
.cm-lifestyle-card .lifestyle-fallback {
    position: absolute; inset: 0;
    background: linear-gradient(135deg, var(--cm-primary) 0%, var(--cm-primary-dark) 100%);
    display: flex; align-items: center; justify-content: center;
}
.cm-lifestyle-card .lifestyle-fallback i {
    font-size: 48px; color: rgba(255,255,255,0.25);
}
.cm-lifestyle-card .lifestyle-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.05) 55%, transparent 100%);
    z-index: 1;
}
.cm-lifestyle-card .lifestyle-content {
    position: absolute; bottom: 0; left: 0; right: 0;
    padding: 20px; z-index: 2; color: #fff;
}
.cm-lifestyle-card .lifestyle-count {
    font-size: 11px; font-weight: 500; color: rgba(255,255,255,0.75);
    text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 4px;
    display: block;
}
.cm-lifestyle-card .lifestyle-name {
    font-size: 17px; font-weight: 700; color: #fff;
    margin: 0; line-height: 1.3;
    display: -webkit-box; -webkit-line-clamp: 2;
    -webkit-box-orient: vertical; overflow: hidden;
}

/* ── Product Section (Swiper Slider) ── */
.cm-cat-products {
    padding-bottom: 8px;
}
.cm-product-swiper {
    position: relative;
    padding: 0 20px;
}
.cm-product-swiper .swiper-slide {
    height: auto;
}
.cm-product-swiper .swiper-button-prev,
.cm-product-swiper .swiper-button-next {
    color: var(--cm-dark);
    background: var(--cm-white);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    box-shadow: 0 2px 12px rgba(0,0,0,0.12);
    transition: all var(--cm-transition);
}
.cm-product-swiper .swiper-button-prev::after,
.cm-product-swiper .swiper-button-next::after {
    font-size: 16px;
}
.cm-product-swiper .swiper-button-prev:hover,
.cm-product-swiper .swiper-button-next:hover {
    background: var(--cm-primary);
    color: #fff;
    box-shadow: 0 4px 16px rgba(255,56,56,0.3);
}
.cm-product-swiper .swiper-button-prev { left: 0; }
.cm-product-swiper .swiper-button-next { right: 0; }
@media (max-width: 575px) {
    .cm-product-swiper .swiper-button-prev,
    .cm-product-swiper .swiper-button-next { display: none; }
}

/* ── Promo Info Cards ── */
.cm-promo-cards {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;
}
.cm-promo-card {
    display: flex; align-items: center; gap: 14px;
    padding: 20px; border-radius: 10px; background: #fff;
    border: 1px solid var(--cm-gray-200);
    transition: all var(--cm-transition);
}
.cm-promo-card:hover {
    box-shadow: var(--cm-shadow); transform: translateY(-2px);
}
.cm-promo-card .promo-icon {
    width: 52px; height: 52px; border-radius: 50%;
    background: #fff0f0; color: var(--cm-primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; flex-shrink: 0;
}
.cm-promo-card .promo-text h6 {
    font-size: 14px; font-weight: 700; color: var(--cm-dark);
    margin-bottom: 2px;
}
.cm-promo-card .promo-text p {
    font-size: 12px; color: var(--cm-gray-500); margin: 0;
}
@media (max-width: 991px) { .cm-promo-cards { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 575px) {
    .cm-promo-cards { grid-template-columns: 1fr; }
    .cm-section-header h2 { font-size: 22px; }
    .cm-hero .slide-title { font-size: 28px; }
    .cm-hero .slide-desc { font-size: 15px; }
    .cm-hero .carousel-item { min-height: 300px; }
    .cm-mid-banners { grid-template-columns: 1fr; }
}
@media (max-width: 991px) {
    .cm-hero-wrapper { flex-direction: column; }
    .cm-categories-sidebar { width: 100%; }
    .cm-sidebar-list { max-height: 200px; }
    .cm-hero .carousel-item { min-height: 300px; }
    .cm-sidebar-submenu { display: none !important; }
}
@media (min-width: 992px) {
    .cm-hero .carousel-item { min-height: 460px; }
}

/* ── Brand Carousel ── */
.cm-brand-scroll {
    display: flex; gap: 32px; overflow-x: auto;
    scrollbar-width: none; padding: 8px 0;
    align-items: center;
}
.cm-brand-scroll::-webkit-scrollbar { display: none; }
.cm-brand-item {
    flex-shrink: 0; text-align: center; text-decoration: none;
    padding: 16px 24px; border-radius: 10px;
    border: 2px solid var(--cm-gray-200);
    transition: all var(--cm-transition); min-width: 140px;
    background: #fff;
}
.cm-brand-item:hover {
    border-color: var(--cm-primary);
    box-shadow: 0 4px 16px rgba(255,56,56,0.1);
    transform: translateY(-3px);
}
.cm-brand-item img {
    height: 40px; width: auto; object-fit: contain;
    margin-bottom: 6px; filter: grayscale(100%);
    transition: filter var(--cm-transition);
}
.cm-brand-item:hover img { filter: grayscale(0); }
.cm-brand-item .brand-name {
    font-size: 12px; font-weight: 600; color: var(--cm-gray-500);
    transition: color var(--cm-transition);
}
.cm-brand-item:hover .brand-name { color: var(--cm-primary); }
</style>

<!-- ══════════════════════════════════════════════════════════════ -->
<!-- SECTION 1 : HERO (Categories Sidebar + Slider)               -->
<!-- ══════════════════════════════════════════════════════════════ -->
<div class="cm-hero-wrapper">
    <!-- Categories Sidebar -->
    <aside class="cm-categories-sidebar d-none d-lg-block">
        <div class="cm-sidebar-header">
            <i class="bi bi-list"></i>
            Categories
        </div>
        <ul class="cm-sidebar-list">
            <?php
            $sidebarTree = $sidebarCategories ?? [];
            if (empty($sidebarTree) && isset($categories) && !empty($categories)) {
                $sidebarTree = $categories;
            }
            if (empty($sidebarTree)):
                try {
                    $catModelTree = new App\Models\Category();
                    $sidebarTree = $catModelTree->getSidebarTree();
                } catch (\Throwable $e) {}
            endif;
            foreach ($sidebarTree as $cat):
                $catIcon = !empty($cat['icon_class']) ? $cat['icon_class'] : (!empty($cat['icon']) ? $cat['icon'] : 'bi-grid');
                $hasChildren = !empty($cat['children']);
            ?>
                <li class="cm-sidebar-item">
                    <a href="<?= $hasChildren ? '#' : APP_URL . '/shop?category=' . urlencode($cat['slug']) ?>">
                        <span class="sidebar-cat-icon"><i class="bi <?= htmlspecialchars($catIcon) ?>"></i></span>
                        <span class="sidebar-cat-name"><?= Sanitizer::clean($cat['name']) ?></span>
                        <?php if ($hasChildren): ?>
                            <i class="bi bi-chevron-right sidebar-arrow"></i>
                        <?php endif; ?>
                    </a>
                    <?php if ($hasChildren): ?>
                        <ul class="cm-sidebar-submenu">
                            <?php foreach ($cat['children'] as $child): ?>
                                <li>
                                    <a href="<?= APP_URL ?>/shop?category=<?= urlencode($child['slug']) ?>">
                                        <?= Sanitizer::clean($child['name']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <a href="<?= APP_URL ?>/shop" class="cm-sidebar-footer">
            <i class="bi bi-grid-3x3-gap me-1"></i> All Categories
        </a>
    </aside>

    <!-- Hero Slider -->
    <div class="cm-hero-slider-wrap">
        <section class="cm-hero">
            <div id="heroSlider" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
                <?php if (!empty($heroSliders)): ?>
                    <div class="carousel-indicators">
                        <?php foreach ($heroSliders as $i => $slide): ?>
                            <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="<?= $i ?>"
                                class="<?= $i === 0 ? 'active' : '' ?>" aria-label="Slide <?= $i + 1 ?>"></button>
                        <?php endforeach; ?>
                    </div>
                    <div class="carousel-inner">
                        <?php foreach ($heroSliders as $i => $slide): ?>
                            <?php
                            $slideImg = !empty($slide['image']) ? Sanitizer::image($slide['image']) : APP_URL . '/assets/images/Slider/Hero_s3.webp';
                            $slideTitle = Sanitizer::clean($slide['title'] ?? '');
                            $slideDesc = Sanitizer::clean($slide['description'] ?? '');
                            $slideLink = !empty($slide['link']) ? $slide['link'] : APP_URL . '/shop';
                            $slideBtnText = Sanitizer::clean($slide['button_text'] ?? 'Shop Now');
                            ?>
                            <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                                <div class="slide-bg" style="background-image:url('<?= $slideImg ?>');"></div>
                                <div class="carousel-caption">
                                    <?php if ($slideTitle): ?>
                                        <h2 class="slide-title"><?= $slideTitle ?></h2>
                                    <?php endif; ?>
                                    <?php if ($slideDesc): ?>
                                        <p class="slide-desc"><?= $slideDesc ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="carousel-control-prev" data-slide-custom="prev">
                        <i class="bi bi-chevron-left fs-4"></i>
                    </button>
                    <button type="button" class="carousel-control-next" data-slide-custom="next">
                        <i class="bi bi-chevron-right fs-4"></i>
                    </button>
                <?php else: ?>
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="0" class="active"></button>
                        <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="1"></button>
                        <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="2"></button>
                    </div>
                    <div class="carousel-inner">
                        <?php foreach ($placeholderBanners as $i => $ph): ?>
                            <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                                <div class="slide-bg" style="background:<?= $ph['bg_color'] ?>;"></div>
                                <div class="carousel-caption">
                                    <h2 class="slide-title"><?= $ph['title'] ?></h2>
                                    <p class="slide-desc"><?= $ph['description'] ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="carousel-control-prev" data-slide-custom="prev">
                        <i class="bi bi-chevron-left fs-4"></i>
                    </button>
                    <button type="button" class="carousel-control-next" data-slide-custom="next">
                        <i class="bi bi-chevron-right fs-4"></i>
                    </button>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════ -->
<!-- SECTION 2 : MIDDLE BANNERS (below hero)                      -->
<!-- ══════════════════════════════════════════════════════════════ -->
<section class="cm-section" style="padding-bottom:0;">
    <div class="container">
        <div class="cm-mid-banners">
            <?php if (!empty($middleBanners)): ?>
                <?php foreach (array_slice($middleBanners, 0, 3) as $banner): ?>
                    <?php
                    $bannerImg = !empty($banner['image']) ? Sanitizer::image($banner['image']) : '';
                    $bannerLink = !empty($banner['link']) ? $banner['link'] : APP_URL . '/shop';
                    ?>
                    <a href="<?= htmlspecialchars($bannerLink) ?>" class="cm-mid-banner">
                        <?php if ($bannerImg): ?>
                            <div class="banner-bg" style="background-image:url('<?= $bannerImg ?>');"></div>
                        <?php endif; ?>
                        <div class="banner-content">
                            <?php if (!empty($banner['subtitle'])): ?>
                                <span class="b-subtitle"><?= Sanitizer::clean($banner['subtitle']) ?></span>
                            <?php endif; ?>
                            <h4><?= Sanitizer::clean($banner['title'] ?? '') ?></h4>
                            <?php if (!empty($banner['description'])): ?>
                                <p><?= Sanitizer::clean($banner['description']) ?></p>
                            <?php endif; ?>
                            <span class="b-btn">
                                <?= Sanitizer::clean($banner['button_text'] ?? 'Shop Now') ?>
                                <i class="bi bi-arrow-right"></i>
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <?php foreach ($placeholderBanners as $ph): ?>
                    <a href="<?= $ph['link'] ?>" class="cm-mid-banner" style="background:<?= $ph['bg_color'] ?>;">
                        <div class="banner-content">
                            <span class="b-subtitle"><?= $ph['subtitle'] ?></span>
                            <h4><?= $ph['title'] ?></h4>
                            <p><?= $ph['description'] ?></p>
                            <span class="b-btn"><?= $ph['button_text'] ?> <i class="bi bi-arrow-right"></i></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════════════════════ -->
<!-- SECTION 3 : FEATURED PRODUCTS                                 -->
<!-- ══════════════════════════════════════════════════════════════ -->
<?php if (!empty($featuredProducts)): ?>
<section class="cm-section">
    <div class="container">
        <div class="cm-section-header">
            <h2>Featured Products</h2>
            <a href="<?= APP_URL ?>/shop" class="view-all">
                View All <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="swiper cm-product-swiper" id="featuredProducts">
            <div class="swiper-wrapper">
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="swiper-slide">
                        <?php include __DIR__ . '/partials/product_card.php'; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-button-prev cm-swiper-prev"></div>
            <div class="swiper-button-next cm-swiper-next"></div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ══════════════════════════════════════════════════════════════ -->
<!-- SECTION 4 : PRODUCT TABS                                     -->
<!-- ══════════════════════════════════════════════════════════════ -->
<!-- SECTION 4 : PRODUCTS BY CATEGORY (Slider)                    -->
<!-- ══════════════════════════════════════════════════════════════ -->
<?php if (!empty($categoryProducts)): ?>
<?php foreach ($categoryProducts as $catSection): ?>
    <?php
    $catData = $catSection['category'];
    $catProducts = $catSection['products'];
    $catSlug = $catData['slug'] ?? '';
    $catName = Sanitizer::clean($catData['name'] ?? '');
    $catId = (int) ($catData['id'] ?? 0);
    $sliderId = 'cat-slider-' . $catId;
    ?>
    <section class="cm-section cm-cat-products">
        <div class="container">
            <div class="cm-section-header">
                <h2><?= $catName ?></h2>
                <a href="<?= APP_URL ?>/shop?category=<?= urlencode($catSlug) ?>" class="view-all">
                    View All <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="swiper cm-product-swiper" id="<?= $sliderId ?>">
                <div class="swiper-wrapper">
                    <?php foreach ($catProducts as $product): ?>
                        <div class="swiper-slide">
                            <?php include __DIR__ . '/partials/product_card.php'; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-button-prev cm-swiper-prev"></div>
                <div class="swiper-button-next cm-swiper-next"></div>
            </div>
        </div>
    </section>
<?php endforeach; ?>
<?php endif; ?>

<!-- ══════════════════════════════════════════════════════════════ -->
<!-- SECTION 5 : PROMO FEATURES                                   -->
<!-- ══════════════════════════════════════════════════════════════ -->
<section class="cm-section" style="background:var(--cm-white);">
    <div class="container">
        <div class="cm-promo-cards">
            <?php foreach ($promoFeatures as $promo): ?>
                <div class="cm-promo-card">
                    <div class="promo-icon">
                        <i class="bi <?= $promo['icon'] ?>"></i>
                    </div>
                    <div class="promo-text">
                        <h6><?= $promo['title'] ?></h6>
                        <p><?= $promo['text'] ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════════════════════ -->
<!-- SECTION 6 : TOP BRANDS                                       -->
<!-- ══════════════════════════════════════════════════════════════ -->
<?php if (!empty($brands)): ?>
<section class="cm-section" style="background:var(--cm-gray-100);">
    <div class="container">
        <div class="cm-section-header">
            <h2>Top Brands</h2>
            <a href="<?= APP_URL ?>/shop" class="view-all">
                View All <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="cm-brand-scroll">
            <?php foreach ($brands as $brand): ?>
                <?php
                $brandLogo = !empty($brand['logo']) ? Sanitizer::image($brand['logo']) : (!empty($brand['image']) ? Sanitizer::image($brand['image']) : '');
                ?>
                <a href="<?= APP_URL ?>/shop?brand=<?= urlencode($brand['slug']) ?>" class="cm-brand-item">
                    <?php if ($brandLogo): ?>
                        <img src="<?= $brandLogo ?>" alt="<?= Sanitizer::clean($brand['name']) ?>" loading="lazy">
                    <?php else: ?>
                        <div style="height:40px;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:800;color:var(--cm-primary);">
                            <?= strtoupper(substr($brand['name'], 0, 2)) ?>
                        </div>
                    <?php endif; ?>
                    <div class="brand-name"><?= Sanitizer::clean($brand['name']) ?></div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.cm-product-swiper').forEach(function (el) {
        new Swiper(el, {
            slidesPerView: 5,
            slidesPerGroup: 1,
            speed: 500,
            spaceBetween: 20,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            navigation: {
                prevEl: el.querySelector('.cm-swiper-prev'),
                nextEl: el.querySelector('.cm-swiper-next'),
            },
            breakpoints: {
                0:    { slidesPerView: 1 },
                576:  { slidesPerView: 2 },
                768:  { slidesPerView: 3 },
                992:  { slidesPerView: 4 },
                1200: { slidesPerView: 5 },
            }
        });
    });

    /* ── Hero carousel: slide without page scroll ── */
    var heroEl = document.getElementById('heroSlider');
    if (heroEl) {
        var heroCarousel = bootstrap.Carousel.getOrCreateInstance(heroEl);
        var heroPrev = document.querySelector('.cm-hero .carousel-control-prev');
        var heroNext = document.querySelector('.cm-hero .carousel-control-next');
        if (heroPrev) heroPrev.addEventListener('click', function () {
            heroCarousel.prev();
            heroCarousel.cycle();
        });
        if (heroNext) heroNext.addEventListener('click', function () {
            heroCarousel.next();
            heroCarousel.cycle();
        });
    }

});
</script>
