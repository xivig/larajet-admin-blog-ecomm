@extends('layouts.home')
@section('title', '404 - Page Not Found | Xivig')
@section('content')

<section class="section py-5 text-center" style="min-height: 70vh; display: flex; align-items: center; justify-content: center; background: #090d16;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <span class="badge-elite badge-elite-dark mb-4">
                    <span class="badge-dot pulse"></span> Error 404 &bull; Resource Missing
                </span>
                <h1 class="display-1 font-weight-bold text-gradient mb-3" style="font-size: 8rem; line-height: 1;">404</h1>
                <h2 class="text-white font-weight-bold mb-3">Page Not Found</h2>
                <p class="text-muted lead mb-5" style="max-width: 550px; margin: 0 auto;">
                    The endpoint, document, or route you are attempting to access does not exist or may have been relocated.
                </p>
                <div class="d-flex justify-content-center" style="gap: 16px;">
                    <a href="{{ url('/') }}" class="btn-elite btn-elite-primary">
                        <i class="fas fa-home mr-2"></i> Return to Homepage
                    </a>
                    <a href="{{ url('/blog-list') }}" class="btn-elite btn-elite-glass">
                        <i class="fas fa-newspaper mr-2"></i> Browse Journal
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
