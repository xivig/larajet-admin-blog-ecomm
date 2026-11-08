@extends('blog')
@section('title', 'Xivig Store - Developer & Enterprise Solutions')
@section('content')

<!-- E-Commerce Hero Banner -->
<section class="page-title bg-1" style="background: #090d16; padding: 110px 0 60px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge-elite badge-elite-dark mb-3">
                    <span class="badge-dot pulse"></span> Official Enterprise Store
                </span>
                <h1 class="text-white font-weight-bold mb-3 display-4" style="letter-spacing: -0.03em;">Developer Toolkits & Software</h1>
                <p class="text-muted lead mb-0">Production-grade boilerplates, architecture blueprints, and enterprise integrations engineered for scale.</p>
            </div>
            <div class="col-lg-4 mt-4 mt-lg-0 text-lg-right">
                <a href="#catalog" class="btn-elite btn-elite-primary btn-sm">
                    <i class="fas fa-shopping-bag mr-2"></i> Explore Catalog
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Trust Metrics -->
<section class="py-4 ecomm-trust-bar">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-4 py-2">
                <div class="d-flex align-items-center justify-content-center">
                    <i class="fas fa-bolt text-primary fa-2x mr-3"></i>
                    <div class="text-left">
                        <h6 class="mb-0 font-weight-bold text-dark">Instant Digital Delivery</h6>
                        <small class="text-muted">Direct GitHub access & composer packages</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 py-2">
                <div class="d-flex align-items-center justify-content-center">
                    <i class="fas fa-shield-alt text-success fa-2x mr-3"></i>
                    <div class="text-left">
                        <h6 class="mb-0 font-weight-bold text-dark">Enterprise Grade Security</h6>
                        <small class="text-muted">Fortify 2FA & strict vulnerability audits</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 py-2">
                <div class="d-flex align-items-center justify-content-center">
                    <i class="fas fa-headset text-info fa-2x mr-3"></i>
                    <div class="text-left">
                        <h6 class="mb-0 font-weight-bold text-dark">Dedicated Support</h6>
                        <small class="text-muted">Direct architect consultation included</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Products Catalog Section -->
