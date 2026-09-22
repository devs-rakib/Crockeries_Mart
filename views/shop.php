<?php
/**
 * Shop - Product Listing Page
 * Variables in scope: $products, $pagination, $filters, $sort, $categories, $brands,
 *                     $currentCategory, $currentBrand
 */
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="bg-light py-3 mb-4">
    <div class="container">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/shop">Shop</a></li>
            <?php if (!empty($currentCategory)): ?>
                <li class="breadcrumb-item active" aria-current="page">
                    <?= htmlspecialchars($currentCategory['name']) ?>
                </li>
            <?php elseif (!empty($currentBrand)): ?>
                <li class="breadcrumb-item"><a href="<?= APP_URL ?>/shop">All Products</a></li>
                <li class="breadcrumb-item active" aria-current="page">
                    <?= htmlspecialchars($currentBrand['name']) ?>
                </li>
            <?php else: ?>
                <li class="breadcrumb-item active" aria-current="page">All Products</li>
            <?php endif; ?>
        </ol>
    </div>
</nav>

<!-- Page Title -->
<div class="container mb-4">
    <h1 class="h2">
        <?php if (!empty($currentCategory)): ?>
            <?= htmlspecialchars($currentCategory['name']) ?>
        <?php elseif (!empty($currentBrand)): ?>
            <?= htmlspecialchars($currentBrand['name']) ?> Products
        <?php else: ?>
            All Products
        <?php endif; ?>
    </h1>
</div>

