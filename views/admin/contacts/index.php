<?php
use App\Helpers\Sanitizer;
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Contacts</h4>
    <div class="text-muted">
        Total: <?= $total ?> messages
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($contacts)): ?>
            <div class="text-center py-5">
                <i class="fas fa-envelope-open fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No contact messages</h5>
                <p class="text-muted">When customers contact you, messages will appear here.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;"></th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th class="text-center" style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contacts as $contact): ?>
                            <tr class="<?= $contact['status'] == 0 ? 'table-light' : '' ?>">
                                <td>
                                    <?php if ($contact['status'] == 0): ?>
                                        <i class="fas fa-circle text-primary" style="font-size: 8px;"></i>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= Sanitizer::clean($contact['name']) ?></strong>
                                </td>
                                <td><?= Sanitizer::clean($contact['email']) ?></td>
                                <td><?= Sanitizer::clean($contact['subject'] ?? 'No subject') ?></td>
                                <td><small class="text-muted"><?= Sanitizer::timeAgo($contact['created_at']) ?></small></td>
                                <td class="text-center">
                                    <a href="<?= ADMIN_URL ?>contacts/view/<?= (int)$contact['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form method="POST" action="<?= ADMIN_URL ?>contacts/destroy/<?= (int)$contact['id'] ?>" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                        <?= CSRF::field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
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
                ?>
                <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
                    <div class="text-muted small">
                        Showing <?= $firstItem ?> to <?= $lastItem ?> of <?= $total ?> contacts
                    </div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= ADMIN_URL ?>contacts?page=<?= $page - 1 ?>">Previous</a>
                                </li>
                            <?php endif; ?>
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= ADMIN_URL ?>contacts?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= ADMIN_URL ?>contacts?page=<?= $page + 1 ?>">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
