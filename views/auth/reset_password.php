<?php
use App\Helpers\CSRF;
?>

<style>
    .auth-page {
        min-height: 80vh; display: flex; align-items: center; justify-content: center;
        background: var(--cm-gray-100); padding: 40px 16px;
    }
    .auth-card {
        background: var(--cm-white); border-radius: 16px; box-shadow: var(--cm-shadow-lg);
        padding: 40px 36px; width: 100%; max-width: 450px;
    }
    .auth-logo { text-align: center; margin-bottom: 28px; }
    .auth-logo a { text-decoration: none; display: inline-flex; align-items: center; gap: 10px; }
    .auth-logo i { font-size: 36px; color: var(--cm-primary); }
    .auth-logo span { font-size: 22px; font-weight: 800; color: var(--cm-dark); }
    .auth-logo span em { font-style: normal; color: var(--cm-primary); }
    .auth-card h4 { text-align: center; font-weight: 700; color: var(--cm-dark); margin-bottom: 6px; }
    .auth-card .auth-subtitle { text-align: center; color: var(--cm-gray-500); font-size: 14px; margin-bottom: 28px; }
    .auth-card .form-label { font-weight: 600; color: var(--cm-dark); font-size: 13px; }
    .auth-card .form-control {
        border: 2px solid var(--cm-gray-200); border-radius: 8px; padding: 11px 14px; font-size: 14px;
        transition: border-color var(--cm-transition);
    }
    .auth-card .form-control:focus { border-color: var(--cm-primary); box-shadow: 0 0 0 3px rgba(255,56,56,.1); }
    .btn-submit {
        width: 100%; padding: 12px; border: none; border-radius: 8px;
        background: var(--cm-primary); color: #fff; font-size: 15px; font-weight: 700;
        cursor: pointer; transition: all var(--cm-transition);
    }
    .btn-submit:hover { background: var(--cm-primary-dark); }
    .btn-submit:disabled { opacity: .6; cursor: not-allowed; }
    .btn-submit .spinner-border { width: 18px; height: 18px; border-width: 2px; }
    .auth-footer { text-align: center; margin-top: 24px; font-size: 14px; color: var(--cm-gray-500); }
    .auth-footer a { color: var(--cm-primary); font-weight: 600; }
    .auth-footer a:hover { text-decoration: underline; }
    .auth-alert { display: none; padding: 10px 14px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; font-weight: 500; }
    .auth-alert.show { display: block; }
    .auth-alert.alert-danger { background: #fff0f0; color: #dc3545; border: 1px solid #f5c6cb; }
    .auth-alert.alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .auth-icon { text-align: center; margin-bottom: 20px; }
    .auth-icon i { font-size: 48px; color: var(--cm-primary); opacity: .8; }
    .password-strength { height: 4px; border-radius: 2px; margin-top: 6px; transition: all .3s; }
    .password-strength.weak { background: #dc3545; width: 33%; }
    .password-strength.medium { background: #ffc107; width: 66%; }
    .password-strength.strong { background: #28a745; width: 100%; }
</style>

<div class="auth-page">
    <div class="auth-card">

        <div class="auth-logo">
            <a href="<?= APP_URL ?>">
                <i class="bi bi-box-seam"></i>
                <span><em>Crockeries</em>Mart</span>
            </a>
        </div>

        <div class="auth-icon">
            <i class="bi bi-shield-lock"></i>
        </div>

        <h4>Reset Password</h4>
        <p class="auth-subtitle">Enter your new password below.</p>

        <div id="resetAlert" class="auth-alert"></div>

        <form id="resetForm" novalidate>
            <?= CSRF::field() ?>
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <div class="mb-3">
                <label class="form-label">New Password</label>
                <input type="password" class="form-control" name="password" id="newPassword" required placeholder="At least 6 characters" autocomplete="new-password" minlength="6">
                <div class="password-strength" id="passwordStrength"></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" class="form-control" name="password_confirmation" required placeholder="Re-enter your password" autocomplete="new-password" minlength="6">
            </div>

            <button type="submit" class="btn-submit" id="btnReset">
                <span class="btn-text">Reset Password</span>
                <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-2"></span>Resetting...</span>
            </button>
        </form>

        <div class="auth-footer">
            <a href="<?= APP_URL ?>/login"><i class="bi bi-arrow-left me-1"></i> Back to Login</a>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var alertBox   = document.getElementById('resetAlert');
    var form       = document.getElementById('resetForm');
    var btn        = document.getElementById('btnReset');
    var passInput  = document.getElementById('newPassword');
    var strengthEl = document.getElementById('passwordStrength');

    function showAlert(msg, type) {
        alertBox.textContent = msg;
        alertBox.className = 'auth-alert show alert-' + type;
    }

    passInput.addEventListener('input', function () {
        var val = this.value;
        var score = 0;
        if (val.length >= 6) score++;
        if (val.length >= 10) score++;
        if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        strengthEl.className = 'password-strength';
        if (val.length > 0) {
            if (score <= 2) strengthEl.classList.add('weak');
            else if (score <= 3) strengthEl.classList.add('medium');
            else strengthEl.classList.add('strong');
        }
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        alertBox.className = 'auth-alert';

        var formData = new FormData(form);

        btn.disabled = true;
        btn.querySelector('.btn-text').classList.add('d-none');
        btn.querySelector('.btn-loading').classList.remove('d-none');

        fetch('<?= APP_URL ?>/reset-password', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            showAlert(data.message, data.success ? 'success' : 'danger');
            if (data.success && data.redirect) {
                setTimeout(function () {
                    window.location.href = data.redirect;
                }, 1500);
            }
            btn.disabled = false;
            btn.querySelector('.btn-text').classList.remove('d-none');
            btn.querySelector('.btn-loading').classList.add('d-none');
        })
        .catch(function () {
            showAlert('Network error. Please try again.', 'danger');
            btn.disabled = false;
            btn.querySelector('.btn-text').classList.remove('d-none');
            btn.querySelector('.btn-loading').classList.add('d-none');
        });
    });
});
</script>
