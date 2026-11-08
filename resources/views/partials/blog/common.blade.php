<section class="page-title bg-1" style="background: #090d16; padding: 110px 0 60px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="block text-center">
                    <span class="badge-elite badge-elite-dark mb-3">
                        <span class="badge-dot pulse"></span> @yield('page-title', 'Overview')
                    </span>
                    <h1 class="text-white font-weight-bold mb-4 display-4" style="letter-spacing: -0.03em;">@yield('page-heading')</h1>
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item"><a href="{{ url('/') }}" class="text-muted small">Home</a></li>
                        <li class="list-inline-item"><span class="text-muted small">/</span></li>
                        <li class="list-inline-item text-primary font-weight-bold small">@yield('page-title')</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
