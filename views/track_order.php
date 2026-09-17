<?php
/**
 * Track Order Page
 * @var array $order   Order data
 * @var array $items   Order items
 * @var array $history Status history timeline
 */

use App\Helpers\Sanitizer;

$statusClasses = [
    'pending'    => 'bg-warning text-dark',
    'processing' => 'bg-info',
    'completed'  => 'bg-success',
    'cancelled'  => 'bg-danger',
    'shipped'    => 'bg-primary',
];

$statusIcons = [
    'pending'    => 'bi-clock',
    'processing' => 'bi-gear',
    'completed'  => 'bi-check-circle',
    'cancelled'  => 'bi-x-circle',
    'shipped'    => 'bi-truck',
];

$statusKey = $order['order_status'] ?? $order['status'] ?? 'pending';
$statusClass = $statusClasses[$statusKey] ?? 'bg-secondary';
$statusIcon = $statusIcons[$statusKey] ?? 'bi-circle';
$subtotal = ($order['total_amount'] ?? 0) - ($order['shipping_cost'] ?? $order['shipping_charge'] ?? 0);
?>

<style>
    .track-page { padding: 40px 0; background: var(--cm-gray-100); min-height: 70vh; }
    .track-page h1 { font-weight: 800; color: var(--cm-dark); }

    .track-card {
        background: var(--cm-white); border-radius: 12px;
        box-shadow: var(--cm-shadow); overflow: hidden;
    }
    .track-card .card-header-custom {
        background: var(--cm-dark); color: #fff; padding: 20px 24px;
        display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;
    }
    .track-card .card-header-custom .order-num { font-size: 18px; font-weight: 800; }
    .track-card .card-header-custom .badge { font-size: 14px; padding: 8px 18px; border-radius: 20px; }
    .track-card .card-body-custom { padding: 24px; }

    .section-title {
        font-size: 16px; font-weight: 700; color: var(--cm-dark);
        margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid var(--cm-gray-200);
        display: flex; align-items: center; gap: 8px;
    }
    .section-title i { color: var(--cm-primary); }

    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .info-item .label { font-size: 12px; color: var(--cm-gray-500); font-weight: 500; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 2px; }
    .info-item .value { font-size: 14px; color: var(--cm-dark); font-weight: 600; }

    .items-table { width: 100%; border-collapse: collapse; }
    .items-table th {
        background: var(--cm-gray-100); padding: 10px 14px; font-size: 12px;
        font-weight: 600; color: var(--cm-gray-500); text-transform: uppercase; letter-spacing: .5px;
        text-align: left; border-bottom: 2px solid var(--cm-gray-200);
    }
    .items-table th:last-child { text-align: right; }
    .items-table td { padding: 12px 14px; border-bottom: 1px solid var(--cm-gray-200); font-size: 13px; vertical-align: middle; }
    .items-table td:last-child { text-align: right; font-weight: 600; }
    .items-table .item-img { width: 48px; height: 48px; object-fit: cover; border-radius: 8px; }
    .items-table .product-name { font-weight: 600; color: var(--cm-dark); }

    .totals-box { max-width: 320px; margin-left: auto; }
    .total-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; }
    .total-row.grand {
        border-top: 2px solid var(--cm-gray-200); margin-top: 8px; padding-top: 12px;
        font-weight: 800; font-size: 18px;
    }
    .total-row.grand span:last-child { color: var(--cm-primary); }

    /* Timeline */
    .timeline { position: relative; padding-left: 30px; }
    .timeline::before {
        content: ''; position: absolute; left: 11px; top: 4px; bottom: 4px;
        width: 2px; background: var(--cm-gray-200);
    }
    .timeline-item { position: relative; padding-bottom: 28px; }
    .timeline-item:last-child { padding-bottom: 0; }
    .timeline-item .timeline-dot {
        position: absolute; left: -30px; top: 2px; width: 24px; height: 24px;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        z-index: 1; font-size: 11px; color: #fff;
    }
    .timeline-item .timeline-content { padding-left: 4px; }
    .timeline-item .timeline-status { font-weight: 700; font-size: 14px; color: var(--cm-dark); }
    .timeline-item .timeline-date { font-size: 12px; color: var(--cm-gray-500); margin-top: 2px; }
    .timeline-item .timeline-note {
        font-size: 13px; color: var(--cm-gray-500); margin-top: 4px;
        background: var(--cm-gray-100); padding: 8px 12px; border-radius: 6px;
    }

    .btn-continue {
        display: inline-flex; align-items: center; gap: 8px; padding: 12px 32px;
        background: var(--cm-primary); color: #fff; border: none; border-radius: 8px;
        font-weight: 700; font-size: 14px; transition: background var(--cm-transition); text-decoration: none;
    }
    .btn-continue:hover { background: var(--cm-primary-dark); color: #fff; }

    @media (max-width: 575px) {
        .info-grid { grid-template-columns: 1fr; }
        .totals-box { max-width: 100%; }
    }
</style>

<div class="track-page">
    <div class="container">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= APP_URL ?>/">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= APP_URL ?>/orders">My Orders</a></li>
                <li class="breadcrumb-item active" aria-current="page">Track Order</li>
            </ol>
        </nav>

        <h1 class="h2 mb-4"><i class="bi bi-geo-alt me-2"></i>Track Order</h1>

        <div class="row g-4">

            <!-- LEFT COLUMN -->
            <div class="col-lg-8">

                <!-- Order Header Card -->
                <div class="track-card mb-4">
                    <div class="card-header-custom">
                        <div>
                            <span class="order-num"><i class="bi bi-hash"></i><?= Sanitizer::clean($order['order_number']) ?></span>
                            <br><small class="text-white-50">Placed on <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></small>
                        </div>
                        <span class="badge <?= $statusClass ?>">
                            <i class="bi <?= $statusIcon ?> me-1"></i>
                            <?= ucfirst(Sanitizer::clean($statusKey)) ?>
                        </span>
                    </div>
                </div>

                <!-- Order Items -->
                <?php if (!empty($items)): ?>
                <div class="track-card mb-4">
                    <div class="card-body-custom">
                        <div class="section-title"><i class="bi bi-bag me-2"></i>Order Items</div>
                        <div class="table-responsive">
                            <table class="items-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item):
                                        $lineTotal = ($item['unit_price'] ?? $item['price'] ?? 0) * ($item['quantity'] ?? 1);
                                    ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <?php if (!empty($item['image'])): ?>
                                                        <img src="<?= Sanitizer::image($item['image']) ?>" alt="" class="item-img">
                                                    <?php else: ?>
                                                        <div class="item-img bg-light d-flex align-items-center justify-content-center rounded">
                                                            <i class="bi bi-image text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <div class="product-name"><?= Sanitizer::clean($item['product_name'] ?? $item['name'] ?? '') ?></div>
                                                        <?php if (!empty($item['variant'])): ?>
                                                            <small class="text-muted"><?= Sanitizer::clean($item['variant']) ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center"><?= (int) ($item['quantity'] ?? 1) ?></td>
                                            <td class="text-end"><?= Sanitizer::banglaPrice($item['unit_price'] ?? $item['price'] ?? 0) ?></td>
                                            <td class="text-end fw-bold"><?= Sanitizer::banglaPrice($lineTotal) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Totals -->
                        <div class="totals-box mt-3">
                            <div class="total-row">
                                <span>Subtotal</span>
                                <span><?= Sanitizer::banglaPrice($subtotal) ?></span>
                            </div>
                            <div class="total-row">
                                <span>Shipping</span>
                                <span><?= Sanitizer::banglaPrice($order['shipping_cost'] ?? $order['shipping_charge'] ?? 0) ?></span>
                            </div>
                            <div class="total-row grand">
                                <span>Grand Total</span>
                                <span><?= Sanitizer::banglaPrice($order['total_amount'] ?? 0) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Status Timeline -->
                <?php if (!empty($history)): ?>
                <div class="track-card mb-4">
                    <div class="card-body-custom">
                        <div class="section-title"><i class="bi bi-clock-history me-2"></i>Status History</div>
                        <div class="timeline">
                            <?php foreach ($history as $entry):
                                $histStatus = $entry['status'] ?? 'pending';
                                $histClass = $statusClasses[$histStatus] ?? 'bg-secondary';
                                $histIcon = $statusIcons[$histStatus] ?? 'bi-circle';
                            ?>
                                <div class="timeline-item">
                                    <div class="timeline-dot <?= $histClass ?>">
                                        <i class="bi <?= $histIcon ?>"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="timeline-status"><?= ucfirst(Sanitizer::clean($histStatus)) ?></div>
                                        <div class="timeline-date"><?= date('d M Y, h:i A', strtotime($entry['created_at'])) ?></div>
                                        <?php if (!empty($entry['note'])): ?>
                                            <div class="timeline-note"><?= Sanitizer::clean($entry['note']) ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($entry['changed_by'])): ?>
                                            <small class="text-muted">Updated by <?= Sanitizer::clean($entry['changed_by']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <!-- RIGHT COLUMN -->
            <div class="col-lg-4">

                <!-- Customer Info -->
                <div class="track-card mb-4">
                    <div class="card-body-custom">
                        <div class="section-title"><i class="bi bi-person me-2"></i>Customer Info</div>
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="label">Name</div>
                                <div class="value"><?= Sanitizer::clean($order['customer_name'] ?? '') ?></div>
                            </div>
                            <div class="info-item">
                                <div class="label">Phone</div>
                                <div class="value"><?= Sanitizer::clean($order['customer_phone'] ?? $order['phone'] ?? '') ?></div>
                            </div>
                            <div class="info-item" style="grid-column: 1 / -1;">
                                <div class="label">Address</div>
                                <div class="value"><?= Sanitizer::clean($order['shipping_address'] ?? $order['address'] ?? '') ?></div>
                            </div>
                            <div class="info-item">
                                <div class="label">Payment</div>
                                <div class="value"><?= ucfirst(str_replace('_', ' ', $order['payment_method'] ?? '')) ?></div>
                            </div>
                            <div class="info-item">
                                <div class="label">Payment Status</div>
                                <div class="value">
                                    <?php
                                    $payStatus = $order['payment_status'] ?? 'pending';
                                    $payClasses = [
                                        'pending'   => 'bg-warning text-dark',
                                        'paid'      => 'bg-success',
                                        'failed'    => 'bg-danger',
                                        'refunded'  => 'bg-info',
                                    ];
                                    ?>
                                    <span class="badge <?= $payClasses[$payStatus] ?? 'bg-secondary' ?>">
                                        <?= ucfirst(Sanitizer::clean($payStatus)) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Continue Shopping -->
                <div class="text-center">
                    <a href="<?= APP_URL ?>/shop" class="btn-continue">
                        <i class="bi bi-arrow-left"></i> Continue Shopping
                    </a>
                </div>

            </div>

        </div>
    </div>
</div>
