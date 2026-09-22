<?php
use App\Helpers\Sanitizer;
/**
 * Product Card Partial - CrokersesMart
 * Reusable product card for product grids
 * @var mixed $product Product data array (already extracted)
 */

// Extract product data with defaults
$productId    = $product['id'] ?? 0;
$productName  = $product['name'] ?? 'Product';
$productSlug  = $product['slug'] ?? '';
$productImage = $product['main_image'] ?? $product['image'] ?? '';
$categoryName = $product['category_name'] ?? '';
$categoryId   = $product['category_id'] ?? '';
$regularPrice = $product['price'] ?? 0;
$salePrice    = $product['discount_price'] ?? 0;
$discount     = $product['discount'] ?? 0;
$rating       = $product['rating'] ?? 0;
$ratingCount  = $product['rating_count'] ?? 0;
$isNew        = $product['is_new'] ?? false;
$isHot        = $product['is_hot'] ?? false;
$isInStock    = $product['in_stock'] ?? true;

// Calculate discount percentage
$discountPercent = 0;
if ($salePrice > 0 && $regularPrice > $salePrice) {
    $discountPercent = round((($regularPrice - $salePrice) / $regularPrice) * 100);
} elseif ($discount > 0) {
    $discountPercent = $discount;
    $salePrice = $regularPrice - (($regularPrice * $discount) / 100);
}

// Determine display price
$displayPrice = ($salePrice > 0 && $salePrice < $regularPrice) ? $salePrice : $regularPrice;
$hasDiscount = ($salePrice > 0 && $salePrice < $regularPrice);

// Build product URL
$productUrl = APP_URL . '/product/' . urlencode($productSlug);

// Sanitize image
$imageUrl = '';
if (!empty($productImage)) {
    $imageUrl = Sanitizer::image($productImage);
}

// Format prices with Bangla numerals
$formattedPrice = Sanitizer::banglaPrice($displayPrice);
$formattedRegularPrice = Sanitizer::banglaPrice($regularPrice);
?>

<div class="product-card" data-product-id="<?= $productId ?>">
    <!-- Product Image -->
    <div class="product-image">
        <?php if ($imageUrl): ?>
            <a href="<?= $productUrl ?>">
                <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($productName) ?>" loading="lazy" width="400" height="400">
            </a>
        <?php else: ?>
            <a href="<?= $productUrl ?>">
                <img src="<?= APP_URL ?>/assets/images/placeholder-product.jpg" alt="<?= htmlspecialchars($productName) ?>" loading="lazy" width="400" height="400">
            </a>
        <?php endif; ?>

        <!-- Badges -->
        <div class="product-badge">
            <?php if ($hasDiscount && $discountPercent > 0): ?>
                <span class="badge badge-sale">-<?= $discountPercent ?>%</span>
            <?php endif; ?>
            <?php if ($isNew): ?>
                <span class="badge badge-new">New</span>
            <?php endif; ?>
            <?php if ($isHot): ?>
                <span class="badge badge-hot">Hot</span>
            <?php endif; ?>
        </div>

        <!-- Action Buttons -->
        <div class="product-actions">
            <button type="button" class="action-btn wishlist-btn" data-product-id="<?= $productId ?>" title="Add to Wishlist">
                <i class="bi bi-heart"></i>
            </button>
            <a href="<?= $productUrl ?>" class="action-btn quick-view-btn" title="Quick View">
                <i class="bi bi-eye"></i>
            </a>
        </div>
    </div>

    <!-- Product Info -->
    <div class="product-info">
        <!-- Category -->
        <?php if ($categoryName): ?>
            <span class="product-category">
                <?php if ($categoryId): ?>
                    <a href="<?= APP_URL ?>/shop?category=<?= urlencode($categoryId) ?>"><?= htmlspecialchars($categoryName) ?></a>
                <?php else: ?>
                    <?= htmlspecialchars($categoryName) ?>
                <?php endif; ?>
            </span>
        <?php endif; ?>

        <!-- Product Name -->
        <h3 class="product-name">
            <a href="<?= $productUrl ?>"><?= htmlspecialchars($productName) ?></a>
        </h3>

        <!-- Rating -->
        <?php if ($rating > 0): ?>
            <div class="product-rating">
                <span class="stars">
                    <?php
                    $fullStars = floor($rating);
                    $halfStar = ($rating - $fullStars) >= 0.5;
                    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

                    for ($i = 0; $i < $fullStars; $i++):
                        echo '<i class="bi bi-star-fill"></i>';
                    endfor;
                    if ($halfStar):
                        echo '<i class="bi bi-star-half"></i>';
                    endif;
                    for ($i = 0; $i < $emptyStars; $i++):
                        echo '<i class="bi bi-star"></i>';
                    endfor;
                    ?>
                </span>
                <?php if ($ratingCount > 0): ?>
                    <span class="rating-count">(<?= $ratingCount ?>)</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Price -->
        <div class="product-price">
            <span class="current-price"><?= $formattedPrice ?></span>
            <?php if ($hasDiscount): ?>
                <span class="original-price"><?= $formattedRegularPrice ?></span>
                <span class="discount-badge"><?= $discountPercent ?>% OFF</span>
            <?php endif; ?>
        </div>

        <!-- Action Buttons -->
        <div class="product-btn-group">
            <button type="button"
                    class="btn btn-order btn-sm"
                    data-buy-now="<?= $productId ?>"
                    data-product-name="<?= htmlspecialchars($productName) ?>"
                    <?= !$isInStock ? 'disabled' : '' ?>>
                <?= $isInStock ? 'Order Now' : 'Out of Stock' ?>
            </button>
            <button type="button"
                    class="btn btn-add-cart btn-sm"
                    data-add-to-cart="<?= $productId ?>"
                    data-product-name="<?= htmlspecialchars($productName) ?>"
                    <?= !$isInStock ? 'disabled' : '' ?>>
                Add to Cart
            </button>
        </div>
    </div>
</div>
