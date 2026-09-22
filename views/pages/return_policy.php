<?php use App\Helpers\Sanitizer; ?>

<style>
    .page-hero { background: linear-gradient(135deg, var(--cm-dark) 0%, var(--cm-dark-light) 100%); color: #fff; padding: 60px 0 40px; text-align: center; }
    .page-hero h1 { font-size: 32px; font-weight: 800; }
    .page-content { padding: 60px 0; }
    .page-content h2 { font-weight: 700; color: var(--cm-dark); margin: 24px 0 12px; font-size: 20px; }
    .page-content h3 { font-weight: 600; color: var(--cm-dark); margin: 20px 0 10px; font-size: 17px; }
    .page-content p, .page-content li { color: #555; line-height: 1.8; font-size: 15px; }
    .page-content ul { padding-left: 20px; }
    .page-content li { margin-bottom: 8px; }
</style>

<div class="page-hero">
    <div class="container" style="color: #fff;"><h1>Return Policy</h1></div>
</div>

<div class="page-content">
    <div class="container" style="max-width: 800px;">
        <h2>1. Return Eligibility</h2>
        <p>We accept returns within <strong>7 days</strong> of delivery if:</p>
        <ul>
            <li>The product is damaged, defective, or not as described</li>
            <li>The product is in its original packaging and unused</li>
            <li>You have the original receipt or order confirmation</li>
        </ul>

        <h2>2. Non-Returnable Items</h2>
        <ul>
            <li>Products that have been used, washed, or altered</li>
            <li>Items without original packaging</li>
            <li>Gift cards and promotional items</li>
        </ul>

        <h2>3. How to Initiate a Return</h2>
        <ul>
            <li>Contact our support team at <?= SITE_PHONE ?> or <?= SITE_EMAIL ?></li>
            <li>Provide your order number and reason for return</li>
            <li>We will arrange a pickup or guide you through the return process</li>
        </ul>

        <h2>4. Refund Process</h2>
        <p>Once we receive and inspect the returned item, your refund will be processed within <strong>5-7 business days</strong>. Refunds will be credited to your original payment method.</p>

        <h2>5. Exchange Policy</h2>
        <p>If you'd like to exchange a product for a different item, please contact us within 7 days of delivery. Exchanges are subject to product availability.</p>
    </div>
</div>
