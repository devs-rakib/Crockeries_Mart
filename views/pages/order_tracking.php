<?php
use App\Helpers\Sanitizer;
use App\Helpers\CSRF;
?>

<style>
    .page-hero { background: linear-gradient(135deg, var(--cm-dark) 0%, var(--cm-dark-light) 100%); color: #fff; padding: 60px 0 40px; text-align: center; }
    .page-hero h1 { font-size: 32px; font-weight: 800; }
    .tracking-section { padding: 40px 0 60px; }
    .tracking-card { background: var(--cm-white); border-radius: 12px; box-shadow: var(--cm-shadow); padding: 32px; max-width: 600px; margin: 0 auto; }
    .tracking-card h5 { font-weight: 700; color: var(--cm-dark); margin-bottom: 20px; text-align: center; }
    .tracking-form { display: flex; gap: 10px; margin-bottom: 24px; }
    .tracking-form .form-control { border: 2px solid var(--cm-gray-200); border-radius: 8px; padding: 12px 16px; font-size: 14px; }
    .tracking-form .form-control:focus { border-color: var(--cm-primary); }
    .tracking-form .btn-track {
        padding: 12px 28px; border: none; border-radius: 8px; background: var(--cm-primary);
        color: #fff; font-weight: 600; cursor: pointer; white-space: nowrap;
    }
    .tracking-form .btn-track:hover { background: var(--cm-primary-dark); }
    .order-result { margin-top: 20px; }
    .order-result .result-header { background: var(--cm-gray-100); border-radius: 8px; padding: 16px; margin-bottom: 16px; }
    .result-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; border-bottom: 1px solid var(--cm-gray-200); }
    .result-row:last-child { border-bottom: none; }
    .result-label { color: var(--cm-gray-500); font-weight: 500; }
    .result-value { color: var(--cm-dark); font-weight: 600; }
    .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-processing { background: #cce5ff; color: #004085; }
    .status-completed { background: #d4edda; color: #155724; }
    .status-cancelled { background: #f8d7da; color: #721c24; }
    .status-paid { background: #d4edda; color: #155724; }
    .status-failed { background: #f8d7da; color: #721c24; }
    .not-found { text-align: center; padding: 20px; color: var(--cm-gray-500); }
    .not-found i { font-size: 48px; margin-bottom: 12px; display: block; color: var(--cm-gray-200); }
</style>

<div class="page-hero">
    <div class="container" style="color: #fff;">
        <h1>Order Tracking</h1>
    </div>
</div>

<div class="tracking-section">
    <div class="container">
        <div class="tracking-card">
            <h5><i class="bi bi-search me-2"></i>Track Your Order</h5>

            <form class="tracking-form" method="GET" action="<?= APP_URL ?>/order/track">
                <input type="text" class="form-control" name="order" placeholder="Enter order number (e.g. CM-260921-0001)" value="<?= Sanitizer::clean($orderNumber ?? '') ?>" required>
                <button type="submit" class="btn-track">Track</button>
            </form>

            <?php if (!empty($orderNumber)): ?>
                <?php if ($order): ?>
                    <div class="order-result">
                        <div class="result-header">
                            <div class="result-row">
                                <span class="result-label">Order Number</span>
                                <span class="result-value"><?= Sanitizer::clean($order['order_number']) ?></span>
                            </div>
                            <div class="result-row">
                                <span class="result-label">Date</span>
                                <span class="result-value"><?= date('M d, Y h:i A', strtotime($order['created_at'])) ?></span>
                            </div>
                            <div class="result-row">
                                <span class="result-label">Total</span>
                                <span class="result-value"><?= Sanitizer::banglaPrice($order['total_amount']) ?></span>
                            </div>
                            <div class="result-row">
                                <span class="result-label">Payment Status</span>
                                <span class="result-value"><span class="status-badge status-<?= $order['payment_status'] ?>"><?= ucfirst($order['payment_status']) ?></span></span>
                            </div>
                            <div class="result-row">
                                <span class="result-label">Order Status</span>
                                <span class="result-value"><span class="status-badge status-<?= $order['order_status'] ?>"><?= ucfirst($order['order_status']) ?></span></span>
                            </div>
                            <div class="result-row">
                                <span class="result-label">Shipping Address</span>
                                <span class="result-value"><?= Sanitizer::clean($order['shipping_address']) ?></span>
                            </div>
                        </div>

                        <?php if (!empty($orderItems)): ?>
                            <h6 class="fw-bold mt-3 mb-2">Items</h6>
                            <?php foreach ($orderItems as $item): ?>
                                <div class="result-row">
                                    <span class="result-label"><?= Sanitizer::clean($item['product_name']) ?> × <?= $item['quantity'] ?></span>
                                    <span class="result-value"><?= Sanitizer::banglaPrice($item['subtotal']) ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="not-found">
                        <i class="bi bi-exclamation-circle"></i>
                        <p>Order not found. Please check your order number and try again.</p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
