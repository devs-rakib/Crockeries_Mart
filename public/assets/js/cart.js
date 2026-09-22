/**
 * CrockeriesMart - AJAX Cart Engine
 * Zero page reloads. localStorage + PHP session sync.
 */

'use strict';

const Cart = (() => {
    const STORAGE_KEY = 'crockeries_cart';
    const BASE_URL = document.querySelector('meta[name="base-url"]')?.content || '';
    const AJAX_URL = BASE_URL + '/ajax_handler.php';
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
        return isNaN(num) ? '৳0' : '৳' + num.toLocaleString('en-BD', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
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

    const openDrawer = () => {
        const el = document.getElementById('cartDrawer');
        if (!el) return;
        const instance = bootstrap.Offcanvas.getOrCreateInstance(el);
        if (instance && !instance._isShown) instance.show();
    };

    const calcTotal = () => items.reduce((sum, i) => sum + (parseFloat(i.discount_price || i.price) * parseInt(i.quantity)), 0);
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
                if (typeof window.updateShippingBar === 'function') {
                    window.updateShippingBar(calcTotal());
                }
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
                if (typeof window.updateShippingBar === 'function') {
                    window.updateShippingBar(calcTotal());
                }
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
        if (typeof window.updateShippingBar === 'function') {
            window.updateShippingBar(calcTotal());
        }
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
                window.location.href = BASE_URL + '/checkout';
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

        const body = document.getElementById('cartItems');
        const footer = document.getElementById('cartFooter');
        if (!body) return;

        if (!cartItems || cartItems.length === 0) {
            body.innerHTML = `
                <div class="cm-cart-empty">
                    <i class="bi bi-bag-x"></i>
                    <p>Your cart is empty</p>
                    <a href="` + BASE_URL + `/shop" class="btn btn-sm btn-outline-primary" style="border-color:var(--cm-primary);color:var(--cm-primary);border-radius:20px;">Start Shopping</a>
                </div>`;
            if (footer) footer.style.display = 'none';
            return;
        }

        body.innerHTML = cartItems.map((item) => `
            <div class="cm-cart-item" data-key="${escapeHtml(String(item.key))}">
                <img src="${BASE_URL}/uploads/${escapeHtml(item.image || 'images/placeholder.svg')}" alt="${escapeHtml(item.name)}" loading="lazy">
                <div class="item-info flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <div class="item-name">${escapeHtml(item.name)}</div>
                        <button class="item-remove cart-remove-btn" data-key="${escapeHtml(String(item.key))}" title="Remove"><i class="bi bi-x-lg"></i></button>
                    </div>
                    ${item.variant_name ? '<small class="text-muted">' + escapeHtml(item.variant_name) + '</small>' : ''}
                    <div class="item-price">${formatPrice(item.discount_price || item.price)}</div>
                    <div class="qty-control">
                        <button class="cart-qty-btn cart-qty-dec" data-key="${escapeHtml(String(item.key))}" data-action="decrease">-</button>
                        <span>${parseInt(item.quantity)}</span>
                        <button class="cart-qty-btn cart-qty-inc" data-key="${escapeHtml(String(item.key))}" data-action="increase">+</button>
                    </div>
                </div>
            </div>
        `).join('');

        if (footer) {
            footer.style.display = '';
            const subtotalEl = document.getElementById('cartSubtotal');
            if (subtotalEl) subtotalEl.textContent = formatPrice(calcTotal());
        }
    };

    const updateCartBadge = (count) => {
        const badge = document.getElementById('cartCountBadge');
        const text = document.getElementById('cartCountText');
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 0 ? '' : 'none';
        }
        if (text) text.textContent = count;
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
                const span = btn.parentElement.querySelector('span');
                const currentQty = span ? parseInt(span.textContent) : 1;
                updateCartItem(key, currentQty + 1);
                return;
            }

            if (e.target.closest('.cart-qty-dec')) {
                const btn = e.target.closest('.cart-qty-dec');
                const key = btn.dataset.key;
                const span = btn.parentElement.querySelector('span');
                const currentQty = span ? parseInt(span.textContent) : 2;
                if (currentQty > 1) {
                    updateCartItem(key, currentQty - 1);
                }
                return;
            }

            if (e.target.closest('.cart-remove-btn')) {
                const btn = e.target.closest('.cart-remove-btn');
                const key = btn.dataset.key;
                removeFromCart(key);
                return;
            }
        });
    };

    // ── Init ─────────────────────────────────────────────────────

    const init = () => {
        bindEvents();
        loadCart();
    };

    return { init, addToCart, updateCartItem, removeFromCart, loadCart, quickBuyNow, renderCartDrawer, updateCartBadge, openDrawer };
})();

document.addEventListener('DOMContentLoaded', Cart.init);
