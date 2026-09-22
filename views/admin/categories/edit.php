<?php
use App\Helpers\CSRF;
use App\Helpers\Sanitizer;
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Edit Category</h4>
    <a href="<?= ADMIN_URL ?>categories" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Categories
    </a>
</div>

<form method="POST" action="<?= ADMIN_URL ?>categories/update/<?= (int)$category['id'] ?>" enctype="multipart/form-data">
    <?= CSRF::field() ?>
    <input type="hidden" name="id" value="<?= (int)$category['id'] ?>">

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Category Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required value="<?= Sanitizer::clean(old('name', $category['name'])) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" class="form-control" id="slug" name="slug" value="<?= Sanitizer::clean(old('slug', $category['slug'])) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="parent_id" class="form-label">Parent Category</label>
                        <select class="form-select" id="parent_id" name="parent_id">
                            <option value="">None (Top Level)</option>
                            <?php foreach ($allCategories as $cat): ?>
                                <?php if ($cat['id'] != $category['id']): ?>
                                    <option value="<?= (int)$cat['id'] ?>" <?= old('parent_id', $category['parent_id']) == $cat['id'] ? 'selected' : '' ?>>
                                        <?= Sanitizer::clean($cat['name']) ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Leave empty for a top-level category. Cannot select itself as parent.</div>
                    </div>

                    <div class="mb-3">
                        <label for="icon_class" class="form-label">Icon Class</label>
                        <input type="text" class="form-control" id="icon_class" name="icon_class" value="<?= Sanitizer::clean(old('icon_class', $category['icon_class'])) ?>" placeholder="e.g., fas fa-mug-hot">
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
                    <?php if (!empty($category['image'])): ?>
                        <div class="mb-3 text-center">
                            <img src="<?= Sanitizer::image($category['image']) ?>" alt="<?= Sanitizer::clean($category['name']) ?>" class="img-fluid rounded" style="max-height: 150px;">
                            <div class="form-text">Current image</div>
                        </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    <div class="form-text">Leave empty to keep current image</div>
                    <div id="imagePreview" class="text-center mt-3 d-none">
                        <img src="#" alt="New Preview" class="img-fluid rounded" style="max-height: 150px;">
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
                        <input type="number" class="form-control" id="position" name="position" value="<?= Sanitizer::clean(old('position', $category['position'])) ?>">
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" <?= old('is_featured', $category['is_featured']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_featured">Featured Category</label>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" <?= old('status', $category['status']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="status">Active Status</label>
                    </div>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-lg me-1"></i> Update Category
                </button>
            </div>
        </div>
    </div>
</form>

<script>
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
