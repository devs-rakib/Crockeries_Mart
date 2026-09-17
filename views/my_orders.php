<?php
/**
 * My Orders - Order History Page
 * @var array $orders  Array of order objects
 */

use App\Helpers\Sanitizer;

$statusClasses = [
    'pending'    => 'bg-warning text-dark',
    'processing' => 'bg-info',
    'completed'  => 'bg-success',
    'cancelled'  => 'bg-danger',
    'shipped'    => 'bg-primary',
];
?>

<style>
    .orders-page { padding: 40px 0; background: var(--cm-gray-100); min-height: 70vh; }
    .orders-page h1 { font-weight: 800; color: var(--cm-dark); }

    .order-table { background: var(--cm-white); border-radius: 12px; box-shadow: var(--cm-shadow); overflow: hidden; }
    .order-table thead th {
        background: var(--cm-dark); color: #fff; font-size: 13px; font-weight: 600;
        padding: 14px 16px; white-space: nowrap;
    }
    .order-table tbody td { padding: 14px 16px; vertical-align: middle; font-size: 14px; color: var(--cm-dark); }
    .order-table tbody tr { border-bottom: 1px solid var(--cm-gray-200); transition: background var(--cm-transition); }
    .order-table tbody tr:last-child { border-bottom: none; }
    .order-table tbody tr:hover { background: var(--cm-gray-100); }

    .order-num { font-weight: 700; color: var(--cm-primary); }
    .order-date { color: var(--cm-gray-500); font-size: 13px; }

    .btn-view {
        display: inline-flex; align-items: center; gap: 4px; padding: 6px 14px;
        border-radius: 6px; font-size: 12px; font-weight: 600;
        border: 1px solid var(--cm-primary); color: var(--cm-primary);
        background: transparent; transition: all var(--cm-transition); text-decoration: none;
    }
    .btn-view:hover { background: var(--cm-primary); color: #fff; }

    /* Empty State */
    .empty-state {
        text-align: center; padding: 80px 20px; background: var(--cm-white);
        border-radius: 12px; box-shadow: var(--cm-shadow);
    }
    .empty-state i { font-size: 64px; color: var(--cm-gray-200); margin-bottom: 20px; display: block; }
    .empty-state h3 { font-weight: 700; color: var(--cm-dark); margin-bottom: 8px; }
    .empty-state p { color: var(--cm-gray-500); margin-bottom: 28px; }
    .empty-state .btn-shop {
        display: inline-flex; align-items: center; gap: 8px; padding: 12px 32px;
        background: var(--cm-primary); color: #fff; border: none; border-radius: 8px;
        font-weight: 700; font-size: 14px; transition: background var(--cm-transition); text-decoration: none;
    }
    .empty-state .btn-shop:hover { background: var(--cm-primary-dark); }

    /* Mobile Cards */
    .order-card {
        background: var(--cm-white); border-radius: 12px; box-shadow: var(--cm-shadow);
        padding: 16px; margin-bottom: 12px;
    }
    .order-card .card-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; }
    .order-card .card-top .order-num { font-size: 15px; }
    .order-card .card-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; border-top: 1px solid var(--cm-gray-200); }
    .order-card .card-row .label { color: var(--cm-gray-500); font-weight: 500; }
    .order-card .card-row .value { font-weight: 600; color: var(--cm-dark); }
    .order-card .card-actions { margin-top: 12px; padding-top: 10px; border-top: 1px solid var(--cm-gray-200); text-align: right; }

    @media (max-width: 767px) {
        .d-desktop { display: none !important; }
        .d-mobile { display: block !important; }
    }
    @media (min-width: 768px) {
        .d-mobile { display: none !important; }
    }
</style>

<div class="orders-page">
    <div class="container">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= APP_URL ?>/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">My Orders</li>
            </ol>
        </nav>

        <h1 class="h2 mb-4"><i class="bi bi-box-seam me-2"></i>My Orders</h1>

        <?php if (empty($orders)): ?>

            <!-- Empty State -->
            <div class="empty-state">
                <i class="bi bi-bag-x"></i>
                <h3>No orders yet</h3>
                <p>You haven't placed any orders. Start exploring our collection!</p>
                <a href="<?= APP_URL ?>/shop" class="btn-shop">
                    <i class="bi bi-arrow-left"></i> Start Shopping
                </a>
            </div>

        <?php else: ?>

            <!-- Desktop Table -->
            <div class="order-table d-desktop">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th class="text-center">Items</th>
                                <th class="text-end">Total</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order):
                                $statusKey = $order['order_status'] ?? $order['status'] ?? 'pending';
                                $statusClass = $statusClasses[$statusKey] ?? 'bg-secondary';
                                $itemCount = $order['item_count'] ?? count($order['items'] ?? []);
                            ?>
                                <tr>
                                    <td>
                                        <span class="order-num">#<?= Sanitizer::clean($order['order_number']) ?></span>
                                    </td>
                                    <td>
                                        <span class="order-date">
                                            <?= date('d M Y', strtotime($order['created_at'])) ?>
                                        </span>
                                        <br>
                                        <small class="text-muted"><?= date('h:i A', strtotime($order['created_at'])) ?></small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border"><?= (int) $itemCount ?> item<?= $itemCount != 1 ? 's' : '' ?></span>
                                    </td>
                                    <td class="text-end fw-bold" style="color: var(--cm-primary);">
                                        <?= Sanitizer::banglaPrice($order['total_amount'] ?? 0) ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= $statusClass ?> rounded-pill px-3 py-2">
                                            <?= ucfirst(Sanitizer::clean($statusKey)) ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= APP_URL ?>/order/track/<?= htmlspecialchars($order['order_number']) ?>" class="btn-view">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Cards -->
            <div class="d-mobile">
                <?php foreach ($orders as $order):
                    $statusKey = $order['order_status'] ?? $order['status'] ?? 'pending';
                    $statusClass = $statusClasses[$statusKey] ?? 'bg-secondary';
                    $itemCount = $order['item_count'] ?? count($order['items'] ?? []);
                ?>
                    <div class="order-card">
                        <div class="card-top">
                            <div>
                                <div class="order-num">#<?= Sanitizer::clean($order['order_number']) ?></div>
                                <small class="order-date"><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></small>
                            </div>
                            <span class="badge <?= $statusClass ?> rounded-pill px-3 py-2">
                                <?= ucfirst(Sanitizer::clean($statusKey)) ?>
                            </span>
                        </div>
                        <div class="card-row">
                            <span class="label">Items</span>
                            <span class="value"><?= (int) $itemCount ?> item<?= $itemCount != 1 ? 's' : '' ?></span>
                        </div>
                        <div class="card-row">
                            <span class="label">Total</span>
                            <span class="value" style="color: var(--cm-primary);">
                                <?= Sanitizer::banglaPrice($order['total_amount'] ?? 0) ?>
                            </span>
                        </div>
                        <div class="card-actions">
                            <a href="<?= APP_URL ?>/order/track/<?= htmlspecialchars($order['order_number']) ?>" class="btn-view">
                                <i class="bi bi-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>

    </div>
</div>
