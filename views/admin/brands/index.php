<?php
use App\Helpers\CSRF;
use App\Helpers\Sanitizer;
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Brands</h4>
    <a href="<?= ADMIN_URL ?>brands/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add Brand
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($brands)): ?>
            <div class="text-center py-5">
                <i class="bi bi-tag fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No brands found</h5>
                <p class="text-muted">Start by adding your first brand.</p>
                <a href="<?= ADMIN_URL ?>brands/create" class="btn btn-primary">Add Brand</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">Logo</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($brands as $brand): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($brand['logo'])): ?>
                                        <img src="<?= Sanitizer::image($brand['logo']) ?>" alt="<?= Sanitizer::clean($brand['name']) ?>" class="rounded bg-light p-1" style="width: 50px; height: 50px; object-fit: contain;">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= Sanitizer::clean($brand['name']) ?></strong></td>
                                <td><code><?= Sanitizer::clean($brand['slug']) ?></code></td>
                                <td class="text-center">
                                    <?php if ($brand['status']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= ADMIN_URL ?>brands/edit/<?= (int)$brand['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="<?= ADMIN_URL ?>brands/destroy/<?= (int)$brand['id'] ?>" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this brand?');">
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
        <?php endif; ?>
    </div>
</div>
