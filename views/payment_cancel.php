<?php
/**
 * Payment Cancelled Page - CrokersesMart
 * @var string $orderNumber
 */

$orderNumber = $orderNumber ?? '';
?>

<style>
    .payment-result-page {
        min-height: 60vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px 0;
        background: var(--cm-gray-100);
    }
    .result-card {
        background: var(--cm-white);
        border-radius: var(--cm-radius);
        box-shadow: var(--cm-shadow);
        padding: 48px;
        text-align: center;
        max-width: 480px;
        width: 100%;
    }
    .result-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
        font-size: 36px;
    }
    .result-icon.cancel {
        background: #fff8e1;
        color: #f59e0b;
        border: 3px solid #fde68a;
    }
    .result-card h2 {
        font-weight: 700;
        color: var(--cm-dark);
        margin-bottom: 12px;
    }
    .result-card p {
        color: var(--cm-gray-500);
        font-size: 15px;
        margin-bottom: 8px;
    }
    .result-card .order-ref {
        background: var(--cm-gray-100);
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 600;
        color: var(--cm-dark);
        display: inline-block;
        margin: 12px 0 24px;
    }
    .result-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
        flex-wrap: wrap;
    }
    .result-actions .btn {
        padding: 10px 28px;
        font-weight: 600;
        border-radius: 8px;
    }
</style>

<div class="payment-result-page">
    <div class="container">
        <div class="result-card">
            <div class="result-icon cancel">
                <i class="fas fa-ban"></i>
            </div>
            <h2>Payment Cancelled</h2>
            <p>You cancelled the payment process. Your order has not been placed.</p>
            <?php if ($orderNumber): ?>
                <div class="order-ref">Order: <?= htmlspecialchars($orderNumber) ?></div>
            <?php endif; ?>
            <div class="result-actions">
                <a href="<?= APP_URL ?>/checkout" class="btn btn-primary">
                    <i class="fas fa-credit-card me-2"></i>Return to Checkout
                </a>
                <a href="<?= APP_URL ?>/shop" class="btn btn-outline-secondary">
                    <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                </a>
            </div>
        </div>
    </div>
</div>
