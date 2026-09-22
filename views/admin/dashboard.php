<?php
$pageTitle = 'Dashboard';

$totalProducts   = $totalProducts ?? 0;
$totalOrders     = $totalOrders ?? 0;
$pendingOrders   = $pendingOrders ?? 0;
$processingOrders = $processingOrders ?? 0;
$completedOrders = $completedOrders ?? 0;
$cancelledOrders = $cancelledOrders ?? 0;
$totalUsers      = $totalUsers ?? 0;
$totalRevenue    = $totalRevenue ?? 0;
$recentOrders    = $recentOrders ?? [];
$dailyRevenue    = $dailyRevenue ?? [];
$orderStatusCounts = $orderStatusCounts ?? ['pending'=>0,'processing'=>0,'completed'=>0,'cancelled'=>0];
$stockReport     = $stockReport ?? [];
$newCustomers    = $newCustomers ?? [];
$metrics         = $metrics ?? [];
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

<!-- ═══ EVALUATION METRICS ═══ -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #8b5cf6 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted mb-1" style="font-size:12px;">Avg Order Value</div>
                        <div class="fs-5 fw-bold mb-0">৳<?= number_format($metrics['avg_order_value'] ?? 0, 2) ?></div>
                    </div>
                    <i class="bi bi-receipt" style="font-size:24px;color:#8b5cf6;opacity:.5;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #06b6d4 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted mb-1" style="font-size:12px;">Completion Rate</div>
                        <div class="fs-5 fw-bold mb-0"><?= $metrics['completion_rate'] ?? 0 ?>%</div>
                    </div>
                    <i class="bi bi-check-circle" style="font-size:24px;color:#06b6d4;opacity:.5;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #10b981 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted mb-1" style="font-size:12px;">Revenue Growth</div>
                        <div class="fs-5 fw-bold mb-0 <?= ($metrics['revenue_growth'] ?? 0) >= 0 ? 'text-success' : 'text-danger' ?>">
                            <?= ($metrics['revenue_growth'] ?? 0) >= 0 ? '+' : '' ?><?= $metrics['revenue_growth'] ?? 0 ?>%
                        </div>
                    </div>
                    <i class="bi bi-graph-up" style="font-size:24px;color:#10b981;opacity:.5;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #f59e0b !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-muted mb-1" style="font-size:12px;">Completed Orders</div>
                        <div class="fs-5 fw-bold mb-0"><?= number_format($metrics['completed_orders'] ?? 0) ?></div>
                    </div>
                    <i class="bi bi-trophy" style="font-size:24px;color:#f59e0b;opacity:.5;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ═══ CHARTS ROW ═══ -->
<div class="row g-3 mb-4">
    <!-- Revenue Bar Chart -->
    <div class="col-xl-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-bar-chart me-2"></i>Revenue Overview (Last 7 Days)</h6>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="260"></canvas>
            </div>
        </div>
    </div>
    <!-- Order Status Pie Chart -->
    <div class="col-xl-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-pie-chart me-2"></i>Order Status</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="orderStatusChart" height="220"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- ═══ STOCK REPORT + NEW CUSTOMERS ═══ -->
