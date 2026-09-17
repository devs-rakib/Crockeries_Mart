/**
 * CrockeriesMart - AJAX Cart Engine
 * Zero page reloads. localStorage + PHP session sync.
 */

'use strict';

const Cart = (() => {
    const STORAGE_KEY = 'crockeries_cart';
    const AJAX_URL = 'ajax_handler.php';
    let items = [];
    let isLoading = false;

    // ── Persistence ──────────────────────────────────────────────

    const saveLocal = () => {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
        } catch { /* quota exceeded or private browsing */ }
    };

    const loadLocal = () => {
        try {
            const data = localStorage.getItem(STORAGE_KEY);
            return data ? JSON.parse(data) : [];
        } catch {
            return [];
        }
    };

    // ── Helpers ──────────────────────────────────────────────────

    const getCsrfToken = () => {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    };

    const formatPrice = (price) => {
        const num = parseFloat(price);
        return isNaN(num) ? '₦0.00' : '₦' + num.toLocaleString('en-NG', { minimumFractionDigits: 2 });
    };

    const escapeHtml = (str) => {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    };

    const toast = (msg, type = 'success') => {
        if (typeof App !== 'undefined' && App.toast) {
            App.toast(msg, type);
        }
    };

    const calcTotal = () => items.reduce((sum, i) => sum + (parseFloat(i.price) * parseInt(i.quantity)), 0);
    const calcCount = () => items.reduce((sum, i) => sum + parseInt(i.quantity), 0);

    // ── API Calls ────────────────────────────────────────────────

    const apiPost = async (action, payload = {}) => {
        const params = new URLSearchParams({ action, ...payload });
        try {
            const res = await fetch(AJAX_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: params
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return await res.json();
        } catch (err) {
            console.error(`Cart API error [${action}]:`, err);
            return { success: false, message: 'Network error. Please try again.' };
        }
    };

    // ── Core Operations ──────────────────────────────────────────

    const addToCart = async (productId, quantity = 1, variantId = null) => {
        if (isLoading) return;
        isLoading = true;

        try {
            const res = await apiPost('add_to_cart', {
                product_id: productId,
                quantity,
                variant_id: variantId || ''
            });

            if (res.success) {
                toast(res.message || 'Added to cart!', 'success');
                await loadCart();
            } else {
                toast(res.message || 'Failed to add item.', 'error');
            }
            return res;
        } finally {
            isLoading = false;
        }
    };

    const updateCartItem = async (key, quantity) => {
        quantity = parseInt(quantity);
        if (quantity < 1) return removeFromCart(key);
        if (isLoading) return;
        isLoading = true;

        try {
            const res = await apiPost('update_cart', { key, quantity });

            if (res.success) {
                const idx = items.findIndex((i) => String(i.key) === String(key));
                if (idx !== -1) items[idx].quantity = quantity;
                saveLocal();
                renderCartDrawer(items);
                updateCartBadge(calcCount());
            } else {
                toast(res.message || 'Update failed.', 'error');
            }
            return res;
        } finally {
            isLoading = false;
        }
    };

    const removeFromCart = async (key) => {
        if (isLoading) return;
        isLoading = true;

        try {
            const res = await apiPost('remove_from_cart', { key });

            if (res.success) {
                items = items.filter((i) => String(i.key) !== String(key));
                saveLocal();
                renderCartDrawer(items);
                updateCartBadge(calcCount());
                toast(res.message || 'Item removed.', 'success');
            } else {
                toast(res.message || 'Remove failed.', 'error');
            }
            return res;
        } finally {
            isLoading = false;
        }
    };

    const loadCart = async () => {
        const res = await apiPost('get_cart');

        if (res.success && Array.isArray(res.items)) {
            items = res.items;
        } else {
            items = loadLocal();
        }
        saveLocal();
        renderCartDrawer(items);
        updateCartBadge(calcCount());
        return items;
    };

    // ── Quick Buy ────────────────────────────────────────────────

    const quickBuyNow = async (productId, quantity = 1, variantId = null) => {
        if (isLoading) return;
        isLoading = true;

        try {
            const res = await apiPost('add_to_cart', {
                product_id: productId,
                quantity,
                variant_id: variantId || ''
            });

            if (res.success) {
                window.location.href = 'checkout.php';
            } else {
                toast(res.message || 'Failed. Please try again.', 'error');
            }
        } finally {
            isLoading = false;
        }
    };

    // ── Rendering ────────────────────────────────────────────────

    const renderCartDrawer = (cartItems) => {
        const drawer = document.getElementById('cartDrawer');
        if (!drawer) return;

        const body = drawer.querySelector('.cart-drawer__body');
        const footer = drawer.querySelector('.cart-drawer__footer');
        if (!body) return;

        if (!cartItems || cartItems.length === 0) {
            body.innerHTML = `
                <div class="cart-drawer__empty text-center py-5">
                    <i class="bi bi-bag fs-1 text-muted"></i>
                    <p class="mt-3 text-muted">Your cart is empty</p>
                    <a href="products.php" class="btn btn-outline-primary btn-sm mt-2">Start Shopping</a>
                </div>`;
            if (footer) footer.style.display = 'none';
            return;
        }

        body.innerHTML = cartItems.map((item) => `
            <div class="cart-drawer__item" data-key="${escapeHtml(String(item.key))}">
                <div class="cart-drawer__item-img">
                    <img src="${escapeHtml(item.image || 'assets/images/placeholder.jpg')}" alt="${escapeHtml(item.name)}" loading="lazy">
                </div>
                <div class="cart-drawer__item-details">
                    <h6 class="cart-drawer__item-name">${escapeHtml(item.name)}</h6>
                    ${item.variant ? `<small class="text-muted">${escapeHtml(item.variant)}</small>` : ''}
                    <div class="cart-drawer__item-price">${formatPrice(item.price)}</div>
                    <div class="cart-drawer__item-qty d-flex align-items-center mt-1">
                        <button class="btn btn-sm btn-outline-secondary cart-qty-dec" data-key="${escapeHtml(String(item.key))}" data-action="dec" aria-label="Decrease">−</button>
                        <input type="number" class="form-control form-control-sm mx-1 text-center cart-qty-input" data-key="${escapeHtml(String(item.key))}" value="${parseInt(item.quantity)}" min="1" max="99" style="width:50px">
                        <button class="btn btn-sm btn-outline-secondary cart-qty-inc" data-key="${escapeHtml(String(item.key))}" data-action="inc" aria-label="Increase">+</button>
                        <button class="btn btn-sm btn-link text-danger ms-2 cart-remove-btn" data-key="${escapeHtml(String(item.key))}" aria-label="Remove"><i class="bi bi-trash"></i></button>
                    </div>
                </div>
            </div>
        `).join('');

        if (footer) {
            footer.style.display = '';
            const totalEl = footer.querySelector('.cart-drawer__total');
            if (totalEl) totalEl.textContent = formatPrice(calcTotal());
        }
    };

    const updateCartBadge = (count) => {
        document.querySelectorAll('.cart-badge, .cart-count').forEach((el) => {
            el.textContent = count;
            el.style.display = count > 0 ? '' : 'none';
        });
    };

    // ── Event Delegation ─────────────────────────────────────────

    const bindEvents = () => {
        document.addEventListener('click', (e) => {
            const target = e.target.closest('[data-add-to-cart]');
            if (target) {
                e.preventDefault();
                const id = target.dataset.addToCart || target.dataset.productId;
                const qty = parseInt(target.dataset.quantity) || 1;
                const vid = target.dataset.variantId || null;
                if (id) addToCart(id, qty, vid);
                return;
            }

            const buyTarget = e.target.closest('[data-buy-now]');
            if (buyTarget) {
                e.preventDefault();
                const id = buyTarget.dataset.buyNow || buyTarget.dataset.productId;
                const qty = parseInt(buyTarget.dataset.quantity) || 1;
                const vid = buyTarget.dataset.variantId || null;
                if (id) quickBuyNow(id, qty, vid);
                return;
            }

            if (e.target.closest('.cart-qty-inc')) {
                const btn = e.target.closest('.cart-qty-inc');
                const key = btn.dataset.key;
                const input = document.querySelector(`.cart-qty-input[data-key="${key}"]`);
                const newQty = (input ? parseInt(input.value) : 1) + 1;
                updateCartItem(key, newQty);
                return;
            }

            if (e.target.closest('.cart-qty-dec')) {
                const btn = e.target.closest('.cart-qty-dec');
                const key = btn.dataset.key;
                const input = document.querySelector(`.cart-qty-input[data-key="${key}"]`);
                const newQty = (input ? parseInt(input.value) : 2) - 1;
                updateCartItem(key, newQty);
                return;
            }

            if (e.target.closest('.cart-remove-btn')) {
                const btn = e.target.closest('.cart-remove-btn');
                const key = btn.dataset.key;
                if (confirm('Remove this item from cart?')) {
                    removeFromCart(key);
                }
                return;
            }
        });

        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('cart-qty-input')) {
                const key = e.target.dataset.key;
                const val = parseInt(e.target.value);
                if (isNaN(val) || val < 1) {
                    e.target.value = 1;
                    updateCartItem(key, 1);
                } else {
                    updateCartItem(key, val);
                }
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.target.classList.contains('cart-qty-input') && e.key === 'Enter') {
                e.preventDefault();
                e.target.blur();
            }
        });
    };

    // ── Init ─────────────────────────────────────────────────────

    const init = () => {
        bindEvents();
        loadCart();
    };

    return { init, addToCart, updateCartItem, removeFromCart, loadCart, quickBuyNow, renderCartDrawer, updateCartBadge };
})();

document.addEventListener('DOMContentLoaded', Cart.init);
