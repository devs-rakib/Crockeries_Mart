<?php
/**
 * Login Page - CrokersesMart
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
    .input-group .btn-outline-secondary {
        border: 2px solid var(--cm-gray-200); border-left: none; border-radius: 0 8px 8px 0;
        background: var(--cm-gray-100); color: var(--cm-gray-500);
    }
    .input-group .btn-outline-secondary:hover { color: var(--cm-primary); }
    .input-group .form-control { border-right: none; }
    .remember-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; font-size: 13px; }
    .remember-row label { color: var(--cm-gray-500); display: flex; align-items: center; gap: 6px; cursor: pointer; }
    .remember-row label input { accent-color: var(--cm-primary); }
    .remember-row a { color: var(--cm-primary); font-weight: 600; }
    .remember-row a:hover { text-decoration: underline; }
    .btn-login {
        width: 100%; padding: 12px; border: none; border-radius: 8px;
        background: var(--cm-primary); color: #fff; font-size: 15px; font-weight: 700;
        cursor: pointer; transition: all var(--cm-transition);
    }
    .btn-login:hover { background: var(--cm-primary-dark); }
    .btn-login:disabled { opacity: .6; cursor: not-allowed; }
    .btn-login .spinner-border { width: 18px; height: 18px; border-width: 2px; }
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

        <h4>Welcome Back</h4>
        <p class="auth-subtitle">Login to your account to continue</p>

        <div id="loginAlert" class="auth-alert"></div>

        <form id="loginForm" novalidate>
            <?= CSRF::field() ?>

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control" name="email" required placeholder="your@email.com" autocomplete="email">
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" name="password" id="loginPassword" required placeholder="Enter your password" autocomplete="current-password">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div class="remember-row">
                <label>
                    <input type="checkbox" name="remember" value="1"> Remember Me
                </label>
                <a href="<?= APP_URL ?>/forgot-password">Forgot Password?</a>
            </div>

            <button type="submit" class="btn-login" id="btnLogin">
                <span class="btn-text">Login</span>
                <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-2"></span>Logging in...</span>
            </button>
        </form>

        <div class="auth-footer">
            Don't have an account? <a href="<?= APP_URL ?>/register">Register</a>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form    = document.getElementById('loginForm');
    const btn     = document.getElementById('btnLogin');
    const alertBox = document.getElementById('loginAlert');

    // Toggle password visibility
    const toggleBtn = document.getElementById('togglePassword');
    const passInput = document.getElementById('loginPassword');
    if (toggleBtn && passInput) {
        toggleBtn.addEventListener('click', function () {
            const isPassword = passInput.type === 'password';
            passInput.type = isPassword ? 'text' : 'password';
            this.innerHTML = isPassword ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
        });
    }

    function showAlert(msg, type) {
        alertBox.textContent = msg;
        alertBox.className = 'auth-alert show alert-' + type;
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        alertBox.className = 'auth-alert';

        const formData = new FormData(form);

        btn.disabled = true;
        btn.querySelector('.btn-text').classList.add('d-none');
        btn.querySelector('.btn-loading').classList.remove('d-none');

        fetch('<?= APP_URL ?>/login', {
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
                showAlert(data.message || 'Invalid credentials', 'danger');
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
