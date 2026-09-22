<?php
use App\Helpers\CSRF;
use App\Helpers\Sanitizer;
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Users</h4>
    <div class="text-muted">
        Total: <strong><?= $total ?? 0 ?></strong> users
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="<?= ADMIN_URL ?>users" class="row g-3">
            <div class="col-md-8">
                <input type="text" name="search" class="form-control" placeholder="Search by name, email, phone..." value="<?= Sanitizer::clean($search ?? '') ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="bi bi-search me-1"></i> Search
                </button>
                <?php if (!empty($search)): ?>
                    <a href="<?= ADMIN_URL ?>users" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> Clear
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($users)): ?>
            <div class="text-center py-5">
                <i class="bi bi-people fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No users found</h5>
                <p class="text-muted">There are no users matching your search.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th class="text-center">Role</th>
                            <th class="text-center">Status</th>
                            <th>Joined</th>
                            <th class="text-center" style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px; font-size: 0.85rem;">
                                            <?= strtoupper(substr(Sanitizer::clean($user['name']), 0, 1)) ?>
                                        </div>
                                        <strong><?= Sanitizer::clean($user['name']) ?></strong>
                                    </div>
                                </td>
                                <td><?= Sanitizer::clean($user['email']) ?></td>
                                <td><?= Sanitizer::clean($user['phone'] ?? '—') ?></td>
                                <td class="text-center">
                                    <?php
                                    $roleClasses = [
                                        'admin' => 'bg-danger',
                                        'staff' => 'bg-warning text-dark',
                                        'customer' => 'bg-info',
                                    ];
                                    $roleClass = $roleClasses[$user['role']] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?= $roleClass ?>"><?= ucfirst(Sanitizer::clean($user['role'])) ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if ($user['status']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                                <td class="text-center">
                                    <form method="POST" action="<?= ADMIN_URL ?>users/toggleStatus/<?= (int)$user['id'] ?>" class="d-inline">
                                        <?= CSRF::field() ?>
                                        <button type="submit" class="btn btn-sm <?= $user['status'] ? 'btn-outline-warning' : 'btn-outline-success' ?>" title="<?= $user['status'] ? 'Deactivate' : 'Activate' ?>">
                                            <i class="bi bi-<?= $user['status'] ? 'slash-circle' : 'check-lg' ?>"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="<?= ADMIN_URL ?>users/destroy/<?= (int)$user['id'] ?>" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                        <?= CSRF::field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
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
                    $searchParam = !empty($search) ? '&search=' . urlencode($search) : '';
                ?>
                <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
                    <div class="text-muted small">
                        Showing <?= $firstItem ?> to <?= $lastItem ?> of <?= $total ?> users
                    </div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= ADMIN_URL ?>users?page=<?= $page - 1 ?><?= $searchParam ?>">Previous</a>
                                </li>
                            <?php endif; ?>
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= ADMIN_URL ?>users?page=<?= $i ?><?= $searchParam ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= ADMIN_URL ?>users?page=<?= $page + 1 ?><?= $searchParam ?>">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
