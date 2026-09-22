<?php
use App\Helpers\Sanitizer;
/**
 * Home Page - CrokersesMart
 *
 * @var array $heroSliders       Hero slider banners
 * @var array $categories        Featured categories
 * @var array $categoryProducts  Products grouped by category
 * @var array $brands            Brand list
 */

$heroSliders       = $heroSliders ?? [];
$categories        = $categories ?? [];
$sidebarCategories = $sidebarCategories ?? [];
$categoryProducts  = $categoryProducts ?? [];
$featuredProducts  = $featuredProducts ?? [];
$todaysDeals       = $todaysDeals ?? [];
$brands            = $brands ?? [];

$promoFeatures = $promoFeatures ?? [
    ['icon' => 'bi-truck', 'title' => 'Free Delivery', 'text' => 'On orders over à§³1,500 within Dhaka'],
    ['icon' => 'bi-arrow-return-left', 'title' => 'Easy Returns', 'text' => '7-day hassle-free return policy'],
    ['icon' => 'bi-shield-check', 'title' => 'Secure Payment', 'text' => '100% secure checkout with SSL'],
    ['icon' => 'bi-headset', 'title' => '24/7 Support', 'text' => 'Dedicated customer support team'],
];
?>

<style>
/* â”€â”€ Home Page Styles â”€â”€ */
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

/* â”€â”€ Modern 3-Column Hero Section â”€â”€ */
.cm-hero-section {
    padding: 16px 0 24px;
    background: #fafafa;
    border-bottom: 1px solid #ebebeb;
}
.cm-hero-grid {
    display: grid;
    grid-template-columns: 240px 1fr 240px;
    gap: 16px;
    align-items: stretch;
}

