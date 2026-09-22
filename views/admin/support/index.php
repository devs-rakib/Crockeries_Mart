<?php
use App\Helpers\CSRF;

$statusClasses = [
    'open'    => 'bg-success',
    'replied' => 'bg-info',
    'closed'  => 'bg-secondary',
];
$counts = $counts ?? ['all'=>0,'open'=>0,'replied'=>0,'closed'=>0];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Support Tickets</h4>
    <span class="text-muted"><?= number_format($total) ?> total tickets</span>
</div>

<!-- Status Tabs -->
<div class="d-flex gap-2 mb-4 flex-wrap">
    <a href="<?= APP_URL ?>/admin/support" class="btn <?= $status === '' ? 'btn-primary' : 'btn-outline-secondary' ?> btn-sm rounded-pill px-3">
        All <span class="badge bg-white text-primary ms-1"><?= $counts['all'] ?></span>
    </a>
    <a href="<?= APP_URL ?>/admin/support?status=open" class="btn <?= $status === 'open' ? 'btn-success' : 'btn-outline-success' ?> btn-sm rounded-pill px-3">
        Open <span class="badge bg-white text-success ms-1"><?= $counts['open'] ?></span>
    </a>
    <a href="<?= APP_URL ?>/admin/support?status=replied" class="btn <?= $status === 'replied' ? 'btn-info' : 'btn-outline-info' ?> btn-sm rounded-pill px-3">
        Replied <span class="badge bg-white text-info ms-1"><?= $counts['replied'] ?></span>
    </a>
    <a href="<?= APP_URL ?>/admin/support?status=closed" class="btn <?= $status === 'closed' ? 'btn-secondary' : 'btn-outline-secondary' ?> btn-sm rounded-pill px-3">
        Closed <span class="badge bg-white text-secondary ms-1"><?= $counts['closed'] ?></span>
    </a>
</div>

<!-- Search -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="<?= APP_URL ?>/admin/support" class="d-flex gap-2">
            <input type="hidden" name="status" value="<?= htmlspecialchars($status) ?>">
            <input type="text" class="form-control" name="search" placeholder="Search by name, email, subject, order #..." value="<?= htmlspecialchars($search) ?>" style="max-width:400px;">
            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Search</button>
            <?php if ($search): ?>
                <a href="<?= APP_URL ?>/admin/support?status=<?= urlencode($status) ?>" class="btn btn-outline-secondary btn-sm">Clear</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Tickets Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <?php if (!empty($tickets)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="font-size:13px;">#</th>
                            <th style="font-size:13px;">Customer</th>
                            <th style="font-size:13px;">Subject</th>
                            <th style="font-size:13px;">Category</th>
                            <th style="font-size:13px;">Order #</th>
                            <th style="font-size:13px;">Status</th>
                            <th style="font-size:13px;">Date</th>
                            <th style="font-size:13px;text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $ticket):
                            $statusKey = $ticket['status'] ?? 'open';
                            $statusClass = $statusClasses[$statusKey] ?? 'bg-secondary';
                        ?>
                            <tr>
                                <td class="fw-semibold">#<?= $ticket['id'] ?></td>
                                <td>
                                    <div class="fw-semibold" style="font-size:13px;"><?= htmlspecialchars($ticket['name']) ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($ticket['email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($ticket['subject']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= ucfirst(htmlspecialchars($ticket['category'])) ?></span></td>
                                <td><?= $ticket['order_number'] ? '#' . htmlspecialchars($ticket['order_number']) : '-' ?></td>
                                <td><span class="badge <?= $statusClass ?> rounded-pill px-3"><?= ucfirst($statusKey) ?></span></td>
                                <td class="text-muted" style="font-size:13px;"><?= date('d M Y', strtotime($ticket['created_at'])) ?></td>
                                <td class="text-center">
                                    <a href="<?= APP_URL ?>/admin/support/view/<?= $ticket['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form method="POST" action="<?= APP_URL ?>/admin/support/destroy/<?= $ticket['id'] ?>" class="d-inline" onsubmit="return confirm('Delete this ticket?');">
                                        <?= CSRF::field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="d-flex justify-content-center py-3">
                    <nav>
                        <ul class="pagination mb-0">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= APP_URL ?>/admin/support?page=<?= $i ?>&status=<?= urlencode($status) ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-ticket text-muted" style="font-size:48px;"></i>
                <p class="text-muted mt-2 mb-0">No tickets found.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
