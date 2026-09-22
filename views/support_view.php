<?php
use App\Helpers\CSRF;
use App\Helpers\Sanitizer;

$statusClasses = [
    'open'    => 'bg-success',
    'replied' => 'bg-info',
    'closed'  => 'bg-secondary',
];
$statusClass = $statusClasses[$ticket['status']] ?? 'bg-secondary';
?>

<style>
    .support-page { padding: 40px 0; background: var(--cm-gray-100); min-height: 70vh; }
    .ticket-card { background: var(--cm-white); border-radius: 12px; box-shadow: var(--cm-shadow); padding: 24px; margin-bottom: 20px; }
    .ticket-meta { display: flex; gap: 20px; flex-wrap: wrap; font-size: 13px; color: var(--cm-gray-500); margin-bottom: 16px; }
    .ticket-meta strong { color: var(--cm-dark); }
    .message-box { padding: 16px; border-radius: 10px; margin-bottom: 12px; }
    .message-box.customer { background: #f0f7ff; border-left: 4px solid #3b82f6; }
    .message-box.admin { background: #f0fdf4; border-left: 4px solid #22c55e; }
    .message-box .sender { font-weight: 700; font-size: 13px; margin-bottom: 4px; }
    .message-box .time { font-size: 11px; color: var(--cm-gray-500); }
    .message-box .text { margin-top: 8px; font-size: 14px; line-height: 1.6; }
    .reply-form textarea { border: 2px solid var(--cm-gray-200); border-radius: 8px; padding: 12px; font-size: 14px; width: 100%; }
    .reply-form textarea:focus { border-color: var(--cm-primary); outline: none; }
    .reply-form button { padding: 10px 24px; border: none; border-radius: 8px; background: var(--cm-primary); color: #fff; font-weight: 700; cursor: pointer; }
</style>

<div class="support-page">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= APP_URL ?>/">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= APP_URL ?>/support">Support</a></li>
                <li class="breadcrumb-item active">Ticket #<?= $ticket['id'] ?></li>
            </ol>
        </nav>

        <div class="ticket-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h4 class="fw-bold mb-0">#<?= $ticket['id'] ?> — <?= htmlspecialchars($ticket['subject']) ?></h4>
                <span class="badge <?= $statusClass ?> rounded-pill px-3 py-2"><?= ucfirst($ticket['status']) ?></span>
            </div>
            <div class="ticket-meta">
                <span><strong>Category:</strong> <?= ucfirst(htmlspecialchars($ticket['category'])) ?></span>
                <span><strong>Date:</strong> <?= date('d M Y, h:i A', strtotime($ticket['created_at'])) ?></span>
                <?php if ($ticket['order_number']): ?>
                    <span><strong>Order:</strong> #<?= htmlspecialchars($ticket['order_number']) ?></span>
                <?php endif; ?>
            </div>
            <div class="message-box customer">
                <div class="sender"><i class="bi bi-person me-1"></i><?= htmlspecialchars($ticket['name']) ?></div>
                <div class="text"><?= nl2br(htmlspecialchars($ticket['message'])) ?></div>
            </div>
        </div>

        <!-- Replies -->
        <?php if (!empty($replies)): ?>
            <?php foreach ($replies as $reply): ?>
                <div class="message-box <?= $reply['sender'] === 'admin' ? 'admin' : 'customer' ?>" style="margin-left: <?= $reply['sender'] === 'admin' ? '40px' : '0' ?>; margin-right: <?= $reply['sender'] === 'customer' ? '40px' : '0' ?>;">
                    <div class="sender">
                        <i class="bi bi-<?= $reply['sender'] === 'admin' ? 'shield-check' : 'person' ?> me-1"></i>
                        <?= htmlspecialchars($reply['sender_name'] ?? ($reply['sender'] === 'admin' ? 'Admin' : $ticket['name'])) ?>
                        <span class="time ms-2"><?= date('d M Y, h:i A', strtotime($reply['created_at'])) ?></span>
                    </div>
                    <div class="text"><?= nl2br(htmlspecialchars($reply['message'])) ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Reply Form -->
        <?php if ($ticket['status'] !== 'closed'): ?>
            <div class="ticket-card mt-3">
                <h6 class="fw-bold mb-3"><i class="bi bi-reply me-2"></i>Reply</h6>
                <div id="replyAlert" style="display:none;padding:10px 14px;border-radius:8px;margin-bottom:12px;font-size:13px;"></div>
                <form id="replyForm" class="reply-form">
                    <?= CSRF::field() ?>
                    <textarea name="message" rows="3" required placeholder="Type your reply..."></textarea>
                    <button type="submit" class="mt-2" id="btnReply">
                        <span class="btn-text">Send Reply</span>
                        <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-1"></span>Sending...</span>
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('replyForm');
    var btn = document.getElementById('btnReply');
    var alertBox = document.getElementById('replyAlert');

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(form);
            btn.disabled = true;
            btn.querySelector('.btn-text').classList.add('d-none');
            btn.querySelector('.btn-loading').classList.remove('d-none');

            fetch('<?= APP_URL ?>/support/ticket/<?= $ticket['id'] ?>/reply', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    location.reload();
                } else {
                    alertBox.textContent = data.message;
                    alertBox.style.display = 'block';
                    alertBox.style.background = '#fff0f0';
                    alertBox.style.color = '#dc3545';
                    btn.disabled = false;
                    btn.querySelector('.btn-text').classList.remove('d-none');
                    btn.querySelector('.btn-loading').classList.add('d-none');
                }
            })
            .catch(function() {
                alertBox.textContent = 'Network error. Please try again.';
                alertBox.style.display = 'block';
                alertBox.style.background = '#fff0f0';
                alertBox.style.color = '#dc3545';
                btn.disabled = false;
                btn.querySelector('.btn-text').classList.remove('d-none');
                btn.querySelector('.btn-loading').classList.add('d-none');
            });
        });
    }
});
</script>
