@extends('layouts.blog')
@section('title', 'Xivig Store - Developer Toolkits & Enterprise Checkout')
@section('content')

    @if (!empty($settings->get('store_announcement')))
        <div class="ecomm-announcement-bar text-center">
            <i class="bi bi-stars text-primary me-1 mr-1"></i>
            {{ $settings->get('store_announcement') }}
        </div>
    @endif

    <!-- E-Commerce Hero Banner -->
    <section class="page-title" style="padding: 40px 0 36px 0;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="badge-elite badge-elite-dark mb-3">
                        <span class="badge-dot pulse"></span> Official Enterprise Store
                    </span>
                    <h1 class="font-weight-bold mb-3 display-4" style="letter-spacing: -0.03em;">Developer Toolkits &amp;
                        Software</h1>
                    <p class="text-muted lead mb-0" style="max-width: 650px;">
                        Production-grade boilerplates, multi-tenant starters, and infrastructure blueprints with instant
                        license key delivery.
                    </p>
                </div>
                <div class="col-lg-4 mt-4 mt-lg-0 text-lg-end text-lg-right">
                    <div class="d-inline-flex flex-wrap align-items-center" style="gap: 10px;">
                        <a href="#catalog" class="btn-elite btn-elite-outline btn-sm">
                            <i class="bi bi-grid-fill me-1 mr-1"></i> Catalog
                        </a>
                        <button type="button" class="btn-elite btn-elite-primary btn-sm position-relative"
                            id="openCartDrawerBtn">
                            <i class="bi bi-cart3 me-1 mr-1"></i>
                            <span>Cart</span>
                            <span class="badge rounded-pill ms-1 ml-1" id="ecommHeaderCartBadge"
                                style="background: rgba(255,255,255,0.24); color: #ffffff;">
                                {{ $cart['count'] ?? 0 }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust Metrics Strip -->
    <section class="py-4 ecomm-trust-bar">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 py-2">
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="bi bi-lightning-charge-fill text-primary fa-2x me-3 mr-3"></i>
                        <div class="text-start text-left">
                            <h6 class="mb-0 font-weight-bold">Instant License Delivery</h6>
                            <small class="text-muted">Automated keys &amp; direct download vault</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 py-2">
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="bi bi-shield-lock-fill text-success fa-2x me-3 mr-3"></i>
                        <div class="text-start text-left">
                            <h6 class="mb-0 font-weight-bold">Active Payment Gateways</h6>
                            <small class="text-muted">
                                @if ($gateways->isNotEmpty())
                                    {{ $gateways->pluck('name')->take(3)->implode(' • ') }}
                                @else
                                    Managed by Admin Settings
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 py-2">
                    <div class="d-flex align-items-center justify-content-center">
                        <i class="bi bi-headset text-info fa-2x me-3 mr-3"></i>
                        <div class="text-start text-left">
                            <h6 class="mb-0 font-weight-bold">Enterprise Commercial Rights</h6>
                            <small class="text-muted">Clean PSR-12 source &amp; priority updates</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Catalog Section -->
    <section class="section py-5 ecomm-catalog" id="catalog">
        <div class="container">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2 mr-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 mr-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (!$storeEnabled)
                <div class="portfolio-cta-elite text-center my-4">
                    <span class="badge-elite mb-3">Store Paused</span>
                    <h2 class="font-weight-bold text-dark mb-2">Storefront Temporarily Under Scheduled Update</h2>
                    <p class="text-muted mb-4 mx-auto" style="max-width: 560px;">
                        Checkout is temporarily paused from the Administration Panel. Reach out to our team for immediate
                        manual licensing.
                    </p>
                    <a href="{{ url('/contact') }}" class="btn-elite btn-elite-primary">
                        <i class="bi bi-envelope-fill me-2 mr-2"></i> Contact Licensing Desk
                    </a>
                </div>
            @else
                <!-- Store Filter, Search & Sort Controls Bar -->
                <div class="portfolio-filter-bar mb-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap: 14px;">
                        <div class="d-flex flex-wrap align-items-center" style="gap: 8px;" id="storeFilterTabs">
                            <button type="button" class="portfolio-filter-btn active" data-category="all">
                                <i class="bi bi-grid-1x2-fill"></i>
                                <span>All Toolkits</span>
                                <span class="portfolio-filter-count">{{ $products->count() }}</span>
                            </button>
                            @foreach ($categories as $catName => $catCount)
                                <button type="button" class="portfolio-filter-btn"
                                    data-category="{{ strtolower($catName) }}">
                                    <span>{{ $catName }}</span>
                                    <span class="portfolio-filter-count">{{ $catCount }}</span>
                                </button>
                            @endforeach
                        </div>

                        <div class="d-flex flex-wrap align-items-center" style="gap: 10px;">
                            <div class="position-relative" style="min-width: 210px;">
                                <input type="text" id="storeSearchInput" class="form-control form-control-elite"
                                    style="border-radius: 999px; padding-left: 36px; height: 40px; font-size: 13.5px;"
                                    placeholder="Search SKU or toolkit...">
                                <i class="bi bi-search text-muted position-absolute"
                                    style="left: 14px; top: 50%; transform: translateY(-50%); font-size: 13px; pointer-events: none;"></i>
                            </div>

                            <select id="storeSortSelect" class="form-select form-control-elite"
                                style="width: auto; border-radius: 999px; height: 40px; font-size: 13px; padding-right: 32px;">
                                <option value="featured">Featured First</option>
                                <option value="price-asc">Price: Low to High</option>
                                <option value="price-desc">Price: High to Low</option>
                                <option value="rating">Highest Rated</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="row" id="storeProductsGrid">
                    @forelse($products as $index => $product)
                        @php
                            $inStock = $product->isInStock();
                            $effPrice = $product->effective_price;
                        @endphp
                        <div class="col-lg-4 col-md-6 mb-4 store-product-col"
                            data-category="{{ strtolower($product->category) }}" data-price="{{ $effPrice }}"
                            data-rating="{{ $product->rating }}" data-order="{{ $index }}"
                            data-search="{{ strtolower($product->title . ' ' . $product->sku . ' ' . $product->category . ' ' . $product->short_description) }}">
                            <article class="portfolio-card-elite">
                                <!-- Visual Header & Decoupled Badges -->
                                <div class="portfolio-img-wrap" style="height: 205px;">
                                    <a href="javascript:void(0)" class="d-block w-100 h-100 inspect-product-btn"
                                        data-product-id="{{ $product->id }}">
                                        <img src="{{ $product->resolved_image_url }}" alt="{{ $product->title }}"
                                            loading="{{ $index < 3 ? 'eager' : 'lazy' }}">
                                    </a>
                                    <div class="portfolio-img-scrim"></div>

                                    <div class="portfolio-card-badges">
                                        @if ($product->has_discount)
                                            <span class="badge-elite-pinned">
                                                <i class="bi bi-tag-fill me-1 mr-1"></i> SALE
                                            </span>
                                        @elseif($product->is_featured)
                                            <span class="badge-portfolio-status">
                                                <span class="badge-dot pulse" style="background: #34d399;"></span>
                                                Featured
                                            </span>
                                        @else
                                            <span></span>
                                        @endif

                                        <span class="badge-elite-category"
                                            title="{{ $product->badge_label ?: $product->category }}">
                                            {{ $product->badge_label ?: $product->category }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Card Body -->
                                <div class="portfolio-body-elite">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="d-inline-flex align-items-center small" style="gap: 4px;">
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <strong
                                                class="text-dark">{{ number_format((float) $product->rating, 1) }}</strong>
                                            <span class="text-muted">({{ $product->reviews_count }})</span>
                                            <span class="text-muted mx-1">&middot;</span>
                                            <span class="text-muted"
                                                style="font-size: 11.5px;">{{ $product->sales_count }} sold</span>
                                        </div>
                                        <div class="text-end text-right">
                                            @if ($product->has_discount)
                                                <small
                                                    class="text-muted text-decoration-line-through me-1 mr-1">{{ $currencySymbol }}{{ number_format((float) $product->price, 0) }}</small>
                                            @endif
                                            <span class="font-weight-bold text-primary h5 mb-0"
                                                style="font-family: var(--font-heading);">
                                                {{ $currencySymbol }}{{ number_format($effPrice, 0) }}
                                            </span>
                                        </div>
                                    </div>

                                    <h3 class="portfolio-title-elite">
                                        <a href="javascript:void(0)"
                                            class="text-decoration-none text-reset inspect-product-btn"
                                            data-product-id="{{ $product->id }}">
                                            {{ $product->title }}
                                        </a>
                                    </h3>

                                    <p class="portfolio-desc-elite">{{ $product->short_description }}</p>

                                    <div class="portfolio-tech-pills">
                                        <span class="portfolio-tech-chip">{{ $product->category }}</span>
                                        <span class="portfolio-tech-chip">SKU: {{ $product->sku }}</span>
                                        @if ($inStock)
                                            <span class="portfolio-tech-chip"
                                                style="color: #10b981; border-color: rgba(16, 185, 129, 0.3);">
                                                <i class="bi bi-check-circle-fill"></i>
                                                {{ $product->stock_qty === -1 ? 'Instant Digital' : $product->stock_qty . ' left' }}
                                            </span>
                                        @else
                                            <span class="portfolio-tech-chip text-danger">Out of Stock</span>
                                        @endif
                                    </div>

                                    <div class="portfolio-footer-elite">
                                        <button type="button" class="btn-read-elite add-to-cart-btn"
                                            data-product-id="{{ $product->id }}" {{ !$inStock ? 'disabled' : '' }}>
                                            <i class="bi bi-cart-plus-fill"></i> Add to Cart
                                        </button>

                                        <div class="d-inline-flex align-items-center" style="gap: 6px;">
                                            <button type="button"
                                                class="btn-elite btn-elite-outline btn-sm px-3 py-1 inspect-product-btn"
                                                style="font-size: 12px; border-radius: 999px;"
                                                data-product-id="{{ $product->id }}">
                                                Specs
                                            </button>
                                            <form action="{{ route('ecomm.cart.add') }}" method="POST"
                                                class="d-inline m-0">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                <input type="hidden" name="buy_now" value="1">
                                                <button type="submit"
                                                    class="btn-sm btn-elite  py-1 btn-elite-outline text-primary fw-bold text-decoration-none"
                                                    style="font-size: 12.5px;" {{ !$inStock ? 'disabled' : '' }}>
                                                    Buy Now &rarr;
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="portfolio-cta-elite text-center">
                                <i class="bi bi-box-seam text-primary mb-2" style="font-size: 2.5rem;"></i>
                                <h4 class="font-weight-bold text-dark">No Active Products Configured</h4>
                                <p class="text-muted mb-0">Add products from the Admin Panel &rarr; E-Commerce Store &rarr;
                                    Products &amp; Catalog.</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Filter Empty State -->
                <div id="storeFilterEmpty" class="text-center py-5 d-none">
                    <div class="portfolio-kpi-card d-inline-flex flex-column text-center px-5 py-4">
                        <i class="bi bi-search text-primary mb-2" style="font-size: 2rem;"></i>
                        <h5 class="font-weight-bold text-dark mb-1">No matching products found</h5>
                        <p class="text-muted small mb-3">Reset filters to view all available toolkits &amp; blueprints.</p>
                        <button type="button" id="resetStoreFilterBtn"
                            class="btn-elite btn-elite-primary btn-sm mx-auto">Reset Filters</button>
                    </div>
                </div>
            @endif

            <!-- Enterprise Custom Work CTA -->
            <div class="portfolio-cta-elite mt-5 text-center">
                <span class="badge-elite mb-3">Enterprise Custom Work</span>
                <h3 class="font-weight-bold text-dark mb-2">Need Custom Architecture or Bespoke Modules?</h3>
                <p class="text-muted lead mb-4 mx-auto" style="max-width: 640px; font-size: 1rem;">
                    Our dedicated engineering team designs custom high-throughput modules, multi-gateway billing systems,
                    and cloud infrastructure tailored to your stack.
                </p>
                <a href="{{ url('/contact') }}?subject={{ urlencode('Custom Store Module Inquiry') }}"
                    class="btn-elite btn-elite-primary">
                    <i class="bi bi-chat-dots-fill me-2 mr-2"></i> Discuss Your Project
                </a>
            </div>
        </div>
    </section>

    <!-- Slide-Over Shopping Cart Offcanvas Drawer -->
    <div class="offcanvas offcanvas-end ecomm-cart-drawer" tabindex="-1" id="ecommCartDrawer"
        aria-labelledby="ecommCartDrawerLabel" style="width: 420px; max-width: 95vw;">
        <div class="offcanvas-header border-bottom px-4 py-3">
            <h5 class="offcanvas-title font-weight-bold d-flex align-items-center" id="ecommCartDrawerLabel">
                <i class="bi bi-bag-check-fill text-primary me-2 mr-2"></i> Your Cart
                <span class="badge bg-primary rounded-pill ms-2 ml-2" id="drawerCartCountBadge"
                    style="font-size: 12px;">{{ $cart['count'] ?? 0 }}</span>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body d-flex flex-column p-4">
            <div id="drawerCartItemsContainer" class="flex-grow-1 overflow-auto pe-1" style="gap: 12px;"></div>

            <!-- Promo Code Box -->
            <div id="drawerCartFooterSection" class="pt-3 mt-3 border-top d-none">
                <div class="mb-3">
                    <label class="small font-weight-bold text-muted mb-1 d-block">Promo / Discount Code</label>
                    <div class="position-relative w-100">
                        <input type="text" id="drawerCouponInput" class="form-control form-control-elite"
                            style="height: 46px; border-radius: 999px; padding: 8px 120px 8px 18px; font-size: 13.5px;"
                            placeholder="Try ELITE20 or XIVIG50" value="{{ $cart['coupon_code'] ?? '' }}">
                        <div class="position-absolute d-flex align-items-center" style="top: 50%; right: 5px; transform: translateY(-50%); gap: 4px; z-index: 5;">
                            <button type="button" id="drawerApplyCouponBtn"
                                class="btn-elite btn-elite-primary btn-sm"
                                style="border-radius: 999px; height: 36px; padding: 0 16px; display: inline-flex; align-items: center; justify-content: center;">Apply</button>
                            <button type="button" id="drawerRemoveCouponBtn"
                                class="btn btn-outline-danger rounded-circle {{ empty($cart['coupon_code']) ? 'd-none' : '' }}"
                                style="width: 34px; height: 34px; padding: 0; line-height: 32px; text-align: center; font-size: 12px;"
                                title="Remove Promo">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                    <small id="drawerCouponFeedback" class="d-block mt-1" style="font-size: 11.5px;"></small>
                </div>

                <div class="d-flex justify-content-between small text-muted mb-1">
                    <span>Subtotal</span>
                    <span id="drawerSubtotal">{{ $currencySymbol }}{{ number_format($cart['subtotal'] ?? 0, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between small text-success mb-1 {{ ($cart['discount'] ?? 0) > 0 ? '' : 'd-none' }}"
                    id="drawerDiscountRow">
                    <span>Discount (<span id="drawerCouponLabel">{{ $cart['coupon_code'] }}</span>)</span>
                    <span id="drawerDiscount">-{{ $currencySymbol }}{{ number_format($cart['discount'] ?? 0, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between small text-muted mb-2 {{ ($cart['tax'] ?? 0) > 0 ? '' : 'd-none' }}"
                    id="drawerTaxRow">
                    <span>Tax ({{ $cart['tax_rate'] ?? 0 }}%)</span>
                    <span id="drawerTax">{{ $currencySymbol }}{{ number_format($cart['tax'] ?? 0, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-2 border-top mb-3">
                    <span class="font-weight-bold h6 mb-0">Total</span>
                    <span class="font-weight-bold text-primary h4 mb-0" id="drawerTotal"
                        style="font-family: var(--font-heading);">
                        {{ $currencySymbol }}{{ number_format($cart['total'] ?? 0, 2) }}
                    </span>
                </div>

                <div class="d-grid gap-2">
                    <a href="{{ route('ecomm.checkout') }}"
                        class="btn-elite btn-elite-primary w-100 justify-content-center text-center">
                        <i class="bi bi-shield-lock-fill me-2 mr-2"></i> Proceed to Checkout
                    </a>
                    <button type="button" id="drawerClearCartBtn"
                        class="btn btn-sm btn-link text-muted text-decoration-none">
                        Clear Cart
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Specifications Modal -->
    <div class="modal fade portfolio-modal-elite" id="productDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="portfolio-modal-hero" style="height: 220px;">
                    <img id="prodModalImg" src="" alt="Product Preview">
                    <div class="portfolio-img-scrim"></div>
                    <button type="button" class="btn-close btn-close-white position-absolute" data-bs-dismiss="modal"
                        aria-label="Close"
                        style="top: 16px; right: 16px; z-index: 5; background-color: rgba(15,23,42,0.75); padding: 10px; border-radius: 50%; opacity: 1;"></button>
                    <div class="position-absolute d-flex align-items-center"
                        style="bottom: 14px; left: 20px; z-index: 4; gap: 8px;">
                        <span class="badge-portfolio-status" id="prodModalSku"></span>
                        <span class="badge-elite-category" id="prodModalCat" style="margin-left: 0 !important;"></span>
                    </div>
                </div>
                <div class="modal-body p-4 p-md-5">
                    <div class="d-flex flex-wrap justify-content-between align-items-start mb-2" style="gap: 10px;">
                        <h3 class="font-weight-bold text-dark mb-0" id="prodModalTitle"></h3>
                        <div class="h3 font-weight-bold text-primary mb-0" id="prodModalPrice"
                            style="font-family: var(--font-heading);"></div>
                    </div>
                    <p class="text-muted mb-4" id="prodModalDesc" style="line-height: 1.65;"></p>

                    <h6 class="font-weight-bold text-dark mb-2">
                        <i class="bi bi-check2-all text-primary me-1 mr-1"></i> Included Capabilities &amp; Deliverables
                    </h6>
                    <ul class="list-unstyled mb-4" id="prodModalFeatures" style="display: grid; gap: 8px;"></ul>

                    <div class="d-flex flex-wrap align-items-center justify-content-between pt-3 border-top"
                        style="gap: 10px;">
                        <button type="button" class="btn-elite btn-elite-primary btn-sm" id="prodModalAddBtn">
                            <i class="bi bi-cart-plus-fill me-2 mr-2"></i> Add to Cart
                        </button>
                        <button type="button" class="btn-elite btn-elite-outline btn-sm"
                            data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Mini Cart Pill (Visible cleanly at bottom-right when cart has items) -->
    <button type="button" id="floatingCartPill"
        class="btn-elite btn-elite-primary position-fixed shadow-lg {{ ($cart['count'] ?? 0) > 0 ? '' : 'd-none' }}"
        style="bottom: 24px; right: 24px; z-index: 1040; border-radius: 999px; padding: 12px 20px;">
        <i class="bi bi-cart-check-fill me-2 mr-2"></i>
        <span id="floatingCartCount">{{ $cart['count'] ?? 0 }}</span> item(s) &middot;
        <strong class="ms-1 ml-1"
            id="floatingCartTotal">{{ $currencySymbol }}{{ number_format($cart['total'] ?? 0, 2) }}</strong>
    </button>

    @php
        $productsMapPayload = $products->mapWithKeys(function ($p) use ($currencySymbol) {
            return [
                $p->id => [
                    'id' => $p->id,
                    'title' => $p->title,
                    'sku' => $p->sku,
                    'category' => $p->category,
                    'badge' => $p->badge_label ?: $p->category,
                    'image' => $p->resolved_image_url,
                    'price_display' => $currencySymbol . number_format($p->effective_price, 2),
                    'description' => $p->description ?: $p->short_description,
                    'features' => $p->features ?? [],
                    'in_stock' => $p->isInStock(),
                ],
            ];
        });
    @endphp

    <div id="ecommStoreConfig" class="d-none"
        data-cart-add-url="{{ route('ecomm.cart.add') }}"
        data-cart-update-url="{{ route('ecomm.cart.update') }}"
        data-cart-clear-url="{{ route('ecomm.cart.clear') }}"
        data-cart-coupon-url="{{ route('ecomm.cart.coupon') }}"></div>
    <script id="ecommProductsData" type="application/json">@json($productsMapPayload)</script>
    <script id="ecommCartData" type="application/json">@json($cart)</script>
@endsection