/* â”€â”€ Left Column: Categories â”€â”€ */
.cm-hero-col-left {
    background: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 6px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    display: flex;
    flex-direction: column;
    position: relative;
    z-index: 100;
}
.cm-hero-head {
    background: #453939;
    color: #fff;
    font-size: 15px;
    font-weight: 700;
    height: 44px;
    padding: 0 16px;
    display: flex;
    align-items: center;
    gap: 10px;
    letter-spacing: 0.3px;
    border-radius: 5px 5px 0 0;
}
.cm-hero-head i { font-size: 18px; }
.cm-hero-cat-list {
    list-style: none;
    margin: 0;
    padding: 4px 0;
    flex: 1;
}
.cm-hero-cat-item {
    position: relative;
    border-bottom: 1px solid #f6f6f6;
}
.cm-hero-cat-item:last-child {
    border-bottom: none;
}
.cm-hero-cat-item > a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8.5px 14px;
    font-size: 13.5px;
    font-weight: 500;
    color: #333;
    text-decoration: none;
    transition: all 0.2s ease;
}
.cm-hero-cat-item > a:hover,
.cm-hero-cat-item:hover > a {
    background: #fdf5f5;
    color: var(--cm-primary);
    transform: translateX(3px);
}
.cm-hero-cat-item .cat-icon {
    width: 22px;
    height: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    color: var(--cm-primary);
    flex-shrink: 0;
}
.cm-hero-cat-item .cat-name {
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.cm-hero-cat-item .cat-arrow {
    font-size: 11px;
    color: #bbb;
    transition: transform 0.2s;
}
.cm-hero-cat-item:hover > a .cat-arrow {
    color: var(--cm-primary);
    transform: translateX(3px);
}
.cm-hero-cat-submenu {
    display: none;
    position: absolute;
    top: -1px;
    left: 100%;
    width: 240px;
    background: #fff;
    border: 1px solid #e5e5e5;
    border-left: 3px solid var(--cm-primary);
    border-radius: 0 6px 6px 0;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    z-index: 9999;
    list-style: none;
    margin: 0;
    padding: 6px 0;
}
.cm-hero-cat-item:hover > .cm-hero-cat-submenu {
    display: block;
    animation: catSubmenuFade 0.15s ease;
}
@keyframes catSubmenuFade {
    from { opacity: 0; transform: translateX(-4px); }
    to { opacity: 1; transform: translateX(0); }
}
.cm-hero-cat-submenu li a {
    display: block;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 500;
    color: #333;
    text-decoration: none;
    transition: all 0.2s;
}
.cm-hero-cat-submenu li a:hover {
    background: #fdf5f5;
    color: var(--cm-primary);
    transform: translateX(6px);
}
.cm-hero-cat-all-item {
    border-top: 1px solid #eee;
    margin-top: auto;
}
.cm-hero-cat-all {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    font-size: 13px;
    font-weight: 700;
    color: #333;
    background: #fafafa;
    text-decoration: none;
    border-radius: 0 0 5px 5px;
    transition: all 0.2s;
}
.cm-hero-cat-all:hover {
    background: var(--cm-primary);
    color: #fff;
}
.cm-hero-cat-all:hover .cat-icon {
    color: #fff;
}
.cm-hero-cat-all:hover .cat-arrow {
    color: #fff;
    transform: translateX(3px);
}

/* â”€â”€ Center Column: Nav + Banner + Features â”€â”€ */
.cm-hero-col-center {
    display: flex;
    flex-direction: column;
    min-width: 0;
    gap: 10px;
}
.cm-center-nav {
    height: 44px;
    display: flex;
    align-items: center;
    gap: 28px;
    padding: 0 10px;
    background: transparent;
}
.cm-center-nav .nav-item {
    font-size: 15px;
    font-weight: 600;
    color: #333;
    text-decoration: none;
    padding: 6px 0;
    position: relative;
    transition: color 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.cm-center-nav .nav-item:hover,
.cm-center-nav .nav-item.active {
    color: var(--cm-primary);
}
.cm-center-nav .nav-item.active::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2.5px;
    background: var(--cm-primary);
    border-radius: 2px;
}
.cm-center-nav .badge-hot {
    background: #ff3838;
    color: #fff;
    font-size: 9.5px;
    font-weight: 800;
    text-transform: uppercase;
    padding: 2px 6px;
    border-radius: 4px;
    letter-spacing: 0.3px;
}

/* Center Banner Frame */
.cm-banner-frame {
    background: #f4eee7;
    border: 1px solid #e3dcd3;
    border-radius: 6px;
    overflow: hidden;
    position: relative;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    flex: 1;
}
.cm-banner-frame .carousel,
.cm-banner-frame .carousel-inner,
.cm-banner-frame .carousel-item {
    height: 100%;
    min-height: 330px;
}
.cm-banner-img-link {
    display: block;
    width: 100%;
    height: 100%;
    min-height: 330px;
    position: relative;
    text-decoration: none;
    overflow: hidden;
    background: #f4eee7;
}
.cm-banner-full-img {
    width: 100%;
    height: 100%;
    min-height: 330px;
    max-height: 345px;
    object-fit: cover;
    object-position: center;
    display: block;
    transition: transform 0.4s ease;
}
.cm-banner-img-link:hover .cm-banner-full-img {
    transform: scale(1.02);
}
.cm-banner-slide-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 100%;
    min-height: 330px;
    padding: 28px 40px;
    position: relative;
    background: #f4eee7;
}
.cm-slide-text {
    max-width: 52%;
    z-index: 2;
}
.cm-slide-subtitle {
    display: block;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 1.5px;
    color: #63534b;
    text-transform: uppercase;
    margin-bottom: 8px;
}
.cm-slide-title {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 36px;
    font-weight: 800;
    line-height: 1.15;
    color: #362923;
    letter-spacing: 2px;
    margin: 0 0 22px 0;
    text-transform: uppercase;
}
.cm-slide-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    font-weight: 800;
    letter-spacing: 1px;
    color: #362923;
    text-transform: uppercase;
    text-decoration: none;
    border-bottom: 2px solid #362923;
    padding-bottom: 3px;
    transition: all 0.2s ease;
}
.cm-slide-btn:hover {
    color: var(--cm-primary);
    border-bottom-color: var(--cm-primary);
    gap: 10px;
}
.cm-slide-media {
    position: absolute;
    right: 24px;
    top: 50%;
    transform: translateY(-50%);
    max-width: 48%;
    max-height: 280px;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: center;
}
.cm-slide-media img {
    max-width: 100%;
    max-height: 280px;
    object-fit: contain;
    filter: drop-shadow(0 6px 14px rgba(0,0,0,0.12));
}

