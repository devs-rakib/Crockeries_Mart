<?php
use App\Helpers\CSRF;

$statusClasses = [
    'open'    => 'bg-success',
    'replied' => 'bg-info',
    'closed'  => 'bg-secondary',
];
$statusClass = $statusClasses[$ticket['status']] ?? 'bg-secondary';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Ticket #<?= $ticket['id'] ?></h4>
        <small class="text-muted"><?= htmlspecialchars($ticket['subject']) ?></small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= APP_URL ?>/admin/support" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
        <form method="POST" action="<?= APP_URL ?>/admin/support/updateStatus/<?= $ticket['id'] ?>" class="d-inline">
            <?= CSRF::field() ?>
            <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                <option value="open" <?= $ticket['status'] === 'open' ? 'selected' : '' ?>>Open</option>
                <option value="replied" <?= $ticket['status'] === 'replied' ? 'selected' : '' ?>>Replied</option>
                <option value="closed" <?= $ticket['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
            </select>
        </form>
    </div>
</div>

<div class="row g-4">
    <!-- Ticket Info -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold">Customer Info</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Name</small>
                    <strong><?= htmlspecialchars($ticket['name']) ?></strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Email</small>
                    <a href="mailto:<?= htmlspecialchars($ticket['email']) ?>"><?= htmlspecialchars($ticket['email']) ?></a>
                </div>
                <?php if ($ticket['phone']): ?>
                    <div class="mb-3">
                        <small class="text-muted d-block">Phone</small>
                        <a href="tel:<?= htmlspecialchars($ticket['phone']) ?>"><?= htmlspecialchars($ticket['phone']) ?></a>
                    </div>
                <?php endif; ?>
                <div class="mb-3">
                    <small class="text-muted d-block">Category</small>
                    <span class="badge bg-light text-dark border"><?= ucfirst(htmlspecialchars($ticket['category'])) ?></span>
                </div>
                <?php if ($ticket['order_number']): ?>
                    <div class="mb-3">
                        <small class="text-muted d-block">Order Number</small>
                        <a href="<?= APP_URL ?>/admin/orders/view?search=<?= urlencode($ticket['order_number']) ?>">#<?= htmlspecialchars($ticket['order_number']) ?></a>
                    </div>
                <?php endif; ?>
                <div class="mb-3">
                    <small class="text-muted d-block">Status</small>
                    <span class="badge <?= $statusClass ?> rounded-pill px-3"><?= ucfirst($ticket['status']) ?></span>
                </div>
                <div>
                    <small class="text-muted d-block">Submitted</small>
                    <?= date('d M Y, h:i A', strtotime($ticket['created_at'])) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Conversation -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold">Conversation</h6>
            </div>
            <div class="card-body">
                <!-- Original Message -->
                <div class="mb-3 p-3 rounded" style="background:#f0f7ff;border-left:4px solid #3b82f6;">
                    <div class="fw-semibold mb-1" style="font-size:13px;">
                        <i class="bi bi-person me-1"></i> <?= htmlspecialchars($ticket['name']) ?>
                        <span class="text-muted ms-2" style="font-size:11px;"><?= date('d M Y, h:i A', strtotime($ticket['created_at'])) ?></span>
                    </div>
                    <div style="font-size:14px;line-height:1.6;"><?= nl2br(htmlspecialchars($ticket['message'])) ?></div>
                </div>

                <!-- Replies -->
                <?php if (!empty($replies)): ?>
                    <?php foreach ($replies as $reply):
                        $isAdmin = $reply['sender'] === 'admin';
                    ?>
                        <div class="mb-3 p-3 rounded" style="background:<?= $isAdmin ? '#f0fdf4' : '#f0f7ff' ?>;border-left:4px solid <?= $isAdmin ? '#22c55e' : '#3b82f6' ?>;<?= $isAdmin ? 'margin-left:20px;' : '' ?>">
                            <div class="fw-semibold mb-1" style="font-size:13px;">
                                <i class="bi bi-<?= $isAdmin ? 'shield-check' : 'person' ?> me-1"></i>
                                <?= htmlspecialchars($reply['sender_name'] ?? ($isAdmin ? 'Admin' : $ticket['name'])) ?>
                                <span class="badge bg-<?= $isAdmin ? 'success' : 'primary' ?> bg-opacity-10 text-<?= $isAdmin ? 'success' : 'primary' ?> ms-1" style="font-size:10px;"><?= ucfirst($reply['sender']) ?></span>
                                <span class="text-muted ms-2" style="font-size:11px;"><?= date('d M Y, h:i A', strtotime($reply['created_at'])) ?></span>
                            </div>
                            <div style="font-size:14px;line-height:1.6;"><?= nl2br(htmlspecialchars($reply['message'])) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Reply Form -->
                <?php if ($ticket['status'] !== 'closed'): ?>
                    <hr>
                    <form method="POST" action="<?= APP_URL ?>/admin/support/view/<?= $ticket['id'] ?>">
                        <?= CSRF::field() ?>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Reply</label>
                            <textarea class="form-control" name="message" rows="4" required placeholder="Type your reply..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-send me-1"></i> Send Reply
                        </button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-secondary mb-0 mt-3">
                        <i class="bi bi-lock me-1"></i> This ticket is closed.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