<div class="shop-page-wrap">
<div class="container">
    <div class="row">

        <!-- LEFT SIDEBAR -->
        <div class="col-lg-3 mb-4">
            <form id="shopForm" method="GET" action="<?= APP_URL ?>/shop">

                <!-- Category Tree -->
                <div class="card mb-4">
                    <div class="card-header fw-semibold">Categories</div>
                    <div class="card-body p-2">
                        <ul class="list-unstyled category-tree mb-0">
                            <li>
                                <a href="<?= APP_URL ?>/shop"
                                   class="d-block py-1 px-2 text-decoration-none <?= empty($currentCategory) ? 'fw-bold text-primary' : 'text-dark' ?>">
                                    All Categories
                                </a>
                            </li>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $category): ?>
                                    <li>
                                        <a href="<?= APP_URL ?>/shop?category=<?= (int)$category['id'] ?>"
                                           class="d-block py-1 px-2 text-decoration-none <?= (!empty($currentCategory) && $currentCategory['id'] == $category['id']) ? 'fw-bold text-primary' : 'text-dark' ?>">
                                            <?= htmlspecialchars($category['name']) ?>
                                            <?php if (!empty($category['children']) && count($category['children']) > 0): ?>
                                                <i class="fas fa-chevron-down float-end mt-1 toggle-icon"
                                                   data-bs-toggle="collapse"
                                                   data-bs-target="#cat-children-<?= (int)$category['id'] ?>"></i>
                                            <?php endif; ?>
                                        </a>
                                        <?php if (!empty($category['children']) && count($category['children']) > 0): ?>
                                            <ul class="list-unstyled collapse <?= (!empty($currentCategory) && $currentCategory['id'] == $category['id']) ? 'show' : '' ?>"
                                                id="cat-children-<?= (int)$category['id'] ?>">
                                                <?php foreach ($category['children'] as $child): ?>
                                                    <li>
                                                        <a href="<?= APP_URL ?>/shop?category=<?= (int)$child['id'] ?>"
                                                           class="d-block py-1 px-2 ps-3 text-decoration-none <?= (!empty($currentCategory) && $currentCategory['id'] == $child['id']) ? 'fw-bold text-primary' : 'text-secondary' ?>">
                                                            <?= htmlspecialchars($child['name']) ?>
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <!-- Price Range Filter -->
                <div class="card mb-4">
                    <div class="card-header fw-semibold">Price Range</div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-6">
                                <label for="priceMin" class="form-label small">Min</label>
                                <input type="number" id="priceMin" name="price_min" class="form-control form-control-sm"
                                       placeholder="0" min="0"
                                       value="<?= !empty($filters['price_min']) ? (int)$filters['price_min'] : '' ?>">
                            </div>
                            <div class="col-6">
                                <label for="priceMax" class="form-label small">Max</label>
                                <input type="number" id="priceMax" name="price_max" class="form-control form-control-sm"
                                       placeholder="Any" min="0"
                                       value="<?= !empty($filters['price_max']) ? (int)$filters['price_max'] : '' ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Brand Filter -->
                <?php if (!empty($brands)): ?>
                    <div class="card mb-4">
                        <div class="card-header fw-semibold">Brands</div>
                        <div class="card-body">
                            <?php foreach ($brands as $brand): ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input brand-checkbox" type="checkbox"
                                           name="brands[]" value="<?= (int)$brand['id'] ?>"
                                           id="brand-<?= (int)$brand['id'] ?>"
                                           <?= (!empty($filters['brands']) && in_array($brand['id'], $filters['brands'])) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="brand-<?= (int)$brand['id'] ?>">
                                        <?= htmlspecialchars($brand['name']) ?>
                                        <?php if (!empty($brand['product_count'])): ?>
                                            <span class="text-muted small">(<?= (int)$brand['product_count'] ?>)</span>
                                        <?php endif; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Apply Filters -->
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-2"></i>Apply Filters
                </button>
                <a href="<?= APP_URL ?>/shop" class="btn btn-outline-secondary w-100 mt-2">
                    Clear All
                </a>
            </form>
        </div>

        <!-- RIGHT CONTENT -->
        <div class="col-lg-9">

            <!-- Sorting Bar -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 bg-light p-3 rounded">
                <div class="mb-2 mb-md-0">
                    <span class="text-muted">
                        Showing <?= count($products) ?> of <?= (int)$pagination['total'] ?> products
                    </span>
                </div>
                <div class="d-flex align-items-center">
                    <label for="sortSelect" class="me-2 text-nowrap">Sort by:</label>
                    <select id="sortSelect" class="form-select form-select-sm" style="width: auto;">
                        <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
                        <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                        <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                        <option value="popularity" <?= $sort === 'popularity' ? 'selected' : '' ?>>Popularity</option>
                        <option value="rating" <?= $sort === 'rating' ? 'selected' : '' ?>>Top Rated</option>
                    </select>
                </div>
            </div>

            <!-- Active Filters -->
            <?php if (!empty($filters)): ?>
                <div class="mb-3 d-flex flex-wrap gap-2">
                    <?php if (!empty($currentCategory)): ?>
                        <span class="badge bg-primary d-flex align-items-center">
                            <?= htmlspecialchars($currentCategory['name']) ?>
                            <a href="<?= APP_URL ?>/shop<?= http_build_query(array_filter(['sort' => $sort, 'price_min' => $filters['price_min'] ?? '', 'price_max' => $filters['price_max'] ?? ''])) ?>"
                               class="text-white ms-2 text-decoration-none">&times;</a>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($currentBrand)): ?>
                        <span class="badge bg-primary d-flex align-items-center">
                            <?= htmlspecialchars($currentBrand['name']) ?>
                            <a href="<?= APP_URL ?>/shop<?= http_build_query(array_filter(['category' => $currentCategory['id'] ?? '', 'sort' => $sort, 'price_min' => $filters['price_min'] ?? '', 'price_max' => $filters['price_max'] ?? ''])) ?>"
                               class="text-white ms-2 text-decoration-none">&times;</a>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($filters['price_min']) || !empty($filters['price_max'])): ?>
                        <span class="badge bg-primary d-flex align-items-center">
                            Price:
                            <?= !empty($filters['price_min']) ? '$' . (int)$filters['price_min'] : '$0' ?> -
                            <?= !empty($filters['price_max']) ? '$' . (int)$filters['price_max'] : 'Any' ?>
                            <a href="<?= APP_URL ?>/shop<?= http_build_query(array_filter(['category' => $currentCategory['id'] ?? '', 'sort' => $sort])) ?>"
                               class="text-white ms-2 text-decoration-none">&times;</a>
                        </span>
                    <?php endif; ?>
                    <?php if (!empty($filters['brands'])): ?>
                        <?php foreach ($brands as $brand): ?>
                            <?php if (in_array($brand['id'], $filters['brands'])): ?>
                                <span class="badge bg-primary d-flex align-items-center">
                                    <?= htmlspecialchars($brand['name']) ?>
                                </span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Product Grid -->
            <div id="productGrid" class="row g-2 gy-3">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $index => $product): ?>
                        <div class="col-6 col-md-4 col-lg-3 product-card-wrapper">
                            <?php
                            $currentProduct = $product;
                            $sanitize = function ($input) {
                                return htmlspecialchars($input ?? '', ENT_QUOTES, 'UTF-8');
                            };
                            ?>
                            <?php include __DIR__ . '/partials/product_card.php'; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h3>No products found</h3>
                            <p class="text-muted">Try adjusting your filters or browse all products.</p>
                            <a href="<?= APP_URL ?>/shop" class="btn btn-primary">View All Products</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($pagination['total_pages'] > 1): ?>
                <nav id="paginationLinks" aria-label="Product pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <!-- Previous -->
                        <li class="page-item <?= ($pagination['page'] <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link pagination-link" href="#" data-page="<?= $pagination['page'] - 1 ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

                        <?php
                        $startPage = max(1, $pagination['page'] - 2);
                        $endPage = min($pagination['total_pages'], $pagination['page'] + 2);

                        if ($startPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link pagination-link" href="#" data-page="1">1</a>
                            </li>
                            <?php if ($startPage > 2): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <li class="page-item <?= ($i === $pagination['page']) ? 'active' : '' ?>">
                                <a class="page-link pagination-link" href="#" data-page="<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($endPage < $pagination['total_pages']): ?>
                            <?php if ($endPage < $pagination['total_pages'] - 1): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link pagination-link" href="#" data-page="<?= $pagination['total_pages'] ?>">
                                    <?= $pagination['total_pages'] ?>
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- Next -->
                        <li class="page-item <?= ($pagination['page'] >= $pagination['total_pages']) ? 'disabled' : '' ?>">
                            <a class="page-link pagination-link" href="#" data-page="<?= $pagination['page'] + 1 ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>

        </div>
    </div>
</div>
</div>

<style>
.shop-page-wrap { padding-bottom: 60px; }
.category-tree li {
    border-bottom: 1px solid #f0f0f0;
}
.category-tree li:last-child {
    border-bottom: none;
}
.category-tree a:hover {
    background-color: #f8f9fa;
}
.product-card-wrapper {
    transition: all 0.3s ease;
}
.product-card-wrapper:hover {
    transform: translateY(-5px);
}
#shopForm .form-check-input:checked + .form-check-label {
    font-weight: 500;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const shopForm = document.getElementById('shopForm');
    const sortSelect = document.getElementById('sortSelect');
    const productGrid = document.getElementById('productGrid');

    // AJAX Sort
    sortSelect.addEventListener('change', function() {
        const url = new URL(window.location);
        url.searchParams.set('sort', this.value);
        window.location.href = url.toString();
    });

    // AJAX Filter Submission
    shopForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const params = new URLSearchParams(formData).toString();
        window.location.href = '<?= APP_URL ?>/shop?' + params;
    });

    // Pagination AJAX
    document.querySelectorAll('.pagination-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const page = this.dataset.page;
            const url = new URL(window.location);
            url.searchParams.set('page', page);
            window.location.href = url.toString();
        });
    });

    function buildProductCard(product) {
        return `
            <div class="card h-100 border-0 shadow-sm">
                <a href="<?= APP_URL ?>/product/${product.slug}">
                    <img src="${APP_URL}${product.image}" class="card-img-top" alt="${product.name}"
                         style="height: 220px; object-fit: cover;">
                </a>
                <div class="card-body d-flex flex-column">
                    <h6 class="card-title">
                        <a href="<?= APP_URL ?>/product/${product.slug}" class="text-decoration-none text-dark">
                            ${product.name}
                        </a>
                    </h6>
                    <div class="mb-2">
                        <span class="text-warning small">
                            ${Array(5).fill().map((_, i) => i < product.rating ? '&#9733;' : '&#9734;').join('')}
                        </span>
                        <span class="text-muted small">(${product.review_count})</span>
                    </div>
                    <div class="mt-auto">
                        <span class="fw-bold text-primary fs-5">$${product.price}</span>
                        ${product.discount_price ? `<span class="text-decoration-line-through text-muted ms-2">$${product.original_price}</span>` : ''}
                    </div>
                </div>
            </div>
        `;
    }
});
</script>
