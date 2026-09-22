<?php
use App\Helpers\Sanitizer;
/**
 * Wishlist Page
 * @var array $items Wishlist items
 */
$items = $items ?? [];
?>

<style>
.wishlist-section { padding: 40px 0 80px; }
.wishlist-section h1 { font-weight: 800; margin-bottom: 30px; }
.wishlist-empty {
    text-align: center; padding: 60px 0;
}
.wishlist-empty i { font-size: 64px; color: #ddd; }
.wishlist-empty p { color: #888; margin: 16px 0 24px; }

.wishlist-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}
@media (max-width: 991px) { .wishlist-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 767px) { .wishlist-grid { grid-template-columns: repeat(2, 1fr); } }

.wishlist-card {
    background: #fff;
    border: 1px solid #eee;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
}
.wishlist-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.08); transform: translateY(-3px); }

.wishlist-card .card-img {
    position: relative; padding-top: 100%; overflow: hidden; background: #f9f9f9;
}
.wishlist-card .card-img img {
    position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover;
    transition: transform 0.4s;
}
.wishlist-card:hover .card-img img { transform: scale(1.06); }

.wishlist-card .remove-btn {
    position: absolute; top: 10px; right: 10px;
    width: 32px; height: 32px; border-radius: 50%;
    background: #fff; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all 0.2s; z-index: 2;
}
.wishlist-card .remove-btn:hover { background: #ff3838; color: #fff; }

.wishlist-card .card-body { padding: 14px; }
.wishlist-card .card-category { font-size: 11px; text-transform: uppercase; color: #999; letter-spacing: 0.5px; }
.wishlist-card .card-title {
    font-size: 14px; font-weight: 600; margin: 6px 0 8px;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.wishlist-card .card-title a { color: #222; text-decoration: none; }
.wishlist-card .card-title a:hover { color: #ff3838; }
.wishlist-card .card-price { font-size: 16px; font-weight: 700; color: #ff3838; }
.wishlist-card .card-price .old-price {
    font-size: 13px; color: #aaa; text-decoration: line-through; font-weight: 400; margin-left: 6px;
}
.wishlist-card .card-actions { display: flex; gap: 8px; margin-top: 10px; }
.wishlist-card .card-actions .btn { flex: 1; font-size: 12px; padding: 8px; border-radius: 8px; font-weight: 600; }
</style>

<section class="wishlist-section">
    <div class="container">
        <h1><i class="bi bi-heart-fill" style="color:#ff3838"></i> My Wishlist</h1>

        <?php if (empty($items)): ?>
            <div class="wishlist-empty">
                <i class="bi bi-heart"></i>
                <p>Your wishlist is empty</p>
                <a href="<?= APP_URL ?>/shop" class="btn btn-primary" style="background:#ff3838;border:none;border-radius:8px;padding:10px 28px;">
                    Continue Shopping
                </a>
            </div>
        <?php else: ?>
            <div class="wishlist-grid">
                <?php foreach ($items as $item): ?>
                    <?php
                    $itemImage = !empty($item['main_image']) ? Sanitizer::image($item['main_image']) : APP_URL . '/assets/images/placeholder-product.jpg';
                    $itemPrice = $item['discount_price'] > 0 ? $item['discount_price'] : $item['price'];
                    $hasDiscount = $item['discount_price'] > 0 && $item['discount_price'] < $item['price'];
                    ?>
                    <div class="wishlist-card" id="wishlist-item-<?= (int)$item['product_id'] ?>">
                        <div class="card-img">
                            <a href="<?= APP_URL ?>/product/<?= urlencode($item['slug']) ?>">
                                <img src="<?= $itemImage ?>" alt="<?= htmlspecialchars($item['name']) ?>" loading="lazy">
                            </a>
                            <button class="remove-btn" data-product-id="<?= (int)$item['product_id'] ?>" title="Remove">
                                <i class="bi bi-x-lg" style="font-size:12px;"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($item['category_name'])): ?>
                                <span class="card-category"><?= htmlspecialchars($item['category_name']) ?></span>
                            <?php endif; ?>
                            <h6 class="card-title">
                                <a href="<?= APP_URL ?>/product/<?= urlencode($item['slug']) ?>"><?= htmlspecialchars($item['name']) ?></a>
                            </h6>
                            <div class="card-price">
                                <?= Sanitizer::banglaPrice($itemPrice) ?>
                                <?php if ($hasDiscount): ?>
                                    <span class="old-price"><?= Sanitizer::banglaPrice($item['price']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="card-actions">
                                <button type="button" class="btn btn-add-cart btn-sm" data-add-to-cart="<?= (int)$item['product_id'] ?>" style="background:#ff3838;color:#fff;border:none;">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
document.querySelectorAll('.wishlist-card .remove-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var productId = this.dataset.productId;
        var card = document.getElementById('wishlist-item-' + productId);
        fetch('<?= APP_URL ?>/ajax_handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body: 'action=toggle_wishlist&product_id=' + productId
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                if (card) {
                    card.classList.add('removing');
                    setTimeout(function() {
                        card.remove();
                        if (document.querySelectorAll('.wishlist-card').length === 0) {
                            location.reload();
                        }
                    }, 300);
                }
            }
        });
    });
});
</script>
