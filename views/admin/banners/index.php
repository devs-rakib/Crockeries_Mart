<?php
use App\Helpers\CSRF;
use App\Helpers\Sanitizer;
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Banners</h4>
    <a href="<?= ADMIN_URL ?>banners/create" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Add Banner
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($banners)): ?>
            <div class="text-center py-5">
                <i class="fas fa-images fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No banners found</h5>
                <p class="text-muted">Start by adding your first banner.</p>
                <a href="<?= ADMIN_URL ?>banners/create" class="btn btn-primary">Add Banner</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 120px;">Image</th>
                            <th>Title</th>
                            <th class="text-center">Type</th>
                            <th class="text-center">Position</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($banners as $banner): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($banner['image'])): ?>
                                        <img src="<?= Sanitizer::image($banner['image']) ?>" alt="<?= Sanitizer::clean($banner['title']) ?>" class="rounded" style="width: 100px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 100px; height: 50px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= Sanitizer::clean($banner['title']) ?></strong></td>
                                <td class="text-center">
                                    <?php
                                    $typeClasses = [
                                        'hero_slider' => 'bg-primary',
                                        'middle_banner' => 'bg-info',
                                        'sidebar_banner' => 'bg-warning text-dark',
                                    ];
                                    $typeClass = $typeClasses[$banner['type']] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?= $typeClass ?>"><?= ucfirst(str_replace('_', ' ', $banner['type'])) ?></span>
                                </td>
                                <td class="text-center"><?= (int)($banner['position'] ?? 0) ?></td>
                                <td class="text-center">
                                    <?php if ($banner['status']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= ADMIN_URL ?>banners/edit/<?= (int)$banner['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="<?= ADMIN_URL ?>banners/destroy/<?= (int)$banner['id'] ?>" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this banner?');">
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
        <?php endif; ?>
    </div>
</div>
