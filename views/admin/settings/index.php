<?php
use App\Helpers\CSRF;
use App\Helpers\Sanitizer;
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Site Settings</h4>
</div>

<form method="POST" action="<?= ADMIN_URL ?>settings/update">
    <?= CSRF::field() ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">General Settings</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="site_name" class="form-label">Site Name</label>
                        <input type="text" class="form-control" id="site_name" name="site_name" value="<?= Sanitizer::clean($settings['site_name'] ?? SITE_NAME) ?>">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="site_email" class="form-label">Site Email</label>
                            <input type="email" class="form-control" id="site_email" name="site_email" value="<?= Sanitizer::clean($settings['site_email'] ?? SITE_EMAIL) ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="site_phone" class="form-label">Site Phone</label>
                            <input type="text" class="form-control" id="site_phone" name="site_phone" value="<?= Sanitizer::clean($settings['site_phone'] ?? SITE_PHONE) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="site_address" class="form-label">Site Address</label>
                        <textarea class="form-control" id="site_address" name="site_address" rows="2"><?= Sanitizer::clean($settings['site_address'] ?? SITE_ADDRESS) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Shipping Settings</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="shipping_inside" class="form-label">Inside Dhaka (৳)</label>
                            <input type="number" step="0.01" class="form-control" id="shipping_inside" name="shipping_inside" value="<?= Sanitizer::clean($settings['shipping_inside'] ?? SHIPPING_INSIDE_DHAKA) ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="shipping_outside" class="form-label">Outside Dhaka (৳)</label>
                            <input type="number" step="0.01" class="form-control" id="shipping_outside" name="shipping_outside" value="<?= Sanitizer::clean($settings['shipping_outside'] ?? SHIPPING_OUTSIDE_DHAKA) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Social Media Links</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="site_facebook" class="form-label">Facebook URL</label>
                        <input type="url" class="form-control" id="site_facebook" name="site_facebook" value="<?= Sanitizer::clean($settings['site_facebook'] ?? SITE_FACEBOOK) ?>" placeholder="https://facebook.com/page">
                    </div>
                    <div class="mb-3">
                        <label for="site_youtube" class="form-label">YouTube URL</label>
                        <input type="url" class="form-control" id="site_youtube" name="site_youtube" value="<?= Sanitizer::clean($settings['site_youtube'] ?? SITE_YOUTUBE) ?>" placeholder="https://youtube.com/channel">
                    </div>
                    <div class="mb-0">
                        <label for="site_instagram" class="form-label">Instagram URL</label>
                        <input type="url" class="form-control" id="site_instagram" name="site_instagram" value="<?= Sanitizer::clean($settings['site_instagram'] ?? SITE_INSTAGRAM) ?>" placeholder="https://instagram.com/profile">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-lg me-1"></i> Save Settings
                        </button>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">SMTP Configuration</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <small>
                            <i class="bi bi-info-circle me-1"></i>
                            SMTP settings are configured in <code>config/config.php</code>. 
                            Update SMTP_HOST, SMTP_USER, SMTP_PASS constants to enable email.
                        </small>
                    </div>
                </div>
            </div>

            <!-- Database Backup -->
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-database me-2"></i>Database Backup</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= ADMIN_URL ?>settings/backup" class="mb-3">
                        <?= CSRF::field() ?>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-primary" onclick="this.disabled=true;this.innerHTML='<span class=\'spinner-border spinner-border-sm me-2\'></span>Creating...'">
                                <i class="bi bi-download me-1"></i> Create Backup Now
                            </button>
                        </div>
                    </form>

                    <?php if (!empty($backups)): ?>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>File</th>
                                        <th class="text-end">Size</th>
                                        <th class="text-center" style="width:80px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($backups as $backup): ?>
                                        <tr>
                                            <td>
                                                <small class="text-muted"><?= $backup['name'] ?></small>
                                                <br><small class="text-muted"><?= $backup['date'] ?></small>
                                            </td>
                                            <td class="text-end"><small><?= $backup['size_fmt'] ?></small></td>
                                            <td class="text-center">
                                                <a href="<?= ADMIN_URL ?>settings/download/<?= urlencode($backup['name']) ?>" class="btn btn-sm btn-outline-success" title="Download">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                <form method="POST" action="<?= ADMIN_URL ?>settings/deleteBackup/<?= urlencode($backup['name']) ?>" class="d-inline" onsubmit="return confirm('Delete this backup?')">
                                                    <?= CSRF::field() ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-inbox" style="font-size:24px;"></i>
                            <p class="mb-0 mt-2">No backups yet</p>
                        </div>
                    <?php endif; ?>

                    <div class="alert alert-info mt-3 mb-0">
                        <small>
                            <i class="bi bi-info-circle me-1"></i>
                            Auto-cleanup: backups older than 30 days are removed automatically.
                            For scheduled backups, run <code>storage/backups/backup.bat</code> via Windows Task Scheduler.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
