<?php
use App\Helpers\Sanitizer;
use App\Helpers\CSRF;
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0"><?= Sanitizer::clean($user['name']) ?></h4>
        <small class="text-muted">Joined <?= date('d M Y', strtotime($user['created_at'])) ?></small>
    </div>
    <a href="<?= ADMIN_URL ?>users" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Users
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">User Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Name:</strong> <?= Sanitizer::clean($user['name']) ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Email:</strong> <?= Sanitizer::clean($user['email']) ?>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Phone:</strong> <?= Sanitizer::clean($user['phone'] ?? '—') ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Role:</strong>
                        <?php
                            $roleClass = match($user['role'] ?? 'customer') {
                                'admin' => 'bg-danger',
                                'vendor' => 'bg-warning text-dark',
                                default => 'bg-info',
                            };
                        ?>
                        <span class="badge <?= $roleClass ?>"><?= ucfirst(Sanitizer::clean($user['role'] ?? 'customer')) ?></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        <?php if ($user['status'] ?? 1): ?>
                            <span class="badge bg-success">Active</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inactive</span>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Address:</strong> <?= Sanitizer::clean($user['address'] ?? '—') ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Orders</h5>
                <span class="badge bg-primary"><?= count($orders) ?> orders</span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($orders)): ?>
                    <div class="text-center py-4">
                        <p class="text-muted mb-0">No orders found for this user.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><strong>#<?= Sanitizer::clean($order['order_number']) ?></strong></td>
                                        <td><small><?= date('d M Y', strtotime($order['created_at'])) ?></small></td>
                                        <td class="text-end"><?= number_format($order['total_amount'], 2) ?></td>
                                        <td class="text-center">
                                            <?php
                                                $statusClass = match($order['status']) {
                                                    'pending' => 'bg-warning text-dark',
                                                    'processing' => 'bg-info',
                                                    'completed' => 'bg-success',
                                                    'cancelled' => 'bg-danger',
                                                    default => 'bg-secondary',
                                                };
                                            ?>
                                            <span class="badge <?= $statusClass ?>"><?= ucfirst(Sanitizer::clean($order['status'])) ?></span>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= ADMIN_URL ?>orders/view/<?= (int)$order['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">Actions</h5>
            </div>
            <div class="card-body d-grid gap-2">
                <?php if (($user['role'] ?? 'customer') !== 'admin'): ?>
                    <form method="POST" action="<?= ADMIN_URL ?>users/toggleStatus/<?= (int)$user['id'] ?>">
                        <?= CSRF::field() ?>
                        <button type="submit" class="btn btn-outline-warning w-100">
                            <i class="bi bi-toggle-on me-1"></i> Toggle Status
                        </button>
                    </form>
                    <form method="POST" action="<?= ADMIN_URL ?>users/destroy/<?= (int)$user['id'] ?>" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                        <?= CSRF::field() ?>
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-trash me-1"></i> Delete User
                        </button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-info mb-0">
                        <small><i class="bi bi-info-circle me-1"></i> Admin users cannot be deleted or deactivated from here.</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
