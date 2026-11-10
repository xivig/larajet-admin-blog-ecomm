@extends('home')
@section('title', ($page->meta_title ?: $page->title) . ' - Xivig')
@section('page-title', 'About Us')
@section('page-heading', $page->title ?: 'Our Mission & Architecture')

@section('content')
<!-- Section About Start -->
<section class="section position-relative" style="padding: 36px 0 44px;">
    <div class="container">
        <div class="row align-items-center mb-4">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="about-item pr-lg-4 pe-lg-4">
                    <span class="badge-elite mb-2">Our Origins</span>
                    <h2 class="mt-2 mb-3 font-weight-bold fw-bold text-dark display-5" style="letter-spacing: -0.02em;">
                        We are a dynamic engineering collective
                    </h2>
                    @if (!empty($page->content))
                        <div class="text-muted lead mb-4 about-dynamic-copy" style="font-size: 16px; line-height: 1.75;">
                            {!! $page->content !!}
                        </div>
                    @else
                        <p class="text-muted lead mb-3">
                            Founded on principles of simplicity and modularity, Xivig crafts software ecosystems that empower developers and enterprises to scale without technical debt.
                        </p>
                        <p class="text-muted mb-4">
                            We continuously evaluate cutting-edge framework architectures, containerization patterns, and developer ergonomics to deliver solutions that stand the test of time.
                        </p>
                    @endif
                    <a href="{{ url('/services') }}" class="btn-elite btn-elite-primary">
                        Explore Our Services <i class="fas fa-arrow-right ms-2 ml-2" style="font-size: 11px;"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <img loading="lazy" src="{{ asset('xivig-public/images/about/engineering-team-collaboration.jpg') }}" alt="Xivig Engineering Team Collaboration" class="img-fluid rounded-lg shadow-lg" style="border-radius: 20px;">
            </div>
        </div>

        <!-- Core Values -->
        <div class="row mt-4 pt-3">
            <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                <div class="intro-item h-100">
                    <div class="icon-box-elite"><i class="ti-light-bulb"></i></div>
                    <h4 class="font-weight-bold fw-bold text-dark mb-3">Relentless Innovation</h4>
                    <p class="text-muted mb-0">Adopting early modern standards to ensure your platform remains forward-compatible and future-proof.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                <div class="intro-item h-100">
                    <div class="icon-box-elite"><i class="ti-shield"></i></div>
                    <h4 class="font-weight-bold fw-bold text-dark mb-3">Enterprise Security</h4>
                    <p class="text-muted mb-0">Multi-layer authentication, token isolation, and automated validation guarding every endpoint.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="intro-item h-100">
                    <div class="icon-box-elite"><i class="ti-pulse"></i></div>
                    <h4 class="font-weight-bold fw-bold text-dark mb-3">High Throughput</h4>
                    <p class="text-muted mb-0">Optimized database query flows, deterministic caching, and lightning-fast page response times.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Section About End -->
@endsection
