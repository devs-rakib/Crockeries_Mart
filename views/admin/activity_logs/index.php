<?php
use App\Helpers\Sanitizer;

$actionIcons = [
    'user_created'    => ['bi-person-plus', 'text-success'],
    'user_deleted'    => ['bi-person-dash', 'text-danger'],
    'role_changed'    => ['bi-shield-lock', 'text-primary'],
    'order_status'    => ['bi-cart-check', 'text-info'],
    'login'           => ['bi-box-arrow-in-right', 'text-warning'],
];
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Activity Logs</h4>
    <div class="text-muted">
        Total: <strong><?= $total ?? 0 ?></strong> entries
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="<?= ADMIN_URL ?>activity-logs" class="row g-3">
            <div class="col-md-8">
                <input type="text" name="search" class="form-control" placeholder="Search by action, target, user..." value="<?= Sanitizer::clean($search ?? '') ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="bi bi-search me-1"></i> Search
                </button>
                <?php if (!empty($search)): ?>
                    <a href="<?= ADMIN_URL ?>activity-logs" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> Clear
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($logs)): ?>
            <div class="text-center py-5">
                <i class="bi bi-clock-history fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No activity logs found</h5>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;"></th>
                            <th>Action</th>
                            <th>Target</th>
                            <th>User</th>
                            <th>Details</th>
                            <th>IP</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <?php
                            $actionKey = $log['action'] ?? '';
                            $iconData = $actionIcons[$actionKey] ?? ['bi-circle', 'text-secondary'];
                            ?>
                            <tr>
                                <td>
                                    <i class="bi <?= $iconData[0] ?> <?= $iconData[1] ?>"></i>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars(ucwords(str_replace('_', ' ', $actionKey))) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($log['target'] ?? '—') ?></td>
                                <td>
                                    <?php if (!empty($log['user_name'])): ?>
                                        <?= htmlspecialchars($log['user_name']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">System</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-muted"><?= htmlspecialchars(mb_strimwidth($log['details'] ?? '—', 0, 80, '...')) ?></small>
                                </td>
                                <td><small class="text-muted"><?= htmlspecialchars($log['ip_address'] ?? '—') ?></small></td>
                                <td><small class="text-muted"><?= date('d M Y H:i', strtotime($log['created_at'])) ?></small></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <?php
                    $perPage = 30;
                    $firstItem = ($page - 1) * $perPage + 1;
                    $lastItem = min($page * $perPage, $total);
                    $searchParam = !empty($search) ? '&search=' . urlencode($search) : '';
                ?>
                <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
                    <div class="text-muted small">
                        Showing <?= $firstItem ?> to <?= $lastItem ?> of <?= $total ?> entries
                    </div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= ADMIN_URL ?>activity-logs?page=<?= $page - 1 ?><?= $searchParam ?>">Previous</a>
                                </li>
                            <?php endif; ?>
                            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= ADMIN_URL ?>activity-logs?page=<?= $i ?><?= $searchParam ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= ADMIN_URL ?>activity-logs?page=<?= $page + 1 ?><?= $searchParam ?>">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
