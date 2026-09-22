<?php
use App\Helpers\Sanitizer;
$inStock = ($product['stock_quantity'] ?? 0) > 0;
$hasDiscount = !empty($product['discount_price']) && $product['discount_price'] > 0 && $product['discount_price'] < $product['price'];
$saveAmount = $hasDiscount ? ($product['price'] - $product['discount_price']) : 0;
$discountPct = $hasDiscount ? round((($product['price'] - $product['discount_price']) / $product['price']) * 100) : 0;
$totalImages = max(count($images), 1);
?>

<!-- ═══ BREADCRUMB ═══ -->
<div class="pd-breadcrumb">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= APP_URL ?>"><i class="bi bi-house-door me-1"></i>Home</a></li>
                <?php if (!empty($product['category_name'])): ?>
                    <li class="breadcrumb-item"><a href="<?= APP_URL ?>/shop?category=<?= (int)$product['category_id'] ?>"><?= htmlspecialchars($product['category_name']) ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name']) ?></li>
            </ol>
        </nav>
    </div>
</div>

<!-- ═══ MAIN PRODUCT SECTION ═══ -->
<section class="pd-main">
    <div class="container">
        <div class="row g-4">

            <!-- LEFT: Image Gallery -->
            <div class="col-lg-6">
                <div class="pd-gallery" id="pdGallery">
                    <div class="pd-gallery-main">
                        <div class="pd-gallery-badge">
                            <?php if ($hasDiscount): ?><span class="pd-badge-sale">-<?= $discountPct ?>%</span><?php endif; ?>
                            <?php if (($product['is_new'] ?? 0)): ?><span class="pd-badge-new">New</span><?php endif; ?>
                        </div>
                        <img id="pdMainImage"
                             src="<?= Sanitizer::image($images[0]['image_path'] ?? $product['main_image'] ?? '') ?>"
                             alt="<?= htmlspecialchars($product['name']) ?>"
                             class="pd-main-img"
                             loading="eager">
                        <div class="pd-img-counter"><span id="pdImgIndex">1</span> / <?= $totalImages ?></div>
                    </div>
                    <?php if ($totalImages > 1): ?>
                        <div class="pd-gallery-thumbs" id="pdThumbs">
                            <?php foreach ($images as $idx => $image): ?>
                                <button type="button"
                                        class="pd-thumb <?= $idx === 0 ? 'active' : '' ?>"
                                        data-index="<?= $idx ?>"
                                        data-image="<?= Sanitizer::image($image['image_path']) ?>">
                                    <img src="<?= Sanitizer::image($image['image_path']) ?>" alt="Image <?= $idx + 1 ?>" loading="lazy">
                                </button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- RIGHT: Product Info -->
            <div class="col-lg-6">
                <div class="pd-info" id="pdInfo">

                    <!-- Category -->
                    <?php if (!empty($product['category_name'])): ?>
                        <a href="<?= APP_URL ?>/shop?category=<?= (int)$product['category_id'] ?>" class="pd-category"><?= htmlspecialchars($product['category_name']) ?></a>
                    <?php endif; ?>

                    <!-- Name -->
                    <h1 class="pd-title"><?= htmlspecialchars($product['name']) ?></h1>

                    <!-- Rating -->
                    <div class="pd-rating" onclick="document.getElementById('pdAccordionReviews').click()">
                        <div class="pd-stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php if ($i <= floor($avgRating)): ?>
                                    <i class="bi bi-star-fill"></i>
                                <?php elseif ($i - $avgRating < 1 && $i - $avgRating > 0): ?>
                                    <i class="bi bi-star-half"></i>
                                <?php else: ?>
                                    <i class="bi bi-star"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                        <span class="pd-rating-num"><?= number_format($avgRating, 1) ?></span>
                        <span class="pd-rating-count">(<?= (int)$reviewCount ?> reviews)</span>
                    </div>

                    <!-- Price -->
                    <div class="pd-price">
                        <?php if ($hasDiscount): ?>
                            <span class="pd-price-current"><?= Sanitizer::banglaPrice($product['discount_price']) ?></span>
                            <span class="pd-price-original"><?= Sanitizer::banglaPrice($product['price']) ?></span>
                            <span class="pd-price-badge">-<?= $discountPct ?>% OFF</span>
                            <p class="pd-save"><i class="bi bi-piggy-bank me-1"></i>You save <?= Sanitizer::banglaPrice($saveAmount) ?></p>
                        <?php else: ?>
                            <span class="pd-price-current"><?= Sanitizer::banglaPrice($product['price']) ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Short Description -->
                    <?php if (!empty($product['short_description'])): ?>
                        <div class="pd-short-desc"><?= nl2br(htmlspecialchars($product['short_description'])) ?></div>
                    <?php endif; ?>

                    <!-- Variant Selector -->
                    <?php if (!empty($variants)): ?>
                        <div class="pd-variants">
                            <label class="pd-label">Color: <span id="pdVariantLabel"></span></label>
                            <div class="pd-variant-list" id="pdVariantSelector">
                                <?php foreach ($variants as $idx => $variant): ?>
                                    <button type="button"
                                            class="pd-variant-btn <?= $idx === 0 ? 'active' : '' ?>"
                                            data-variant-id="<?= (int)$variant['id'] ?>"
                                            data-price="<?= number_format($product['price'] + ($variant['extra_price'] ?? 0), 2) ?>"
                                            data-stock="<?= (int)($product['stock_quantity'] ?? 0) ?>"
                                            data-name="<?= htmlspecialchars($variant['color_name'] ?? '') ?>"
                                            title="<?= htmlspecialchars($variant['color_name'] ?? '') ?>"
                                            style="background-color: <?= htmlspecialchars($variant['color_code'] ?? '#cccccc') ?>;">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Quantity -->
                    <div class="pd-quantity">
                        <label class="pd-label">Quantity</label>
                        <div class="pd-qty-control">
                            <button type="button" class="pd-qty-btn" id="pdQtyMinus"><i class="bi bi-dash-lg"></i></button>
                            <input type="number" id="pdQty" value="1" min="1" max="<?= (int)($product['stock_quantity'] ?? 10) ?>">
                            <button type="button" class="pd-qty-btn" id="pdQtyPlus"><i class="bi bi-plus-lg"></i></button>
                        </div>
                    </div>

                    <!-- Stock -->
                    <div class="pd-stock <?= $inStock ? 'in-stock' : 'out-of-stock' ?>">
                        <span class="pd-stock-dot"></span>
                        <span id="pdStockText"><?= $inStock ? 'In Stock' : 'Out of Stock' ?></span>
                        <?php if ($inStock): ?>
                            <span class="pd-stock-qty">(<?= (int)$product['stock_quantity'] ?> available)</span>
                        <?php endif; ?>
                    </div>

                    <!-- Trust Badges -->
                    <div class="pd-trust">
                        <div class="pd-trust-item"><i class="bi bi-truck"></i><span>Free Delivery</span></div>
                        <div class="pd-trust-item"><i class="bi bi-shield-check"></i><span>Secure Payment</span></div>
                        <div class="pd-trust-item"><i class="bi bi-arrow-return-left"></i><span>Easy Returns</span></div>
                        <div class="pd-trust-item"><i class="bi bi-patch-check"></i><span>100% Authentic</span></div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pd-actions">
                        <button type="button" class="pd-btn pd-btn-cart" id="pdAddToCart"
                                data-product-id="<?= (int)$product['id'] ?>"
                                <?= !$inStock ? 'disabled' : '' ?>>
                            <i class="bi bi-bag-check"></i> Add to Cart
                        </button>
                        <button type="button" class="pd-btn pd-btn-buy" id="pdBuyNow"
                                data-product-id="<?= (int)$product['id'] ?>"
                                <?= !$inStock ? 'disabled' : '' ?>>
                            <i class="bi bi-lightning-charge"></i> Buy Now
                        </button>
                        <button type="button" class="pd-btn pd-btn-wish" id="pdWishlist"
                                data-product-id="<?= (int)$product['id'] ?>">
                            <i class="bi bi-heart" id="pdWishIcon"></i>
                        </button>
                    </div>

                    <!-- Meta -->
                    <div class="pd-meta">
                        <?php if (!empty($product['brand_name'])): ?>
                            <div class="pd-meta-row"><span class="pd-meta-label">Brand:</span> <a href="<?= APP_URL ?>/shop?brand=<?= (int)$product['brand_id'] ?>"><?= htmlspecialchars($product['brand_name']) ?></a></div>
                        <?php endif; ?>
                        <div class="pd-meta-row"><span class="pd-meta-label">SKU:</span> <?= htmlspecialchars($product['sku'] ?? 'N/A') ?></div>
                    </div>

                    <!-- Share -->
                    <div class="pd-share">
                        <span class="pd-meta-label">Share:</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(APP_URL . '/product/' . $product['slug']) ?>" target="_blank" class="pd-share-btn" title="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="https://wa.me/?text=<?= urlencode($product['name'] . ' - ' . APP_URL . '/product/' . $product['slug']) ?>" target="_blank" class="pd-share-btn" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                        <button type="button" class="pd-share-btn" id="pdCopyLink" title="Copy Link"><i class="bi bi-link-45deg"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══ PRODUCT ACCORDION ═══ -->
