@extends('layouts.blog')

@section('content')
<section class="page-title py-5 text-white" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); padding: 80px 0;">
    <div class="container text-center">
        <span class="badge badge-pill badge-primary px-3 py-2 text-uppercase mb-3 font-weight-bold" style="background: rgba(255,255,255,0.2);">Company Overview</span>
        <h1 class="display-5 font-weight-bold mb-3">{{ $page->title }}</h1>
        <p class="lead mx-auto text-light opacity-80" style="max-width: 700px;">
            {{ $page->excerpt ?: 'Pioneering intelligent digital media, cloud system designs, and technical publications.' }}
        </p>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <span class="text-uppercase text-primary font-weight-bold small letter-spacing-1">Our Mission</span>
                <h2 class="font-weight-bold mb-3">Crafting High-Performance Publishing & Cloud Architectures</h2>
                <div class="text-secondary" style="font-size: 16px; line-height: 1.8;">
                    {!! $page->content ?: '<p>We build resilient publishing software designed for speed, search visibility, and exceptional developer experience.</p>' !!}
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row text-center">
                    <div class="col-6 mb-4">
                        <div class="p-4 bg-light rounded-lg border shadow-sm">
                            <h2 class="font-weight-bold text-primary mb-1">50M+</h2>
                            <span class="text-muted small">Monthly Pageviews</span>
                        </div>
                    </div>
                    <div class="col-6 mb-4">
                        <div class="p-4 bg-light rounded-lg border shadow-sm">
                            <h2 class="font-weight-bold text-success mb-1">99.99%</h2>
                            <span class="text-muted small">System Uptime</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-4 bg-light rounded-lg border shadow-sm">
                            <h2 class="font-weight-bold text-warning mb-1">120+</h2>
                            <span class="text-muted small">Countries Reached</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-4 bg-light rounded-lg border shadow-sm">
                            <h2 class="font-weight-bold text-info mb-1">15k+</h2>
                            <span class="text-muted small">Subscribers</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
