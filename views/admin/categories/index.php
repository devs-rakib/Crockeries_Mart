<?php
use App\Helpers\CSRF;
use App\Helpers\Sanitizer;
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Categories</h4>
    <a href="<?= ADMIN_URL ?>categories/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add Category
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($categories)): ?>
            <div class="text-center py-5">
                <i class="bi bi-folder fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No categories found</h5>
                <p class="text-muted">Start by creating your first category.</p>
                <a href="<?= ADMIN_URL ?>categories/create" class="btn btn-primary">Add Category</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Parent</th>
                            <th>Slug</th>
                            <th class="text-center">Featured</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Position</th>
                            <th class="text-center">Products</th>
                            <th class="text-center" style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($category['icon_class'])): ?>
                                        <i class="<?= Sanitizer::clean($category['icon_class']) ?> me-2"></i>
                                    <?php endif; ?>
                                    <?php if (!empty($category['image'])): ?>
                                        <img src="<?= Sanitizer::image($category['image']) ?>" alt="" class="rounded me-2" style="width: 30px; height: 30px; object-fit: cover;">
                                    <?php endif; ?>
                                    <strong><?= Sanitizer::clean($category['name']) ?></strong>
                                </td>
                                <td><?= Sanitizer::clean($category['parent_name'] ?? '—') ?></td>
                                <td><code><?= Sanitizer::clean($category['slug']) ?></code></td>
                                <td class="text-center">
                                    <?php if (!empty($category['is_featured'])): ?>
                                        <span class="badge bg-warning text-dark">Featured</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted">No</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($category['status']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center"><?= (int)($category['position'] ?? 0) ?></td>
                                <td class="text-center">
                                    <span class="badge bg-info"><?= (int)($category['products_count'] ?? 0) ?></span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= ADMIN_URL ?>categories/edit/<?= (int)$category['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="<?= ADMIN_URL ?>categories/destroy/<?= (int)$category['id'] ?>" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category? Products in this category will be unassigned.');">
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