/* Slider Controls */
.cm-slider-arrow {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255,255,255,0.75);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    color: #444;
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    opacity: 0.85;
    transition: all 0.2s;
}
.cm-slider-arrow:hover {
    background: #fff;
    color: var(--cm-primary);
    opacity: 1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
.cm-slider-arrow.left { left: 12px; }
.cm-slider-arrow.right { right: 12px; }

.cm-slider-dots {
    margin-bottom: 12px;
    gap: 6px;
}
.cm-slider-dots button {
    width: 22px;
    height: 4px;
    border-radius: 2px;
    background: rgba(0,0,0,0.25);
    border: none;
    padding: 0;
    transition: background-color 0.2s ease, transform 0.2s ease;
}
.cm-slider-dots button.active {
    background: #e53935;
    transform: scaleX(1.55);
    transform-origin: center;
}

/* 4 Feature Cards Under Banner */
.cm-center-features {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    background: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 6px;
    padding: 12px 14px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.02);
}
.cm-feat-card {
    display: flex;
    align-items: center;
    gap: 10px;
}
.cm-feat-icon {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.cm-feat-icon.icon-truck { background: #e8f1fd; color: #1a73e8; }
.cm-feat-icon.icon-return { background: #e6f8f5; color: #00a884; }
.cm-feat-icon.icon-payment { background: #f4ecfb; color: #8e24aa; }
.cm-feat-icon.icon-support { background: #e8f4fc; color: #0288d1; }
.cm-feat-body h6 {
    font-size: 11.5px;
    font-weight: 800;
    color: #222;
    margin: 0 0 2px 0;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.cm-feat-body span {
    font-size: 11px;
    color: #777;
    display: block;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* â”€â”€ Right Column: Todays Deal â”€â”€ */
.cm-hero-col-right {
    background: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 6px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.cm-deal-head {
    justify-content: space-between;
}
.cm-deal-hot-badge {
    background: #e53935;
    color: #fff;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    padding: 2px 8px;
    border-radius: 10px;
    letter-spacing: 0.5px;
}
.cm-deal-list {
    padding: 8px;
    display: flex;
    flex-direction: column;
    gap: 7px;
    flex: 1;
}
.cm-deal-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 10px;
    background: #fff;
    border: 1px solid #f0f0f0;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.2s ease;
}
.cm-deal-item:hover {
    border-color: #ffd4d4;
    box-shadow: 0 3px 10px rgba(0,0,0,0.06);
    transform: translateY(-1.5px);
}
.cm-deal-item-thumb {
    width: 48px;
    height: 48px;
    border-radius: 6px;
    background: #fafafa;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
}
.cm-deal-item-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.cm-deal-item-info {
    text-align: right;
}
.cm-deal-price-current {
    font-size: 14.5px;
    font-weight: 800;
    color: #e53935;
    line-height: 1.2;
    display: block;
}
.cm-deal-price-old {
    font-size: 11.5px;
    color: #999;
    text-decoration: line-through;
    margin-top: 2px;
    line-height: 1.2;
    display: block;
}

/* â”€â”€ Media Queries for 3-Column Hero â”€â”€ */
@media (max-width: 1199px) {
    .cm-hero-grid {
        grid-template-columns: 210px 1fr 210px;
        gap: 12px;
    }
    .cm-slide-title { font-size: 28px; }
    .cm-banner-slide-content { padding: 24px 30px; }
}
@media (max-width: 991px) {
    .cm-hero-grid {
        grid-template-columns: 1fr;
    }
    .cm-hero-col-left {
        display: none;
    }
    .cm-hero-col-right {
        margin-top: 10px;
    }
    .cm-deal-list {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 575px) {
    .cm-center-features {
        grid-template-columns: repeat(2, 1fr);
    }
    .cm-deal-list {
        grid-template-columns: 1fr;
    }
    .cm-slide-text { max-width: 100%; }
    .cm-slide-media { display: none; }
    .cm-slide-title { font-size: 24px; }
}

/* â”€â”€ Categories Scroll (Lifestyle Cards) â”€â”€ */
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

/* â”€â”€ Product Section (Swiper Slider) â”€â”€ */
.cm-cat-products {
    padding-bottom: 24px;
    margin-bottom: 8px;
}
.cm-product-swiper {
    position: relative;
    /* padding: 0 20px; */
}
.cm-product-swiper .swiper-slide {
    height: auto;
    padding: 0;
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

/* â”€â”€ Promo Info Cards â”€â”€ */
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
}


/* â”€â”€ Hero Bottom Banners â”€â”€ */
.cm-hero-bottom { padding: 20px 0 0; }
.cm-hero-banner-card {
    display: flex; align-items: flex-end;
    border-radius: 12px; overflow: hidden;
    background-size: cover; background-position: center;
    min-height: 200px; padding: 28px;
    position: relative; text-decoration: none;
    transition: all var(--cm-transition);
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}
.cm-hero-banner-card::before {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.1) 60%, transparent 100%);
    border-radius: 12px; z-index: 1;
}
.cm-hero-banner-card:hover {
    transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.18);
}
.cm-hero-banner-overlay {
    position: relative; z-index: 2; color: #fff;
}
.cm-hero-banner-tag {
    display: inline-block; padding: 4px 12px; border-radius: 20px;
    background: var(--cm-primary); color: #fff;
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.5px; margin-bottom: 10px;
}
.cm-hero-banner-overlay h3 {
    font-size: 22px; font-weight: 800; margin-bottom: 4px; color: #fff;
}
.cm-hero-banner-overlay p {
    font-size: 14px; color: #fff; margin-bottom: 14px;
}
.cm-hero-banner-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 18px; border-radius: 8px;
    background: #fff; color: var(--cm-dark);
    font-size: 13px; font-weight: 700;
    transition: all var(--cm-transition);
}
.cm-hero-banner-card:hover .cm-hero-banner-btn {
    background: var(--cm-primary); color: #fff;
}

/* â”€â”€ Brand Carousel â”€â”€ */
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
    margin-bottom: 6px; 

}

.cm-brand-item .brand-name {
    font-size: 12px; font-weight: 600; color: var(--cm-gray-500);
    transition: color var(--cm-transition);
}
.cm-brand-item:hover .brand-name { color: var(--cm-primary); }
</style>

<!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
<!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
<!-- SECTION 1 : HERO (3-Column Layout: Categories, Center Banner, Todays Deal) -->
<!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
<section class="cm-hero-section">
    <div class="container">
        <div class="cm-hero-grid">

            <!-- 1. LEFT COLUMN: Categories Menu Bar -->
            <aside class="cm-hero-col-left">
                <div class="cm-hero-head">
                    <i class="bi bi-list"></i>
                    <span>Categories</span>
                </div>
                <ul class="cm-hero-cat-list">
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

                    // Display up to 11 categories
                    $displayedCats = array_slice($sidebarTree, 0, 11);
                    foreach ($displayedCats as $cat):
                        $catIcon = !empty($cat['icon_class']) ? $cat['icon_class'] : 'bi-grid-fill';
                        $catIcon = preg_replace('/^(fas |far |fab |fa-)/', '', $catIcon);
                        if (strpos($catIcon, 'bi-') !== 0) $catIcon = 'bi-' . $catIcon;
                        $hasChildren = !empty($cat['children']);
                    ?>
                        <li class="cm-hero-cat-item">
                            <a href="<?= $hasChildren ? '#' : APP_URL . '/shop?category=' . urlencode($cat['slug']) ?>">
                                <span class="cat-icon"><i class="bi <?= htmlspecialchars($catIcon) ?>"></i></span>
                                <span class="cat-name"><?= Sanitizer::clean($cat['name']) ?></span>
                                <i class="bi bi-chevron-right cat-arrow"></i>
                            </a>
                            <?php if ($hasChildren): ?>
                                <ul class="cm-hero-cat-submenu">
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
                    <li class="cm-hero-cat-all-item">
                        <a href="<?= APP_URL ?>/shop" class="cm-hero-cat-all">
                            <span class="cat-icon"><i class="bi bi-grid-3x3-gap-fill"></i></span>
                            <span class="cat-name">All Categories</span>
                            <i class="bi bi-chevron-right cat-arrow"></i>
                        </a>
                    </li>
                </ul>
            </aside>

            <!-- 2. CENTER COLUMN: Nav + Banner Slider + 4 Features -->
            <div class="cm-hero-col-center">
                <!-- Center Top Nav -->
                <nav class="cm-center-nav">
                    <a href="<?= APP_URL ?>/" class="nav-item active">Home</a>
                    <a href="<?= APP_URL ?>/shop" class="nav-item">Shop</a>
                    <a href="<?= APP_URL ?>/offer" class="nav-item">
                        Offer
                        <span class="badge-hot">Hot</span>
                    </a>
                    <a href="<?= APP_URL ?>/about" class="nav-item">About</a>
                </nav>

                <!-- Center Banner / Slider -->
                <div class="cm-banner-frame">
                    <div id="heroSlider" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
                        <?php if (!empty($heroSliders)): ?>
                        <!-- Dynamic Indicators from DB -->
                        <div class="carousel-indicators cm-slider-dots">
                            <?php foreach ($heroSliders as $i => $slide): ?>
                                <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="<?= $i ?>"
                                    class="<?= $i === 0 ? 'active' : '' ?>"
                                    <?= $i === 0 ? 'aria-current="true"' : '' ?>
                                    aria-label="Slide <?= $i + 1 ?>"></button>
                            <?php endforeach; ?>
                        </div>
                        <!-- Dynamic Slides from DB -->
                        <div class="carousel-inner">
                            <?php foreach ($heroSliders as $i => $slide):
                                $slideImg    = Sanitizer::image($slide['image']);
                                $slideLink   = !empty($slide['link']) ? $slide['link'] : APP_URL . '/shop';
                                $slideTitle  = Sanitizer::clean($slide['title'] ?? 'Banner');
                                $slideDesc   = Sanitizer::clean($slide['description'] ?? '');
                                $slideBtnTxt = Sanitizer::clean($slide['button_text'] ?? 'Shop Now');
                            ?>
                            <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                                <a href="<?= htmlspecialchars($slideLink) ?>" class="cm-banner-img-link">
                                    <img src="<?= $slideImg ?>"
                                         alt="<?= $slideTitle ?>"
                                         class="cm-banner-full-img"
                                         width="1200" height="400"
                                         loading="<?= $i === 0 ? 'eager' : 'lazy' ?>"
                                         <?= $i === 0 ? 'fetchpriority="high"' : '' ?>
                                         onerror="this.src='<?= APP_URL ?>/assets/images/placeholder.svg'">
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <!-- Fallback: Static Slides (no DB banners) -->
                        <div class="carousel-indicators cm-slider-dots">
                            <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                            <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="1" aria-label="Slide 2"></button>
                            <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="2" aria-label="Slide 3"></button>
                        </div>
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <div class="cm-banner-slide-content">
                                    <div class="cm-slide-text">
                                        <span class="cm-slide-subtitle">MOST SELING PRODUCT</span>
                                        <h2 class="cm-slide-title">CROCKERIES<br>MART</h2>
                                        <a href="<?= APP_URL ?>/shop" class="cm-slide-btn">ORDER NOW <i class="bi bi-arrow-right"></i></a>
                                    </div>
                                    <div class="cm-slide-media">
                                        <img src="<?= APP_URL ?>/assets/images/Slider/Hero_s1.webp" alt="Crockeries Mart" width="500" height="400" loading="eager" onerror="this.src='<?= APP_URL ?>/assets/images/placeholder.svg'">
                                    </div>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="cm-banner-slide-content">
                                    <div class="cm-slide-text">
                                        <span class="cm-slide-subtitle">PREMIUM SELECTION</span>
                                        <h2 class="cm-slide-title">KITCHEN &amp;<br>DINING</h2>
                                        <a href="<?= APP_URL ?>/shop" class="cm-slide-btn">SHOP NOW <i class="bi bi-arrow-right"></i></a>
                                    </div>
                                    <div class="cm-slide-media">
                                        <img src="<?= APP_URL ?>/assets/images/Slider/Hero_s2.webp" alt="Kitchen Dining" width="500" height="400" loading="lazy" onerror="this.src='<?= APP_URL ?>/assets/images/placeholder.svg'">
                                    </div>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="cm-banner-slide-content">
                                    <div class="cm-slide-text">
                                        <span class="cm-slide-subtitle">EXCLUSIVE OFFERS</span>
                                        <h2 class="cm-slide-title">BEST DEALS<br>TODAY</h2>
                                        <a href="<?= APP_URL ?>/offer" class="cm-slide-btn">VIEW OFFERS <i class="bi bi-arrow-right"></i></a>
                                    </div>
                                    <div class="cm-slide-media">
                                        <img src="<?= APP_URL ?>/assets/images/Slider/Hero_s3.webp" alt="Best Deals" width="500" height="400" loading="lazy" onerror="this.src='<?= APP_URL ?>/assets/images/placeholder.svg'">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Slider Controls -->
                        <button class="carousel-control-prev cm-slider-arrow left" type="button" data-bs-target="#heroSlider" data-bs-slide="prev" aria-label="Previous">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button class="carousel-control-next cm-slider-arrow right" type="button" data-bs-target="#heroSlider" data-bs-slide="next" aria-label="Next">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>

                <!-- 4 Features Bar Directly Under Banner -->
                <div class="cm-center-features">
                    <div class="cm-feat-card">
                        <div class="cm-feat-icon icon-truck"><i class="bi bi-truck"></i></div>
                        <div class="cm-feat-body">
                            <h6>FREE SHIPPING</h6>
                            <span>Free Shipping All Order</span>
                        </div>
                    </div>
                    <div class="cm-feat-card">
                        <div class="cm-feat-icon icon-return"><i class="bi bi-arrow-counterclockwise"></i></div>
                        <div class="cm-feat-body">
                            <h6>30 DAY RETURNS</h6>
                            <span>30-Day Return Policy</span>
                        </div>
                    </div>
                    <div class="cm-feat-card">
                        <div class="cm-feat-icon icon-payment"><i class="bi bi-check-circle-fill"></i></div>
                        <div class="cm-feat-body">
                            <h6>PAYMENT METHOD</h6>
                            <span>Secure Payment</span>
                        </div>
                    </div>
                    <div class="cm-feat-card">
                        <div class="cm-feat-icon icon-support"><i class="bi bi-headset"></i></div>
                        <div class="cm-feat-body">
                            <h6>HELP CENTER</h6>
                            <span>24/7 Support</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. RIGHT COLUMN: Todays Deal -->
            <aside class="cm-hero-col-right">
                <div class="cm-hero-head cm-deal-head">
                    <span>Todays Deal</span>
                    <span class="cm-deal-hot-badge">Hot</span>
                </div>
                <div class="cm-deal-list">
                    <?php if (!empty($todaysDeals)): ?>
                        <?php foreach ($todaysDeals as $deal): ?>
                            <?php
                            $dealImg = Sanitizer::image($deal['main_image']);
                            $currentPrice = !empty($deal['discount_price']) && $deal['discount_price'] > 0 ? (float)$deal['discount_price'] : (float)$deal['price'];
                            $oldPrice = !empty($deal['discount_price']) && $deal['discount_price'] > 0 && $deal['discount_price'] < $deal['price'] ? (float)$deal['price'] : null;
                            ?>
                            <a href="<?= APP_URL ?>/product/<?= urlencode($deal['slug']) ?>" class="cm-deal-item" title="<?= Sanitizer::clean($deal['name']) ?>">
                                <div class="cm-deal-item-thumb">
                                    <img src="<?= $dealImg ?>" alt="<?= htmlspecialchars($deal['name']) ?>" loading="lazy" width="48" height="48" onerror="this.src='<?= APP_URL ?>/assets/images/placeholder.svg'">
                                </div>
                                <div class="cm-deal-item-info">
                                    <span class="cm-deal-price-current">Tk <?= number_format($currentPrice, 0) ?></span>
                                    <?php if ($oldPrice): ?>
                                        <span class="cm-deal-price-old">Tk <?= number_format($oldPrice, 0) ?></span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="p-3 text-center text-muted small">No deals available today</div>
                    <?php endif; ?>
                </div>
            </aside>

        </div>
    </div>
</section>

<!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
<!-- HERO BOTTOM BANNERS (2 images)                                -->
<!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
<section class="cm-hero-bottom">
    <div class="container">
        <div class="row g-3">
            <div class="col-md-6">
                <a href="<?= APP_URL ?>/shop?category=new-arrivals" class="cm-hero-banner-card" style="background-image:url('<?= APP_URL ?>/uploads/products/product_1789640676_57ec67e44e452991.webp');">
                    <div class="cm-hero-banner-overlay">
                        <span class="cm-hero-banner-tag">New In</span>
                        <h3>New Arrivals</h3>
                        <p>Discover the latest collection</p>
                        <span class="cm-hero-banner-btn">Shop Now <i class="bi bi-arrow-right"></i></span>
                    </div>
                </a>
            </div>
            <div class="col-md-6">
                <a href="<?= APP_URL ?>/shop?category=combo" class="cm-hero-banner-card" style="background-image:url('<?= APP_URL ?>/uploads/products/product_1789640691_128ccd122c27ef57.webp');">
                    <div class="cm-hero-banner-overlay">
                        <span class="cm-hero-banner-tag">Save More</span>
                        <h3>Combo Offers</h3>
                        <p>Buy together & save big</p>
                        <span class="cm-hero-banner-btn">View Deals <i class="bi bi-arrow-right"></i></span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
<!-- SECTION 3 : OFFER PRODUCTS                                   -->
<!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
<?php if (!empty($offerProducts)): ?>
<section class="cm-section">
    <div class="container">
        <div class="cm-section-header">
            <h2><i class="bi bi-fire" style="color:#ff3838;margin-right:6px;"></i>Offer Products</h2>
            <a href="<?= APP_URL ?>/offer" class="view-all">
                View All <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="swiper cm-product-swiper" id="offerProducts">
            <div class="swiper-wrapper">
                <?php foreach ($offerProducts as $product): ?>
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

<!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
<!-- SECTION 4 : FEATURED PRODUCTS                                 -->
<!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
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

<!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
<!-- SECTION 4 : PRODUCT TABS                                     -->
<!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
<!-- SECTION 4 : PRODUCTS BY CATEGORY (Slider)                    -->
<!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
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

<!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
<!-- SECTION 5 : PROMO FEATURES                                   -->
<!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
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

<!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
<!-- SECTION 6 : TOP BRANDS                                       -->
<!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
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
                $brandLogo = Sanitizer::image($brand['logo']);
                ?>
                <a href="<?= APP_URL ?>/shop?brand=<?= urlencode($brand['slug']) ?>" class="cm-brand-item">
                    <?php if ($brandLogo && strpos($brandLogo, 'placeholder') === false): ?>
                        <img src="<?= $brandLogo ?>" alt="<?= Sanitizer::clean($brand['name']) ?>" loading="lazy" width="120" height="40" onerror="this.style.display='none'">
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

<!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
<!-- TRUST BADGES (compact strip)                                  -->
<!-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• -->
<div class="cm-trust-strip">
    <div class="container">
        <div class="cm-trust-grid">
            <div class="cm-trust-item">
                <i class="bi bi-truck"></i>
                <div>
                    <strong>Express Delivery</strong>
                    <span>Fast delivery across Bangladesh</span>
                </div>
            </div>
            <div class="cm-trust-item">
                <i class="bi bi-arrow-return-left"></i>
                <div>
                    <strong>Return Policy</strong>
                    <span>Easy 7-day return policy</span>
                </div>
            </div>
            <div class="cm-trust-item">
                <i class="bi bi-shield-lock"></i>
                <div>
                    <strong>Secure Payment</strong>
                    <span>100% secure checkout</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.cm-product-swiper').forEach(function (el) {
        new Swiper(el, {
            slidesPerView: 4,
            slidesPerGroup: 1,
            speed: 500,
            spaceBetween: 12,
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
                0:    { slidesPerView: 2 },
                576:  { slidesPerView: 2 },
                768:  { slidesPerView: 3 },
                992:  { slidesPerView: 4 },
                1200: { slidesPerView: 4 },
            }
        });
    });

    /* â”€â”€ Hero carousel: slide without page scroll â”€â”€ */
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
