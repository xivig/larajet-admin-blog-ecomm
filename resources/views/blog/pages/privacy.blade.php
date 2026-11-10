@extends('layouts.blog')

@section('content')
<section class="page-title py-5 bg-light border-bottom">
    <div class="container text-center">
        <span class="badge badge-pill badge-secondary px-3 py-2 text-uppercase mb-2 font-weight-bold">Compliance & Trust</span>
        <h1 class="display-5 font-weight-bold text-dark mb-2">{{ $page->title }}</h1>
        <p class="text-muted small">Effective date: {{ now()->format('F d, Y') }} · Version 2.4</p>
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
                            <h3>1. Information We Collect</h3>
                            <p>We respect your privacy and process personal information strictly in accordance with global data privacy frameworks including the General Data Protection Regulation (GDPR) and the California Consumer Privacy Act (CCPA).</p>
                            
                            <h3>2. How We Use Data</h3>
                            <p>Information collected through newsletter subscriptions, comments, and interactions is utilized solely to deliver customized updates, maintain security, and optimize performance.</p>

                            <h3>3. Cookies and Analytics</h3>
                            <p>We leverage privacy-conscious telemetry to monitor site health and search efficacy. No tracking identifiers are sold or shared with external advertising brokers.</p>

                            <h3>4. Your Privacy Rights</h3>
                            <p>You retain the unconditional right to access, rectify, export, or erase your stored data at any time by contacting our privacy compliance desk.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
