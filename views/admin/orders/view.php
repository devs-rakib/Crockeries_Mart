<?php
use App\Helpers\CSRF;
use App\Helpers\Sanitizer;
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Order #<?= Sanitizer::clean($order['order_number']) ?></h4>
        <small class="text-muted">Placed on <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></small>
    </div>
    <a href="<?= ADMIN_URL ?>orders" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Orders
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Order Items</h5>
                <?php
                $statusClasses = [
                    'pending' => 'bg-warning text-dark',
                    'processing' => 'bg-info',
                    'completed' => 'bg-success',
                    'cancelled' => 'bg-danger',
                    'shipped' => 'bg-primary',
                ];
                $statusClass = $statusClasses[$order['status']] ?? 'bg-secondary';
                ?>
                <span class="badge <?= $statusClass ?> fs-6"><?= ucfirst(Sanitizer::clean($order['status'])) ?></span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($item['image'])): ?>
                                                <img src="<?= Sanitizer::image($item['image']) ?>" alt="" class="rounded me-2" style="width: 45px; height: 45px; object-fit: cover;">
                                            <?php endif; ?>
                                            <div>
                                                <strong><?= Sanitizer::clean($item['product_name']) ?></strong>
                                                <?php if (!empty($item['variant'])): ?>
                                                    <br><small class="text-muted"><?= Sanitizer::clean($item['variant']) ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center"><?= (int)$item['quantity'] ?></td>
                                    <td class="text-end">₹<?= number_format($item['price'], 2) ?></td>
                                    <td class="text-end fw-bold">₹<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end">Subtotal:</td>
                                <td class="text-end">₹<?= number_format($order['subtotal'] ?? $order['total_amount'], 2) ?></td>
                            </tr>
                            <?php if (!empty($order['shipping_charge'])): ?>
                                <tr>
                                    <td colspan="3" class="text-end">Shipping:</td>
                                    <td class="text-end">₹<?= number_format($order['shipping_charge'], 2) ?></td>
                                </tr>
                            <?php endif; ?>
                            <?php if (!empty($order['discount'])): ?>
                                <tr>
                                    <td colspan="3" class="text-end text-success">Discount:</td>
                                    <td class="text-end text-success">-₹<?= number_format($order['discount'], 2) ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr class="border-top">
                                <td colspan="3" class="text-end fw-bold fs-5">Total:</td>
                                <td class="text-end fw-bold fs-5">₹<?= number_format($order['total_amount'], 2) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <?php if (!empty($history)): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Status History</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <?php foreach ($history as $entry): ?>
                            <div class="d-flex mb-3">
                                <div class="me-3">
                                    <?php
                                    $histStatusClasses = [
                                        'pending' => 'bg-warning text-dark',
                                        'processing' => 'bg-info',
                                        'completed' => 'bg-success',
                                        'cancelled' => 'bg-danger',
                                        'shipped' => 'bg-primary',
                                    ];
                                    $histClass = $histStatusClasses[$entry['status']] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?= $histClass ?> rounded-circle p-2">
                                        <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <strong><?= ucfirst(Sanitizer::clean($entry['status'])) ?></strong>
                                        <small class="text-muted"><?= date('d M Y, h:i A', strtotime($entry['created_at'])) ?></small>
                                    </div>
                                    <?php if (!empty($entry['note'])): ?>
                                        <p class="text-muted mb-0 mt-1"><?= Sanitizer::clean($entry['note']) ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($entry['changed_by'])): ?>
                                        <small class="text-muted">by <?= Sanitizer::clean($entry['changed_by']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">Customer Info</h5>
            </div>
            <div class="card-body">
                <h6 class="mb-3"><?= Sanitizer::clean($order['customer_name']) ?></h6>
                <p class="mb-1"><i class="fas fa-phone me-2 text-muted"></i> <?= Sanitizer::clean($order['phone']) ?></p>
                <?php if (!empty($order['email'])): ?>
                    <p class="mb-1"><i class="fas fa-envelope me-2 text-muted"></i> <?= Sanitizer::clean($order['email']) ?></p>
                <?php endif; ?>
                <hr>
                <p class="mb-1"><i class="fas fa-map-marker-alt me-2 text-muted"></i></p>
                <p class="text-muted small mb-0">
                    <?= Sanitizer::clean($order['address'] ?? '') ?>
                    <?php if (!empty($order['city'])): ?>, <?= Sanitizer::clean($order['city']) ?><?php endif; ?>
                    <?php if (!empty($order['state'])): ?>, <?= Sanitizer::clean($order['state']) ?><?php endif; ?>
                    <?php if (!empty($order['pincode'])): ?> - <?= Sanitizer::clean($order['pincode']) ?><?php endif; ?>
                </p>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">Update Order Status</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= ADMIN_URL ?>orders/updateStatus/<?= (int)$order['id'] ?>">
                    <?= CSRF::field() ?>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                            <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                            <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label">Note (optional)</label>
                        <textarea class="form-control" id="note" name="note" rows="3" placeholder="Add a note about this status change..."></textarea>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">Payment Status</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= ADMIN_URL ?>orders/updatePayment/<?= (int)$order['id'] ?>">
                    <?= CSRF::field() ?>
                    <div class="mb-3">
                        <label for="payment_status" class="form-label">Payment Status</label>
                        <select class="form-select" id="payment_status" name="payment_status" required>
                            <option value="pending" <?= ($order['payment_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="paid" <?= ($order['payment_status'] ?? '') === 'paid' ? 'selected' : '' ?>>Paid</option>
                            <option value="failed" <?= ($order['payment_status'] ?? '') === 'failed' ? 'selected' : '' ?>>Failed</option>
                            <option value="refunded" <?= ($order['payment_status'] ?? '') === 'refunded' ? 'selected' : '' ?>>Refunded</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <input type="text" class="form-control" id="payment_method" name="payment_method" value="<?= Sanitizer::clean($order['payment_method'] ?? '') ?>" placeholder="e.g., COD, UPI, Card">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-save me-1"></i> Update Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php if (!empty($order['notes'])): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Customer Notes</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?= nl2br(Sanitizer::clean($order['notes'])) ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
