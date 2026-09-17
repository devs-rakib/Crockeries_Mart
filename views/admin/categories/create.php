<?php
use App\Helpers\CSRF;
use App\Helpers\Sanitizer;
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Add Category</h4>
    <a href="<?= ADMIN_URL ?>categories" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Categories
    </a>
</div>

<form method="POST" action="<?= ADMIN_URL ?>categories/store" enctype="multipart/form-data">
    <?= CSRF::field() ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Category Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required value="<?= Sanitizer::clean(old('name')) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" class="form-control" id="slug" name="slug" value="<?= Sanitizer::clean(old('slug')) ?>" placeholder="Auto-generated if empty">
                    </div>

                    <div class="mb-3">
                        <label for="parent_id" class="form-label">Parent Category</label>
                        <select class="form-select" id="parent_id" name="parent_id">
                            <option value="">None (Top Level)</option>
                            <?php foreach ($allCategories as $cat): ?>
                                <option value="<?= (int)$cat['id'] ?>" <?= old('parent_id') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= Sanitizer::clean($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Leave empty to create a top-level category.</div>
                    </div>

                    <div class="mb-3">
                        <label for="icon_class" class="form-label">Icon Class</label>
                        <input type="text" class="form-control" id="icon_class" name="icon_class" value="<?= Sanitizer::clean(old('icon_class')) ?>" placeholder="e.g., fas fa-mug-hot">
                        <div class="form-text">Font Awesome class for the category icon.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Image</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <div class="form-text">Recommended: 400x400px</div>
                    </div>
                    <div id="imagePreview" class="text-center d-none">
                        <img src="#" alt="Preview" class="img-fluid rounded" style="max-height: 150px;">
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Settings</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="position" class="form-label">Position</label>
                        <input type="number" class="form-control" id="position" name="position" value="<?= Sanitizer::clean(old('position', '0')) ?>">
                        <div class="form-text">Lower numbers appear first.</div>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" <?= old('is_featured') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_featured">Featured Category</label>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" <?= old('status', '1') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="status">Active Status</label>
                    </div>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-1"></i> Create Category
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

document.getElementById('image').addEventListener('change', function(e) {
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
