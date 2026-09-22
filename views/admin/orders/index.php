<?php
use App\Helpers\CSRF;
use App\Helpers\Sanitizer;
use App\Models\Order;

$mainStatuses = [
    'pending'   => 'pending',
    'confirmed' => 'confirmed',
    'processing'=> 'processing',
    'shipped'   => 'shipped',
    'out_for_delivery' => 'out_for_delivery',
    'delivered' => 'delivered',
    'cancelled' => 'cancelled',
];
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Orders</h4>
    <div class="text-muted">
        Total: <strong><?= $total ?? 0 ?></strong> orders
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <ul class="nav nav-pills flex-wrap" style="gap:4px;">
            <li class="nav-item">
                <a class="nav-link <?= empty($status) ? 'active' : '' ?>" href="<?= ADMIN_URL ?>orders">
                    All <span class="badge bg-secondary ms-1"><?= $total ?? 0 ?></span>
                </a>
            </li>
            <?php foreach ($mainStatuses as $label => $key):
                $count = $statusCounts[$key] ?? 0;
            ?>
                <li class="nav-item">
                    <a class="nav-link <?= ($status ?? '') === $key ? 'active' : '' ?>" href="<?= ADMIN_URL ?>orders?status=<?= $key ?>">
                        <?= ucfirst(str_replace('_', ' ', $label)) ?> <span class="badge bg-secondary ms-1"><?= $count ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($orders)): ?>
            <div class="text-center py-5">
                <i class="bi bi-cart fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No orders found</h5>
                <p class="text-muted">There are no orders matching your criteria.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th class="text-end">Amount</th>
                            <th class="text-center">Payment</th>
                            <th class="text-center">Status</th>
                            <th>Date</th>
                            <th class="text-center" style="width: 80px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order):
                            $statusInfo = Order::getStatusInfo($order['order_status']);
                        ?>
                            <tr>
                                <td>
                                    <strong>#<?= Sanitizer::clean($order['order_number']) ?></strong>
                                </td>
                                <td><?= Sanitizer::clean($order['customer_name']) ?></td>
                                <td><?= Sanitizer::clean($order['customer_phone']) ?></td>
                                <td class="text-end fw-bold">৳<?= number_format($order['total_amount'], 2) ?></td>
                                <td class="text-center">
                                    <?php if (($order['payment_status'] ?? '') === 'paid'): ?>
                                        <span class="badge bg-success">Paid</span>
                                    <?php elseif (($order['payment_status'] ?? '') === 'failed'): ?>
                                        <span class="badge bg-danger">Failed</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?= $statusInfo['color'] ?>"><?= Sanitizer::clean($statusInfo['label']) ?></span>
                                </td>
                                <td><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></td>
                                <td class="text-center">
                                    <a href="<?= ADMIN_URL ?>orders/view/<?= (int)$order['id'] ?>" class="btn btn-sm btn-outline-primary" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <?php
                    $perPage = 20;
                    $firstItem = ($page - 1) * $perPage + 1;
                    $lastItem = min($page * $perPage, $total);
                    $statusParam = !empty($status) ? '&status=' . urlencode($status) : '';
                    $searchParam = !empty($search) ? '&search=' . urlencode($search) : '';
                    $filterParams = $statusParam . $searchParam;
                ?>
                <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
                    <div class="text-muted small">
                        Showing <?= $firstItem ?> to <?= $lastItem ?> of <?= $total ?> orders
                    </div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= ADMIN_URL ?>orders?page=<?= $page - 1 ?><?= $filterParams ?>">Previous</a>
                                </li>
                            <?php endif; ?>
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= ADMIN_URL ?>orders?page=<?= $i ?><?= $filterParams ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= ADMIN_URL ?>orders?page=<?= $page + 1 ?><?= $filterParams ?>">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
