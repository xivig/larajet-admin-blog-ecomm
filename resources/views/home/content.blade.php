@extends('layouts.home')
@section('title', 'Xivig - Modern Elite Architecture & Digital Solutions')
@section('content')

<!-- Hero Slider Section Start -->
<section class="slider">
    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-9 col-md-10">
                <div class="block">
                    <span class="badge-elite badge-elite-dark mb-4 animated fadeInDown">
                        <span class="badge-dot pulse"></span> Next-Gen Enterprise Architecture
                    </span>
                    <h1 class="animated fadeInUp mb-4 display-3 font-weight-bold" style="letter-spacing: -0.03em; line-height: 1.15;">
                        Engineering <span class="text-gradient">high-performance</span> digital ecosystems.
                    </h1>
                    <p class="animated fadeInUp text-muted lead mb-5" style="max-width: 680px;">
                        Xivig delivers mission-critical software solutions, cutting-edge full-stack frameworks, and horizontally scalable cloud infrastructures built for tomorrow.
                    </p>
                    <div class="d-flex flex-wrap align-items-center animated fadeInUp" style="gap: 16px;">
                        <a href="{{ url('/services') }}" class="btn-elite btn-elite-primary" aria-label="Explore Capabilities">
                            Explore Services <i class="fas fa-arrow-right ml-2" style="font-size: 13px;"></i>
                        </a>
                        <a href="{{ url('/project') }}" class="btn-elite btn-elite-glass" aria-label="View Portfolio">
                            View Portfolio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Hero Slider Section End -->

<!-- Section Intro Start -->
<section class="section py-5 position-relative" style="margin-top: -40px; z-index: 3;">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8">
                <div class="section-title">
                    <span class="badge-elite mb-2">Pioneering Standards</span>
                    <h2 class="mt-2 font-weight-bold text-dark" style="letter-spacing: -0.02em;">
                        Engineered for speed, resilience, and maximum business impact
                    </h2>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="intro-item">
                    <div class="icon-box-elite">
                        <i class="ti-desktop"></i>
                    </div>
                    <h4 class="font-weight-bold text-dark mb-3">Modern & Responsive Design</h4>
                    <p class="text-muted mb-0">Fluid layouts crafted with precision typography, micro-interactions, and flawless cross-platform responsiveness.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="intro-item">
                    <div class="icon-box-elite">
                        <i class="ti-medall"></i>
                    </div>
                    <h4 class="font-weight-bold text-dark mb-3">Awarded Enterprise Reliability</h4>
                    <p class="text-muted mb-0">Engineered with high security benchmarks, strict automated testing, and bulletproof transactional integrity.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-12 mb-4">
                <div class="intro-item">
                    <div class="icon-box-elite">
                        <i class="ti-layers-alt"></i>
                    </div>
                    <h4 class="font-weight-bold text-dark mb-3">Clean Architecture</h4>
                    <p class="text-muted mb-0">Maintainable microservice and monolithic architectures using modern service providers and typed contracts.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Section Intro END -->

<!-- Section About Start -->
<section class="section py-5 position-relative">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <div class="pr-lg-4 position-relative">
                    <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&auto=format&fit=crop&q=60" alt="Team" class="img-fluid rounded-lg shadow-lg" style="border-radius: 20px;">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-item">
                    <span class="badge-elite mb-2">Who We Are</span>
                    <h2 class="mt-2 mb-4 font-weight-bold text-dark" style="letter-spacing: -0.02em;">We are a dynamic team of visionary engineers</h2>
                    <div class="about-content">
                        <h4 class="mb-3 font-weight-bold text-primary">Precision Engineering Solutions</h4>
                        <p class="text-muted mb-4 lead" style="font-size: 16px;">
                            We provide consulting and software craftsmanship across enterprise architectures, helping forward-thinking companies achieve unprecedented scalability and efficiency.
                        </p>
                        <p class="text-muted mb-5">
                            From high-throughput APIs to real-time interactive portals, our workflows streamline development while guaranteeing maximum stability.
                        </p>
                        <a href="{{ url('/about') }}" class="btn-elite btn-elite-primary">
                            Discover Our Mission <i class="fas fa-chevron-right ml-2" style="font-size: 11px;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Section About End -->

<!-- Section Counter Start -->
<section class="section py-5 section-counter">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4 mb-lg-0">
                <div class="counter-stat">
                    <div class="count-number">1,730+</div>
                    <div class="count-label">Projects Completed</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4 mb-lg-0">
                <div class="counter-stat">
                    <div class="count-number">125M</div>
                    <div class="count-label">Users Worldwide</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4 mb-lg-0">
                <div class="counter-stat">
                    <div class="count-number">39</div>
                    <div class="count-label">Available Countries</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="counter-stat">
                    <div class="count-number">14</div>
                    <div class="count-label">Industry Awards</div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Section Counter End -->

