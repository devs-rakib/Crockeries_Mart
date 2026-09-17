<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/admin_header.php';

$totalProducts   = $totalProducts ?? 0;
$totalOrders     = $totalOrders ?? 0;
$pendingOrders   = $pendingOrders ?? 0;
$completedOrders = $completedOrders ?? 0;
$totalUsers      = $totalUsers ?? 0;
$totalRevenue    = $totalRevenue ?? 0;
$recentOrders    = $recentOrders ?? [];
$dailyRevenue    = $dailyRevenue ?? [];
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Dashboard</h4>
        <small class="text-muted">Welcome back, <?= htmlspecialchars($adminName ?? 'Admin') ?>. Here's your store overview.</small>
    </div>
    <div class="text-end">
        <small class="text-muted"><?= date('l, d M Y') ?></small>
    </div>
</div>

<!-- ═══ STAT CARDS ═══ -->
<div class="row g-3 mb-4">
    <!-- Total Products -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div style="width:56px;height:56px;border-radius:12px;background:#ebf5ff;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-box-seam" style="font-size:26px;color:#3b82f6;"></i>
                </div>
                <div>
                    <div class="text-muted mb-1" style="font-size:13px;">Total Products</div>
                    <div class="fs-4 fw-bold mb-0"><?= number_format($totalProducts) ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Orders -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div style="width:56px;height:56px;border-radius:12px;background:#fef3c7;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-cart-check" style="font-size:26px;color:#f59e0b;"></i>
                </div>
                <div>
                    <div class="text-muted mb-1" style="font-size:13px;">Total Orders</div>
                    <div class="fs-4 fw-bold mb-0"><?= number_format($totalOrders) ?></div>
                    <small class="text-muted"><?= number_format($pendingOrders) ?> pending</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Customers -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div style="width:56px;height:56px;border-radius:12px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-people" style="font-size:26px;color:#22c55e;"></i>
                </div>
                <div>
                    <div class="text-muted mb-1" style="font-size:13px;">Total Customers</div>
                    <div class="fs-4 fw-bold mb-0"><?= number_format($totalUsers) ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div style="width:56px;height:56px;border-radius:12px;background:#fdf2f8;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-currency-dollar" style="font-size:26px;color:#ec4899;"></i>
                </div>
                <div>
                    <div class="text-muted mb-1" style="font-size:13px;">Total Revenue</div>
                    <div class="fs-4 fw-bold mb-0">৳<?= number_format($totalRevenue, 2) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ═══ REVENUE CHART ═══ -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
        <h6 class="mb-0 fw-bold">Revenue Overview (Last 7 Days)</h6>
    </div>
    <div class="card-body">
        <?php if (!empty($dailyRevenue)): ?>
            <?php
            $maxRevenue = max(array_column($dailyRevenue, 'revenue'));
            $maxRevenue = $maxRevenue > 0 ? $maxRevenue : 1;
            ?>
            <div class="d-flex align-items-end gap-2" style="height:200px;">
                <?php foreach ($dailyRevenue as $day): ?>
                    <?php
                    $dayRevenue = (float) ($day['revenue'] ?? 0);
                    $barHeight  = round(($dayRevenue / $maxRevenue) * 100);
                    $label      = date('D', strtotime($day['date'] ?? 'today'));
                    ?>
                    <div class="flex-fill d-flex flex-column align-items-center h-100 justify-content-end">
                        <small class="text-muted mb-1" style="font-size:11px;">৳<?= number_format($dayRevenue, 0) ?></small>
                        <div style="width:100%;max-width:48px;height:<?= $barHeight ?>%;min-height:4px;background:linear-gradient(180deg,#ff3838,#ff6b6b);border-radius:6px 6px 0 0;transition:height 0.3s;"></div>
                        <small class="text-muted mt-2" style="font-size:11px;"><?= htmlspecialchars($label) ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5" id="revenueChartPlaceholder">
                <i class="bi bi-bar-chart-line text-muted" style="font-size:48px;"></i>
                <p class="text-muted mt-2 mb-0">No revenue data available yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ═══ RECENT ORDERS ═══ -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
        <h6 class="mb-0 fw-bold">Recent Orders</h6>
        <a href="<?= APP_URL ?>/admin/orders" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($recentOrders)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="font-size:13px;font-weight:600;">Order #</th>
                            <th style="font-size:13px;font-weight:600;">Customer</th>
                            <th style="font-size:13px;font-weight:600;">Phone</th>
                            <th style="font-size:13px;font-weight:600;">Amount</th>
                            <th style="font-size:13px;font-weight:600;">Status</th>
                            <th style="font-size:13px;font-weight:600;">Date</th>
                            <th style="font-size:13px;font-weight:600;text-align:center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order):
                            $statusClass = match(strtolower($order['status'] ?? '')) {
                                'pending'   => 'warning',
                                'processing'=> 'info',
                                'shipped'   => 'primary',
                                'delivered' => 'success',
                                'cancelled' => 'danger',
                                default     => 'secondary'
                            };
                        ?>
                            <tr>
                                <td>
                                    <span class="fw-semibold">#<?= htmlspecialchars($order['order_number'] ?? $order['id'] ?? '') ?></span>
                                </td>
                                <td><?= htmlspecialchars($order['customer_name'] ?? $order['name'] ?? '') ?></td>
                                <td><?= htmlspecialchars($order['phone'] ?? '') ?></td>
                                <td class="fw-semibold">৳<?= number_format($order['total_amount'] ?? $order['total'] ?? 0, 2) ?></td>
                                <td>
                                    <span class="badge bg-<?= $statusClass ?> bg-opacity-10 text-<?= $statusClass ?> px-2 py-1" style="font-size:12px;font-weight:600;">
                                        <?= ucfirst(htmlspecialchars($order['status'] ?? 'pending')) ?>
                                    </span>
                                </td>
                                <td class="text-muted" style="font-size:13px;"><?= date('d M, Y', strtotime($order['created_at'] ?? 'now')) ?></td>
                                <td class="text-center">
                                    <a href="<?= APP_URL ?>/admin/order/<?= $order['id'] ?? '' ?>" class="btn btn-sm btn-outline-primary" title="View Order">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-cart text-muted" style="font-size:48px;"></i>
                <p class="text-muted mt-2 mb-0">No orders yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/admin_footer.php'; ?>
