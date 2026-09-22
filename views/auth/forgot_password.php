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
            <i class="bi bi-key"></i>
        </div>

        <h4>Forgot Password?</h4>
        <p class="auth-subtitle">Enter your email address and we'll send you a link to reset your password.</p>

        <div id="resetAlert" class="auth-alert"></div>

        <form id="forgotForm" novalidate>
            <?= CSRF::field() ?>

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control" name="email" required placeholder="your@email.com" autocomplete="email">
            </div>

            <button type="submit" class="btn-submit" id="btnReset">
                <span class="btn-text">Send Reset Link</span>
                <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-2"></span>Sending...</span>
            </button>
        </form>

        <div class="auth-footer">
            <a href="<?= APP_URL ?>/login"><i class="bi bi-arrow-left me-1"></i> Back to Login</a>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const alertBox = document.getElementById('resetAlert');
    const form     = document.getElementById('forgotForm');
    const btn      = document.getElementById('btnReset');

    function showAlert(msg, type) {
        alertBox.textContent = msg;
        alertBox.className = 'auth-alert show alert-' + type;
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        alertBox.className = 'auth-alert';

        var formData = new FormData(form);

        btn.disabled = true;
        btn.querySelector('.btn-text').classList.add('d-none');
        btn.querySelector('.btn-loading').classList.remove('d-none');

        fetch('<?= APP_URL ?>/forgot-password', {
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
