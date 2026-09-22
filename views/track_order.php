<?php
use App\Helpers\Sanitizer;
use App\Models\Order;

$statusInfo = Order::getStatusInfo($order['order_status']);
$normalFlow = Order::NORMAL_FLOW;
$currentFlowIndex = array_search($order['order_status'], $normalFlow);
if ($currentFlowIndex === false) $currentFlowIndex = -1;
$subtotal = ($order['total_amount'] ?? 0) - ($order['shipping_cost'] ?? 0);

$statusIcons = [
    'pending'    => 'bi-clock',
    'confirmed'  => 'bi-check-circle',
    'processing' => 'bi-gear',
    'shipped'    => 'bi-truck',
    'out_for_delivery' => 'bi-box-seam',
    'delivered'  => 'bi-check-circle-fill',
    'cancelled'  => 'bi-x-circle',
    'returned'   => 'bi-arrow-return-left',
    'refunded'   => 'bi-cash',
    'failed'     => 'bi-exclamation-circle',
];
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

    /* ── Progress Tracker ── */
    .progress-tracker { display: flex; align-items: flex-start; justify-content: space-between; position: relative; padding: 10px 0; }
    .progress-tracker .step { text-align: center; flex: 1; position: relative; z-index: 1; }
    .progress-tracker .step-dot {
        width: 40px; height: 40px; border-radius: 50%; margin: 0 auto 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; font-weight: 700; transition: all .3s;
    }
    .progress-tracker .step-dot.completed { background: var(--cm-primary); color: #fff; }
    .progress-tracker .step-dot.current { background: var(--cm-primary); color: #fff; box-shadow: 0 0 0 5px rgba(255,56,56,.2); }
    .progress-tracker .step-dot.upcoming { background: #e9ecef; color: #adb5bd; }
    .progress-tracker .step-label { font-size: 11px; font-weight: 600; color: var(--cm-gray-500); }
    .progress-tracker .step-label.active { color: var(--cm-dark); }
    .progress-tracker .step-date { font-size: 10px; color: var(--cm-gray-500); margin-top: 2px; }

    .progress-line {
        position: absolute; top: 30px; left: 10%; right: 10%; height: 3px;
        background: #e9ecef; z-index: 0;
    }
    .progress-line-fill {
        height: 100%; background: var(--cm-primary); transition: width .5s;
    }

    /* ── Vertical Timeline ── */
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

    @media (max-width: 767px) {
        .progress-tracker { flex-direction: column; align-items: flex-start; gap: 0; padding-left: 20px; }
        .progress-tracker .step { display: flex; align-items: flex-start; gap: 12px; text-align: left; flex: none; width: 100%; }
        .progress-tracker .step-dot { margin: 0; flex-shrink: 0; width: 36px; height: 36px; font-size: 14px; }
        .progress-tracker .step-info { padding-top: 6px; }
        .progress-tracker .step-label { font-size: 13px; }
        .progress-tracker .step-date { font-size: 11px; }
        .progress-line { display: none; }
        .mobile-vline { display: block !important; }
        .info-grid { grid-template-columns: 1fr; }
        .totals-box { max-width: 100%; }
    }
    @media (min-width: 768px) {
        .mobile-vline { display: none !important; }
    }
</style>

<div class="track-page">
    <div class="container">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= APP_URL ?>/">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= APP_URL ?>/my-orders">My Orders</a></li>
                <li class="breadcrumb-item active" aria-current="page">Order #<?= Sanitizer::clean($order['order_number']) ?></li>
            </ol>
        </nav>

        <h1 class="h2 mb-4"><i class="bi bi-box-seam me-2"></i>Order Details</h1>

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
                        <span class="badge <?= $statusInfo['color'] ?>">
                            <i class="bi <?= $statusInfo['icon'] ?> me-1"></i>
                            <?= Sanitizer::clean($statusInfo['label']) ?>
                        </span>
                    </div>
                </div>

                <!-- Visual Progress Tracker (Desktop) -->
                <div class="track-card mb-4 d-none d-md-block">
                    <div class="card-body-custom">
                        <div class="section-title"><i class="bi bi-geo-alt me-2"></i>Order Tracking</div>
                        <div class="progress-tracker" style="position:relative;">
                            <div class="progress-line">
                                <div class="progress-line-fill" style="width: <?= $currentFlowIndex >= 0 ? (($currentFlowIndex / (count($normalFlow) - 1)) * 100) : 0 ?>%;"></div>
                            </div>
                            <?php foreach ($normalFlow as $i => $step):
                                $sInfo = Order::getStatusInfo($step);
                                $isCompleted = $i < $currentFlowIndex;
                                $isCurrent = $i === $currentFlowIndex;
                                $dotClass = $isCompleted ? 'completed' : ($isCurrent ? 'current' : 'upcoming');
                                $labelClass = $isCompleted || $isCurrent ? 'active' : '';
                            ?>
                                <div class="step">
                                    <div class="step-dot <?= $dotClass ?>">
                                        <?php if ($isCompleted): ?>
                                            <i class="bi bi-check-lg"></i>
                                        <?php else: ?>
                                            <?= $i + 1 ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="step-label <?= $labelClass ?>"><?= $sInfo['label'] ?></div>
                                    <?php
                                    $matchingHistory = null;
                                    foreach ($history as $h) {
                                        if ($h['status'] === $step) {
                                            $matchingHistory = $h;
                                            break;
                                        }
                                    }
                                    ?>
                                    <?php if ($matchingHistory): ?>
                                        <div class="step-date"><?= date('d M, h:i A', strtotime($matchingHistory['created_at'])) ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Visual Progress Tracker (Mobile - Vertical) -->
                <div class="track-card mb-4 d-md-none">
                    <div class="card-body-custom">
                        <div class="section-title"><i class="bi bi-geo-alt me-2"></i>Order Tracking</div>
                        <div style="position:relative; padding-left: 24px;">
                            <div style="position:absolute; left:11px; top:4px; bottom:4px; width:2px; background:var(--cm-gray-200);"></div>
                            <?php foreach ($normalFlow as $i => $step):
                                $sInfo = Order::getStatusInfo($step);
                                $isCompleted = $i < $currentFlowIndex;
                                $isCurrent = $i === $currentFlowIndex;
                                $dotColor = $isCompleted || $isCurrent ? 'background:var(--cm-primary);color:#fff;' : 'background:#e9ecef;color:#adb5bd;';
                                $lineColor = $isCompleted ? 'background:var(--cm-primary);' : '';
                            ?>
                                <div style="display:flex; align-items:flex-start; gap:12px; margin-bottom:20px; position:relative;">
                                    <div style="position:absolute; left:-24px; top:0; width:24px; height:24px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; z-index:1; <?= $dotColor ?>">
                                        <?php if ($isCompleted): ?>
                                            <i class="bi bi-check-lg"></i>
                                        <?php else: ?>
                                            <?= $i + 1 ?>
                                        <?php endif; ?>
                                    </div>
                                    <div style="padding-top:2px;">
                                        <div style="font-weight:700; font-size:14px; color: <?= $isCompleted || $isCurrent ? 'var(--cm-dark)' : '#adb5bd' ?>;"><?= $sInfo['label'] ?></div>
                                        <?php
                                        $matchingHistory = null;
                                        foreach ($history as $h) {
                                            if ($h['status'] === $step) {
                                                $matchingHistory = $h;
                                                break;
                                            }
                                        }
                                        ?>
                                        <?php if ($matchingHistory): ?>
                                            <div style="font-size:12px; color:var(--cm-gray-500);"><?= date('d M, h:i A', strtotime($matchingHistory['created_at'])) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <?php if (!in_array($order['order_status'], $normalFlow)): ?>
                <div class="track-card mb-4">
                    <div class="card-body-custom text-center py-4">
                        <i class="bi <?= $statusInfo['icon'] ?> mb-2" style="font-size:48px; color:var(--cm-primary);"></i>
                        <h5 class="fw-bold"><?= Sanitizer::clean($statusInfo['label']) ?></h5>
                        <p class="text-muted mb-0"><?= $order['order_status'] === 'cancelled' ? 'Your order has been cancelled.' : ($order['order_status'] === 'returned' ? 'Your order has been returned.' : ($order['order_status'] === 'refunded' ? 'Your order has been refunded.' : 'Order status updated.')) ?></p>
                    </div>
                </div>
                <?php endif; ?>

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
                                        $lineTotal = ($item['unit_price'] ?? 0) * ($item['quantity'] ?? 1);
                                    ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <?php if (!empty($item['main_image'])): ?>
                                                        <img src="<?= Sanitizer::image($item['main_image']) ?>" alt="" class="item-img">
                                                    <?php else: ?>
                                                        <div class="item-img bg-light d-flex align-items-center justify-content-center rounded">
                                                            <i class="bi bi-image text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <div class="product-name"><?= Sanitizer::clean($item['product_name'] ?? '') ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center"><?= (int) ($item['quantity'] ?? 1) ?></td>
                                            <td class="text-end"><?= Sanitizer::banglaPrice($item['unit_price'] ?? 0) ?></td>
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
                                <span><?= Sanitizer::banglaPrice($order['shipping_cost'] ?? 0) ?></span>
                            </div>
                            <div class="total-row grand">
                                <span>Grand Total</span>
                                <span><?= Sanitizer::banglaPrice($order['total_amount'] ?? 0) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Status History -->
                <?php if (!empty($history)): ?>
                <div class="track-card mb-4">
                    <div class="card-body-custom">
                        <div class="section-title"><i class="bi bi-clock-history me-2"></i>Status History</div>
                        <div class="timeline">
                            <?php foreach ($history as $entry):
                                $histStatus = $entry['status'] ?? 'pending';
                                $histInfo = Order::getStatusInfo($histStatus);
                                $histIcon = $statusIcons[$histStatus] ?? 'bi-circle';
                            ?>
                                <div class="timeline-item">
                                    <div class="timeline-dot <?= $histInfo['color'] ?>">
                                        <i class="bi <?= $histIcon ?>"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="timeline-status"><?= Sanitizer::clean($histInfo['label']) ?></div>
                                        <div class="timeline-date"><?= date('d M Y, h:i A', strtotime($entry['created_at'])) ?></div>
                                        <?php if (!empty($entry['note'])): ?>
                                            <div class="timeline-note"><?= Sanitizer::clean($entry['note']) ?></div>
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
                        <div class="section-title"><i class="bi bi-person me-2"></i>Shipping Info</div>
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="label">Name</div>
                                <div class="value"><?= Sanitizer::clean($order['customer_name'] ?? '') ?></div>
                            </div>
                            <div class="info-item">
                                <div class="label">Phone</div>
                                <div class="value"><?= Sanitizer::clean($order['customer_phone'] ?? '') ?></div>
                            </div>
                            <div class="info-item" style="grid-column: 1 / -1;">
                                <div class="label">Address</div>
                                <div class="value"><?= Sanitizer::clean($order['shipping_address'] ?? '') ?></div>
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
