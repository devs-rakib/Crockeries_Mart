<?php
/**
 * Product Detail Page
 * Variables in scope: $product, $images, $variants, $relatedProducts,
 *                     $reviews, $avgRating, $ratingDist, $reviewCount
 */
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="bg-light py-3 mb-4">
    <div class="container">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/">Home</a></li>
            <?php if (!empty($product['category'])): ?>
                <li class="breadcrumb-item">
                    <a href="<?= APP_URL ?>/shop?category=<?= (int)$product['category_id'] ?>">
                        <?= htmlspecialchars($product['category']) ?>
                    </a>
                </li>
            <?php endif; ?>
            <li class="breadcrumb-item active" aria-current="page">
                <?= htmlspecialchars($product['name']) ?>
            </li>
        </ol>
    </div>
</nav>

<div class="container mb-5">

    <!-- Product Main Section -->
    <div class="row g-5">

        <!-- LEFT: Image Gallery -->
        <div class="col-lg-6 mb-4">
            <div class="position-relative">
                <!-- Main Image -->
                <div class="border rounded overflow-hidden mb-3" id="mainImageContainer">
                    <img id="mainImage"
                         src="<?= APP_URL . '/' . htmlspecialchars($images[0]['path'] ?? $product['image'] ?? '') ?>"
                         alt="<?= htmlspecialchars($product['name']) ?>"
                         class="img-fluid w-100"
                         style="max-height: 500px; object-fit: contain; cursor: crosshair;"
                         loading="eager">
                </div>

                <!-- Thumbnails -->
                <?php if (!empty($images) && count($images) > 1): ?>
                    <div class="d-flex gap-2 flex-wrap" id="thumbnailRow">
                        <?php foreach ($images as $index => $image): ?>
                            <button type="button"
                                    class="thumbnail-btn border rounded p-1 <?= $index === 0 ? 'border-primary' : '' ?>"
                                    data-index="<?= $index ?>"
                                    data-image="<?= APP_URL . '/' . htmlspecialchars($image['path']) ?>"
                                    data-zoom="<?= APP_URL . '/' . htmlspecialchars($image['path']) ?>">
                                <img src="<?= APP_URL . '/' . htmlspecialchars($image['path']) ?>"
                                     alt="<?= htmlspecialchars($product['name']) ?> - Image <?= $index + 1 ?>"
                                     width="70"
                                     height="70"
                                     style="object-fit: cover;"
                                     class="rounded">
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- RIGHT: Product Details -->
        <div class="col-lg-6">
            <h1 class="h2 mb-2"><?= htmlspecialchars($product['name']) ?></h1>

            <!-- SKU -->
            <p class="text-muted mb-3">SKU: <span class="fw-medium"><?= htmlspecialchars($product['sku'] ?? 'N/A') ?></span></p>

            <!-- Rating -->
            <div class="d-flex align-items-center mb-3">
                <div class="text-warning me-2">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?php if ($i <= floor($avgRating)): ?>
                            <i class="fas fa-star"></i>
                        <?php elseif ($i - $avgRating < 1 && $i - $avgRating > 0): ?>
                            <i class="fas fa-star-half-alt"></i>
                        <?php else: ?>
                            <i class="far fa-star"></i>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
                <span class="fw-semibold me-2"><?= number_format($avgRating, 1) ?></span>
                <span class="text-muted">(<?= (int)$reviewCount ?> reviews)</span>
            </div>

            <!-- Price -->
            <div class="mb-4 py-3 border-top border-bottom">
                <?php if (!empty($product['discount_price']) && $product['discount_price'] > 0): ?>
                    <span class="h3 text-danger me-2">$<?= number_format($product['discount_price'], 2) ?></span>
                    <span class="h5 text-muted text-decoration-line-through me-2">$<?= number_format($product['price'], 2) ?></span>
                    <span class="badge bg-danger fs-6">
                        -<?= round((($product['price'] - $product['discount_price']) / $product['price']) * 100) ?>% OFF
                    </span>
                <?php else: ?>
                    <span class="h3">$<?= number_format($product['price'], 2) ?></span>
                <?php endif; ?>
                <?php if (!empty($product['discount_price']) && $product['discount_price'] > 0): ?>
                    <p class="text-success small mb-0 mt-2">
                        <i class="fas fa-check-circle me-1"></i>
                        You save $<?= number_format($product['price'] - $product['discount_price'], 2) ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Short Description -->
            <?php if (!empty($product['short_description'])): ?>
                <div class="mb-4">
                    <?= nl2br(htmlspecialchars($product['short_description'])) ?>
                </div>
            <?php endif; ?>

            <!-- Color/Variant Selector -->
            <?php if (!empty($variants)): ?>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Color: <span id="selectedVariantLabel"></span></label>
                    <div class="d-flex gap-2 flex-wrap" id="variantSelector">
                        <?php foreach ($variants as $index => $variant): ?>
                            <button type="button"
                                    class="variant-btn rounded-circle border-2 shadow-sm <?= $index === 0 ? 'border-dark' : 'border-light' ?>"
                                    data-variant-id="<?= (int)$variant['id'] ?>"
                                    data-price="<?= number_format($variant['price'] ?? $product['price'], 2) ?>"
                                    data-stock="<?= (int)($variant['stock'] ?? 0) ?>"
                                    data-image="<?= !empty($variant['image']) ? APP_URL . '/' . htmlspecialchars($variant['image']) : '' ?>"
                                    data-name="<?= htmlspecialchars($variant['name'] ?? '') ?>"
                                    title="<?= htmlspecialchars($variant['name'] ?? 'Option ' . ($index + 1)) ?>"
                                    style="width: 40px; height: 40px; background-color: <?= htmlspecialchars($variant['color'] ?? '#cccccc') ?>;">
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Quantity -->
            <div class="mb-4">
                <label for="quantity" class="form-label fw-semibold">Quantity</label>
                <div class="d-flex align-items-center">
                    <button type="button" class="btn btn-outline-secondary" id="qtyMinus">
                        <i class="fas fa-minus"></i>
                    </button>
                    <input type="number" id="quantity" name="quantity" class="form-control text-center mx-2"
                           value="1" min="1" max="<?= (int)($product['stock'] ?? 10) ?>" style="width: 80px;">
                    <button type="button" class="btn btn-outline-secondary" id="qtyPlus">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>

            <!-- Stock Availability -->
            <div class="mb-4">
                <?php if (($product['stock'] ?? 0) > 0): ?>
                    <span class="text-success">
                        <i class="fas fa-check-circle me-1"></i>
                        <span id="stockStatus">In Stock</span>
                        <span class="text-muted">(<?= (int)$product['stock'] ?> available)</span>
                    </span>
                <?php else: ?>
                    <span class="text-danger">
                        <i class="fas fa-times-circle me-1"></i>
                        Out of Stock
                    </span>
                <?php endif; ?>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-3 mb-4">
                <button type="button" class="btn btn-primary btn-lg flex-grow-1" id="addToCartBtn"
                        data-product-id="<?= (int)$product['id'] ?>"
                        <?= ($product['stock'] ?? 0) <= 0 ? 'disabled' : '' ?>>
                    <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                </button>
                <button type="button" class="btn btn-outline-primary btn-lg" id="buyNowBtn"
                        data-product-id="<?= (int)$product['id'] ?>"
                        <?= ($product['stock'] ?? 0) <= 0 ? 'disabled' : '' ?>>
                    <i class="fas fa-bolt me-2"></i>Buy Now
                </button>
                <button type="button" class="btn btn-outline-danger btn-lg" id="wishlistBtn"
                        data-product-id="<?= (int)$product['id'] ?>">
                    <i class="far fa-heart" id="wishlistIcon"></i>
                </button>
            </div>

            <!-- Product Meta -->
            <div class="border-top pt-3">
                <?php if (!empty($product['brand'])): ?>
                    <p class="mb-1"><strong>Brand:</strong>
                        <a href="<?= APP_URL ?>/shop?brand=<?= (int)$product['brand_id'] ?>" class="text-decoration-none">
                            <?= htmlspecialchars($product['brand']) ?>
                        </a>
                    </p>
                <?php endif; ?>
                <?php if (!empty($product['category'])): ?>
                    <p class="mb-1"><strong>Category:</strong>
                        <a href="<?= APP_URL ?>/shop?category=<?= (int)$product['category_id'] ?>" class="text-decoration-none">
                            <?= htmlspecialchars($product['category']) ?>
                        </a>
                    </p>
                <?php endif; ?>
                <?php if (!empty($product['tags'])): ?>
                    <p class="mb-0"><strong>Tags:</strong>
                        <?php
                        $tags = is_string($product['tags']) ? explode(',', $product['tags']) : $product['tags'];
                        foreach ($tags as $tag):
                        ?>
                            <span class="badge bg-light text-dark border me-1"><?= htmlspecialchars(trim($tag)) ?></span>
                        <?php endforeach; ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Product Tabs -->
    <div class="mt-5">
        <ul class="nav nav-tabs" id="productTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="description-tab" data-bs-toggle="tab"
                        data-bs-target="#description" type="button" role="tab"
                        aria-controls="description" aria-selected="true">
                    <i class="fas fa-info-circle me-2"></i>Description
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="specs-tab" data-bs-toggle="tab"
                        data-bs-target="#specs" type="button" role="tab"
                        aria-controls="specs" aria-selected="false">
                    <i class="fas fa-list me-2"></i>Specifications
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab"
                        data-bs-target="#reviews" type="button" role="tab"
                        aria-controls="reviews" aria-selected="false">
                    <i class="fas fa-star me-2"></i>Reviews (<?= (int)$reviewCount ?>)
                </button>
            </li>
        </ul>

        <div class="tab-content border border-top-0 rounded-bottom p-4" id="productTabContent">

            <!-- Description Tab -->
            <div class="tab-pane fade show active" id="description" role="tabpanel"
                 aria-labelledby="description-tab">
                <?php if (!empty($product['long_description'])): ?>
                    <?= $product['long_description'] ?>
                <?php else: ?>
                    <p class="text-muted">No description available for this product.</p>
                <?php endif; ?>
            </div>

            <!-- Specifications Tab -->
            <div class="tab-pane fade" id="specs" role="tabpanel" aria-labelledby="specs-tab">
                <?php if (!empty($product['specifications']) && is_array($product['specifications'])): ?>
                    <table class="table table-bordered">
                        <tbody>
                            <?php foreach ($product['specifications'] as $spec): ?>
                                <tr>
                                    <th style="width: 35%; background-color: #f8f9fa;">
                                        <?= htmlspecialchars($spec['label'] ?? $spec['name'] ?? '') ?>
                                    </th>
                                    <td><?= htmlspecialchars($spec['value'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php elseif (!empty($product['specifications']) && is_string($product['specifications'])): ?>
                    <?= $product['specifications'] ?>
                <?php else: ?>
                    <p class="text-muted">No specifications available for this product.</p>
                <?php endif; ?>
            </div>

            <!-- Reviews Tab -->
            <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                <div class="row">

                    <!-- Rating Summary -->
                    <div class="col-md-4 mb-4 mb-md-0">
                        <div class="text-center border rounded p-4 bg-light">
                            <div class="display-4 fw-bold text-primary"><?= number_format($avgRating, 1) ?></div>
                            <div class="text-warning mb-2">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= floor($avgRating)): ?>
                                        <i class="fas fa-star"></i>
                                    <?php elseif ($i - $avgRating < 1 && $i - $avgRating > 0): ?>
                                        <i class="fas fa-star-half-alt"></i>
                                    <?php else: ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <p class="text-muted mb-3"><?= (int)$reviewCount ?> reviews</p>

                            <!-- Rating Distribution -->
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <?php
                                $count = $ratingDist[$i] ?? 0;
                                $percentage = $reviewCount > 0 ? ($count / $reviewCount) * 100 : 0;
                                ?>
                                <div class="d-flex align-items-center mb-1">
                                    <span class="me-2 text-nowrap" style="width: 40px;"><?= $i ?> <i class="fas fa-star text-warning" style="font-size: 0.7rem;"></i></span>
                                    <div class="progress flex-grow-1" style="height: 8px;">
                                        <div class="progress-bar bg-warning" style="width: <?= round($percentage) ?>%"></div>
                                    </div>
                                    <span class="ms-2 text-muted small" style="width: 35px;"><?= (int)$count ?></span>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <!-- Reviews List -->
                    <div class="col-md-8">
                        <?php if (!empty($reviews)): ?>
                            <div class="reviews-list">
                                <?php foreach ($reviews as $review): ?>
                                    <div class="border-bottom pb-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="text-warning mb-1">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="fas fa-star <?= $i <= $review['rating'] ? '' : 'far text-muted' ?>"></i>
                                                    <?php endfor; ?>
                                                </div>
                                                <h6 class="mb-1 fw-semibold"><?= htmlspecialchars($review['title'] ?? $review['name'] ?? 'Anonymous') ?></h6>
                                            </div>
                                            <small class="text-muted"><?= date('M d, Y', strtotime($review['created_at'] ?? '')) ?></small>
                                        </div>
                                        <p class="mb-1"><?= nl2br(htmlspecialchars($review['comment'] ?? $review['body'] ?? '')) ?></p>
                                        <?php if (!empty($review['user_name'])): ?>
                                            <small class="text-muted">by <?= htmlspecialchars($review['user_name']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-comments fa-2x text-muted mb-2"></i>
                                <p class="text-muted">No reviews yet. Be the first to review this product!</p>
                            </div>
                        <?php endif; ?>

                        <!-- Review Form (if logged in) -->
                        <?php if (!empty($_SESSION['user_id'])): ?>
                            <div class="card mt-4" id="reviewFormCard">
                                <div class="card-header fw-semibold">
                                    <i class="fas fa-pen me-2"></i>Write a Review
                                </div>
                                <div class="card-body">
                                    <form id="reviewForm" data-product-id="<?= (int)$product['id'] ?>">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Your Rating</label>
                                            <div class="star-rating" id="starRating">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="far fa-star text-warning star-btn" data-rating="<?= $i ?>"
                                                       style="font-size: 1.5rem; cursor: pointer;"></i>
                                                <?php endfor; ?>
                                                <input type="hidden" name="rating" id="ratingInput" value="0">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="reviewTitle" class="form-label fw-semibold">Review Title</label>
                                            <input type="text" id="reviewTitle" name="title" class="form-control"
                                                   placeholder="Summarize your experience" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="reviewComment" class="form-label fw-semibold">Your Review</label>
                                            <textarea id="reviewComment" name="comment" class="form-control"
                                                      rows="4" placeholder="Share your thoughts about this product..." required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane me-2"></i>Submit Review
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info mt-4">
                                <i class="fas fa-info-circle me-2"></i>
                                Please <a href="<?= APP_URL ?>/login">login</a> to write a review.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($relatedProducts) && count($relatedProducts) > 0): ?>
        <div class="mt-5">
            <h2 class="h4 mb-4"><i class="fas fa-th-large me-2"></i>Related Products</h2>
            <div id="relatedProductsCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php
                    $chunkSize = 4;
                    $chunks = array_chunk($relatedProducts, $chunkSize);
                    foreach ($chunks as $chunkIndex => $chunk):
                    ?>
                        <div class="carousel-item <?= $chunkIndex === 0 ? 'active' : '' ?>">
                            <div class="row g-4">
                                <?php foreach ($chunk as $relatedProduct): ?>
                                    <div class="col-6 col-md-3">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <a href="<?= APP_URL ?>/product/<?= htmlspecialchars($relatedProduct['slug']) ?>">
                                                <img src="<?= APP_URL . '/' . htmlspecialchars($relatedProduct['image'] ?? '') ?>"
                                                     class="card-img-top" alt="<?= htmlspecialchars($relatedProduct['name']) ?>"
                                                     style="height: 180px; object-fit: cover;"
                                                     loading="lazy">
                                            </a>
                                            <div class="card-body d-flex flex-column">
                                                <h6 class="card-title">
                                                    <a href="<?= APP_URL ?>/product/<?= htmlspecialchars($relatedProduct['slug']) ?>"
                                                       class="text-decoration-none text-dark">
                                                        <?= htmlspecialchars($relatedProduct['name']) ?>
                                                    </a>
                                                </h6>
                                                <div class="text-warning small mb-2">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="fas fa-star <?= $i <= ($relatedProduct['avg_rating'] ?? 0) ? '' : 'far text-muted' ?>"></i>
                                                    <?php endfor; ?>
                                                </div>
                                                <div class="mt-auto">
                                                    <?php if (!empty($relatedProduct['discount_price'])): ?>
                                                        <span class="fw-bold text-danger">$<?= number_format($relatedProduct['discount_price'], 2) ?></span>
                                                        <span class="text-decoration-line-through text-muted small ms-1">
                                                            $<?= number_format($relatedProduct['price'], 2) ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="fw-bold text-primary">$<?= number_format($relatedProduct['price'], 2) ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (count($chunks) > 1): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#relatedProductsCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon bg-dark rounded-circle p-3" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#relatedProductsCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon bg-dark rounded-circle p-3" aria-hidden="true"></span>
                    </button>

                    <!-- Carousel Indicators -->
                    <div class="carousel-indicators mt-3 position-static">
                        <?php foreach ($chunks as $index => $chunk): ?>
                            <button type="button" data-bs-target="#relatedProductsCarousel"
                                    data-bs-slide-to="<?= $index ?>"
                                    class="<?= $index === 0 ? 'active' : '' ?>"
                                    style="width: 12px; height: 12px; border-radius: 50%;"></button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<style>
.thumbnail-btn {
    transition: all 0.2s ease;
}
.thumbnail-btn:hover,
.thumbnail-btn.active {
    border-color: var(--bs-primary) !important;
    transform: scale(1.05);
}
.variant-btn {
    transition: all 0.2s ease;
    width: 40px;
    height: 40px;
}
.variant-btn:hover {
    transform: scale(1.1);
}
.variant-btn.border-dark {
    box-shadow: 0 0 0 3px rgba(0,0,0,0.1);
}
#mainImage {
    transition: transform 0.3s ease;
}
#mainImage:hover {
    transform: scale(1.5);
    z-index: 10;
}
.star-rating .star-btn {
    transition: all 0.15s ease;
}
.star-rating .star-btn:hover {
    transform: scale(1.2);
}
.nav-tabs .nav-link.active {
    font-weight: 600;
    border-bottom: 3px solid var(--bs-primary);
}
.carousel-indicators button {
    background-color: #6c757d;
    border: none;
}
.carousel-indicators button.active {
    background-color: var(--bs-primary);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mainImage = document.getElementById('mainImage');
    const thumbnailBtns = document.querySelectorAll('.thumbnail-btn');
    const qtyInput = document.getElementById('quantity');
    const qtyMinus = document.getElementById('qtyMinus');
    const qtyPlus = document.getElementById('qtyPlus');
    const addToCartBtn = document.getElementById('addToCartBtn');
    const buyNowBtn = document.getElementById('buyNowBtn');
    const wishlistBtn = document.getElementById('wishlistBtn');
    const variantBtns = document.querySelectorAll('.variant-btn');
    const starBtns = document.querySelectorAll('#starRating .star-btn');

    // Thumbnail Image Swap
    thumbnailBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            thumbnailBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            mainImage.src = this.dataset.image;

            if (this.dataset.zoom) {
                mainImage.dataset.zoomSrc = this.dataset.zoom;
            }
        });
    });

    // Quantity Controls
    qtyMinus.addEventListener('click', function() {
        let val = parseInt(qtyInput.value) || 1;
        if (val > 1) qtyInput.value = val - 1;
    });

    qtyPlus.addEventListener('click', function() {
        let val = parseInt(qtyInput.value) || 1;
        let max = parseInt(qtyInput.max) || 100;
        if (val < max) qtyInput.value = val + 1;
    });

    qtyInput.addEventListener('change', function() {
        let val = parseInt(this.value) || 1;
        let max = parseInt(this.max) || 100;
        let min = parseInt(this.min) || 1;
        this.value = Math.max(min, Math.min(max, val));
    });

    // Variant Selection
    variantBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            variantBtns.forEach(b => {
                b.classList.remove('border-dark');
                b.classList.add('border-light');
            });
            this.classList.remove('border-light');
            this.classList.add('border-dark');

            const label = document.getElementById('selectedVariantLabel');
            if (label && this.dataset.name) {
                label.textContent = '- ' + this.dataset.name;
            }

            // Update price
            if (this.dataset.price) {
                const priceEl = document.querySelector('.h3.text-danger, .h3');
                if (priceEl) {
                    priceEl.textContent = '$' + this.dataset.price;
                }
            }

            // Update stock
            const stockStatus = document.getElementById('stockStatus');
            if (stockStatus) {
                const stock = parseInt(this.dataset.stock);
                if (stock > 0) {
                    stockStatus.textContent = 'In Stock (' + stock + ' available)';
                    stockStatus.closest('.text-success').classList.remove('text-danger');
                    stockStatus.closest('.text-success').classList.add('text-success');
                    addToCartBtn.disabled = false;
                    buyNowBtn.disabled = false;
                } else {
                    stockStatus.textContent = 'Out of Stock';
                    addToCartBtn.disabled = true;
                    buyNowBtn.disabled = true;
                }
            }

            // Update main image if variant has image
            if (this.dataset.image) {
                mainImage.src = this.dataset.image;
            }
        });
    });

    // Star Rating in Review Form
    starBtns.forEach(function(star) {
        star.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            document.getElementById('ratingInput').value = rating;

            starBtns.forEach((s, index) => {
                if (index < rating) {
                    s.classList.remove('far');
                    s.classList.add('fas');
                } else {
                    s.classList.remove('fas');
                    s.classList.add('far');
                }
            });
        });

        star.addEventListener('mouseenter', function() {
            const rating = parseInt(this.dataset.rating);
            starBtns.forEach((s, index) => {
                if (index < rating) {
                    s.classList.remove('far');
                    s.classList.add('fas');
                } else {
                    s.classList.remove('fas');
                    s.classList.add('far');
                }
            });
        });

        star.addEventListener('mouseleave', function() {
            const currentRating = parseInt(document.getElementById('ratingInput').value);
            starBtns.forEach((s, index) => {
                if (index < currentRating) {
                    s.classList.remove('far');
                    s.classList.add('fas');
                } else {
                    s.classList.remove('fas');
                    s.classList.add('far');
                }
            });
        });
    });

    // Add to Cart
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const quantity = qtyInput.value;
            const variantSelector = document.querySelector('.variant-btn.border-dark');
            const variantId = variantSelector ? variantSelector.dataset.variantId : null;

            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding...';
            this.disabled = true;

            fetch('<?= APP_URL ?>/api/cart/add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity,
                    variant_id: variantId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart count in navbar
                    if (data.cart_count !== undefined) {
                        const cartCountEl = document.querySelector('.cart-count');
                        if (cartCountEl) cartCountEl.textContent = data.cart_count;
                    }

                    // Show success message
                    showToast('Product added to cart!', 'success');

                    this.innerHTML = '<i class="fas fa-check me-2"></i>Added!';
                    setTimeout(() => {
                        this.innerHTML = '<i class="fas fa-shopping-cart me-2"></i>Add to Cart';
                        this.disabled = false;
                    }, 2000);
                } else {
                    showToast(data.message || 'Error adding to cart', 'error');
                    this.innerHTML = '<i class="fas fa-shopping-cart me-2"></i>Add to Cart';
                    this.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error adding to cart', 'error');
                this.innerHTML = '<i class="fas fa-shopping-cart me-2"></i>Add to Cart';
                this.disabled = false;
            });
        });
    }

    // Buy Now
    if (buyNowBtn) {
        buyNowBtn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const quantity = qtyInput.value;
            const variantSelector = document.querySelector('.variant-btn.border-dark');
            const variantId = variantSelector ? variantSelector.dataset.variantId : null;

            fetch('<?= APP_URL ?>/api/cart/add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity,
                    variant_id: variantId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '<?= APP_URL ?>/checkout';
                } else {
                    showToast(data.message || 'Error', 'error');
                }
            })
            .catch(error => {
                showToast('Error', 'error');
            });
        });
    }

    // Wishlist Toggle
    if (wishlistBtn) {
        wishlistBtn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const icon = document.getElementById('wishlistIcon');

            fetch('<?= APP_URL ?>/api/wishlist/toggle', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.wishlisted) {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        showToast('Added to wishlist!', 'success');
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        showToast('Removed from wishlist', 'info');
                    }
                } else {
                    showToast(data.message || 'Please login to add to wishlist', 'error');
                }
            })
            .catch(error => {
                showToast('Error updating wishlist', 'error');
            });
        });
    }

    // Review Form Submit
    const reviewForm = document.getElementById('reviewForm');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const rating = document.getElementById('ratingInput').value;
            const title = document.getElementById('reviewTitle').value;
            const comment = document.getElementById('reviewComment').value;
            const productId = this.dataset.productId;

            if (rating == 0) {
                showToast('Please select a rating', 'error');
                return;
            }

            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
            submitBtn.disabled = true;

            fetch('<?= APP_URL ?>/api/reviews/store', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({
                    product_id: productId,
                    rating: rating,
                    title: title,
                    comment: comment
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Review submitted successfully!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(data.message || 'Error submitting review', 'error');
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Submit Review';
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                showToast('Error submitting review', 'error');
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Submit Review';
                submitBtn.disabled = false;
            });
        });
    }

    // Toast Notification Helper
    function showToast(message, type) {
        const toastContainer = document.getElementById('toastContainer') || createToastContainer();
        const toastId = 'toast-' + Date.now();
        const iconClass = type === 'success' ? 'fas fa-check-circle' :
                          type === 'error' ? 'fas fa-times-circle' :
                          'fas fa-info-circle';
        const bgClass = type === 'success' ? 'bg-success' :
                        type === 'error' ? 'bg-danger' :
                        'bg-info';

        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = `toast align-items-center text-white ${bgClass} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="${iconClass} me-2"></i>${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        toastContainer.appendChild(toast);

        const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
        bsToast.show();

        toast.addEventListener('hidden.bs.toast', () => toast.remove());
    }

    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '1080';
        document.body.appendChild(container);
        return container;
    }
});
</script>
