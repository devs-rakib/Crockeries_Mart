<?php
use App\Helpers\CSRF;
use App\Helpers\Sanitizer;
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Products</h4>
    <a href="<?= ADMIN_URL ?>products/create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add Product
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="<?= ADMIN_URL ?>products" class="row g-3">
            <div class="col-md-8">
                <input type="text" name="search" class="form-control" placeholder="Search products by name, SKU..." value="<?= Sanitizer::clean($search ?? '') ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="bi bi-search me-1"></i> Search
                </button>
                <?php if (!empty($search)): ?>
                    <a href="<?= ADMIN_URL ?>products" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> Clear
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($products)): ?>
            <div class="text-center py-5">
                <i class="bi bi-box fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No products found</h5>
                <p class="text-muted">Start by adding your first product.</p>
                <a href="<?= ADMIN_URL ?>products/create" class="btn btn-primary">Add Product</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">Image</th>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th class="text-end">Price</th>
                            <th class="text-center">Stock</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($product['main_image'])): ?>
                                        <img src="<?= Sanitizer::image($product['main_image']) ?>" alt="<?= Sanitizer::clean($product['name']) ?>" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= Sanitizer::clean($product['name']) ?></strong>
                                    <?php if (!empty($product['is_featured'])): ?>
                                        <span class="badge bg-warning text-dark ms-1">Featured</span>
                                    <?php endif; ?>
                                </td>
                                <td><code><?= Sanitizer::clean($product['sku']) ?></code></td>
                                <td><?= Sanitizer::clean($product['category_name'] ?? 'N/A') ?></td>
                                <td class="text-end">
                                    <?php if (!empty($product['discount_price']) && $product['discount_price'] < $product['price']): ?>
                                        <span class="text-decoration-line-through text-muted d-block"><?= number_format($product['price'], 2) ?></span>
                                        <span class="text-danger fw-bold"><?= number_format($product['discount_price'], 2) ?></span>
                                    <?php else: ?>
                                        <span class="fw-bold"><?= number_format($product['price'], 2) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($product['stock_quantity'] > 0): ?>
                                        <span class="badge bg-success"><?= $product['stock_quantity'] ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Out of Stock</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($product['status']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= ADMIN_URL ?>products/edit/<?= (int)$product['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="<?= ADMIN_URL ?>products/destroy/<?= (int)$product['id'] ?>" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
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
                        Showing <?= $firstItem ?> to <?= $lastItem ?> of <?= $total ?> products
                    </div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= ADMIN_URL ?>products?page=<?= $page - 1 ?><?= $searchParam ?>">Previous</a>
                                </li>
                            <?php endif; ?>
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= ADMIN_URL ?>products?page=<?= $i ?><?= $searchParam ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= ADMIN_URL ?>products?page=<?= $page + 1 ?><?= $searchParam ?>">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
