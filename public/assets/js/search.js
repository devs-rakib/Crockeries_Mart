/**
 * CrockeriesMart - Live AJAX Search
 * Debounced input, keyboard navigation, touch-friendly.
 */

'use strict';

const LiveSearch = (() => {
    const DEBOUNCE_MS = 300;
    const MIN_CHARS = 2;

    let debounceTimer = null;
    let activeIndex = -1;
    let isOpen = false;
    let lastQuery = '';
    let currentResults = [];

    // ── DOM References ───────────────────────────────────────────

    const getElements = () => ({
        input: document.querySelector('#liveSearch input, #liveSearch'),
        results: document.getElementById('searchResults')
    });

    const getBaseUrl = () => {
        const el = document.getElementById('liveSearch');
        return el ? el.dataset.baseUrl : '';
    };

    // ── Helpers ──────────────────────────────────────────────────

    const getCsrfToken = () => {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    };

    const escapeHtml = (str) => {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    };

    const formatPrice = (price) => {
        const num = parseFloat(price);
        return isNaN(num) ? '৳0' : '৳' + num.toLocaleString('en-IN');
    };

    const debounce = (fn, delay) => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fn, delay);
    };

    // ── Search API ───────────────────────────────────────────────

    const search = async (query) => {
        const { results } = getElements();
        if (!results) return;

        if (query.length < MIN_CHARS) {
            close();
            return;
        }

        if (query === lastQuery) return;
        lastQuery = query;

        showLoading(results);

        try {
            const res = await fetch(getBaseUrl() + '/ajax_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({ action: 'live_search', q: query })
            });

            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data = await res.json();

            if (data.success && Array.isArray(data.results)) {
                currentResults = data.results;
                renderResults(results, data.results, query);
            } else {
                renderEmpty(results, query);
            }
        } catch (err) {
            console.error('Search error:', err);
            renderEmpty(results, query, true);
        }
    };

    // ── Rendering ────────────────────────────────────────────────

    const renderResults = (container, items, query) => {
        if (items.length === 0) {
            renderEmpty(container, query);
            return;
        }

        const highlighted = (text) => {
            if (!text) return '';
            const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
            return escapeHtml(text).replace(regex, '<mark>$1</mark>');
        };

        container.innerHTML = `
            <div class="search-dropdown">
                <div class="search-dropdown__header">
                    <span class="text-muted small">${items.length} result${items.length !== 1 ? 's' : ''} found</span>
                </div>
                <ul class="search-dropdown__list" role="listbox">
                    ${items.map((item, idx) => `
                        <li class="search-dropdown__item ${idx === activeIndex ? 'active' : ''}"
                            role="option"
                            data-index="${idx}"
                            data-url="${escapeHtml(item.url || '#')}">
                            <a href="${escapeHtml(item.url || '#')}" class="search-dropdown__link" tabindex="-1">
                                <img class="search-dropdown__img"
                                     src="${escapeHtml(item.image || 'assets/images/placeholder.jpg')}"
                                     alt="${escapeHtml(item.name)}"
                                     loading="lazy"
                                     onerror="this.src='assets/images/placeholder.jpg'">
                                <div class="search-dropdown__info">
                                    <div class="search-dropdown__name">${highlighted(item.name)}</div>
                                    ${item.category ? `<div class="search-dropdown__category text-muted small">${highlighted(item.category)}</div>` : ''}
                                    <div class="search-dropdown__price fw-bold">${formatPrice(item.price)}</div>
                                </div>
                            </a>
                        </li>
                    `).join('')}
                </ul>
                <div class="search-dropdown__footer">
                    <a href="${getBaseUrl()}/search?q=${encodeURIComponent(query)}" class="search-dropdown__view-all">
                        View all results for "${escapeHtml(query)}"
                    </a>
                </div>
            </div>`;

        open();
    };

    const renderEmpty = (container, query, isError = false) => {
        container.innerHTML = `
            <div class="search-dropdown">
                <div class="search-dropdown__empty text-center py-4">
                    <i class="bi ${isError ? 'bi-exclamation-circle' : 'bi-search'} fs-3 text-muted"></i>
                    <p class="mt-2 mb-0 text-muted">
                        ${isError
                            ? 'Something went wrong. Please try again.'
                            : `No results found for "<strong>${escapeHtml(query)}</strong>"`}
                    </p>
                </div>
            </div>`;
        open();
    };

    const showLoading = (container) => {
        container.innerHTML = `
            <div class="search-dropdown">
                <div class="search-dropdown__loading text-center py-3">
                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                    <span class="ms-2 text-muted small">Searching...</span>
                </div>
            </div>`;
        open();
    };

    // ── Open / Close ─────────────────────────────────────────────

    const open = () => {
        const { results } = getElements();
        if (results) {
            results.classList.add('show');
            isOpen = true;
        }
    };

    const close = () => {
        const { results } = getElements();
        if (results) {
            results.classList.remove('show');
            isOpen = false;
            activeIndex = -1;
            currentResults = [];
        }
    };

    // ── Keyboard Navigation ──────────────────────────────────────

    const handleKeydown = (e) => {
        if (!isOpen) return;

        const items = document.querySelectorAll('.search-dropdown__item');
        const count = items.length;
        if (count === 0) return;

        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                activeIndex = (activeIndex + 1) % count;
                updateActive(items);
                break;

            case 'ArrowUp':
                e.preventDefault();
                activeIndex = activeIndex <= 0 ? count - 1 : activeIndex - 1;
                updateActive(items);
                break;

            case 'Enter':
                if (activeIndex >= 0 && activeIndex < count) {
                    e.preventDefault();
                    const link = items[activeIndex].querySelector('a');
                    if (link) link.click();
                }
                break;

            case 'Escape':
                close();
                break;
        }
    };

    const updateActive = (items) => {
        items.forEach((el, i) => {
            el.classList.toggle('active', i === activeIndex);
            if (i === activeIndex) {
                el.scrollIntoView({ block: 'nearest' });
                const { input } = getElements();
                if (input) input.setAttribute('aria-activedescendant', `search-result-${i}`);
            }
        });
    };

    // ── Touch Handling ───────────────────────────────────────────

    const handleTouchOutside = (e) => {
        if (!isOpen) return;
        const { input, results } = getElements();
        if (!results) return;

        const target = e.target;
        if (input && input.contains(target)) return;
        if (results && results.contains(target)) return;

        close();
    };

    // ── Event Binding ────────────────────────────────────────────

    const bindEvents = () => {
        const { input, results } = getElements();
        if (!input) return;

        input.addEventListener('input', (e) => {
            const val = e.target.value.trim();
            activeIndex = -1;
            if (val.length < MIN_CHARS) {
                close();
                lastQuery = '';
                return;
            }
            debounce(() => search(val), DEBOUNCE_MS);
        });

        input.addEventListener('focus', (e) => {
            const val = e.target.value.trim();
            if (val.length >= MIN_CHARS && !isOpen) {
                search(val);
            }
        });

        input.addEventListener('keydown', handleKeydown);

        if (results) {
            results.addEventListener('click', (e) => {
                const item = e.target.closest('.search-dropdown__item');
                if (item) {
                    close();
                    input.blur();
                }
            });
        }

        document.addEventListener('click', handleTouchOutside);
        document.addEventListener('touchstart', handleTouchOutside, { passive: true });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && isOpen) {
                close();
                input.blur();
            }
        });

        const form = input.closest('form');
        if (form) {
            form.addEventListener('submit', (e) => {
                const val = input.value.trim();
                if (!val || val.length < MIN_CHARS) {
                    e.preventDefault();
                    return;
                }
                if (isOpen && activeIndex >= 0 && currentResults[activeIndex]) {
                    e.preventDefault();
                    window.location.href = currentResults[activeIndex].url || `${getBaseUrl()}/search?q=${encodeURIComponent(val)}`;
                }
            });
        }
    };

    // ── Init ─────────────────────────────────────────────────────

    const init = () => {
        bindEvents();
    };

    return { init, search, close, open };
})();

document.addEventListener('DOMContentLoaded', LiveSearch.init);
