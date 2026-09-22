<?php
use App\Helpers\Auth;
use App\Helpers\Sanitizer;

$currentUser = $user ?? null;
$orders = [];
if ($currentUser) {
    $db = \App\Helpers\Database::getInstance();
    $orders = $db->fetchAll("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 10", [$currentUser['id']]);
}
?>

<style>
    .page-hero { background: linear-gradient(135deg, var(--cm-dark) 0%, var(--cm-dark-light) 100%); color: #fff; padding: 60px 0 40px; text-align: center; }
    .page-hero h1 { font-size: 32px; font-weight: 800; }
    .account-section { padding: 40px 0; }
    .account-card { background: var(--cm-white); border-radius: 12px; box-shadow: var(--cm-shadow); padding: 28px; margin-bottom: 24px; }
    .account-card h5 { font-weight: 700; color: var(--cm-dark); margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid var(--cm-gray-200); }

    .profile-alert { background: #fff3cd; border: 1px solid #ffc107; border-radius: 10px; padding: 16px 20px; margin-bottom: 24px; display: flex; align-items: flex-start; gap: 12px; }
    .profile-alert i { font-size: 22px; color: #856404; margin-top: 2px; }
    .profile-alert .alert-text { flex: 1; }
    .profile-alert .alert-text strong { display: block; margin-bottom: 4px; color: #856404; }
    .profile-alert .alert-text small { color: #856404; }

    .avatar-wrapper { position: relative; width: 120px; height: 120px; margin: 0 auto 16px; }
    .avatar-img { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid var(--cm-primary); }
    .avatar-placeholder { width: 120px; height: 120px; border-radius: 50%; background: var(--cm-primary); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 40px; font-weight: 700; border: 4px solid var(--cm-primary); }
    .avatar-edit { position: absolute; bottom: 0; right: 0; width: 36px; height: 36px; border-radius: 50%; background: var(--cm-primary); color: #fff; border: 3px solid #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 14px; transition: transform .2s; }
    .avatar-edit:hover { transform: scale(1.1); }
    .avatar-edit input { display: none; }

    .profile-info { text-align: center; margin-bottom: 16px; }
    .profile-info h6 { font-weight: 700; color: var(--cm-dark); font-size: 18px; margin-bottom: 4px; }
    .profile-info small { color: var(--cm-gray-500); }
    .profile-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
    .badge-complete { background: #d4edda; color: #155724; }
    .badge-incomplete { background: #fff3cd; color: #856404; }

    .profile-stats { display: flex; gap: 8px; justify-content: center; margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--cm-gray-200); }
    .profile-stat { text-align: center; flex: 1; }
    .profile-stat .stat-val { font-size: 20px; font-weight: 700; color: var(--cm-primary); }
    .profile-stat .stat-lbl { font-size: 11px; color: var(--cm-gray-500); }

    .edit-form .form-label { font-weight: 600; color: var(--cm-dark); font-size: 13px; }
    .edit-form .form-control { border: 2px solid var(--cm-gray-200); border-radius: 8px; padding: 10px 14px; font-size: 14px; transition: border-color var(--cm-transition); }
    .edit-form .form-control:focus { border-color: var(--cm-primary); box-shadow: 0 0 0 3px rgba(255,56,56,.1); }

    .btn-save { padding: 10px 24px; border: none; border-radius: 8px; background: var(--cm-primary); color: #fff; font-weight: 600; cursor: pointer; transition: all var(--cm-transition); }
    .btn-save:hover { background: var(--cm-primary-dark); }
    .btn-save:disabled { opacity: .6; cursor: not-allowed; }
    .btn-save .spinner-border { width: 16px; height: 16px; border-width: 2px; }

    .order-table { width: 100%; border-collapse: collapse; }
    .order-table th, .order-table td { padding: 12px; border-bottom: 1px solid var(--cm-gray-200); text-align: left; font-size: 14px; }
    .order-table th { background: var(--cm-gray-100); font-weight: 600; color: var(--cm-dark); }
    .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-processing { background: #cce5ff; color: #004085; }
    .status-completed { background: #d4edda; color: #155724; }
    .status-cancelled { background: #f8d7da; color: #721c24; }
    .status-paid { background: #d4edda; color: #155724; }
    .status-failed { background: #f8d7da; color: #721c24; }

    .profile-toast { position: fixed; top: 20px; right: 20px; padding: 14px 20px; border-radius: 10px; font-size: 14px; font-weight: 500; z-index: 9999; transform: translateX(120%); transition: transform .3s ease; display: flex; align-items: center; gap: 10px; }
    .profile-toast.show { transform: translateX(0); }
    .profile-toast.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .profile-toast.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

    .password-section .form-label { font-weight: 600; font-size: 13px; }
    .password-section .form-control { border: 2px solid var(--cm-gray-200); border-radius: 8px; padding: 10px 14px; font-size: 14px; }
    .password-section .form-control:focus { border-color: var(--cm-primary); box-shadow: 0 0 0 3px rgba(255,56,56,.1); }

    .nav-pills-custom { gap: 8px; margin-bottom: 24px; }
    .nav-pills-custom .nav-link { border: 2px solid var(--cm-gray-200); border-radius: 8px; padding: 10px 16px; font-size: 13px; font-weight: 600; color: var(--cm-gray-500); transition: all var(--cm-transition); }
    .nav-pills-custom .nav-link.active { background: var(--cm-primary); color: #fff; border-color: var(--cm-primary); }
    .nav-pills-custom .nav-link:hover:not(.active) { border-color: var(--cm-primary); color: var(--cm-primary); }
</style>

<div class="page-hero">
    <div class="container" style="color: #fff;">
        <h1>My Account</h1>
    </div>
</div>

<div id="profileToast" class="profile-toast"></div>

<div class="account-section">
    <div class="container">
        <?php if ($isIncomplete): ?>
        <div class="profile-alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div class="alert-text">
                <strong>Profile Incomplete</strong>
                <small>Please update your information to complete your profile. This helps us serve you better.</small>
            </div>
        </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Left Column: Profile Card -->
            <div class="col-lg-4">
                <div class="account-card">
                    <div class="avatar-wrapper" id="avatarWrapper">
                        <?php if (!empty($currentUser['avatar'])): ?>
                            <img src="<?= Sanitizer::image($currentUser['avatar']) ?>" alt="Avatar" class="avatar-img" id="avatarImg">
                        <?php else: ?>
                            <div class="avatar-placeholder" id="avatarPlaceholder">
                                <?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                        <label class="avatar-edit" title="Change Photo">
                            <i class="bi bi-camera-fill"></i>
                            <input type="file" id="avatarInput" accept="image/*">
                        </label>
                    </div>

                    <div class="profile-info">
                        <h6 id="displayName"><?= Sanitizer::clean($currentUser['name']) ?></h6>
                        <small id="displayPhone"><?= Sanitizer::clean($currentUser['phone']) ?></small>
                        <br>
                        <?php if ($isIncomplete): ?>
                            <span class="profile-badge badge-incomplete mt-2">Incomplete Profile</span>
                        <?php else: ?>
                            <span class="profile-badge badge-complete mt-2">Complete Profile</span>
                        <?php endif; ?>
                    </div>

                    <div class="profile-stats">
                        <div class="profile-stat">
                            <div class="stat-val"><?= count($orders) ?></div>
                            <div class="stat-lbl">Orders</div>
                        </div>
                        <div class="profile-stat">
                            <div class="stat-val"><?= date('M Y', strtotime($currentUser['created_at'])) ?></div>
                            <div class="stat-lbl">Joined</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Edit Forms -->
            <div class="col-lg-8">
                <!-- Tabs -->
                <ul class="nav nav-pills nav-pills-custom" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-edit" type="button">
                            <i class="bi bi-pencil-square me-1"></i> Edit Profile
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-password" type="button">
                            <i class="bi bi-lock me-1"></i> Change Password
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-orders" type="button">
                            <i class="bi bi-bag me-1"></i> Orders
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Edit Profile Tab -->
                    <div class="tab-pane fade show active" id="tab-edit">
                        <div class="account-card">
                            <h5><i class="bi bi-person-lines-fill me-2"></i>Personal Information</h5>
                            <form id="profileForm" class="edit-form" novalidate>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" value="<?= Sanitizer::clean($currentUser['name']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Phone <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" name="phone" value="<?= Sanitizer::clean($currentUser['phone']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" value="<?= Sanitizer::clean($currentUser['email'] ?? '') ?>" placeholder="your@email.com">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">City</label>
                                        <input type="text" class="form-control" name="city" value="<?= Sanitizer::clean($currentUser['city'] ?? '') ?>" placeholder="e.g. Dhaka">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Address</label>
                                        <textarea class="form-control" name="address" rows="2" placeholder="Your full address"><?= Sanitizer::clean($currentUser['address'] ?? '') ?></textarea>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn-save" id="btnSaveProfile">
                                        <span class="btn-text"><i class="bi bi-check-lg me-1"></i>Save Changes</span>
                                        <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-1"></span>Saving...</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Change Password Tab -->
                    <div class="tab-pane fade" id="tab-password">
                        <div class="account-card password-section">
                            <h5><i class="bi bi-shield-lock me-2"></i>Change Password</h5>
                            <form id="passwordForm" novalidate>
                                <div class="mb-3">
                                    <label class="form-label">Current Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="current_password" required placeholder="Enter current password">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">New Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="new_password" required placeholder="Min 6 characters">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="confirm_password" required placeholder="Re-enter new password">
                                </div>
                                <button type="submit" class="btn-save" id="btnChangePass">
                                    <span class="btn-text"><i class="bi bi-check-lg me-1"></i>Change Password</span>
                                    <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-1"></span>Changing...</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Orders Tab -->
                    <div class="tab-pane fade" id="tab-orders">
                        <div class="account-card">
                            <h5><i class="bi bi-bag me-2"></i>Recent Orders</h5>
                            <?php if (empty($orders)): ?>
                                <p class="text-muted text-center py-4">No orders yet. <a href="<?= APP_URL ?>/shop">Start shopping!</a></p>
                            <?php else: ?>
                                <div style="overflow-x:auto;">
                                    <table class="order-table">
                                        <thead>
                                            <tr>
                                                <th>Order #</th>
                                                <th>Date</th>
                                                <th>Total</th>
                                                <th>Payment</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($orders as $o): ?>
                                                <tr>
                                                    <td><strong><?= Sanitizer::clean($o['order_number']) ?></strong></td>
                                                    <td><?= date('M d, Y', strtotime($o['created_at'])) ?></td>
                                                    <td><?= Sanitizer::banglaPrice($o['total_amount']) ?></td>
                                                    <td><span class="status-badge status-<?= $o['payment_status'] ?>"><?= ucfirst($o['payment_status']) ?></span></td>
                                                    <td><span class="status-badge status-<?= $o['order_status'] ?>"><?= ucfirst($o['order_status']) ?></span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const profileToast = document.getElementById('profileToast');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    function showToast(msg, type) {
        profileToast.textContent = msg;
        profileToast.className = 'profile-toast ' + type + ' show';
        setTimeout(function () { profileToast.className = 'profile-toast'; }, 3000);
    }

    function setLoading(btn, loading) {
        if (loading) {
            btn.disabled = true;
            btn.querySelector('.btn-text').classList.add('d-none');
            btn.querySelector('.btn-loading').classList.remove('d-none');
        } else {
            btn.disabled = false;
            btn.querySelector('.btn-text').classList.remove('d-none');
            btn.querySelector('.btn-loading').classList.add('d-none');
        }
    }

    /* ── Profile Update ── */
    document.getElementById('profileForm').addEventListener('submit', function (e) {
        e.preventDefault();
        var btn = document.getElementById('btnSaveProfile');
        setLoading(btn, true);

        var formData = new FormData(this);

        fetch('<?= APP_URL ?>/account/update-profile', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                showToast(data.message, 'success');
                var name = formData.get('name');
                if (name) document.getElementById('displayName').textContent = name;
                var phone = formData.get('phone');
                if (phone) document.getElementById('displayPhone').textContent = phone;
            } else {
                showToast(data.message || 'Update failed', 'error');
            }
            setLoading(btn, false);
        })
        .catch(function () {
            showToast('Network error. Please try again.', 'error');
            setLoading(btn, false);
        });
    });

    /* ── Password Change ── */
    document.getElementById('passwordForm').addEventListener('submit', function (e) {
        e.preventDefault();
        var btn = document.getElementById('btnChangePass');
        var newPass = this.querySelector('input[name="new_password"]').value;
        var confirmPass = this.querySelector('input[name="confirm_password"]').value;

        if (newPass !== confirmPass) {
            showToast('Passwords do not match', 'error');
            return;
        }
        if (newPass.length < 6) {
            showToast('Password must be at least 6 characters', 'error');
            return;
        }

        setLoading(btn, true);

        var formData = new FormData(this);

        fetch('<?= APP_URL ?>/account/change-password', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                showToast(data.message, 'success');
                document.getElementById('passwordForm').reset();
            } else {
                showToast(data.message || 'Failed', 'error');
            }
            setLoading(btn, false);
        })
        .catch(function () {
            showToast('Network error. Please try again.', 'error');
            setLoading(btn, false);
        });
    });

    /* ── Avatar Upload ── */
    document.getElementById('avatarInput').addEventListener('change', function () {
        var file = this.files[0];
        if (!file) return;

        if (file.size > 2 * 1024 * 1024) {
            showToast('Image must be under 2MB', 'error');
            return;
        }

        var formData = new FormData();
        formData.append('avatar', file);

        fetch('<?= APP_URL ?>/account/upload-avatar', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                showToast(data.message, 'success');
                var wrapper = document.getElementById('avatarWrapper');
                var existing = wrapper.querySelector('.avatar-img, .avatar-placeholder');
                if (existing) existing.remove();
                var img = document.createElement('img');
                img.src = data.avatar;
                img.alt = 'Avatar';
                img.className = 'avatar-img';
                img.id = 'avatarImg';
                wrapper.insertBefore(img, wrapper.firstChild);
            } else {
                showToast(data.message || 'Upload failed', 'error');
            }
        })
        .catch(function () {
            showToast('Network error. Please try again.', 'error');
        });
    });
});
</script>