<div class="row g-3 mb-4">
    <!-- Stock In/Out Report -->
    <div class="col-xl-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-box-seam me-2"></i>Stock Status (Last 30 Days)</h6>
                <a href="<?= APP_URL ?>/admin/products" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($stockReport)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="font-size:12px;">Product</th>
                                    <th style="font-size:12px;" class="text-center">Current Stock</th>
                                    <th style="font-size:12px;" class="text-center">Sold (30d)</th>
                                    <th style="font-size:12px;" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stockReport as $item):
                                    $stock = (int) $item['stock_quantity'];
                                    $sold = (int) $item['total_sold'];
                                    if ($stock <= 0) { $statusText = 'Out of Stock'; $statusColor = 'danger'; }
                                    elseif ($stock <= 5) { $statusText = 'Low Stock'; $statusColor = 'warning'; }
                                    else { $statusText = 'In Stock'; $statusColor = 'success'; }
                                ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold" style="font-size:13px;"><?= htmlspecialchars($item['name']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($item['sku'] ?? '') ?></small>
                                        </td>
                                        <td class="text-center fw-bold"><?= $stock ?></td>
                                        <td class="text-center text-muted"><?= $sold ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-<?= $statusColor ?> bg-opacity-10 text-<?= $statusColor ?>" style="font-size:11px;">
                                                <?= $statusText ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-box-seam text-muted" style="font-size:36px;"></i>
                        <p class="text-muted mt-2 mb-0">No stock data available.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- New Customers -->
    <div class="col-xl-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-person-plus me-2"></i>New Customers (7 Days)</h6>
                <a href="<?= APP_URL ?>/admin/users" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($newCustomers)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($newCustomers as $customer): ?>
                            <div class="list-group-item d-flex align-items-center gap-3 py-3">
                                <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#ff3838,#ff6b6b);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0;">
                                    <?= strtoupper(substr($customer['name'], 0, 1)) ?>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold" style="font-size:13px;"><?= htmlspecialchars($customer['name']) ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($customer['email'] ?? $customer['phone']) ?></small>
                                </div>
                                <small class="text-muted" style="font-size:11px;"><?= date('d M', strtotime($customer['created_at'])) ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-people text-muted" style="font-size:36px;"></i>
                        <p class="text-muted mt-2 mb-0">No new customers this week.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
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
                            <th style="font-size:13px;font-weight:600;">Amount</th>
                            <th style="font-size:13px;font-weight:600;">Status</th>
                            <th style="font-size:13px;font-weight:600;">Date</th>
                            <th style="font-size:13px;font-weight:600;text-align:center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order):
                            $statusClass = match(strtolower($order['order_status'] ?? '')) {
                                'pending'   => 'warning',
                                'processing'=> 'info',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                                default     => 'secondary'
                            };
                        ?>
                            <tr>
                                <td><span class="fw-semibold">#<?= htmlspecialchars($order['order_number'] ?? '') ?></span></td>
                                <td><?= htmlspecialchars($order['customer_name'] ?? '') ?></td>
                                <td class="fw-semibold">৳<?= number_format($order['total_amount'] ?? 0, 2) ?></td>
                                <td>
                                    <span class="badge bg-<?= $statusClass ?> bg-opacity-10 text-<?= $statusClass ?> px-2 py-1" style="font-size:12px;font-weight:600;">
                                        <?= ucfirst(htmlspecialchars($order['order_status'] ?? 'pending')) ?>
                                    </span>
                                </td>
                                <td class="text-muted" style="font-size:13px;"><?= date('d M, Y', strtotime($order['created_at'] ?? 'now')) ?></td>
                                <td class="text-center">
                                    <a href="<?= APP_URL ?>/admin/orders/view/<?= $order['id'] ?? '' ?>" class="btn btn-sm btn-outline-primary" title="View Order">
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

<!-- ═══ CHART.JS SCRIPTS ═══ -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Revenue Bar Chart
    var revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        var revenueData = <?= json_encode($dailyRevenue) ?>;
        var labels = revenueData.map(function(d) {
            var date = new Date(d.date);
            return date.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
        });
        var revenues = revenueData.map(function(d) { return parseFloat(d.revenue) || 0; });
        var orderCounts = revenueData.map(function(d) { return parseInt(d.orders) || 0; });

        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Revenue (৳)',
                        data: revenues,
                        backgroundColor: 'rgba(255, 56, 56, 0.8)',
                        borderColor: '#ff3838',
                        borderWidth: 1,
                        borderRadius: 6,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Orders',
                        data: orderCounts,
                        type: 'line',
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        borderWidth: 2,
                        pointRadius: 4,
                        pointBackgroundColor: '#8b5cf6',
                        fill: true,
                        tension: 0.3,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'top' } },
                scales: {
                    y: {
                        beginAtZero: true,
                        position: 'left',
                        ticks: { callback: function(v) { return '৳' + v.toLocaleString(); } }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

    // Order Status Pie Chart
    var statusCtx = document.getElementById('orderStatusChart');
    if (statusCtx) {
        var statusData = <?= json_encode($orderStatusCounts) ?>;
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Processing', 'Completed', 'Cancelled'],
                datasets: [{
                    data: [statusData.pending, statusData.processing, statusData.completed, statusData.cancelled],
                    backgroundColor: ['#f59e0b', '#06b6d4', '#22c55e', '#ef4444'],
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true, pointStyle: 'circle' } }
                },
                cutout: '65%'
            }
        });
    }
});
</script>
