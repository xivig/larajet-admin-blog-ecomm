@extends('layouts.blog')

@section('content')
<!-- Hero Section -->
<section class="py-5 text-center text-white position-relative" style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%); padding: 100px 0;">
    <div class="container position-relative" style="z-index: 2;">
        <span class="badge badge-pill badge-primary px-3 py-2 text-uppercase mb-3 font-weight-bold" style="background: rgba(99, 102, 241, 0.3); border: 1px solid rgba(165, 180, 252, 0.4);">
            Enterprise Landing Platform
        </span>
        <h1 class="display-4 font-weight-bold mb-4">{{ $page->title }}</h1>
        <p class="lead mx-auto mb-5 text-light opacity-80" style="max-width: 800px; font-size: 1.25rem;">
            {{ $page->excerpt ?: 'Empowering engineering teams with reactive architectures, scalable microservices, and next-generation cloud infrastructure.' }}
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ url('/blog') }}" class="btn btn-primary btn-lg px-4 font-weight-bold rounded-pill mr-3" style="background: linear-gradient(135deg, #4f46e5, #06b6d4); border: none;">
                Explore Articles <i class="fas fa-arrow-right ml-2"></i>
            </a>
            <a href="{{ url('/page/contact-us') }}" class="btn btn-outline-light btn-lg px-4 font-weight-bold rounded-pill">
                Get in Touch
            </a>
        </div>
    </div>
</section>

<!-- Content Features Section -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-lg-10 mx-auto">
                <div class="card border-0 shadow-lg rounded-xl p-4 p-md-5">
                    <div class="landing-rich-content" style="font-size: 17px; line-height: 1.85;">
                        {!! $page->content !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Lead Capture Section -->
<section class="py-5 bg-light border-top">
    <div class="container text-center py-4">
        <div class="col-lg-8 mx-auto">
            <h3 class="font-weight-bold mb-2">Ready to Elevate Your Engineering Systems?</h3>
            <p class="text-muted mb-4">Join hundreds of high-growth technology teams building on our cloud blueprints.</p>
            <form action="{{ route('newsletter.subscribe') }}" method="POST" class="d-flex justify-content-center mx-auto" style="max-width: 500px;">
                @csrf
                <input type="hidden" name="source" value="landing_{{ $page->slug }}">
                <input type="email" name="email" class="form-control mr-2" placeholder="Enter work email..." required style="height: 48px; border-radius: 999px;">
                <button type="submit" class="btn btn-primary px-4 font-weight-bold" style="height: 48px; border-radius: 999px; white-space: nowrap; background: linear-gradient(135deg, #4f46e5, #06b6d4); border: none;">
                    Get Started
                </button>
            </form>
        </div>
    </div>
</section>
@endsection
