<?php use App\Helpers\Sanitizer; ?>

<style>
    .page-hero { background: linear-gradient(135deg, var(--cm-dark) 0%, var(--cm-dark-light) 100%); color: #fff; padding: 60px 0 40px; text-align: center; }
    .page-hero h1 { font-size: 32px; font-weight: 800; }
    .page-content { padding: 60px 0; }
    .page-content h2 { font-weight: 700; color: var(--cm-dark); margin: 24px 0 12px; font-size: 20px; }
    .page-content p, .page-content li { color: #555; line-height: 1.8; font-size: 15px; }
    .page-content ul { padding-left: 20px; }
    .page-content li { margin-bottom: 8px; }
    .shipping-table { width: 100%; border-collapse: collapse; margin: 16px 0; }
    .shipping-table th, .shipping-table td { padding: 12px 16px; border: 1px solid #e9ecef; text-align: left; }
    .shipping-table th { background: var(--cm-gray-100); font-weight: 600; color: var(--cm-dark); }
    .shipping-table td { color: #555; }
</style>

<div class="page-hero">
    <div class="container" style="color: #fff;">
        <h1>Shipping Info</h1>
    </div>
</div>

<div class="page-content">
    <div class="container" style="max-width: 800px;">
        <h2>Delivery Coverage</h2>
        <p>We deliver to all 64 districts across Bangladesh. Our delivery partners ensure your order reaches you safely and on time.</p>

        <h2>Shipping Charges</h2>
        <table class="shipping-table">
            <thead>
                <tr><th>Area</th><th>Shipping Fee</th><th>Estimated Delivery</th></tr>
            </thead>
            <tbody>
                <tr><td>Inside Dhaka</td><td>৳<?= SHIPPING_INSIDE_DHAKA ?></td><td>1-2 business days</td></tr>
                <tr><td>Outside Dhaka</td><td>৳<?= SHIPPING_OUTSIDE_DHAKA ?></td><td>3-5 business days</td></tr>
            </tbody>
        </table>

        <h2>Free Shipping</h2>
        <p>Enjoy <strong>FREE shipping</strong> on all orders above <strong>৳3,000</strong> within Bangladesh!</p>

        <h2>Order Processing</h2>
        <ul>
            <li>Orders placed before 12:00 PM are processed the same day</li>
            <li>Orders placed after 12:00 PM are processed the next business day</li>
            <li>You will receive a confirmation email/SMS once your order is shipped</li>
        </ul>

        <h2>Track Your Order</h2>
        <p>Once your order is shipped, you can track it using your order number. <a href="<?= APP_URL ?>/order/track">Track Order →</a></p>
    </div>
</div>
