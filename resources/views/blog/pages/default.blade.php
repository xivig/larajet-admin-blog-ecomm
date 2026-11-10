@extends('layouts.blog')

@section('content')
<section class="page-title py-5 bg-light border-bottom">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto text-center">
                <nav aria-label="breadcrumb" class="mb-2">
                    <ol class="breadcrumb justify-content-center bg-transparent p-0 small text-uppercase font-weight-bold">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-muted"><i class="fas fa-home mr-1"></i> Home</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">{{ $page->title }}</li>
                    </ol>
                </nav>
                <h1 class="font-weight-bold display-5 text-dark mb-3">{{ $page->title }}</h1>
                @if(!empty($page->excerpt))
                    <p class="lead text-muted mx-auto" style="max-width: 750px;">{{ $page->excerpt }}</p>
                @endif
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-lg p-4 p-md-5 bg-white">
                    <article class="article-body-content text-dark" style="font-size: 16px; line-height: 1.8;">
                        {!! $page->content !!}
                    </article>
                </div>
            </div>
            <div class="col-lg-4">
                @include('blog.partials.sidebar')
            </div>
        </div>
    </div>
</section>
@endsection
