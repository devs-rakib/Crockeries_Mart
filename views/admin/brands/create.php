<?php
use App\Helpers\CSRF;
use App\Helpers\Sanitizer;
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Add Brand</h4>
    <a href="<?= ADMIN_URL ?>brands" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Brands
    </a>
</div>

<form method="POST" action="<?= ADMIN_URL ?>brands/store" enctype="multipart/form-data">
    <?= CSRF::field() ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Brand Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Brand Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required value="<?= Sanitizer::clean(old('name')) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" class="form-control" id="slug" name="slug" value="<?= Sanitizer::clean(old('slug')) ?>" placeholder="Auto-generated if empty">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Logo</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                        <div class="form-text">Recommended: 300x100px, PNG with transparent background</div>
                    </div>
                    <div id="logoPreview" class="text-center d-none">
                        <img src="#" alt="Preview" class="img-fluid rounded bg-light p-2" style="max-height: 100px; object-fit: contain;">
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Settings</h5>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" <?= old('status', '1') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="status">Active Status</label>
                    </div>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-1"></i> Create Brand
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

document.getElementById('logo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('logoPreview');
            preview.querySelector('img').src = e.target.result;
            preview.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }
});
</script>
