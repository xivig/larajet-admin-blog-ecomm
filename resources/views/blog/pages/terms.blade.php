@extends('layouts.blog')

@section('content')
<section class="page-title py-5 bg-light border-bottom">
    <div class="container text-center">
        <span class="badge badge-pill badge-secondary px-3 py-2 text-uppercase mb-2 font-weight-bold">Legal Agreement</span>
        <h1 class="display-5 font-weight-bold text-dark mb-2">{{ $page->title }}</h1>
        <p class="text-muted small">Last updated: {{ now()->format('F d, Y') }}</p>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="card border-0 shadow-sm rounded-lg p-4 p-md-5 bg-white">
                    <div class="legal-body-content text-dark" style="font-size: 15.5px; line-height: 1.8;">
                        @if(!empty($page->content))
                            {!! $page->content !!}
                        @else
                            <h3>1. Acceptance of Terms</h3>
                            <p>By accessing or utilizing our platform, services, APIs, and published media, you agree to be bound by these Terms and Conditions.</p>

                            <h3>2. Intellectual Property Rights</h3>
                            <p>All content, code snippets, visual designs, trademarks, and documentation published across this domain are the intellectual property of XIVIG and its contributors.</p>

                            <h3>3. Code Examples & Software License</h3>
                            <p>Technical blueprints and code samples provided in articles are made available under standard open source licensing (MIT) unless specified otherwise.</p>

                            <h3>4. Limitation of Liability</h3>
                            <p>Information is provided for educational and architectural planning purposes without warranties of merchantability or fitness for a particular implementation.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
