<?php
use App\Helpers\CSRF;
use App\Helpers\Sanitizer;
use App\Models\Order;

$statusInfo = Order::getStatusInfo($order['order_status']);
$normalFlow = Order::NORMAL_FLOW;
$currentFlowIndex = array_search($order['order_status'], $normalFlow);
if ($currentFlowIndex === false) $currentFlowIndex = -1;
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Order #<?= Sanitizer::clean($order['order_number']) ?></h4>
        <small class="text-muted">Placed on <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></small>
    </div>
    <a href="<?= ADMIN_URL ?>orders" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Orders
    </a>
</div>

<!-- Progress Tracker -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h6 class="mb-3 fw-bold">Order Progress</h6>
        <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap: 0;">
            <?php foreach ($normalFlow as $i => $step):
                $stepInfo = Order::getStatusInfo($step);
                $isCompleted = $i < $currentFlowIndex;
                $isCurrent = $i === $currentFlowIndex;
            ?>
                <div class="text-center flex-fill" style="min-width: 80px; position: relative;">
                    <div style="margin: 0 auto; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px;
                        <?php if ($isCompleted): ?>
                            background: var(--cm-primary); color: #fff;
                        <?php elseif ($isCurrent): ?>
                            background: var(--cm-primary); color: #fff; box-shadow: 0 0 0 4px rgba(255,56,56,.2);
                        <?php else: ?>
                            background: #e9ecef; color: #adb5bd;
                        <?php endif; ?>">
                        <?php if ($isCompleted): ?>
                            <i class="bi bi-check-lg"></i>
                        <?php else: ?>
                            <?= $i + 1 ?>
                        <?php endif; ?>
                    </div>
                    <div style="font-size: 11px; font-weight: 600; margin-top: 6px; color: <?= $isCompleted || $isCurrent ? 'var(--cm-dark)' : '#adb5bd' ?>;">
                        <?= $stepInfo['label'] ?>
                    </div>
                </div>
                <?php if ($i < count($normalFlow) - 1): ?>
                    <div style="flex: 1; height: 2px; min-width: 20px; background: <?= $i < $currentFlowIndex ? 'var(--cm-primary)' : '#e9ecef' ?>;"></div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php if (!in_array($order['order_status'], $normalFlow)): ?>
            <div class="mt-3 text-center">
                <span class="badge <?= $statusInfo['color'] ?> fs-6 px-3 py-2">
                    <i class="bi <?= $statusInfo['icon'] ?> me-1"></i> <?= Sanitizer::clean($statusInfo['label']) ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Order Items</h5>
                <span class="badge <?= $statusInfo['color'] ?> fs-6"><?= Sanitizer::clean($statusInfo['label']) ?></span>
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
                                            <?php if (!empty($item['main_image'])): ?>
                                                <img src="<?= Sanitizer::image($item['main_image']) ?>" alt="" class="rounded me-2" style="width: 45px; height: 45px; object-fit: cover;">
                                            <?php endif; ?>
                                            <div>
                                                <strong><?= Sanitizer::clean($item['product_name']) ?></strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center"><?= (int)$item['quantity'] ?></td>
                                    <td class="text-end">৳<?= number_format($item['unit_price'], 2) ?></td>
                                    <td class="text-end fw-bold">৳<?= number_format($item['unit_price'] * $item['quantity'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end">Subtotal:</td>
                                <td class="text-end">৳<?= number_format($order['total_amount'] - ($order['shipping_cost'] ?? 0), 2) ?></td>
                            </tr>
                            <?php if (!empty($order['shipping_cost'])): ?>
                                <tr>
                                    <td colspan="3" class="text-end">Shipping:</td>
                                    <td class="text-end">৳<?= number_format($order['shipping_cost'], 2) ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr class="border-top">
                                <td colspan="3" class="text-end fw-bold fs-5">Total:</td>
                                <td class="text-end fw-bold fs-5">৳<?= number_format($order['total_amount'], 2) ?></td>
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
                        <?php foreach ($history as $entry):
                            $histInfo = Order::getStatusInfo($entry['status']);
                        ?>
                            <div class="d-flex mb-3">
                                <div class="me-3">
                                    <span class="badge <?= $histInfo['color'] ?> rounded-circle p-2">
                                        <i class="bi <?= $histInfo['icon'] ?>" style="font-size: 0.5rem;"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <strong><?= Sanitizer::clean($histInfo['label']) ?></strong>
                                        <small class="text-muted"><?= date('d M Y, h:i A', strtotime($entry['created_at'])) ?></small>
                                    </div>
                                    <?php if (!empty($entry['note'])): ?>
                                        <p class="text-muted mb-0 mt-1"><?= Sanitizer::clean($entry['note']) ?></p>
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
                <p class="mb-1"><i class="bi bi-telephone me-2 text-muted"></i> <?= Sanitizer::clean($order['customer_phone']) ?></p>
                <?php if (!empty($order['customer_email'])): ?>
                    <p class="mb-1"><i class="bi bi-envelope me-2 text-muted"></i> <?= Sanitizer::clean($order['customer_email']) ?></p>
                <?php endif; ?>
                <hr>
                <p class="mb-1"><i class="bi bi-geo-alt me-2 text-muted"></i></p>
                <p class="text-muted small mb-0">
                    <?= Sanitizer::clean($order['shipping_address'] ?? '') ?>
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
                            <?php foreach (Order::STATUSES as $key => $info): ?>
                                <option value="<?= $key ?>" <?= $order['order_status'] === $key ? 'selected' : '' ?>>
                                    <?= Sanitizer::clean($info['label']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label">Note (optional)</label>
                        <textarea class="form-control" id="note" name="note" rows="3" placeholder="Add a note about this status change..."></textarea>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Update Status
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
                            <?php foreach (Order::PAYMENT_STATUSES as $ps): ?>
                                <option value="<?= $ps ?>" <?= ($order['payment_status'] ?? '') === $ps ? 'selected' : '' ?>>
                                    <?= ucfirst($ps) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <input type="text" class="form-control" id="payment_method" name="payment_method" value="<?= Sanitizer::clean($order['payment_method'] ?? '') ?>" placeholder="e.g. cod, sslcommerz">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-check-lg me-1"></i> Update Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
