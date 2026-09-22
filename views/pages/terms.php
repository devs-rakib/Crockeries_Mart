<?php use App\Helpers\Sanitizer; ?>

<style>
    .page-hero { background: linear-gradient(135deg, var(--cm-dark) 0%, var(--cm-dark-light) 100%); color: #fff; padding: 60px 0 40px; text-align: center; }
    .page-hero h1 { font-size: 32px; font-weight: 800; }
    .page-content { padding: 60px 0; }
    .page-content h2 { font-weight: 700; color: var(--cm-dark); margin: 24px 0 12px; font-size: 20px; }
    .page-content p, .page-content li { color: #555; line-height: 1.8; font-size: 15px; }
    .page-content ul { padding-left: 20px; }
    .page-content li { margin-bottom: 8px; }
    .last-updated { color: var(--cm-gray-500); font-size: 13px; margin-top: 30px; }
</style>

<div class="page-hero">
    <div class="container" style="color: #fff;">
        <h1>Terms & Conditions</h1>
    </div>
</div>

<div class="page-content">
    <div class="container" style="max-width: 800px;">
        <h2>1. General Terms</h2>
        <p>By accessing and using <?= SITE_NAME ?> (the "Website"), you agree to be bound by these Terms and Conditions. If you do not agree, please do not use our Website.</p>

        <h2>2. Products & Pricing</h2>
        <ul>
            <li>All product images are for illustration purposes only. Actual products may vary slightly.</li>
            <li>Prices are in Bangladeshi Taka (BDT) and include applicable taxes unless stated otherwise.</li>
            <li><?= SITE_NAME ?> reserves the right to change prices without prior notice.</li>
            <li>In case of pricing errors, we reserve the right to cancel orders and issue full refunds.</li>
        </ul>

        <h2>3. Orders & Payment</h2>
        <ul>
            <li>An order is confirmed only after successful payment or order acceptance for COD.</li>
            <li>We reserve the right to cancel orders due to stock unavailability, pricing errors, or suspected fraud.</li>
            <li>Payment through SSLCommerz is processed securely. We do not store card details.</li>
        </ul>

        <h2>4. Shipping & Delivery</h2>
        <ul>
            <li>Delivery times are estimates and not guaranteed. See <a href="<?= APP_URL ?>/pages/shipping-info">Shipping Info</a>.</li>
            <li>Risk of loss passes to you upon delivery of the product to the delivery address.</li>
        </ul>

        <h2>5. Returns & Refunds</h2>
        <p>Returns and refunds are subject to our <a href="<?= APP_URL ?>/pages/return-policy">Return Policy</a>.</p>

        <h2>6. Intellectual Property</h2>
        <p>All content on this Website, including text, images, logos, and graphics, is the property of <?= SITE_NAME ?> and is protected by copyright laws.</p>

        <h2>7. Limitation of Liability</h2>
        <p><?= SITE_NAME ?> shall not be liable for any indirect, incidental, or consequential damages arising from the use of our products or services.</p>

        <h2>8. Privacy</h2>
        <p>Your personal information is handled according to our privacy practices. We do not sell or share your data with third parties without consent.</p>

        <h2>9. Changes to Terms</h2>
        <p>We may update these Terms & Conditions at any time. Continued use of the Website after changes constitutes acceptance of the new terms.</p>

        <h2>10. Contact</h2>
        <p>For questions about these terms, contact us at <?= SITE_EMAIL ?> or <?= SITE_PHONE ?>.</p>

        <p class="last-updated">Last updated: <?= date('F Y') ?></p>
    </div>
</div>
