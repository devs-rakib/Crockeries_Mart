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

    .auth-tabs {
        display: flex; border-bottom: 2px solid var(--cm-gray-200); margin-bottom: 24px; gap: 0;
    }
    .auth-tab {
        flex: 1; padding: 10px; text-align: center; font-size: 14px; font-weight: 600;
        color: var(--cm-gray-500); cursor: pointer; border-bottom: 2px solid transparent;
        margin-bottom: -2px; transition: all var(--cm-transition); background: none; border-top: none;
        border-left: none; border-right: none;
    }
    .auth-tab:hover { color: var(--cm-dark); }
    .auth-tab.active { color: var(--cm-primary); border-bottom-color: var(--cm-primary); }
    .tab-panel { display: none; }
    .tab-panel.active { display: block; }

    .otp-actions { display: flex; gap: 10px; }
    .otp-actions .form-control { flex: 1; }
    .btn-send-otp {
        padding: 11px 20px; border: 2px solid var(--cm-primary); border-radius: 8px;
        background: transparent; color: var(--cm-primary); font-size: 14px; font-weight: 600;
        cursor: pointer; transition: all var(--cm-transition); white-space: nowrap;
    }
    .btn-send-otp:hover { background: #fff0f0; }
    .btn-send-otp:disabled { opacity: .6; cursor: not-allowed; }
    .otp-hint { font-size: 12px; color: var(--cm-gray-500); margin-top: 6px; }
    .otp-dev-note {
        background: #fff3cd; color: #856404; border: 1px solid #ffc107;
        border-radius: 8px; padding: 10px 14px; font-size: 12px; margin-bottom: 16px;
        display: none;
    }
    .otp-dev-note.show { display: block; }
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

        <!-- Tabs -->
        <div class="auth-tabs">
            <button type="button" class="auth-tab active" data-tab="password">
                <i class="bi bi-lock me-1"></i> Password
            </button>
            <button type="button" class="auth-tab" data-tab="otp">
                <i class="bi bi-phone me-1"></i> OTP
            </button>
        </div>

        <!-- Password Login Panel -->
        <div id="tab-password" class="tab-panel active">
            <form id="loginForm" novalidate>
                <?= CSRF::field() ?>

                <div class="mb-3">
                    <label class="form-label">Email or Phone</label>
                    <input type="text" class="form-control" name="email" required placeholder="your@email.com or 01XXXXXXXXX" autocomplete="username">
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" name="password" id="loginPassword" placeholder="Enter your password" autocomplete="current-password">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <small id="phoneHint" class="text-muted mt-1" style="display:none; font-size:12px;">
                        <i class="bi bi-info-circle"></i> Phone login: enter any password
                    </small>
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
        </div>

        <!-- OTP Login Panel -->
        <div id="tab-otp" class="tab-panel">
            <div id="otpDevNote" class="otp-dev-note">
                <i class="bi bi-info-circle me-1"></i>
                <strong>Dev Mode:</strong> Your OTP will be shown below after sending.
            </div>

            <form id="otpSendForm" novalidate>
                <?= CSRF::field() ?>

                <div class="mb-3">
                    <label class="form-label">Phone Number</label>
                    <div class="otp-actions">
                        <input type="tel" class="form-control" name="phone" id="otpPhone" required placeholder="01XXXXXXXXX">
                        <button type="submit" class="btn-send-otp" id="btnSendOtp">
                            <span class="btn-text">Send OTP</span>
                            <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm"></span></span>
                        </button>
                    </div>
                    <div class="otp-hint">We'll send a 6-digit code to your phone</div>
                </div>
            </form>

            <form id="otpVerifyForm" novalidate style="display:none;">
                <?= CSRF::field() ?>
                <input type="hidden" name="phone" id="otpVerifyPhone">

                <div id="otpShowCode" class="otp-dev-note show" style="display:none;"></div>

                <div class="mb-3">
                    <label class="form-label">Enter OTP Code</label>
                    <input type="text" class="form-control" name="otp" id="otpCode" required placeholder="Enter 6-digit code" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" autocomplete="one-time-code">
                    <div class="otp-hint">Code expires in 5 minutes</div>
                </div>

                <button type="submit" class="btn-login" id="btnVerifyOtp">
                    <span class="btn-text">Verify & Login</span>
                    <span class="btn-loading d-none"><span class="spinner-border spinner-border-sm me-2"></span>Verifying...</span>
                </button>
            </form>
        </div>

        <div class="auth-footer">
            Don't have an account? <a href="<?= APP_URL ?>/register">Register</a>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const alertBox = document.getElementById('loginAlert');

    function showAlert(msg, type) {
        alertBox.textContent = msg;
        alertBox.className = 'auth-alert show alert-' + type;
    }

    /* ── Tab Switching ── */
    document.querySelectorAll('.auth-tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
            document.querySelectorAll('.auth-tab').forEach(function (t) { t.classList.remove('active'); });
            document.querySelectorAll('.tab-panel').forEach(function (p) { p.classList.remove('active'); });
            this.classList.add('active');
            document.getElementById('tab-' + this.dataset.tab).classList.add('active');
            alertBox.className = 'auth-alert';
        });
    });

    /* ── Password Login ── */
    const loginForm    = document.getElementById('loginForm');
    const btnLogin     = document.getElementById('btnLogin');
    const toggleBtn    = document.getElementById('togglePassword');
    const passInput    = document.getElementById('loginPassword');
    const phoneHint    = document.getElementById('phoneHint');
    const idInput      = document.querySelector('input[name="email"]');

    function isPhone(val) {
        return /^\d{6,15}$/.test(val.replace(/\s/g, ''));
    }

    idInput.addEventListener('input', function () {
        if (isPhone(this.value.trim())) {
            passInput.removeAttribute('required');
            passInput.placeholder = 'Enter any password';
            phoneHint.style.display = 'block';
        } else {
            passInput.setAttribute('required', '');
            passInput.placeholder = 'Enter your password';
            phoneHint.style.display = 'none';
        }
    });

    if (toggleBtn && passInput) {
        toggleBtn.addEventListener('click', function () {
            const isPassword = passInput.type === 'password';
            passInput.type = isPassword ? 'text' : 'password';
            this.innerHTML = isPassword ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
        });
    }

    loginForm.addEventListener('submit', function (e) {
        e.preventDefault();
        alertBox.className = 'auth-alert';

        var formData = new FormData(loginForm);

        btnLogin.disabled = true;
        btnLogin.querySelector('.btn-text').classList.add('d-none');
        btnLogin.querySelector('.btn-loading').classList.remove('d-none');

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
                btnLogin.disabled = false;
                btnLogin.querySelector('.btn-text').classList.remove('d-none');
                btnLogin.querySelector('.btn-loading').classList.add('d-none');
            }
        })
        .catch(function () {
            showAlert('Network error. Please try again.', 'danger');
            btnLogin.disabled = false;
            btnLogin.querySelector('.btn-text').classList.remove('d-none');
            btnLogin.querySelector('.btn-loading').classList.add('d-none');
        });
    });

    /* ── OTP: Send Code ── */
    const otpSendForm   = document.getElementById('otpSendForm');
    const btnSendOtp    = document.getElementById('btnSendOtp');
    const otpVerifyForm = document.getElementById('otpVerifyForm');
    const otpShowCode   = document.getElementById('otpShowCode');
    const otpDevNote    = document.getElementById('otpDevNote');

    otpSendForm.addEventListener('submit', function (e) {
        e.preventDefault();
        alertBox.className = 'auth-alert';

        var formData = new FormData(otpSendForm);

        btnSendOtp.disabled = true;
        btnSendOtp.querySelector('.btn-text').classList.add('d-none');
        btnSendOtp.querySelector('.btn-loading').classList.remove('d-none');

        fetch('<?= APP_URL ?>/api/auth/send-otp', {
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
                document.getElementById('otpVerifyPhone').value = document.getElementById('otpPhone').value;
                otpSendForm.style.display = 'none';
                otpVerifyForm.style.display = 'block';

                if (data.otp) {
                    otpShowCode.innerHTML = '<strong>Dev OTP:</strong> ' + data.otp;
                    otpShowCode.style.display = 'block';
                    otpDevNote.classList.add('show');
                }
            } else {
                showAlert(data.message || 'Failed to send OTP', 'danger');
            }
            btnSendOtp.disabled = false;
            btnSendOtp.querySelector('.btn-text').classList.remove('d-none');
            btnSendOtp.querySelector('.btn-loading').classList.add('d-none');
        })
        .catch(function () {
            showAlert('Network error. Please try again.', 'danger');
            btnSendOtp.disabled = false;
            btnSendOtp.querySelector('.btn-text').classList.remove('d-none');
            btnSendOtp.querySelector('.btn-loading').classList.add('d-none');
        });
    });

    /* ── OTP: Verify ── */
    const btnVerifyOtp = document.getElementById('btnVerifyOtp');

    otpVerifyForm.addEventListener('submit', function (e) {
        e.preventDefault();
        alertBox.className = 'auth-alert';

        var formData = new FormData(otpVerifyForm);

        btnVerifyOtp.disabled = true;
        btnVerifyOtp.querySelector('.btn-text').classList.add('d-none');
        btnVerifyOtp.querySelector('.btn-loading').classList.remove('d-none');

        fetch('<?= APP_URL ?>/api/auth/verify-otp', {
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
                showAlert(data.message || 'Invalid OTP', 'danger');
                btnVerifyOtp.disabled = false;
                btnVerifyOtp.querySelector('.btn-text').classList.remove('d-none');
                btnVerifyOtp.querySelector('.btn-loading').classList.add('d-none');
            }
        })
        .catch(function () {
            showAlert('Network error. Please try again.', 'danger');
            btnVerifyOtp.disabled = false;
            btnVerifyOtp.querySelector('.btn-text').classList.remove('d-none');
            btnVerifyOtp.querySelector('.btn-loading').classList.add('d-none');
        });
    });
});
</script>
