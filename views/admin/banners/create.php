<?php
use App\Helpers\CSRF;
use App\Helpers\Sanitizer;
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Add Banner</h4>
    <a href="<?= ADMIN_URL ?>banners" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Banners
    </a>
</div>

<form method="POST" action="<?= ADMIN_URL ?>banners/store" enctype="multipart/form-data">
    <?= CSRF::field() ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Banner Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required value="<?= Sanitizer::clean(old('title')) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Short description for the banner"><?= Sanitizer::clean(old('description')) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="button_text" class="form-label">Button Text</label>
                        <input type="text" class="form-control" id="button_text" name="button_text" value="<?= Sanitizer::clean(old('button_text', 'Shop Now')) ?>" placeholder="Shop Now">
                    </div>

                    <div class="mb-3">
                        <label for="link" class="form-label">Link URL</label>
                        <input type="url" class="form-control" id="link" name="link" value="<?= Sanitizer::clean(old('link')) ?>" placeholder="https://example.com/page">
                        <div class="form-text">Where the banner links to when clicked.</div>
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
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                        <div class="form-text">Recommended: 1920x600px for hero slider</div>
                    </div>
                    <div id="imagePreview" class="text-center d-none">
                        <img src="#" alt="Preview" class="img-fluid rounded" style="max-height: 150px; object-fit: cover;">
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Settings</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="type" class="form-label">Banner Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="hero_slider" <?= old('type') === 'hero_slider' ? 'selected' : '' ?>>Hero Slider</option>
                            <option value="middle_banner" <?= old('type') === 'middle_banner' ? 'selected' : '' ?>>Middle Banner</option>
                            <option value="sidebar_banner" <?= old('type') === 'sidebar_banner' ? 'selected' : '' ?>>Sidebar Banner</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="position" class="form-label">Position</label>
                        <input type="number" class="form-control" id="position" name="position" value="<?= Sanitizer::clean(old('position', '0')) ?>">
                        <div class="form-text">Lower numbers appear first.</div>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" <?= old('status', '1') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="status">Active Status</label>
                    </div>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-lg me-1"></i> Create Banner
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