<!-- Section Services Start -->
<section class="section py-5">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-7 text-center">
                <div class="section-title">
                    <span class="badge-elite mb-2">Our Capabilities</span>
                    <h2 class="mt-2 font-weight-bold text-dark" style="letter-spacing: -0.02em;">Comprehensive Engineering Services</h2>
                    <p class="text-muted">A full-spectrum technology partner accelerating digital transformation.</p>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="service-item">
                    <div class="icon-box-elite">
                        <i class="ti-desktop"></i>
                    </div>
                    <h4 class="font-weight-bold text-dark mb-3">Web Architecture</h4>
                    <p class="text-muted mb-0">High-concurrency web platforms built with modern Laravel, Livewire, and Vite reactive front-ends.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="service-item">
                    <div class="icon-box-elite">
                        <i class="ti-layers"></i>
                    </div>
                    <h4 class="font-weight-bold text-dark mb-3">Interface Design</h4>
                    <p class="text-muted mb-0">User-centric interfaces optimized for conversion, visual hierarchy, accessibility, and delight.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="service-item">
                    <div class="icon-box-elite">
                        <i class="ti-bar-chart"></i>
                    </div>
                    <h4 class="font-weight-bold text-dark mb-3">Business Strategy</h4>
                    <p class="text-muted mb-0">Data-driven software consulting, architecture auditing, and enterprise systems integration.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="service-item">
                    <div class="icon-box-elite">
                        <i class="ti-vector"></i>
                    </div>
                    <h4 class="font-weight-bold text-dark mb-3">Brand Identity</h4>
                    <p class="text-muted mb-0">Distinctive visual brand languages, design tokens, and comprehensive component design systems.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="service-item">
                    <div class="icon-box-elite">
                        <i class="ti-android"></i>
                    </div>
                    <h4 class="font-weight-bold text-dark mb-3">Mobile & APIs</h4>
                    <p class="text-muted mb-0">Native mobile integration, GraphQL endpoints, and resilient RESTful microservices.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="service-item">
                    <div class="icon-box-elite">
                        <i class="ti-pencil-alt"></i>
                    </div>
                    <h4 class="font-weight-bold text-dark mb-3">Technical Content</h4>
                    <p class="text-muted mb-0">Developer documentation, API reference guides, and technical thought leadership publication.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Section Services End -->

<!-- Section CTA Start -->
<section class="section py-5 position-relative section-cta">
    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-7 text-white mb-4 mb-lg-0">
                <span class="badge-elite badge-elite-dark mb-3">
                    <span class="badge-dot pulse"></span> Direct Architect Line
                </span>
                <h2 class="text-white font-weight-bold mb-3 display-4" style="letter-spacing: -0.02em;">Ready to build something extraordinary?</h2>
                <p class="text-white-50 lead mb-0">Partner with our engineering team for rapid development and enterprise deployment.</p>
            </div>
            <div class="col-lg-5 text-lg-right">
                <a href="{{ url('/contact') }}" class="btn-elite btn-elite-primary mr-3 mb-2">
                    <i class="fas fa-paper-plane mr-2"></i> Start a Project
                </a>
                <a href="tel:+23-345-67890" class="btn-elite btn-elite-glass mb-2">
                    <i class="fas fa-phone-alt mr-2"></i> +23-345-67890
                </a>
            </div>
        </div>
    </div>
</section>
<!-- Section CTA End -->

<!-- Section Testimonial Start -->
<section class="section py-5">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-7 text-center">
                <div class="section-title">
                    <span class="badge-elite mb-2">Partner Feedback</span>
                    <h2 class="mt-2 font-weight-bold text-dark" style="letter-spacing: -0.02em;">What industry leaders say about Xivig</h2>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="p-4 bg-light rounded-lg border h-100 d-flex flex-column" style="border-radius: 18px;">
                    <div class="text-warning mb-3">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="text-dark mb-4 flex-grow-1" style="font-size: 15px; line-height: 1.7;">
                        "The architectural clarity and speed of delivery by Xivig exceeded all expectations. Our application easily handles peak global traffic without a stutter."
                    </p>
                    <div class="d-flex align-items-center pt-3 border-top">
                        <div class="comment-avatar mr-3" style="width: 44px; height: 44px; font-size: 16px;">T</div>
                        <div>
                            <h6 class="mb-0 font-weight-bold text-dark">Thomas Johnson</h6>
                            <small class="text-muted">Chief Technology Officer, FinTech Corp</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="p-4 bg-light rounded-lg border h-100 d-flex flex-column" style="border-radius: 18px;">
                    <div class="text-warning mb-3">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="text-dark mb-4 flex-grow-1" style="font-size: 15px; line-height: 1.7;">
                        "Xivig's multi-tenant architecture and Fortify integration gave our team a bulletproof authentication core. Remarkable engineering prowess."
                    </p>
                    <div class="d-flex align-items-center pt-3 border-top">
                        <div class="comment-avatar mr-3" style="width: 44px; height: 44px; font-size: 16px;">M</div>
                        <div>
                            <h6 class="mb-0 font-weight-bold text-dark">Mickel Hussy</h6>
                            <small class="text-muted">Head of Engineering, CloudScale</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-12 mb-4">
                <div class="p-4 bg-light rounded-lg border h-100 d-flex flex-column" style="border-radius: 18px;">
                    <div class="text-warning mb-3">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="text-dark mb-4 flex-grow-1" style="font-size: 15px; line-height: 1.7;">
                        "A masterclass in modern Laravel design. The reactive interfaces and sleek aesthetic made our product launch an instant success."
                    </p>
                    <div class="d-flex align-items-center pt-3 border-top">
                        <div class="comment-avatar mr-3" style="width: 44px; height: 44px; font-size: 16px;">J</div>
                        <div>
                            <h6 class="mb-0 font-weight-bold text-dark">James Watson</h6>
                            <small class="text-muted">Product Director, Apex Labs</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Section Testimonial End -->