<section class="pd-accordion-section">
    <div class="container">
        <div class="pd-accordion">

            <!-- Description -->
            <div class="pd-acc-item open">
                <button class="pd-acc-header" id="pdAccordionDesc">
                    <span><i class="bi bi-info-circle me-2"></i>Description</span>
                    <i class="bi bi-chevron-down"></i>
                </button>
                <div class="pd-acc-body">
                    <?php if (!empty($product['long_description'])): ?>
                        <?= $product['long_description'] ?>
                    <?php else: ?>
                        <p class="pd-empty">No description available for this product.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Specifications -->
            <?php if (!empty($product['specifications'])): ?>
            <div class="pd-acc-item">
                <button class="pd-acc-header" id="pdAccordionSpecs">
                    <span><i class="bi bi-list-check me-2"></i>Specifications</span>
                    <i class="bi bi-chevron-down"></i>
                </button>
                <div class="pd-acc-body">
                    <?php if (is_array($product['specifications'])): ?>
                        <table class="pd-spec-table">
                            <?php foreach ($product['specifications'] as $spec): ?>
                                <tr><td><?= htmlspecialchars($spec['label'] ?? $spec['name'] ?? '') ?></td><td><?= htmlspecialchars($spec['value'] ?? '') ?></td></tr>
                            <?php endforeach; ?>
                        </table>
                    <?php else: ?>
                        <?= $product['specifications'] ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Reviews -->
            <div class="pd-acc-item">
                <button class="pd-acc-header" id="pdAccordionReviews">
                    <span><i class="bi bi-chat-dots me-2"></i>Reviews (<?= (int)$reviewCount ?>)</span>
                    <i class="bi bi-chevron-down"></i>
                </button>
                <div class="pd-acc-body">
                    <div class="pd-reviews-wrap">

                        <!-- Rating Summary -->
                        <div class="pd-review-summary">
                            <div class="pd-review-big-num"><?= number_format($avgRating, 1) ?></div>
                            <div class="pd-review-big-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi <?= $i <= floor($avgRating) ? 'bi-star-fill' : (($i - $avgRating < 1 && $i - $avgRating > 0) ? 'bi-star-half' : 'bi-star') ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <p class="pd-review-total"><?= (int)$reviewCount ?> reviews</p>
                            <div class="pd-review-bars">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <?php $count = $ratingDist[$i] ?? 0; $pct = $reviewCount > 0 ? ($count / $reviewCount) * 100 : 0; ?>
                                    <div class="pd-bar-row">
                                        <span><?= $i ?> <i class="bi bi-star-fill"></i></span>
                                        <div class="pd-bar"><div class="pd-bar-fill" style="width:<?= round($pct) ?>%"></div></div>
                                        <span><?= (int)$count ?></span>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- Reviews List -->
                        <div class="pd-reviews-list">
                            <?php if (!empty($reviews)): ?>
                                <?php foreach ($reviews as $review): ?>
                                    <div class="pd-review-card">
                                        <div class="pd-review-head">
                                            <div class="pd-review-avatar"><?= strtoupper(substr($review['user_name'] ?? 'A', 0, 1)) ?></div>
                                            <div class="pd-review-meta">
                                                <h6><?= htmlspecialchars($review['title'] ?? 'Review') ?></h6>
                                                <small>by <?= htmlspecialchars($review['user_name'] ?? 'Anonymous') ?> &middot; <?= date('M d, Y', strtotime($review['created_at'] ?? '')) ?></small>
                                            </div>
                                            <div class="pd-review-stars">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="bi <?= $i <= $review['rating'] ? 'bi-star-fill' : 'bi-star' ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <p class="pd-review-text"><?= nl2br(htmlspecialchars($review['comment'] ?? '')) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="pd-no-reviews">
                                    <i class="bi bi-chat-square-dots"></i>
                                    <p>No reviews yet. Be the first to review this product!</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Review Form -->
                        <?php if (!empty($_SESSION['user_id'])): ?>
                            <div class="pd-review-form">
                                <h5><i class="bi bi-pencil-square me-2"></i>Write a Review</h5>
                                <form id="reviewForm" data-product-id="<?= (int)$product['id'] ?>">
                                    <div class="pd-form-rating" id="pdStarRating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="bi bi-star pd-star-btn" data-rating="<?= $i ?>"></i>
                                        <?php endfor; ?>
                                        <input type="hidden" name="rating" id="ratingInput" value="0">
                                    </div>
                                    <input type="text" id="reviewTitle" placeholder="Review Title" required class="pd-form-input">
                                    <textarea id="reviewComment" placeholder="Share your experience..." rows="4" required class="pd-form-input"></textarea>
                                    <button type="submit" class="pd-btn pd-btn-submit"><i class="bi bi-send me-2"></i>Submit Review</button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="pd-login-msg">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Please <a href="<?= APP_URL ?>/login">login</a> to write a review.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══ RELATED PRODUCTS ═══ -->