<section class="section py-5 ecomm-catalog" id="catalog">
    <div class="container">
        <div class="row mb-5 justify-content-between align-items-center">
            <div class="col-lg-6">
                <span class="badge-elite mb-2">Featured Products</span>
                <h2 class="font-weight-bold text-dark" style="letter-spacing: -0.02em;">Production Ready Systems</h2>
            </div>
            <div class="col-lg-6 text-lg-right mt-3 mt-lg-0">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-dark btn-sm active px-3">All</button>
                    <button type="button" class="btn btn-outline-dark btn-sm px-3">Starters</button>
                    <button type="button" class="btn btn-outline-dark btn-sm px-3">Blueprints</button>
                    <button type="button" class="btn btn-outline-dark btn-sm px-3">UI Suites</button>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Product 1 -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="blog-item p-0">
                    <div class="blog-img-wrap" style="height: 200px; background: #0f172a;">
                        <img src="https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=600&auto=format&fit=crop&q=60" alt="Product 1">
                        <span class="blog-category-tag" style="background: rgba(79, 70, 229, 0.9);">Laravel 13 Starter</span>
                    </div>
                    <div class="blog-item-content">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="text-warning small">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                <span class="text-muted ml-1">(4.9)</span>
                            </div>
                            <span class="font-weight-bold text-dark h5 mb-0">$149</span>
                        </div>
                        <h4 class="font-weight-bold mb-2">
                            <a href="#" class="blog-title-link">Enterprise SaaS Core & Multi-Tenant Engine</a>
                        </h4>
                        <p class="blog-excerpt mb-4">Complete Fortify + Jetstream architecture with multi-guard admin authentication and teams.</p>
                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                            <span class="badge badge-light text-success font-weight-bold">In Stock</span>
                            <button class="btn-elite btn-elite-primary btn-sm" onclick="alert('Item added to cart!');">
                                <i class="fas fa-shopping-cart mr-1"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product 2 -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="blog-item p-0">
                    <div class="blog-img-wrap" style="height: 200px; background: #0f172a;">
                        <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=600&auto=format&fit=crop&q=60" alt="Product 2">
                        <span class="blog-category-tag" style="background: rgba(6, 182, 212, 0.9);">Analytics & UI</span>
                    </div>
                    <div class="blog-item-content">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="text-warning small">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                                <span class="text-muted ml-1">(4.8)</span>
                            </div>
                            <span class="font-weight-bold text-dark h5 mb-0">$99</span>
                        </div>
                        <h4 class="font-weight-bold mb-2">
                            <a href="#" class="blog-title-link">Elite Executive Admin Panel & Dashboard Suite</a>
                        </h4>
                        <p class="blog-excerpt mb-4">High-performance admin dashboard with DataTables, dynamic charts, dark mode, and widgets.</p>
                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                            <span class="badge badge-light text-success font-weight-bold">In Stock</span>
                            <button class="btn-elite btn-elite-primary btn-sm" onclick="alert('Item added to cart!');">
                                <i class="fas fa-shopping-cart mr-1"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product 3 -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="blog-item p-0">
                    <div class="blog-img-wrap" style="height: 200px; background: #0f172a;">
                        <img src="https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=600&auto=format&fit=crop&q=60" alt="Product 3">
                        <span class="blog-category-tag" style="background: rgba(236, 72, 153, 0.9);">Security & Auth</span>
                    </div>
                    <div class="blog-item-content">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="text-warning small">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                <span class="text-muted ml-1">(5.0)</span>
                            </div>
                            <span class="font-weight-bold text-dark h5 mb-0">$129</span>
                        </div>
                        <h4 class="font-weight-bold mb-2">
                            <a href="#" class="blog-title-link">Fortify Dual-Guard 2FA Authentication Shield</a>
                        </h4>
                        <p class="blog-excerpt mb-4">Hardware-compatible passkeys, TOTP two-factor authentication, and session control package.</p>
                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                            <span class="badge badge-light text-success font-weight-bold">In Stock</span>
                            <button class="btn-elite btn-elite-primary btn-sm" onclick="alert('Item added to cart!');">
                                <i class="fas fa-shopping-cart mr-1"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product 4 -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="blog-item p-0">
                    <div class="blog-img-wrap" style="height: 200px; background: #0f172a;">
                        <img src="https://images.unsplash.com/photo-1504639725590-34d0984388bd?w=600&auto=format&fit=crop&q=60" alt="Product 4">
                        <span class="blog-category-tag" style="background: rgba(16, 185, 129, 0.9);">Cloud Infrastructure</span>
                    </div>
                    <div class="blog-item-content">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="text-warning small">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                <span class="text-muted ml-1">(4.9)</span>
                            </div>
                            <span class="font-weight-bold text-dark h5 mb-0">$199</span>
                        </div>
                        <h4 class="font-weight-bold mb-2">
                            <a href="#" class="blog-title-link">Kubernetes & Docker Enterprise Deployment Blueprints</a>
                        </h4>
                        <p class="blog-excerpt mb-4">Zero-downtime rolling upgrades, Redis caching clusters, and auto-scaling queue workers.</p>
                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                            <span class="badge badge-light text-success font-weight-bold">In Stock</span>
                            <button class="btn-elite btn-elite-primary btn-sm" onclick="alert('Item added to cart!');">
                                <i class="fas fa-shopping-cart mr-1"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product 5 -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="blog-item p-0">
                    <div class="blog-img-wrap" style="height: 200px; background: #0f172a;">
                        <img src="https://images.unsplash.com/photo-1542744094-24638eff58bb?w=600&auto=format&fit=crop&q=60" alt="Product 5">
                        <span class="blog-category-tag" style="background: rgba(139, 92, 246, 0.9);">API & SDK</span>
                    </div>
                    <div class="blog-item-content">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="text-warning small">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                                <span class="text-muted ml-1">(4.7)</span>
                            </div>
                            <span class="font-weight-bold text-dark h5 mb-0">$89</span>
                        </div>
                        <h4 class="font-weight-bold mb-2">
                            <a href="#" class="blog-title-link">Headless REST & GraphQL Gateway Starter</a>
                        </h4>
                        <p class="blog-excerpt mb-4">Sanctum tokens, rate limiting, OpenAPI schema generation, and high-throughput caching.</p>
                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                            <span class="badge badge-light text-success font-weight-bold">In Stock</span>
                            <button class="btn-elite btn-elite-primary btn-sm" onclick="alert('Item added to cart!');">
                                <i class="fas fa-shopping-cart mr-1"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product 6 -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="blog-item p-0">
                    <div class="blog-img-wrap" style="height: 200px; background: #0f172a;">
                        <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600&auto=format&fit=crop&q=60" alt="Product 6">
                        <span class="blog-category-tag" style="background: rgba(245, 158, 11, 0.9);">E-Commerce</span>
                    </div>
                    <div class="blog-item-content">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="text-warning small">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                <span class="text-muted ml-1">(5.0)</span>
                            </div>
                            <span class="font-weight-bold text-dark h5 mb-0">$179</span>
                        </div>
                        <h4 class="font-weight-bold mb-2">
                            <a href="#" class="blog-title-link">Global Payment Gateway & Billing Orchestrator</a>
                        </h4>
                        <p class="blog-excerpt mb-4">Stripe, PayPal, and Webhook dispatchers with automated invoice generation and tax compliance.</p>
                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                            <span class="badge badge-light text-success font-weight-bold">In Stock</span>
                            <button class="btn-elite btn-elite-primary btn-sm" onclick="alert('Item added to cart!');">
                                <i class="fas fa-shopping-cart mr-1"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom Inquiry CTA -->
        <div class="mt-5 p-5 bg-white rounded shadow-sm border text-center">
            <span class="badge-elite mb-3">Enterprise Custom Work</span>
            <h3 class="font-weight-bold mb-3 text-dark">Need Custom Architecture or Bespoke Modules?</h3>
            <p class="text-muted lead mb-4" style="max-width: 650px; margin: 0 auto;">Our dedicated engineering team designs custom high-throughput solutions tailored to your infrastructure requirements.</p>
            <a href="{{ url('/contact') }}" class="btn-elite btn-elite-primary">
                <i class="fas fa-comments mr-2"></i> Discuss Your Project
            </a>
        </div>
    </div>
</section>
@endsection
