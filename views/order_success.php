<?php
/**
 * Order Success Page - CrokersesMart
 * @var array $order  Order data
 * @var array $items  Order items
 */

use App\Helpers\Sanitizer;
?>

<style>
    .success-page { padding: 60px 0; background: var(--cm-gray-100); min-height: 80vh; }
    .success-card {
        background: var(--cm-white); border-radius: 16px; box-shadow: var(--cm-shadow-lg);
        padding: 48px 40px; max-width: 800px; margin: 0 auto; text-align: center;
    }
    .success-icon {
        width: 90px; height: 90px; border-radius: 50%;
        background: linear-gradient(135deg, #28a745, #20c997);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 24px; animation: popIn .5s ease;
    }
    .success-icon i { font-size: 42px; color: #fff; }
    @keyframes popIn {
        0% { transform: scale(0); opacity: 0; }
        60% { transform: scale(1.2); }
        100% { transform: scale(1); opacity: 1; }
    }
    .success-card h2 { font-weight: 800; color: var(--cm-dark); margin-bottom: 8px; }
    .success-card .subtitle { color: var(--cm-gray-500); font-size: 15px; margin-bottom: 28px; }
    .order-number-badge {
        display: inline-block; background: #fff0f0; color: var(--cm-primary);
        padding: 8px 24px; border-radius: 20px; font-weight: 700; font-size: 15px;
        margin-bottom: 32px; border: 2px solid var(--cm-primary-light);
    }
    .order-details { text-align: left; padding: 24px; background: var(--cm-gray-100); border-radius: 12px; margin-bottom: 28px; }
    .order-details h6 { font-weight: 700; color: var(--cm-dark); margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid var(--cm-gray-200); }
    .detail-row { display: flex; padding: 6px 0; font-size: 14px; }
    .detail-row .label { font-weight: 600; color: var(--cm-gray-500); min-width: 140px; }
    .detail-row .value { color: var(--cm-dark); font-weight: 500; }

    .items-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
    .items-table th { background: var(--cm-dark); color: #fff; padding: 12px 16px; font-size: 13px; font-weight: 600; text-align: left; }
    .items-table th:first-child { border-radius: 8px 0 0 0; }
    .items-table th:last-child { border-radius: 0 8px 0 0; text-align: right; }
    .items-table td { padding: 12px 16px; border-bottom: 1px solid var(--cm-gray-200); font-size: 13px; }
    .items-table td:last-child { text-align: right; font-weight: 600; }
    .items-table .item-img { width: 48px; height: 48px; object-fit: cover; border-radius: 6px; }

    .total-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; }
    .total-row.grand { border-top: 2px solid var(--cm-gray-200); margin-top: 8px; padding-top: 12px; font-weight: 700; font-size: 18px; }
    .total-row.grand span:last-child { color: var(--cm-primary); }

    .success-actions { display: flex; gap: 16px; justify-content: center; margin-top: 32px; flex-wrap: wrap; }
    .btn-continue {
        padding: 12px 32px; border: 2px solid var(--cm-primary); border-radius: 8px;
        background: transparent; color: var(--cm-primary); font-weight: 700; font-size: 14px;
        transition: all var(--cm-transition); text-decoration: none;
    }
    .btn-continue:hover { background: var(--cm-primary); color: #fff; }
    .btn-track {
        padding: 12px 32px; border: none; border-radius: 8px;
        background: var(--cm-primary); color: #fff; font-weight: 700; font-size: 14px;
        transition: all var(--cm-transition); text-decoration: none;
    }
    .btn-track:hover { background: var(--cm-primary-dark); }
</style>

<div class="success-page">
    <div class="container">
        <div class="success-card">

            <div class="success-icon">
                <i class="bi bi-check-lg"></i>
            </div>

            <h2>Thank You!</h2>
            <p class="subtitle">Your order has been placed successfully</p>

            <div class="order-number-badge">
                <i class="bi bi-hash"></i><?= htmlspecialchars($order['order_number'] ?? '') ?>
            </div>

            <!-- Order Details -->
            <div class="order-details">
                <h6><i class="bi bi-person me-2"></i>Order Details</h6>
                <div class="detail-row">
                    <span class="label">Name</span>
                    <span class="value"><?= Sanitizer::clean($order['customer_name'] ?? '') ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Phone</span>
                    <span class="value"><?= Sanitizer::clean($order['customer_phone'] ?? '') ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Address</span>
                    <span class="value"><?= Sanitizer::clean($order['shipping_address'] ?? '') ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Payment</span>
                    <span class="value"><?= ucfirst(str_replace('_', ' ', $order['payment_method'] ?? '')) ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Status</span>
                    <span class="value"><span class="badge bg-warning text-dark"><?= ucfirst($order['order_status'] ?? 'pending') ?></span></span>
                </div>
            </div>

            <!-- Order Items -->
            <?php if (!empty($items)): ?>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item):
                        $lineTotal = ($item['unit_price'] ?? 0) * ($item['quantity'] ?? 1);
                    ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <?php if (!empty($item['image'])): ?>
                                        <img src="<?= Sanitizer::image($item['image']) ?>" alt="" class="item-img">
                                    <?php endif; ?>
                                    <span><?= Sanitizer::clean($item['product_name'] ?? $item['name'] ?? '') ?></span>
                                </div>
                            </td>
                            <td><?= (int) ($item['quantity'] ?? 1) ?></td>
                            <td><?= Sanitizer::banglaPrice($item['unit_price'] ?? 0) ?></td>
                            <td><?= Sanitizer::banglaPrice($lineTotal) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>

            <!-- Totals -->
            <div class="order-details" style="background:transparent; padding:16px 24px;">
                <div class="total-row">
                    <span>Subtotal</span>
                    <span><?= Sanitizer::banglaPrice(($order['total_amount'] ?? 0) - ($order['shipping_cost'] ?? 0)) ?></span>
                </div>
                <div class="total-row">
                    <span>Shipping</span>
                    <span><?= Sanitizer::banglaPrice($order['shipping_cost'] ?? 0) ?></span>
                </div>
                <div class="total-row grand">
                    <span>Grand Total</span>
                    <span><?= Sanitizer::banglaPrice($order['total_amount'] ?? 0) ?></span>
                </div>
            </div>

            <!-- Actions -->
            <div class="success-actions">
                <a href="<?= APP_URL ?>/shop" class="btn-continue">
                    <i class="bi bi-arrow-left me-2"></i>Continue Shopping
                </a>
                <a href="<?= APP_URL ?>/order/track/<?= htmlspecialchars($order['order_number'] ?? '') ?>" class="btn-track">
                    <i class="bi bi-geo-alt me-2"></i>Track Order
                </a>
            </div>

        </div>
    </div>
</div>
