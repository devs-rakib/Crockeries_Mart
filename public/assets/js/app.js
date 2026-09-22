/**
 * CrockeriesMart - Main Application JavaScript
 */

'use strict';

const App = (() => {
    let toastTimeout = null;

    const getCsrfToken = () => {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    };

    const setupAjaxDefaults = () => {
        const origFetch = window.fetch;
        window.fetch = (url, opts = {}) => {
            opts.headers = opts.headers || {};
            if (opts.method && opts.method.toUpperCase() !== 'GET') {
                if (opts.headers instanceof Headers) {
                    opts.headers.set('X-CSRF-TOKEN', getCsrfToken());
                    opts.headers.set('X-Requested-With', 'XMLHttpRequest');
                } else {
                    opts.headers['X-CSRF-TOKEN'] = getCsrfToken();
                    opts.headers['X-Requested-With'] = 'XMLHttpRequest';
                }
            }
            return origFetch(url, opts);
        };
    };

    const toast = (message, type = 'success', duration = 4000) => {
        const existing = document.querySelector('.app-toast');
        if (existing) existing.remove();
        if (toastTimeout) clearTimeout(toastTimeout);

        const el = document.createElement('div');
        el.className = `app-toast app-toast--${type}`;
        el.innerHTML = `
            <div class="app-toast__icon">
                <i class="bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'}"></i>
            </div>
            <span class="app-toast__msg">${escapeHtml(message)}</span>
            <button class="app-toast__close" aria-label="Close">&times;</button>
        `;
        el.querySelector('.app-toast__close').addEventListener('click', () => el.remove());

        document.body.appendChild(el);
        requestAnimationFrame(() => el.classList.add('app-toast--visible'));

        toastTimeout = setTimeout(() => {
            el.classList.remove('app-toast--visible');
            el.addEventListener('transitionend', () => el.remove(), { once: true });
        }, duration);
    };

    const _escaper = document.createElement('div');
    const escapeHtml = (str) => {
        _escaper.textContent = str;
        return _escaper.innerHTML;
    };

    const initTooltips = () => {
        if (typeof bootstrap === 'undefined') return;
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => {
            if (!el._tooltip) el._tooltip = new bootstrap.Tooltip(el);
        });
    };

    const initPopovers = () => {
        if (typeof bootstrap === 'undefined') return;
        document.querySelectorAll('[data-bs-toggle="popover"]').forEach((el) => {
            if (!el._popover) el._popover = new bootstrap.Popover(el);
        });
    };

    const initNewsletter = () => {
        const form = document.getElementById('newsletterForm');
        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const emailInput = form.querySelector('input[type="email"]');
            const btn = form.querySelector('button[type="submit"]');
            const email = emailInput.value.trim();

            if (!email || !isValidEmail(email)) {
                toast('Please enter a valid email address.', 'error');
                return;
            }

            btn.disabled = true;
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            try {
                const res = await fetch('ajax_handler.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ action: 'newsletter_subscribe', email })
                });
                const data = await res.json();

                if (data.success) {
                    toast(data.message || 'Subscribed successfully!', 'success');
                    emailInput.value = '';
                } else {
                    toast(data.message || 'Subscription failed.', 'error');
                }
            } catch {
                toast('Network error. Please try again.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
    };

    const initSmoothScroll = () => {
        document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
            anchor.addEventListener('click', (e) => {
                const id = anchor.getAttribute('href');
                if (!id || id === '#') return;
                const target = document.querySelector(id);
                if (!target) return;
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    };

    const initBackToTop = () => {
        const btn = document.getElementById('backToTop');
        if (!btn) return;

        const toggle = () => {
            btn.classList.toggle('show', window.scrollY > 300);
        };

        window.addEventListener('scroll', toggle, { passive: true });
        toggle();

        btn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    };

    const initMobileMenu = () => {
        const toggle = document.querySelector('.navbar-toggler');
        const collapse = document.querySelector('.navbar-collapse');
        if (!toggle || !collapse) return;

        document.querySelectorAll('.navbar-nav .dropdown-toggle').forEach((dd) => {
            dd.addEventListener('click', (e) => {
                if (window.innerWidth < 992) {
                    e.preventDefault();
                    e.stopPropagation();
                    const menu = dd.nextElementSibling;
                    if (menu) {
                        const isOpen = menu.classList.contains('show');
                        document.querySelectorAll('.navbar-nav .dropdown-menu.show').forEach((m) => {
                            m.classList.remove('show');
                        });
                        if (!isOpen) menu.classList.add('show');
                    }
                }
            });
        });
    };

    const initFormValidation = () => {
        document.querySelectorAll('form[data-validate]').forEach((form) => {
            form.addEventListener('submit', (e) => {
                let valid = true;

                form.querySelectorAll('[required]').forEach((input) => {
                    input.classList.remove('is-invalid');
                    if (!input.value.trim()) {
                        input.classList.add('is-invalid');
                        valid = false;
                    }
                    if (input.type === 'email' && input.value && !isValidEmail(input.value)) {
                        input.classList.add('is-invalid');
                        valid = false;
                    }
                });

                form.querySelectorAll('[data-match]').forEach((input) => {
                    const target = document.getElementById(input.dataset.match);
                    if (target && input.value !== target.value) {
                        input.classList.add('is-invalid');
                        valid = false;
                    }
                });

                if (!valid) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });
    };

    const isValidEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

    const initLoadingOverlay = () => {
        window.showLoader = () => {
            let overlay = document.getElementById('appLoader');
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.id = 'appLoader';
                overlay.className = 'app-loader';
                overlay.innerHTML = '<div class="app-loader__spinner"></div>';
                document.body.appendChild(overlay);
            }
            requestAnimationFrame(() => overlay.classList.add('app-loader--active'));
        };

        window.hideLoader = () => {
            const overlay = document.getElementById('appLoader');
            if (overlay) {
                overlay.classList.remove('app-loader--active');
            }
        };
    };

    const initCarousels = () => {
        if (typeof bootstrap === 'undefined') return;

        document.querySelectorAll('.product-carousel').forEach((el) => {
            if (el._carousel) return;
            try {
                el._carousel = new bootstrap.Carousel(el, {
                    interval: 5000,
                    wrap: true,
                    touch: true
                });
            } catch { /* silent */ }
        });
    };

    const init = () => {
        setupAjaxDefaults();
        initTooltips();
        initPopovers();
        initNewsletter();
        initSmoothScroll();
        initBackToTop();
        initMobileMenu();
        initFormValidation();
        initLoadingOverlay();
        initCarousels();
    };

    return { init, toast, escapeHtml };
})();

document.addEventListener('DOMContentLoaded', App.init);
