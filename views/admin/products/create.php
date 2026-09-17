<?php
use App\Helpers\CSRF;
use App\Helpers\Sanitizer;
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Add Product</h4>
    <a href="<?= ADMIN_URL ?>products" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Products
    </a>
</div>

<form method="POST" action="<?= ADMIN_URL ?>products/store" enctype="multipart/form-data">
    <?= CSRF::field() ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Product Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required value="<?= Sanitizer::clean(old('name')) ?>">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control" id="slug" name="slug" value="<?= Sanitizer::clean(old('slug')) ?>" placeholder="Auto-generated if empty">
                        </div>
                        <div class="col-md-6">
                            <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="sku" name="sku" required value="<?= Sanitizer::clean(old('sku')) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="short_description" class="form-label">Short Description</label>
                        <textarea class="form-control" id="short_description" name="short_description" rows="3"><?= Sanitizer::clean(old('short_description')) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="long_description" class="form-label">Long Description</label>
                        <textarea class="form-control" id="long_description" name="long_description" rows="6"><?= Sanitizer::clean(old('long_description')) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Pricing & Stock</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">৳</span>
                                <input type="number" step="0.01" class="form-control" id="price" name="price" required value="<?= Sanitizer::clean(old('price')) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="discount_price" class="form-label">Discount Price</label>
                            <div class="input-group">
                                <span class="input-group-text">৳</span>
                                <input type="number" step="0.01" class="form-control" id="discount_price" name="discount_price" value="<?= Sanitizer::clean(old('discount_price')) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="stock_quantity" class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" required value="<?= Sanitizer::clean(old('stock_quantity', '0')) ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Organization</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= (int)$category['id'] ?>" <?= old('category_id') == $category['id'] ? 'selected' : '' ?>>
                                    <?= Sanitizer::clean($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="brand_id" class="form-label">Brand</label>
                        <select class="form-select" id="brand_id" name="brand_id">
                            <option value="">Select Brand</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?= (int)$brand['id'] ?>" <?= old('brand_id') == $brand['id'] ? 'selected' : '' ?>>
                                    <?= Sanitizer::clean($brand['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" <?= old('is_featured') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_featured">Featured Product</label>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="is_offer" name="is_offer" value="1" <?= old('is_offer') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_offer">Show in Offers</label>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" <?= old('status', '1') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="status">Active Status</label>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Main Image</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="file" class="form-control" id="main_image" name="main_image" accept="image/*">
                        <div class="form-text">Recommended: 800x800px, JPG/PNG/WebP</div>
                    </div>
                    <div id="imagePreview" class="text-center d-none">
                        <img src="#" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                    </div>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-1"></i> Create Product
                </button>
            </div>
        </div>
    </div>
</form>

<script>
document.getElementById('name').addEventListener('input', function() {
    const slug = this.value.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim();
    document.getElementById('slug').value = slug;
});

document.getElementById('main_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('imagePreview');
            preview.querySelector('img').src = e.target.result;
            preview.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }
});
</script>