<?php if (!empty($relatedProducts) && count($relatedProducts) > 0): ?>
<section class="pd-related">
    <div class="container">
        <h2 class="pd-section-title"><i class="bi bi-grid me-2"></i>Related Products</h2>
        <div class="row g-3">
            <?php foreach ($relatedProducts as $rp): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="<?= APP_URL ?>/product/<?= htmlspecialchars($rp['slug']) ?>" class="pd-rel-card">
                        <div class="pd-rel-img">
                            <img src="<?= Sanitizer::image($rp['main_image'] ?? '') ?>" alt="<?= htmlspecialchars($rp['name']) ?>" loading="lazy">
                        </div>
                        <div class="pd-rel-info">
                            <h6><?= htmlspecialchars($rp['name']) ?></h6>
                            <div class="pd-rel-price">
                                <?php if (!empty($rp['discount_price'])): ?>
                                    <span class="pd-rel-current"><?= Sanitizer::banglaPrice($rp['discount_price']) ?></span>
                                    <span class="pd-rel-original"><?= Sanitizer::banglaPrice($rp['price']) ?></span>
                                <?php else: ?>
                                    <span class="pd-rel-current"><?= Sanitizer::banglaPrice($rp['price']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══ DELIVERY INFO ═══ -->
<section class="pd-delivery-info">
    <div class="container">
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <div class="pd-info-card">
                    <i class="bi bi-truck"></i>
                    <h6>Delivery</h6>
                    <p>Inside Dhaka: <?= Sanitizer::banglaPrice(SHIPPING_INSIDE_DHAKA) ?><br>Outside Dhaka: <?= Sanitizer::banglaPrice(SHIPPING_OUTSIDE_DHAKA) ?></p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="pd-info-card">
                    <i class="bi bi-arrow-return-left"></i>
                    <h6>Returns</h6>
                    <p>Easy return within 3 days of delivery</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="pd-info-card">
                    <i class="bi bi-shield-check"></i>
                    <h6>Secure Payment</h6>
                    <p>SSLCommerz, COD & Cards accepted</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="pd-info-card">
                    <i class="bi bi-headset"></i>
                    <h6>Support</h6>
                    <p>24/7 customer support available</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══ MOBILE STICKY BAR ═══ -->
<div class="pd-mobile-bar" id="pdMobileBar">
    <div class="pd-mobile-price" id="pdMobilePrice"><?= $hasDiscount ? Sanitizer::banglaPrice($product['discount_price']) : Sanitizer::banglaPrice($product['price']) ?></div>
    <button type="button" class="pd-btn pd-btn-cart" id="pdMobileCart"
            data-product-id="<?= (int)$product['id'] ?>" <?= !$inStock ? 'disabled' : '' ?>>
        <i class="bi bi-bag-check"></i> Add to Cart
    </button>
</div>

<!-- ═══ STYLES ═══ -->
<style>
/* ── Breadcrumb ── */
.pd-breadcrumb { background: #fafafa; border-bottom: 1px solid #f0f0f0; padding: 12px 0; }
.pd-breadcrumb .breadcrumb { margin: 0; font-size: 13px; }
.pd-breadcrumb .breadcrumb-item a { color: #666; text-decoration: none; transition: color .2s; }
.pd-breadcrumb .breadcrumb-item a:hover { color: var(--cm-primary); }
.pd-breadcrumb .breadcrumb-item.active { color: #333; font-weight: 500; }
.pd-breadcrumb .breadcrumb-item + .breadcrumb-item::before { content: "/"; color: #ccc; font-size: 12px; }

/* ── Gallery ── */
.pd-gallery { position: sticky; top: 80px; }
.pd-gallery-main { position: relative; background: #f8f8f8; border-radius: 16px; overflow: hidden; aspect-ratio: 1/1; display: flex; align-items: center; justify-content: center; }
.pd-main-img { width: 100%; height: 100%; object-fit: contain; transition: transform .3s ease; cursor: zoom-in; padding: 20px; }
.pd-main-img:hover { transform: scale(1.6); z-index: 5; }
.pd-gallery-badge { position: absolute; top: 16px; left: 16px; z-index: 6; display: flex; gap: 6px; }
.pd-badge-sale { background: var(--cm-primary); color: #fff; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; }
.pd-badge-new { background: #28a745; color: #fff; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; }
.pd-img-counter { position: absolute; bottom: 12px; right: 12px; background: rgba(0,0,0,.55); color: #fff; padding: 3px 10px; border-radius: 12px; font-size: 12px; }
.pd-gallery-thumbs { display: flex; gap: 8px; margin-top: 12px; overflow-x: auto; padding-bottom: 4px; }
.pd-gallery-thumbs::-webkit-scrollbar { height: 4px; }
.pd-gallery-thumbs::-webkit-scrollbar-thumb { background: #ddd; border-radius: 4px; }
.pd-thumb { width: 72px; height: 72px; border-radius: 10px; overflow: hidden; border: 2px solid transparent; cursor: pointer; flex-shrink: 0; transition: border-color .2s, transform .2s; padding: 0; background: none; }
.pd-thumb:hover { border-color: #999; transform: scale(1.05); }
.pd-thumb.active { border-color: var(--cm-primary); }
.pd-thumb img { width: 100%; height: 100%; object-fit: cover; border-radius: 8px; }

/* ── Product Info ── */
.pd-info { padding: 0 8px; }
.pd-category { display: inline-block; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .8px; color: var(--cm-primary); margin-bottom: 8px; text-decoration: none; }
.pd-category:hover { text-decoration: underline; }
.pd-title { font-size: 26px; font-weight: 800; color: #1a1a2e; line-height: 1.3; margin-bottom: 12px; }

/* Rating */
.pd-rating { display: flex; align-items: center; gap: 8px; margin-bottom: 16px; cursor: pointer; }
.pd-stars { color: #f5a623; font-size: 14px; display: flex; gap: 2px; }
.pd-rating-num { font-weight: 700; font-size: 14px; color: #333; }
.pd-rating-count { color: #888; font-size: 13px; }

/* Price */
.pd-price { padding: 16px 0; border-top: 1px solid #f0f0f0; border-bottom: 1px solid #f0f0f0; margin-bottom: 16px; }
.pd-price-current { font-size: 32px; font-weight: 800; color: var(--cm-primary); letter-spacing: -1px; }
.pd-price-original { font-size: 16px; color: #aaa; text-decoration: line-through; margin-left: 8px; }
.pd-price-badge { display: inline-block; background: #fff0f0; color: var(--cm-primary); font-size: 13px; font-weight: 700; padding: 3px 10px; border-radius: 20px; margin-left: 10px; }
.pd-save { margin: 6px 0 0; font-size: 13px; color: #28a745; font-weight: 500; }

/* Short Desc */
.pd-short-desc { font-size: 14px; color: #555; line-height: 1.6; margin-bottom: 20px; }

/* Variants */
.pd-variants { margin-bottom: 20px; }
.pd-label { display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 8px; }
.pd-label span { color: var(--cm-primary); font-weight: 500; }
.pd-variant-list { display: flex; gap: 10px; flex-wrap: wrap; }
.pd-variant-btn { width: 42px; height: 42px; border-radius: 50%; border: 3px solid #e0e0e0; cursor: pointer; transition: all .2s; position: relative; display: flex; align-items: center; justify-content: center; padding: 0; }
.pd-variant-btn:hover { transform: scale(1.1); border-color: #999; }
.pd-variant-btn.active { border-color: #333; box-shadow: 0 0 0 2px #fff, 0 0 0 4px #333; }
.pd-variant-btn .bi-check-lg { color: #fff; font-size: 18px; opacity: 0; transition: opacity .2s; text-shadow: 0 1px 3px rgba(0,0,0,.4); }
.pd-variant-btn.active .bi-check-lg { opacity: 1; }

/* Quantity */
.pd-quantity { margin-bottom: 16px; }
.pd-qty-control { display: inline-flex; align-items: center; border: 2px solid #e0e0e0; border-radius: 10px; overflow: hidden; }
.pd-qty-btn { width: 40px; height: 40px; border: none; background: #fafafa; cursor: pointer; font-size: 16px; color: #333; transition: background .2s; display: flex; align-items: center; justify-content: center; }
.pd-qty-btn:hover { background: #f0f0f0; color: var(--cm-primary); }
.pd-qty-control input { width: 50px; height: 40px; border: none; border-left: 1px solid #e0e0e0; border-right: 1px solid #e0e0e0; text-align: center; font-size: 15px; font-weight: 600; outline: none; -moz-appearance: textfield; }
.pd-qty-control input::-webkit-outer-spin-button,
.pd-qty-control input::-webkit-inner-spin-button { -webkit-appearance: none; }

/* Stock */
.pd-stock { display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; margin-bottom: 16px; }
.pd-stock-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
.pd-stock.in-stock { color: #28a745; }
.pd-stock.in-stock .pd-stock-dot { background: #28a745; }
.pd-stock.out-of-stock { color: #dc3545; }
.pd-stock.out-of-stock .pd-stock-dot { background: #dc3545; }
.pd-stock-qty { font-weight: 400; color: #888; }

/* Trust Badges */
.pd-trust { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; padding: 14px 0; border-top: 1px solid #f0f0f0; border-bottom: 1px solid #f0f0f0; margin-bottom: 20px; }
.pd-trust-item { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #555; }
.pd-trust-item i { font-size: 16px; color: var(--cm-primary); }

/* Action Buttons */
.pd-actions { display: flex; gap: 10px; margin-bottom: 20px; }
.pd-btn { border: none; border-radius: 12px; font-size: 15px; font-weight: 700; cursor: pointer; transition: all .25s ease; display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 14px 24px; }
.pd-btn:disabled { opacity: .5; cursor: not-allowed; transform: none !important; }
.pd-btn-cart { flex: 1; background: var(--cm-primary); color: #fff; }
.pd-btn-cart:hover:not(:disabled) { background: var(--cm-primary-dark); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(255,56,56,.3); }
.pd-btn-buy { flex: 1; background: #1a1a2e; color: #fff; }
.pd-btn-buy:hover:not(:disabled) { background: #2a2a4a; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(26,26,46,.3); }
.pd-btn-wish { width: 50px; height: 50px; padding: 0; background: #fff; border: 2px solid #e0e0e0; color: #999; font-size: 20px; border-radius: 12px; flex-shrink: 0; }
.pd-btn-wish:hover { border-color: var(--cm-primary); color: var(--cm-primary); }
.pd-btn-wish.active { border-color: var(--cm-primary); color: var(--cm-primary); background: #fff0f0; }

/* Meta */
.pd-meta { font-size: 13px; color: #666; padding-top: 16px; border-top: 1px solid #f0f0f0; }
.pd-meta-row { margin-bottom: 4px; }
.pd-meta-label { font-weight: 600; color: #333; }
.pd-meta-row a { color: var(--cm-primary); text-decoration: none; }
.pd-meta-row a:hover { text-decoration: underline; }

/* Share */
.pd-share { display: flex; align-items: center; gap: 10px; margin-top: 12px; padding-top: 12px; border-top: 1px solid #f0f0f0; }
.pd-share-btn { width: 34px; height: 34px; border-radius: 50%; border: 1px solid #e0e0e0; background: #fff; color: #666; display: flex; align-items: center; justify-content: center; font-size: 14px; cursor: pointer; transition: all .2s; text-decoration: none; }
.pd-share-btn:hover { background: var(--cm-primary); color: #fff; border-color: var(--cm-primary); }

/* ── Accordion ── */
.pd-accordion-section { padding: 40px 0; }
.pd-accordion { max-width: 900px; margin: 0 auto; }
.pd-acc-item { border: 1px solid #eee; border-radius: 12px; margin-bottom: 10px; overflow: hidden; }
.pd-acc-header { width: 100%; display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; background: #fff; border: none; cursor: pointer; font-size: 15px; font-weight: 600; color: #333; transition: background .2s; }
.pd-acc-header:hover { background: #fafafa; }
.pd-acc-header .bi-chevron-down { transition: transform .3s; font-size: 14px; color: #999; }
.pd-acc-item.open .pd-acc-header .bi-chevron-down { transform: rotate(180deg); }
.pd-acc-body { max-height: 0; overflow: hidden; transition: max-height .35s ease; padding: 0 20px; }
.pd-acc-item.open .pd-acc-body { max-height: 3000px; padding: 0 20px 20px; }
.pd-empty { color: #999; font-size: 14px; }

/* ── Spec Table ── */
.pd-spec-table { width: 100%; border-collapse: collapse; }
.pd-spec-table td { padding: 10px 14px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
.pd-spec-table td:first-child { font-weight: 600; color: #333; width: 35%; background: #fafafa; }

/* ── Reviews ── */
.pd-reviews-wrap { }
.pd-review-summary { background: #fafafa; border-radius: 12px; padding: 24px; text-align: center; margin-bottom: 24px; }
.pd-review-big-num { font-size: 48px; font-weight: 800; color: #1a1a2e; }
.pd-review-big-stars { font-size: 20px; color: #f5a623; margin: 4px 0; }
.pd-review-total { font-size: 13px; color: #888; margin-bottom: 16px; }
.pd-review-bars { text-align: left; }
.pd-bar-row { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; font-size: 12px; color: #666; }
.pd-bar-row span:first-child { width: 40px; text-align: right; white-space: nowrap; }
.pd-bar-row span:last-child { width: 30px; color: #999; }
.pd-bar-row .bi-star-fill { font-size: 10px; color: #f5a623; }
.pd-bar { flex: 1; height: 6px; background: #e9ecef; border-radius: 3px; overflow: hidden; }
.pd-bar-fill { height: 100%; background: #f5a623; border-radius: 3px; }

.pd-reviews-list { margin-bottom: 24px; }
.pd-review-card { padding: 16px 0; border-bottom: 1px solid #f0f0f0; }
.pd-review-head { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 8px; }
.pd-review-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--cm-primary); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px; flex-shrink: 0; }
.pd-review-meta { flex: 1; }
.pd-review-meta h6 { margin: 0; font-size: 14px; font-weight: 600; color: #333; }
.pd-review-meta small { color: #999; font-size: 12px; }
.pd-review-stars { color: #f5a623; font-size: 12px; white-space: nowrap; }
.pd-review-text { font-size: 14px; color: #555; line-height: 1.6; margin: 0; padding-left: 52px; }

.pd-no-reviews { text-align: center; padding: 30px; color: #999; }
.pd-no-reviews i { font-size: 36px; margin-bottom: 8px; display: block; }

.pd-review-form { background: #fafafa; border-radius: 12px; padding: 24px; }
.pd-review-form h5 { font-size: 16px; font-weight: 700; margin-bottom: 16px; }
.pd-form-rating { display: flex; gap: 6px; margin-bottom: 16px; }
.pd-star-btn { font-size: 24px; color: #ddd; cursor: pointer; transition: color .15s, transform .15s; }
.pd-star-btn:hover, .pd-star-btn.active { color: #f5a623; transform: scale(1.15); }
.pd-form-input { width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 10px; font-size: 14px; margin-bottom: 12px; outline: none; transition: border-color .2s; font-family: inherit; }
.pd-form-input:focus { border-color: var(--cm-primary); }
.pd-btn-submit { background: var(--cm-primary); color: #fff; border-radius: 10px; padding: 12px 24px; font-size: 14px; }
.pd-btn-submit:hover { background: var(--cm-primary-dark); }
.pd-login-msg { background: #f0f7ff; padding: 16px 20px; border-radius: 10px; font-size: 14px; color: #333; }
.pd-login-msg a { color: var(--cm-primary); font-weight: 600; }

/* ── Related Products ── */
.pd-related { padding: 40px 0; }
.pd-section-title { font-size: 20px; font-weight: 800; color: #1a1a2e; margin-bottom: 20px; }
.pd-rel-card { display: block; background: #fff; border: 1px solid #f0f0f0; border-radius: 12px; overflow: hidden; text-decoration: none; transition: all .3s; }
.pd-rel-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,.08); transform: translateY(-4px); }
.pd-rel-img { aspect-ratio: 1/1; overflow: hidden; background: #f8f8f8; }
.pd-rel-img img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s; }
.pd-rel-card:hover .pd-rel-img img { transform: scale(1.08); }
.pd-rel-info { padding: 12px; }
.pd-rel-info h6 { font-size: 13px; font-weight: 600; color: #333; margin: 0 0 6px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4; }
.pd-rel-current { font-size: 15px; font-weight: 700; color: var(--cm-primary); }
.pd-rel-original { font-size: 12px; color: #aaa; text-decoration: line-through; margin-left: 4px; }

/* ── Delivery Info ── */
.pd-delivery-info { padding: 30px 0 40px; background: #fafafa; }
.pd-info-card { text-align: center; padding: 20px 12px; }
.pd-info-card i { font-size: 28px; color: var(--cm-primary); margin-bottom: 8px; }
.pd-info-card h6 { font-size: 14px; font-weight: 700; color: #333; margin-bottom: 4px; }
.pd-info-card p { font-size: 12px; color: #888; margin: 0; line-height: 1.5; }

/* ── Mobile Sticky Bar ── */
.pd-mobile-bar { display: none; position: fixed; bottom: 0; left: 0; right: 0; background: #fff; border-top: 1px solid #eee; padding: 10px 16px; z-index: 1040; align-items: center; gap: 12px; box-shadow: 0 -4px 20px rgba(0,0,0,.08); }
.pd-mobile-price { font-size: 22px; font-weight: 800; color: var(--cm-primary); white-space: nowrap; }
.pd-mobile-bar .pd-btn { flex: 1; padding: 12px; font-size: 14px; }

/* ── Responsive ── */
@media (max-width: 991px) {
    .pd-gallery { position: static; }
    .pd-mobile-bar { display: flex; }
    body { padding-bottom: 70px; }
}
@media (max-width: 575px) {
    .pd-title { font-size: 22px; }
    .pd-price-current { font-size: 26px; }
    .pd-actions { flex-direction: column; }
    .pd-btn-wish { width: 100%; height: 44px; }
    .pd-trust { grid-template-columns: 1fr; }
}
</style>

<!-- ═══ SCRIPTS ═══ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pdMainImg = document.getElementById('pdMainImage');
    const pdThumbs = document.querySelectorAll('.pd-thumb');
    const pdQty = document.getElementById('pdQty');
    const pdImgIndex = document.getElementById('pdImgIndex');

    /* ── Thumbnail Gallery ── */
    pdThumbs.forEach(function(btn) {
        btn.addEventListener('click', function() {
            pdThumbs.forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
            pdMainImg.src = this.dataset.image;
            if (pdImgIndex) pdImgIndex.textContent = parseInt(this.dataset.index) + 1;
        });
    });

    /* ── Quantity ── */
    var qtyMinus = document.getElementById('pdQtyMinus');
    var qtyPlus = document.getElementById('pdQtyPlus');
    if (qtyMinus) qtyMinus.addEventListener('click', function() { var v = parseInt(pdQty.value) || 1; if (v > 1) pdQty.value = v - 1; });
    if (qtyPlus) qtyPlus.addEventListener('click', function() { var v = parseInt(pdQty.value) || 1; var m = parseInt(pdQty.max) || 100; if (v < m) pdQty.value = v + 1; });
    pdQty.addEventListener('change', function() { var v = parseInt(this.value) || 1; var m = parseInt(this.max) || 100; this.value = Math.max(1, Math.min(m, v)); });

    /* ── Variant Selection ── */
    var variantBtns = document.querySelectorAll('.pd-variant-btn');
    var priceEls = document.querySelectorAll('.pd-price-current');
    var stockEl = document.getElementById('pdStockText');
    var stockBox = stockEl ? stockEl.parentElement : null;
    var addToCartBtn = document.getElementById('pdAddToCart');
    var buyNowBtn = document.getElementById('pdBuyNow');

    variantBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            variantBtns.forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
            var lbl = document.getElementById('pdVariantLabel');
            if (lbl && this.dataset.name) lbl.textContent = '- ' + this.dataset.name;
            if (this.dataset.price) {
                priceEls.forEach(function(el) { el.textContent = '\u09F3' + parseInt(this.dataset.price.replace(/,/g,'')).toLocaleString(); }.bind(this));
            }
            var stock = parseInt(this.dataset.stock);
            if (stockEl) stockEl.textContent = stock > 0 ? 'In Stock' : 'Out of Stock';
            if (stockBox) { stockBox.className = 'pd-stock ' + (stock > 0 ? 'in-stock' : 'out-of-stock'); }
            if (addToCartBtn) addToCartBtn.disabled = stock <= 0;
            if (buyNowBtn) buyNowBtn.disabled = stock <= 0;
            var mobileCart = document.getElementById('pdMobileCart');
            if (mobileCart) mobileCart.disabled = stock <= 0;
        });
    });

    /* ── Accordion ── */
    document.querySelectorAll('.pd-acc-header').forEach(function(header) {
        header.addEventListener('click', function() {
            var item = this.parentElement;
            var wasOpen = item.classList.contains('open');
            /* Close others optionally:
            document.querySelectorAll('.pd-acc-item').forEach(function(i) { i.classList.remove('open'); });
            */
            if (wasOpen) { item.classList.remove('open'); } else { item.classList.add('open'); }
        });
    });

    /* ── Star Rating ── */
    var starBtns = document.querySelectorAll('#pdStarRating .pd-star-btn');
    starBtns.forEach(function(star) {
        star.addEventListener('click', function() {
            var r = parseInt(this.dataset.rating);
            document.getElementById('ratingInput').value = r;
            starBtns.forEach(function(s, i) { s.classList.toggle('active', i < r); s.classList.toggle('bi-star-fill', i < r); });
        });
        star.addEventListener('mouseenter', function() {
            var r = parseInt(this.dataset.rating);
            starBtns.forEach(function(s, i) { s.style.color = i < r ? '#f5a623' : '#ddd'; });
        });
        star.addEventListener('mouseleave', function() {
            var cur = parseInt(document.getElementById('ratingInput').value);
            starBtns.forEach(function(s, i) { s.style.color = ''; s.classList.toggle('bi-star-fill', i < cur); s.classList.toggle('bi-star', i >= cur); });
        });
    });

    /* ── Add to Cart ── */
    function handleAddToCart(productId) {
        var qty = pdQty.value;
        var sel = document.querySelector('.pd-variant-btn.active');
        var vid = sel ? sel.dataset.variantId : null;
        var btn = document.getElementById('pdAddToCart');
        var origText = btn ? btn.innerHTML : '';
        if (btn) { btn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Adding...'; btn.disabled = true; }

        var params = new URLSearchParams();
        params.append('product_id', productId);
        params.append('quantity', qty);
        if (vid) params.append('variant_id', vid);

        fetch('<?= APP_URL ?>/cart/add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: params.toString()
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                if (typeof Cart !== 'undefined') Cart.loadCart();
                if (typeof window.updateHeaderCartBadge === 'function' && data.cart_count !== undefined) window.updateHeaderCartBadge(data.cart_count);
                if (btn) { btn.innerHTML = '<i class="bi bi-check-lg"></i> Added!'; setTimeout(function() { btn.innerHTML = origText; btn.disabled = false; }, 2000); }
                if (typeof App !== 'undefined' && App.toast) App.toast('Product added to cart!', 'success');
            } else {
                if (btn) { btn.innerHTML = origText; btn.disabled = false; }
                if (typeof App !== 'undefined' && App.toast) App.toast(data.message || 'Error', 'error');
            }
        })
        .catch(function() {
            if (btn) { btn.innerHTML = origText; btn.disabled = false; }
            if (typeof App !== 'undefined' && App.toast) App.toast('Error adding to cart', 'error');
        });
    }

    var addToCartBtnEl = document.getElementById('pdAddToCart');
    if (addToCartBtnEl) addToCartBtnEl.addEventListener('click', function() { handleAddToCart(this.dataset.productId); });
    var mobileCartBtn = document.getElementById('pdMobileCart');
    if (mobileCartBtn) mobileCartBtn.addEventListener('click', function() { handleAddToCart(this.dataset.productId); });

    /* ── Buy Now ── */
    var buyNowBtnEl = document.getElementById('pdBuyNow');
    if (buyNowBtnEl) buyNowBtnEl.addEventListener('click', function() {
        var pid = this.dataset.productId;
        var qty = pdQty.value;
        var sel = document.querySelector('.pd-variant-btn.active');
        var vid = sel ? sel.dataset.variantId : null;
        var params = new URLSearchParams();
        params.append('product_id', pid);
        params.append('quantity', qty);
        if (vid) params.append('variant_id', vid);
        fetch('<?= APP_URL ?>/cart/add', {
            method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: params.toString()
        }).then(function(r) { return r.json(); }).then(function(d) { if (d.success) window.location.href = '<?= APP_URL ?>/checkout'; });
    });

    /* ── Wishlist ── */
    var wishBtn = document.getElementById('pdWishlist');
    if (wishBtn) wishBtn.addEventListener('click', function() {
        var pid = this.dataset.productId;
        var icon = document.getElementById('pdWishIcon');
        var btn = this;
        fetch('<?= APP_URL ?>/ajax_handler.php', {
            method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: 'action=toggle_wishlist&product_id=' + encodeURIComponent(pid)
        }).then(function(r) { return r.json(); }).then(function(data) {
            if (data.success) {
                if (data.added) { icon.classList.remove('bi-heart'); icon.classList.add('bi-heart-fill'); btn.classList.add('active'); if (typeof App !== 'undefined' && App.toast) App.toast('Added to wishlist!', 'success'); }
                else { icon.classList.remove('bi-heart-fill'); icon.classList.add('bi-heart'); btn.classList.remove('active'); if (typeof App !== 'undefined' && App.toast) App.toast('Removed from wishlist', 'info'); }
                var wb = document.getElementById('wishlistCountBadge');
                if (wb) { var c = data.count || 0; wb.textContent = c; wb.style.display = c > 0 ? '' : 'none'; }
            } else { if (typeof App !== 'undefined' && App.toast) App.toast(data.message || 'Please login', 'error'); }
        });
    });

    /* ── Review Form ── */
    var reviewForm = document.getElementById('reviewForm');
    if (reviewForm) reviewForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var rating = document.getElementById('ratingInput').value;
        var title = document.getElementById('reviewTitle').value;
        var comment = document.getElementById('reviewComment').value;
        var pid = this.dataset.productId;
        if (rating == 0) { if (typeof App !== 'undefined' && App.toast) App.toast('Please select a rating', 'error'); return; }
        var subBtn = this.querySelector('button[type="submit"]');
        var origHtml = subBtn.innerHTML;
        subBtn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Submitting...'; subBtn.disabled = true;
        fetch('<?= APP_URL ?>/ajax_handler.php', {
            method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: new URLSearchParams({ action: 'submit_review', product_id: pid, rating: rating, title: title, comment: comment }).toString()
        }).then(function(r) { return r.json(); }).then(function(data) {
            if (data.success) { if (typeof App !== 'undefined' && App.toast) App.toast('Review submitted!', 'success'); setTimeout(function() { location.reload(); }, 1500); }
            else { subBtn.innerHTML = origHtml; subBtn.disabled = false; if (typeof App !== 'undefined' && App.toast) App.toast(data.message || 'Error', 'error'); }
        }).catch(function() { subBtn.innerHTML = origHtml; subBtn.disabled = false; });
    });

    /* ── Copy Link ── */
    var copyBtn = document.getElementById('pdCopyLink');
    if (copyBtn) copyBtn.addEventListener('click', function() {
        navigator.clipboard.writeText(window.location.href);
        if (typeof App !== 'undefined' && App.toast) App.toast('Link copied!', 'success');
    });

    /* ── Spin Animation ── */
    var style = document.createElement('style');
    style.textContent = '@keyframes spin{to{transform:rotate(360deg)}}.spin{animation:spin .8s linear infinite}';
    document.head.appendChild(style);
});
</script>
