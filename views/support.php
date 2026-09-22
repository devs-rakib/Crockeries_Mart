<?php
use App\Helpers\CSRF;
use App\Helpers\Sanitizer;

$statusClasses = [
    'open'    => 'bg-success',
    'replied' => 'bg-info',
    'closed'  => 'bg-secondary',
];
?>

<style>
    .support-page { padding: 40px 0; background: var(--cm-gray-100); min-height: 70vh; }
    .support-page h1 { font-weight: 800; color: var(--cm-dark); }
    .support-card { background: var(--cm-white); border-radius: 12px; box-shadow: var(--cm-shadow); padding: 24px; margin-bottom: 20px; }
    .support-card h5 { font-weight: 700; color: var(--cm-dark); margin-bottom: 20px; }
    .form-label { font-weight: 600; color: var(--cm-dark); font-size: 13px; }
    .form-control, .form-select { border: 2px solid var(--cm-gray-200); border-radius: 8px; padding: 10px 14px; font-size: 14px; }
    .form-control:focus, .form-select:focus { border-color: var(--cm-primary); box-shadow: 0 0 0 3px rgba(255,56,56,.1); }
    .btn-submit { padding: 12px 32px; border: none; border-radius: 8px; background: var(--cm-primary); color: #fff; font-weight: 700; font-size: 14px; cursor: pointer; }
    .btn-submit:hover { background: var(--cm-primary-dark); }
    .ticket-table { background: var(--cm-white); border-radius: 12px; box-shadow: var(--cm-shadow); overflow: hidden; }
    .ticket-table thead th { background: var(--cm-dark); color: #fff; font-size: 13px; font-weight: 600; padding: 14px 16px; }
    .ticket-table tbody td { padding: 14px 16px; font-size: 14px; }
    .ticket-table tbody tr { border-bottom: 1px solid var(--cm-gray-200); }
    .ticket-table tbody tr:hover { background: var(--cm-gray-100); }
    .empty-state { text-align: center; padding: 60px 20px; }
    .empty-state i { font-size: 48px; color: var(--cm-gray-200); }
</style>

<div class="support-page">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= APP_URL ?>/">Home</a></li>
                <li class="breadcrumb-item active">Support</li>
            </ol>
        </nav>

        <h1 class="h2 mb-4"><i class="bi bi-headset me-2"></i>Support Center</h1>

        <!-- Submit Ticket Form -->
        <div class="support-card">
            <h5><i class="bi bi-plus-circle me-2"></i>Submit a Ticket</h5>
            <div id="supportAlert" style="display:none;padding:10px 14px;border-radius:8px;margin-bottom:16px;font-size:13px;font-weight:500;"></div>
            <form id="supportForm" novalidate>
                <?= CSRF::field() ?>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required value="<?= Sanitizer::clean(Auth::name()) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" required value="<?= Sanitizer::clean(Auth::email() ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Phone</label>
                        <input type="tel" class="form-control" name="phone" placeholder="01XXXXXXXXX">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Subject <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="subject" required placeholder="Brief description of your issue">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category">
                            <option value="general">General</option>
                            <option value="order">Order Issue</option>
                            <option value="delivery">Delivery</option>
                            <option value="payment">Payment</option>
                            <option value="return">Return/Refund</option>
                            <option value="product">Product Issue</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Order Number</label>
                        <input type="text" class="form-control" name="order_number" placeholder="CMXXXXXXXXXX (optional)">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Message <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="message" rows="4" required placeholder="Describe your issue in detail..."></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn-submit" id="btnSubmit">
                            <span class="btn-text">Submit Ticket</span>
                            <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-2"></span>Submitting...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- My Tickets -->
        <div class="ticket-table">
            <div class="p-3 border-bottom">
                <h5 class="mb-0 fw-bold"><i class="bi bi-ticket me-2"></i>My Tickets</h5>
            </div>
            <?php if (!empty($tickets)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Subject</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tickets as $ticket):
                                $statusKey = $ticket['status'] ?? 'open';
                                $statusClass = $statusClasses[$statusKey] ?? 'bg-secondary';
                            ?>
                                <tr>
                                    <td class="fw-semibold">#<?= $ticket['id'] ?></td>
                                    <td><?= htmlspecialchars($ticket['subject']) ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= ucfirst(htmlspecialchars($ticket['category'])) ?></span></td>
                                    <td><span class="badge <?= $statusClass ?> rounded-pill px-3"><?= ucfirst($statusKey) ?></span></td>
                                    <td class="text-muted" style="font-size:13px;"><?= date('d M Y', strtotime($ticket['created_at'])) ?></td>
                                    <td class="text-end">
                                        <a href="<?= APP_URL ?>/support/ticket/<?= $ticket['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-ticket d-block"></i>
                    <p class="text-muted">No tickets yet. Submit a ticket above if you need help.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('supportForm');
    var btn = document.getElementById('btnSubmit');
    var alertBox = document.getElementById('supportAlert');

    function showAlert(msg, type) {
        alertBox.textContent = msg;
        alertBox.style.display = 'block';
        alertBox.style.background = type === 'success' ? '#d4edda' : '#fff0f0';
        alertBox.style.color = type === 'success' ? '#155724' : '#dc3545';
        alertBox.style.border = '1px solid ' + (type === 'success' ? '#c3e6cb' : '#f5c6cb');
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        alertBox.style.display = 'none';

        var formData = new FormData(form);
        btn.disabled = true;
        btn.querySelector('.btn-text').classList.add('d-none');
        btn.querySelector('.btn-loading').classList.remove('d-none');

        fetch('<?= APP_URL ?>/support/submit', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            showAlert(data.message, data.success ? 'success' : 'danger');
            if (data.success) form.reset();
            btn.disabled = false;
            btn.querySelector('.btn-text').classList.remove('d-none');
            btn.querySelector('.btn-loading').classList.add('d-none');
        })
        .catch(function() {
            showAlert('Network error. Please try again.', 'danger');
            btn.disabled = false;
            btn.querySelector('.btn-text').classList.remove('d-none');
            btn.querySelector('.btn-loading').classList.add('d-none');
        });
    });
});
</script>
