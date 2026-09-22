<?php
use App\Helpers\Sanitizer;
$siteName = SITE_NAME;
$sitePhone = SITE_PHONE;
$siteEmail = SITE_EMAIL;
$siteAddress = SITE_ADDRESS;
?>

<style>
    .about-hero {
        background: linear-gradient(135deg, var(--cm-dark) 0%, var(--cm-dark-light) 100%);
        color: #fff; padding: 80px 0 60px; text-align: center;
    }
    .about-hero h1 { font-size: 36px; font-weight: 800; margin-bottom: 12px; }
    .about-hero p { font-size: 16px; opacity: .8; max-width: 600px; margin: 0 auto; }
    .about-section { padding: 60px 0; }
    .about-section h2 { font-weight: 700; color: var(--cm-dark); margin-bottom: 20px; }
    .about-section p { color: #555; line-height: 1.8; font-size: 15px; }
    .about-card {
        background: var(--cm-white); border-radius: 12px; padding: 32px;
        box-shadow: var(--cm-shadow); text-align: center; height: 100%;
    }
    .about-card .icon { font-size: 40px; color: var(--cm-primary); margin-bottom: 16px; }
    .about-card h5 { font-weight: 700; color: var(--cm-dark); margin-bottom: 10px; }
    .about-card p { font-size: 14px; color: #666; }
    .values-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; }
</style>

<div class="about-hero">
    <div class="container">
        <h1 style="color: #fff;">About <?= $siteName ?></h1>
        <p>Your trusted partner for premium crockery and kitchenware in Bangladesh</p>
    </div>
</div>

<section class="about-section">
    <div class="container">
        <div class="row align-items-center g-5 mb-5">
            <div class="col-lg-6">
                <h2>Our Story</h2>
                <p><?= $siteName ?> was founded with a simple mission: to bring premium quality crockery and kitchenware to every household in Bangladesh. We believe that beautiful dining starts with beautiful tableware.</p>
                <p>From elegant bone china dinner sets to durable melamine collections, we curate products that combine aesthetics with functionality. Every item in our catalog is handpicked to meet our high standards of quality and design.</p>
            </div>
            <div class="col-lg-6">
                <div class="about-card">
                    <div class="icon"><i class="bi bi-shop"></i></div>
                    <h5>Quality First</h5>
                    <p>We partner with top brands like Prestige, Miyako, Walton, and Fine Ceramics to bring you the best products at competitive prices.</p>
                </div>
            </div>
        </div>

        <div class="values-grid">
            <div class="about-card">
                <div class="icon"><i class="bi bi-truck"></i></div>
                <h5>Fast Delivery</h5>
                <p>Quick and reliable delivery across all 64 districts of Bangladesh. Free shipping on orders above ৳3,000.</p>
            </div>
            <div class="about-card">
                <div class="icon"><i class="bi bi-shield-check"></i></div>
                <h5>Secure Payment</h5>
                <p>Shop with confidence using SSLCommerz secure payment gateway or choose Cash on Delivery.</p>
            </div>
            <div class="about-card">
                <div class="icon"><i class="bi bi-arrow-return-left"></i></div>
                <h5>Easy Returns</h5>
                <p>Not satisfied? Return within 7 days for a full refund. Your satisfaction is our priority.</p>
            </div>
            <div class="about-card">
                <div class="icon"><i class="bi bi-headset"></i></div>
                <h5>24/7 Support</h5>
                <p>Our customer support team is always ready to help you via phone, email, or social media.</p>
            </div>
        </div>
    </div>
</section>

<section class="about-section" style="background: var(--cm-gray-100);">
    <div class="container text-center">
        <h2>Get In Touch</h2>
        <p class="mb-4">Have questions? We'd love to hear from you.</p>
        <div class="d-flex justify-content-center gap-4 flex-wrap">
            <div><i class="bi bi-telephone text-primary me-2"></i> <a href="tel:<?= $sitePhone ?>"><?= $sitePhone ?></a></div>
            <div><i class="bi bi-envelope text-primary me-2"></i> <a href="mailto:<?= $siteEmail ?>"><?= $siteEmail ?></a></div>
            <div><i class="bi bi-geo-alt text-primary me-2"></i> <?= $siteAddress ?></div>
        </div>
    </div>
</section>
