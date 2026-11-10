/**
 * E-Commerce storefront, slide-over cart drawer, and checkout promo code controller.
 */
function escapeHtml(str) {
    return String(str || '').replace(/[&<>"']/g, (m) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;',
    })[m]);
}

export function initStorefront() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    async function postJson(url, payload) {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(payload),
        });
        return res.json();
    }

    // Checkout page promo code handler
    const checkoutApplyBtn = document.getElementById('checkoutCouponApplyBtn');
    const checkoutInput = document.getElementById('checkoutCouponInput');
    if (checkoutApplyBtn && checkoutInput) {
        const couponEndpoint = checkoutApplyBtn.dataset.couponUrl || '/ecomm/cart/coupon';
        checkoutApplyBtn.addEventListener('click', async () => {
            const code = (checkoutInput.value || '').trim();
            if (!code) return;
            const data = await postJson(couponEndpoint, { code });
            if (data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Invalid coupon code.');
            }
        });
    }

    // Storefront page (/ecomm)
    const storeConfigEl = document.getElementById('ecommStoreConfig');
    if (!storeConfigEl) return;

    let productsMap = {};
    let currentCart = {};
    try {
        productsMap = JSON.parse(document.getElementById('ecommProductsData')?.textContent || '{}');
        currentCart = JSON.parse(document.getElementById('ecommCartData')?.textContent || '{}');
    } catch {
        productsMap = {};
        currentCart = {};
    }

    const routes = {
        add: storeConfigEl.dataset.cartAddUrl || '/ecomm/cart/add',
        update: storeConfigEl.dataset.cartUpdateUrl || '/ecomm/cart/update',
        clear: storeConfigEl.dataset.cartClearUrl || '/ecomm/cart/clear',
        coupon: storeConfigEl.dataset.cartCouponUrl || '/ecomm/cart/coupon',
    };

    const offcanvasEl = document.getElementById('ecommCartDrawer');
    let cartOffcanvas = null;

    function openCartDrawer() {
        if (window.bootstrap && window.bootstrap.Offcanvas && offcanvasEl) {
            cartOffcanvas = cartOffcanvas || window.bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
            cartOffcanvas.show();
        }
    }

    function renderCartUI(cart) {
        currentCart = cart;
        const sym = cart.currency_symbol || '$';

        const headerBadge = document.getElementById('ecommHeaderCartBadge');
        const drawerBadge = document.getElementById('drawerCartCountBadge');
        const floatPill = document.getElementById('floatingCartPill');
        const floatCount = document.getElementById('floatingCartCount');
        const floatTotal = document.getElementById('floatingCartTotal');

        if (headerBadge) headerBadge.textContent = cart.count;
        if (drawerBadge) drawerBadge.textContent = cart.count;
        if (floatCount) floatCount.textContent = cart.count;
        if (floatTotal) floatTotal.textContent = sym + Number(cart.total || 0).toFixed(2);
        if (floatPill) floatPill.classList.toggle('d-none', (cart.count || 0) <= 0);

        const itemsBox = document.getElementById('drawerCartItemsContainer');
        const footerBox = document.getElementById('drawerCartFooterSection');
        if (!itemsBox || !footerBox) return;

        if (!cart.items || cart.items.length === 0) {
            itemsBox.innerHTML = '<div class="text-center py-5">'
                + '<i class="bi bi-cart-x text-muted d-block mb-3" style="font-size: 3rem;"></i>'
                + '<h6 class="font-weight-bold">Your cart is empty</h6>'
                + '<p class="text-muted small mb-0">Add developer toolkits or cloud blueprints to begin checkout.</p>'
                + '</div>';
            footerBox.classList.add('d-none');
            return;
        }

        footerBox.classList.remove('d-none');
        itemsBox.innerHTML = cart.items.map((item) =>
            '<div class="d-flex align-items-center p-3 mb-2 rounded-4 border" style="gap: 12px;">'
            + `<img src="${escapeHtml(item.image_url)}" alt="${escapeHtml(item.title)}" class="rounded-3 flex-shrink-0" style="width: 58px; height: 58px; object-fit: cover;">`
            + '<div class="flex-grow-1 min-w-0">'
            + `<div class="fw-bold text-truncate small mb-1">${escapeHtml(item.title)}</div>`
            + `<div class="text-primary fw-bold small">${sym}${Number(item.line_total).toFixed(2)}</div>`
            + '<div class="d-inline-flex align-items-center mt-2 border rounded-pill px-2 py-0" style="gap: 8px;">'
            + `<button type="button" class="btn btn-sm p-0 border-0 text-muted cart-qty-btn" data-id="${item.product_id}" data-qty="${item.quantity - 1}"><i class="bi bi-dash"></i></button>`
            + `<span class="small fw-bold">${item.quantity}</span>`
            + `<button type="button" class="btn btn-sm p-0 border-0 text-muted cart-qty-btn" data-id="${item.product_id}" data-qty="${item.quantity + 1}"><i class="bi bi-plus"></i></button>`
            + '</div>'
            + '</div>'
            + `<button type="button" class="btn btn-sm text-danger p-1 cart-qty-btn" title="Remove" data-id="${item.product_id}" data-qty="0"><i class="bi bi-trash3"></i></button>`
            + '</div>'
        ).join('');

        const subtotalEl = document.getElementById('drawerSubtotal');
        const totalEl = document.getElementById('drawerTotal');
        if (subtotalEl) subtotalEl.textContent = sym + Number(cart.subtotal).toFixed(2);
        if (totalEl) totalEl.textContent = sym + Number(cart.total).toFixed(2);

        const discRow = document.getElementById('drawerDiscountRow');
        const taxRow = document.getElementById('drawerTaxRow');
        const removeCouponBtn = document.getElementById('drawerRemoveCouponBtn');
        const couponInput = document.getElementById('drawerCouponInput');

        if (discRow) {
            if (cart.discount > 0) {
                discRow.classList.remove('d-none');
                const lbl = document.getElementById('drawerCouponLabel');
                const discEl = document.getElementById('drawerDiscount');
                if (lbl) lbl.textContent = cart.coupon_code || '';
                if (discEl) discEl.textContent = `-${sym}${Number(cart.discount).toFixed(2)}`;
            } else {
                discRow.classList.add('d-none');
            }
        }

        if (taxRow) {
            if (cart.tax > 0) {
                taxRow.classList.remove('d-none');
                const taxEl = document.getElementById('drawerTax');
                if (taxEl) taxEl.textContent = sym + Number(cart.tax).toFixed(2);
            } else {
                taxRow.classList.add('d-none');
            }
        }

        if (removeCouponBtn) {
            removeCouponBtn.classList.toggle('d-none', !cart.coupon_code);
        }
        if (couponInput && cart.coupon_code) {
            couponInput.value = cart.coupon_code;
        }
    }

    renderCartUI(currentCart);

    document.getElementById('openCartDrawerBtn')?.addEventListener('click', openCartDrawer);
    document.getElementById('floatingCartPill')?.addEventListener('click', openCartDrawer);

    document.querySelectorAll('.add-to-cart-btn').forEach((btn) => {
        btn.addEventListener('click', async function () {
            const prodId = this.getAttribute('data-product-id');
            const orig = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Adding...';
            try {
                const data = await postJson(routes.add, { product_id: Number(prodId), quantity: 1 });
                if (data.cart) renderCartUI(data.cart);
                if (data.success) {
                    this.innerHTML = '<i class="bi bi-check2"></i> Added';
                    openCartDrawer();
                } else {
                    alert(data.message || 'Could not add item.');
                }
            } catch {
                alert('Network error adding item to cart.');
            }
            setTimeout(() => {
                this.innerHTML = orig;
                this.disabled = false;
            }, 900);
        });
    });

    document.getElementById('drawerCartItemsContainer')?.addEventListener('click', async (e) => {
        const btn = e.target.closest('.cart-qty-btn');
        if (!btn) return;
        const id = Number(btn.getAttribute('data-id'));
        const qty = Number(btn.getAttribute('data-qty'));
        const data = await postJson(routes.update, { product_id: id, quantity: qty });
        if (data.cart) renderCartUI(data.cart);
    });

    document.getElementById('drawerClearCartBtn')?.addEventListener('click', async () => {
        const data = await postJson(routes.clear, {});
        if (data.cart) renderCartUI(data.cart);
    });

    document.getElementById('drawerApplyCouponBtn')?.addEventListener('click', async () => {
        const code = (document.getElementById('drawerCouponInput')?.value || '').trim();
        const fb = document.getElementById('drawerCouponFeedback');
        if (!code) return;
        const data = await postJson(routes.coupon, { code });
        if (data.cart) renderCartUI(data.cart);
        if (fb) {
            fb.className = `d-block mt-1 ${data.success ? 'text-success fw-bold' : 'text-danger'}`;
            fb.textContent = data.message || '';
        }
    });

    document.getElementById('drawerRemoveCouponBtn')?.addEventListener('click', async () => {
        const fb = document.getElementById('drawerCouponFeedback');
        const data = await postJson(routes.coupon, { remove: true });
        const input = document.getElementById('drawerCouponInput');
        if (input) input.value = '';
        if (data.cart) renderCartUI(data.cart);
        if (fb) fb.textContent = '';
    });

    // Quick View Modal
    const modalEl = document.getElementById('productDetailsModal');
    let prodModal = null;
    let activeModalProductId = null;

    document.querySelectorAll('.inspect-product-btn').forEach((el) => {
        el.addEventListener('click', function () {
            const pid = Number(this.getAttribute('data-product-id'));
            const prod = productsMap[pid];
            if (!prod || !modalEl) return;
            activeModalProductId = pid;

            const imgEl = document.getElementById('prodModalImg');
            if (imgEl) {
                imgEl.src = prod.image;
                imgEl.alt = prod.title;
            }
            const setText = (id, text) => {
                const node = document.getElementById(id);
                if (node) node.textContent = text;
            };
            setText('prodModalSku', `SKU: ${prod.sku}`);
            setText('prodModalCat', prod.badge);
            setText('prodModalTitle', prod.title);
            setText('prodModalPrice', prod.price_display);
            setText('prodModalDesc', prod.description);

            const featEl = document.getElementById('prodModalFeatures');
            if (featEl) {
                const fHtml = (prod.features || []).map((f) =>
                    `<li class="d-flex align-items-start small text-muted"><i class="bi bi-check-circle-fill text-success me-2 mr-2 mt-1 flex-shrink-0"></i><span>${escapeHtml(f)}</span></li>`
                ).join('');
                featEl.innerHTML = fHtml || '<li class="small text-muted">Full source codebase & documentation included.</li>';
            }

            if (window.bootstrap && window.bootstrap.Modal) {
                prodModal = prodModal || window.bootstrap.Modal.getOrCreateInstance(modalEl);
                prodModal.show();
            }
        });
    });

    document.getElementById('prodModalAddBtn')?.addEventListener('click', async () => {
        if (!activeModalProductId) return;
        const data = await postJson(routes.add, { product_id: activeModalProductId, quantity: 1 });
        if (data.cart) renderCartUI(data.cart);
        if (prodModal) prodModal.hide();
        openCartDrawer();
    });

    // Store Category Filter, Search & Sort
    const filterTabs = document.querySelectorAll('#storeFilterTabs .portfolio-filter-btn');
    const storeSearch = document.getElementById('storeSearchInput');
    const storeSort = document.getElementById('storeSortSelect');
    const storeGrid = document.getElementById('storeProductsGrid');
    const storeEmpty = document.getElementById('storeFilterEmpty');
    let activeCat = 'all';

    function applyStoreFilterAndSort() {
        if (!storeGrid) return;
        const q = (storeSearch?.value || '').trim().toLowerCase();
        const sortMode = storeSort?.value || 'featured';
        const cols = Array.from(storeGrid.querySelectorAll('.store-product-col'));

        cols.sort((a, b) => {
            if (sortMode === 'price-asc') return Number(a.dataset.price) - Number(b.dataset.price);
            if (sortMode === 'price-desc') return Number(b.dataset.price) - Number(a.dataset.price);
            if (sortMode === 'rating') return Number(b.dataset.rating) - Number(a.dataset.rating);
            return Number(a.dataset.order) - Number(b.dataset.order);
        });

        let shown = 0;
        cols.forEach((col) => {
            storeGrid.appendChild(col);
            const okCat = activeCat === 'all' || col.dataset.category === activeCat;
            const okSearch = !q || (col.dataset.search || '').indexOf(q) !== -1;
            const visible = okCat && okSearch;
            col.classList.toggle('d-none', !visible);
            if (visible) shown++;
        });

        if (storeEmpty) storeEmpty.classList.toggle('d-none', shown > 0);
    }

    filterTabs.forEach((tab) => {
        tab.addEventListener('click', function () {
            filterTabs.forEach((t) => t.classList.remove('active'));
            this.classList.add('active');
            activeCat = this.getAttribute('data-category') || 'all';
            applyStoreFilterAndSort();
        });
    });

    storeSearch?.addEventListener('input', applyStoreFilterAndSort);
    storeSort?.addEventListener('change', applyStoreFilterAndSort);
    document.getElementById('resetStoreFilterBtn')?.addEventListener('click', () => {
        activeCat = 'all';
        if (storeSearch) storeSearch.value = '';
        filterTabs.forEach((t) => t.classList.toggle('active', t.getAttribute('data-category') === 'all'));
        applyStoreFilterAndSort();
    });
}
