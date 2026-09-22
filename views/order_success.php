<?php
/**
 * Order Success Page - CrokersesMart
 * @var array $order  Order data
 * @var array $items  Order items
 */
use App\Helpers\Sanitizer;
?>

<style>
    .success-page { padding: 60px 0; background: var(--cm-gray-100); min-height: 70vh; }
    .success-card { background: var(--cm-white); border-radius: var(--cm-radius); box-shadow: var(--cm-shadow); padding: 48px; text-align: center; max-width: 640px; margin: 0 auto; }
    .success-icon { width: 80px; height: 80px; border-radius: 50%; background: #d4edda; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; }
    .success-icon i { font-size: 36px; color: #198754; }
    .success-card h2 { font-weight: 700; color: var(--cm-dark); margin-bottom: 8px; }
    .success-card .order-number { font-size: 15px; color: var(--cm-gray-500); margin-bottom: 24px; }
    .success-card .order-number strong { color: var(--cm-primary); }
    .success-items { text-align: left; border-top: 1px solid var(--cm-gray-200); padding-top: 20px; margin-top: 12px; }
    .success-item { display: flex; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--cm-gray-100); }
    .success-item:last-child { border-bottom: none; }
    .success-item img { width: 48px; height: 48px; object-fit: cover; border-radius: 6px; }
    .success-item .item-name { font-size: 13px; font-weight: 600; color: var(--cm-dark); }
    .success-item .item-qty { font-size: 12px; color: var(--cm-gray-500); }
    .success-item .item-price { font-size: 13px; font-weight: 700; color: var(--cm-primary); margin-left: auto; white-space: nowrap; }
    .success-total { display: flex; justify-content: space-between; padding: 16px 0; font-weight: 700; font-size: 16px; border-top: 2px solid var(--cm-gray-200); margin-top: 8px; }
    .success-total span:last-child { color: var(--cm-primary); }
    .success-actions { display: flex; gap: 12px; justify-content: center; margin-top: 28px; }
    .success-actions .btn { padding: 10px 24px; border-radius: 8px; font-weight: 600; font-size: 14px; }
</style>

<div class="success-page">
    <div class="container">
        <div class="success-card">
            <div class="success-icon">
                <i class="bi bi-check-lg"></i>
            </div>
            <h2>Order Placed Successfully!</h2>
            <p class="order-number">Order Number: <strong><?= Sanitizer::clean($order['order_number']) ?></strong></p>

            <?php if (!empty($items)): ?>
            <div class="success-items">
                <?php foreach ($items as $item): ?>
                    <div class="success-item">
                        <img src="<?= Sanitizer::image($item['main_image'] ?? '') ?>" alt="<?= Sanitizer::clean($item['product_name']) ?>">
                        <div>
                            <div class="item-name"><?= Sanitizer::clean($item['product_name']) ?></div>
                            <div class="item-qty">Qty: <?= (int) $item['quantity'] ?></div>
                        </div>
                        <div class="item-price"><?= Sanitizer::banglaPrice((float) $item['subtotal']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="success-total">
                <span>Total</span>
                <span><?= Sanitizer::banglaPrice((float) $order['total_amount']) ?></span>
            </div>

            <div class="success-actions">
                <a href="<?= APP_URL ?>/shop" class="btn btn-primary">Continue Shopping</a>
                <a href="<?= APP_URL ?>/my-orders" class="btn btn-outline-secondary">View My Orders</a>
            </div>
        </div>
    </div>
</div>