<!-- Section Latest News Start -->
<section class="section py-5">
    <div class="container">
        <div class="row justify-content-between align-items-center mb-5">
            <div class="col-lg-7">
                <span class="badge-elite mb-2">Latest Dispatches</span>
                <h2 class="mt-2 font-weight-bold text-dark" style="letter-spacing: -0.02em;">From the Xivig Journal</h2>
            </div>
            <div class="col-lg-5 text-lg-right mt-3 mt-lg-0">
                <a href="{{ url('/blog-list') }}" class="btn-elite btn-elite-outline btn-sm">
                    View All Articles <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="blog-item">
                    <div class="blog-img-wrap">
                        <img loading="lazy" src="{{ asset('xivig-blog/images/blog/1.jpg') }}" alt="Article 1">
                        <span class="blog-category-tag">Architecture</span>
                    </div>
                    <div class="blog-item-content">
                        <div class="blog-meta-pills">
                            <span><i class="far fa-calendar-alt"></i> Recent</span>
                            <span><i class="far fa-clock"></i> 4 min read</span>
                        </div>
                        <h4 class="font-weight-bold mb-2">
                            <a href="{{ url('/blog-list') }}" class="blog-title-link">Designing for Hyper-Scale in Modern Laravel</a>
                        </h4>
                        <p class="blog-excerpt mb-4">How caching strategies, decoupled events, and query pipelining keep latency under 15ms.</p>
                        <a href="{{ url('/blog-list') }}" class="btn-elite btn-elite-outline btn-sm mt-auto">Read Story</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="blog-item">
                    <div class="blog-img-wrap">
                        <img loading="lazy" src="{{ asset('xivig-blog/images/blog/2.jpg') }}" alt="Article 2">
                        <span class="blog-category-tag">UI Engineering</span>
                    </div>
                    <div class="blog-item-content">
                        <div class="blog-meta-pills">
                            <span><i class="far fa-calendar-alt"></i> Recent</span>
                            <span><i class="far fa-clock"></i> 5 min read</span>
                        </div>
                        <h4 class="font-weight-bold mb-2">
                            <a href="{{ url('/blog-list') }}" class="blog-title-link">Reactive Components with Zero JavaScript Bloat</a>
                        </h4>
                        <p class="blog-excerpt mb-4">Exploring modern micro-frontend principles and reactive server-driven component states.</p>
                        <a href="{{ url('/blog-list') }}" class="btn-elite btn-elite-outline btn-sm mt-auto">Read Story</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="blog-item">
                    <div class="blog-img-wrap">
                        <img loading="lazy" src="{{ asset('xivig-blog/images/blog/3.jpg') }}" alt="Article 3">
                        <span class="blog-category-tag">Security</span>
                    </div>
                    <div class="blog-item-content">
                        <div class="blog-meta-pills">
                            <span><i class="far fa-calendar-alt"></i> Recent</span>
                            <span><i class="far fa-clock"></i> 6 min read</span>
                        </div>
                        <h4 class="font-weight-bold mb-2">
                            <a href="{{ url('/blog-list') }}" class="blog-title-link">Hardening Fortify & Passkey Authentication</a>
                        </h4>
                        <p class="blog-excerpt mb-4">A comprehensive look at webauthn, TOTP rotation, and multi-tenant guard segmentation.</p>
                        <a href="{{ url('/blog-list') }}" class="btn-elite btn-elite-outline btn-sm mt-auto">Read Story</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Section Latest News End -->

<!-- Section Final CTA -->
<section class="py-5 section-partners">
    <div class="container">
        <div class="p-5 rounded-lg text-center" style="background: linear-gradient(135deg, rgba(79, 70, 229, 0.08) 0%, rgba(236, 72, 153, 0.06) 100%); border: 1px solid rgba(79, 70, 229, 0.15); border-radius: 24px;">
            <span class="badge-elite mb-3">Empowering Innovation</span>
            <h2 class="font-weight-bold text-dark mb-3">Experience the Elite Software Difference</h2>
            <p class="text-muted lead mb-4" style="max-width: 650px; margin: 0 auto;">Connect with our solutions architects today to discuss your next breakthrough project.</p>
            <a href="{{ url('/contact') }}" class="btn-elite btn-elite-primary">
                <i class="fas fa-envelope mr-2"></i> Get in Touch With Us
            </a>
        </div>
    </div>
</section>
@endsection
