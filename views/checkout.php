<?php
/**
 * Checkout Page - CrokersesMart
 * @var array  $cart     Cart items
 * @var float  $subtotal Cart subtotal
 * @var array  $user     Logged-in user data (or null)
 */

use App\Helpers\CSRF;
use App\Helpers\Sanitizer;

$shippingInside  = SHIPPING_INSIDE_DHAKA;
$shippingOutside = SHIPPING_OUTSIDE_DHAKA;
$customerName    = $user['name'] ?? '';
$customerPhone   = $user['phone'] ?? '';
$customerEmail   = $user['email'] ?? '';
?>

<style>
    .checkout-page { padding: 40px 0; background: var(--cm-gray-100); min-height: 80vh; }
    .checkout-card { background: var(--cm-white); border-radius: var(--cm-radius); box-shadow: var(--cm-shadow); padding: 32px; }
    .checkout-card h5 { font-weight: 700; color: var(--cm-dark); margin-bottom: 24px; padding-bottom: 12px; border-bottom: 2px solid var(--cm-gray-200); }
    .checkout-card h5 i { color: var(--cm-primary); margin-right: 8px; }
    .form-label { font-weight: 600; color: var(--cm-dark); font-size: 13px; margin-bottom: 6px; }
    .form-control, .form-select { border: 2px solid var(--cm-gray-200); border-radius: 6px; padding: 10px 14px; font-size: 14px; transition: border-color var(--cm-transition); }
    .form-control:focus, .form-select:focus { border-color: var(--cm-primary); box-shadow: 0 0 0 3px rgba(255,56,56,.1); }
    textarea.form-control { resize: vertical; min-height: 80px; }

    .delivery-option, .payment-option {
        display: flex; align-items: center; gap: 12px;
        padding: 14px 16px; border: 2px solid var(--cm-gray-200); border-radius: 8px;
        cursor: pointer; transition: all var(--cm-transition); margin-bottom: 10px;
    }
    .delivery-option:hover, .payment-option:hover { border-color: var(--cm-primary-light); background: #fff5f5; }
    .delivery-option input:checked ~ .option-info,
    .payment-option input:checked ~ .option-info { color: var(--cm-primary); }
    .delivery-option:has(input:checked), .payment-option:has(input:checked) {
        border-color: var(--cm-primary); background: #fff0f0;
    }
    .payment-option.disabled {
        opacity: 0.6;
        cursor: not-allowed;
        background: var(--cm-gray-100);
        border-color: var(--cm-gray-200);
    }
    .payment-option.disabled:hover {
        border-color: var(--cm-gray-200);
        background: var(--cm-gray-100);
    }
    .delivery-option input, .payment-option input { accent-color: var(--cm-primary); width: 18px; height: 18px; }
    .option-info .option-title { font-weight: 600; font-size: 14px; color: var(--cm-dark); }
    .option-info .option-desc { font-size: 12px; color: var(--cm-gray-500); }
    .option-info .option-price { font-weight: 700; color: var(--cm-primary); font-size: 14px; margin-left: auto; white-space: nowrap; }
    .payment-icon { font-size: 22px; }

    .order-summary { background: var(--cm-white); border-radius: var(--cm-radius); box-shadow: var(--cm-shadow); padding: 28px; position: sticky; top: 100px; }
    .order-summary h5 { font-weight: 700; color: var(--cm-dark); margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid var(--cm-gray-200); }
    .summary-item { display: flex; gap: 12px; padding: 12px 0; border-bottom: 1px solid var(--cm-gray-200); }
    .summary-item:last-child { border-bottom: none; }
    .summary-item img { width: 56px; height: 56px; object-fit: cover; border-radius: 6px; flex-shrink: 0; }
    .summary-item .item-details { flex: 1; min-width: 0; }
    .summary-item .item-name { font-size: 13px; font-weight: 600; color: var(--cm-dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .summary-item .item-qty { font-size: 12px; color: var(--cm-gray-500); }
    .summary-item .item-price { font-size: 14px; font-weight: 700; color: var(--cm-primary); white-space: nowrap; }
    .summary-row { display: flex; justify-content: space-between; padding: 10px 0; font-size: 14px; }
    .summary-row.total { border-top: 2px solid var(--cm-gray-200); margin-top: 8px; padding-top: 14px; font-weight: 700; font-size: 18px; color: var(--cm-dark); }
    .summary-row.total span:last-child { color: var(--cm-primary); }
    .btn-place-order {
        width: 100%; padding: 14px; border: none; border-radius: 8px;
        background: var(--cm-primary); color: #fff; font-size: 16px; font-weight: 700;
        cursor: pointer; transition: all var(--cm-transition); margin-top: 16px;
    }
    .btn-place-order:hover { background: var(--cm-primary-dark); transform: translateY(-1px); }
    .btn-place-order:disabled { opacity: .6; cursor: not-allowed; transform: none; }
    .btn-place-order .spinner-border { width: 18px; height: 18px; border-width: 2px; }
    .checkout-alert { display: none; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; font-weight: 500; }
    .checkout-alert.show { display: block; }
    .checkout-alert.alert-danger { background: #fff0f0; color: #dc3545; border: 1px solid #f5c6cb; }
    .checkout-alert.alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }

    @media (max-width: 991px) {
        .order-summary { position: static; margin-top: 24px; }
    }
</style>

<div class="checkout-page">
    <div class="container">

        <div id="checkoutAlert" class="checkout-alert"></div>

        <div class="row g-4">

            <!-- LEFT: Checkout Form -->
            <div class="col-lg-7">
                <form id="checkoutForm" novalidate>
                    <?= CSRF::field() ?>

                    <!-- Customer Information -->
                    <div class="checkout-card mb-4">
                        <h5><i class="bi bi-person-circle"></i> Customer Information</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="customer_name" required placeholder="Enter your full name" value="<?= Sanitizer::clean($customerName) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" name="customer_phone" required placeholder="01XXXXXXXXX" value="<?= Sanitizer::clean($customerPhone) ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Email Address <small class="text-muted">(Optional)</small></label>
                                <input type="email" class="form-control" name="customer_email" placeholder="your@email.com" value="<?= Sanitizer::clean($customerEmail) ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Address -->
                    <div class="checkout-card mb-4">
                        <h5><i class="bi bi-geo-alt"></i> Delivery Address</h5>
                        <div class="mb-3">
                            <label class="form-label">Full Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="shipping_address" rows="3" required placeholder="House #, Road #, Area, City"></textarea>
                        </div>
                    </div>

                    <!-- Delivery Area -->
                    <div class="checkout-card mb-4">
                        <h5><i class="bi bi-truck"></i> Delivery Area</h5>
                        <div class="delivery-option">
                            <input type="radio" name="delivery_area" value="inside_dhaka" id="insideDhaka" checked>
                            <div class="option-info d-flex flex-grow-1 align-items-center">
                                <div>
                                    <div class="option-title">Inside Dhaka</div>
                                    <div class="option-desc">Delivery within Dhaka city</div>
                                </div>
                                <span class="option-price"><?= Sanitizer::banglaPrice($shippingInside) ?></span>
                            </div>
                        </div>
                        <div class="delivery-option">
                            <input type="radio" name="delivery_area" value="outside_dhaka" id="outsideDhaka">
                            <div class="option-info d-flex flex-grow-1 align-items-center">
                                <div>
                                    <div class="option-title">Outside Dhaka</div>
                                    <div class="option-desc">Delivery outside Dhaka city</div>
                                </div>
                                <span class="option-price"><?= Sanitizer::banglaPrice($shippingOutside) ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="checkout-card mb-4">
                        <h5><i class="bi bi-credit-card"></i> Payment Method</h5>

                        <div class="payment-option">
                            <input type="radio" name="payment_method" value="cod" id="payCod" checked>
                            <i class="bi bi-cash-stack payment-icon text-success"></i>
                            <div class="option-info d-flex flex-grow-1 align-items-center">
                                <div>
                                    <div class="option-title">Cash on Delivery (COD)</div>
                                    <div class="option-desc">Pay when you receive your order</div>
                                </div>
                            </div>
                        </div>

                        <div class="payment-option">
                            <input type="radio" name="payment_method" value="sslcommerz" id="paySsl">
                            <i class="bi bi-shield-lock payment-icon" style="color:#004b8d;"></i>
                            <div class="option-info d-flex flex-grow-1 align-items-center">
                                <div>
                                    <div class="option-title">SSLCommerz</div>
                                    <div class="option-desc">Credit/Debit Card, Net Banking</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-place-order" id="btnPlaceOrder">
                        <span class="btn-text"><i class="bi bi-bag-check me-2"></i>Place Order</span>
                        <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-2"></span>Processing...</span>
                    </button>
                </form>
            </div>

            <!-- RIGHT: Order Summary -->
            <div class="col-lg-5">
                <div class="order-summary">
                    <h5><i class="bi bi-receipt"></i> Order Summary</h5>

                    <div id="summaryItems">
                        <?php if (!empty($cart)): ?>
                            <?php foreach ($cart as $item):
                                $itemPrice = $item['discount_price'] ?: $item['price'];
                                $itemTotal = $itemPrice * $item['quantity'];
                            ?>
                                <div class="summary-item">
                                    <img src="<?= Sanitizer::image($item['image']) ?>" alt="<?= Sanitizer::clean($item['name']) ?>">
                                    <div class="item-details">
                                        <div class="item-name" title="<?= Sanitizer::clean($item['name']) ?>"><?= Sanitizer::clean($item['name']) ?></div>
                                        <div class="item-qty">Qty: <?= (int) $item['quantity'] ?></div>
                                    </div>
                                    <div class="item-price"><?= Sanitizer::banglaPrice($itemTotal) ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="summarySubtotal"><?= Sanitizer::banglaPrice($subtotal) ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span id="summaryShipping"><?= Sanitizer::banglaPrice($shippingInside) ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Grand Total</span>
                        <span id="summaryTotal"><?= Sanitizer::banglaPrice($subtotal + $shippingInside) ?></span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('checkoutForm');
    const btn  = document.getElementById('btnPlaceOrder');
    const alertBox = document.getElementById('checkoutAlert');

    const subtotal = <?= (float) $subtotal ?>;
    const shippingInside  = <?= (int) $shippingInside ?>;
    const shippingOutside = <?= (int) $shippingOutside ?>;

    function showAlert(msg, type) {
        alertBox.textContent = msg;
        alertBox.className = 'checkout-alert show alert-' + type;
    }

    function updateShipping() {
        const area = document.querySelector('input[name="delivery_area"]:checked').value;
        const shipping = area === 'inside_dhaka' ? shippingInside : shippingOutside;
        const total = subtotal + shipping;

        document.getElementById('summaryShipping').textContent = '৳' + shipping.toLocaleString();
        document.getElementById('summaryTotal').textContent = '৳' + total.toLocaleString();
    }

    document.querySelectorAll('input[name="delivery_area"]').forEach(function (el) {
        el.addEventListener('change', updateShipping);
    });

    document.querySelectorAll('.delivery-option').forEach(function (el) {
        el.addEventListener('click', function () {
            var radio = this.querySelector('input[type="radio"]');
            if (radio) { radio.checked = true; updateShipping(); }
        });
    });

    document.querySelectorAll('.payment-option').forEach(function (el) {
        el.addEventListener('click', function () {
            var radio = this.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
        });
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        alertBox.className = 'checkout-alert';

        const formData = new FormData(form);
        formData.append('action', 'place_order');

        btn.disabled = true;
        btn.querySelector('.btn-text').classList.add('d-none');
        btn.querySelector('.btn-loading').classList.remove('d-none');

        fetch('<?= APP_URL ?>/checkout/place', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                showAlert(data.message, 'success');
                setTimeout(function () {
                    window.location.href = data.redirect;
                }, 1000);
            } else {
                showAlert(data.message || 'Something went wrong', 'danger');
                btn.disabled = false;
                btn.querySelector('.btn-text').classList.remove('d-none');
                btn.querySelector('.btn-loading').classList.add('d-none');
            }
        })
        .catch(function () {
            showAlert('Network error. Please try again.', 'danger');
            btn.disabled = false;
            btn.querySelector('.btn-text').classList.remove('d-none');
            btn.querySelector('.btn-loading').classList.add('d-none');
        });
    });
});
</script>
