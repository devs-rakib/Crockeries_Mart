<?php
/**
 * Register Page - CrokersesMart
 */

use App\Helpers\CSRF;
?>

<style>
    .auth-page {
        min-height: 80vh; display: flex; align-items: center; justify-content: center;
        background: var(--cm-gray-100); padding: 40px 16px;
    }
    .auth-card {
        background: var(--cm-white); border-radius: 16px; box-shadow: var(--cm-shadow-lg);
        padding: 40px 36px; width: 100%; max-width: 500px;
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
    .input-group .btn-outline-secondary {
        border: 2px solid var(--cm-gray-200); border-left: none; border-radius: 0 8px 8px 0;
        background: var(--cm-gray-100); color: var(--cm-gray-500);
    }
    .input-group .btn-outline-secondary:hover { color: var(--cm-primary); }
    .input-group .form-control { border-right: none; }
    .btn-register {
        width: 100%; padding: 12px; border: none; border-radius: 8px;
        background: var(--cm-primary); color: #fff; font-size: 15px; font-weight: 700;
        cursor: pointer; transition: all var(--cm-transition); margin-top: 8px;
    }
    .btn-register:hover { background: var(--cm-primary-dark); }
    .btn-register:disabled { opacity: .6; cursor: not-allowed; }
    .btn-register .spinner-border { width: 18px; height: 18px; border-width: 2px; }
    .auth-footer { text-align: center; margin-top: 24px; font-size: 14px; color: var(--cm-gray-500); }
    .auth-footer a { color: var(--cm-primary); font-weight: 600; }
    .auth-footer a:hover { text-decoration: underline; }
    .auth-alert { display: none; padding: 10px 14px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; font-weight: 500; }
    .auth-alert.show { display: block; }
    .auth-alert.alert-danger { background: #fff0f0; color: #dc3545; border: 1px solid #f5c6cb; }
    .auth-alert.alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
</style>

<div class="auth-page">
    <div class="auth-card">

        <div class="auth-logo">
            <a href="<?= APP_URL ?>">
                <i class="bi bi-box-seam"></i>
                <span><em>Crockeries</em>Mart</span>
            </a>
        </div>

        <h4>Create Account</h4>
        <p class="auth-subtitle">Join us and explore premium crockery</p>

        <div id="registerAlert" class="auth-alert"></div>

        <form id="registerForm" novalidate>
            <?= CSRF::field() ?>

            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" name="name" required placeholder="Enter your full name" autocomplete="name">
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control" name="email" required placeholder="your@email.com" autocomplete="email">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" name="phone" required placeholder="01XXXXXXXXX" autocomplete="tel">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" name="password" id="regPassword" required placeholder="Min 6 characters" autocomplete="new-password">
                        <button class="btn btn-outline-secondary" type="button" id="toggleRegPass" tabindex="-1">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" name="password_confirmation" id="regConfirm" required placeholder="Re-enter password" autocomplete="new-password">
                        <button class="btn btn-outline-secondary" type="button" id="toggleRegConfirm" tabindex="-1">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-register" id="btnRegister">
                <span class="btn-text">Register</span>
                <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-2"></span>Creating account...</span>
            </button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="<?= APP_URL ?>/login">Login</a>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form     = document.getElementById('registerForm');
    const btn      = document.getElementById('btnRegister');
    const alertBox = document.getElementById('registerAlert');

    // Toggle password visibility
    function setupToggle(btnId, inputId) {
        const toggleBtn = document.getElementById(btnId);
        const input     = document.getElementById(inputId);
        if (toggleBtn && input) {
            toggleBtn.addEventListener('click', function () {
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                this.innerHTML = isPassword ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
            });
        }
    }
    setupToggle('toggleRegPass', 'regPassword');
    setupToggle('toggleRegConfirm', 'regConfirm');

    function showAlert(msg, type) {
        alertBox.textContent = msg;
        alertBox.className = 'auth-alert show alert-' + type;
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        alertBox.className = 'auth-alert';

        const pass    = document.getElementById('regPassword').value;
        const confirm = document.getElementById('regConfirm').value;

        if (pass !== confirm) {
            showAlert('Passwords do not match', 'danger');
            return;
        }

        if (pass.length < 6) {
            showAlert('Password must be at least 6 characters', 'danger');
            return;
        }

        const formData = new FormData(form);

        btn.disabled = true;
        btn.querySelector('.btn-text').classList.add('d-none');
        btn.querySelector('.btn-loading').classList.remove('d-none');

        fetch('<?= APP_URL ?>/register', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                showAlert(data.message, 'success');
                setTimeout(function () {
                    window.location.href = data.redirect || '<?= APP_URL ?>';
                }, 800);
            } else {
                showAlert(data.message || 'Registration failed', 'danger');
                btn.disabled = false;
                btn.querySelector('.btn-text').classList.remove('d-none');
                btn.querySelector('.btn-loading').classList.add('d-none');
            }
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
