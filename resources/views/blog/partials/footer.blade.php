<footer class="footer section bg-transparent border-0 shadow-none">
    <div class="container bg-transparent border-0 shadow-none">
        @php
            $useWidgetFooter = \App\Models\Widget::hasConfiguredFooterWidgets();
        @endphp

        <div class="row justify-content-between bg-transparent border-0 shadow-none">
            @if ($useWidgetFooter)
                @foreach (\App\Models\Widget::FOOTER_COLUMNS as $footerArea => $colClass)
                    @php $areaWidgets = \App\Models\Widget::getAreaWidgets($footerArea); @endphp
                    @if ($areaWidgets->isNotEmpty())
                        <div class="{{ $colClass }} bg-transparent border-0 shadow-none">
                            @include('blog.partials.widget-renderer', [
                                'area' => $footerArea,
                                'context' => 'footer',
                                'preloadedWidgets' => $areaWidgets,
                            ])
                        </div>
                    @endif
                @endforeach
            @else
                <div class="col-lg-3 col-sm-6 bg-transparent border-0 shadow-none">
                    <div class="footer-widget bg-transparent border-0 shadow-none p-0">
                        <div class="logo mb-3">
                            <span class="text-gradient font-weight-bold h3">Xivig</span>
                        </div>
                        <p class="text-muted small">Modern Elite Enterprise Framework built with Laravel 13 compatibility, Fortify, and Jetstream.</p>
                        <h6 class="mt-3"><a href="mailto:support@xivig.com"><i class="bi bi-envelope me-1 mr-1 text-primary"></i> support@xivig.com</a></h6>
                        <a href="tel:+23-345-67890"><span class="text-gradient font-weight-bold h5">+91 98367 26662</span></a>
                    </div>
                </div>
                @php
                    $footerPrimaryMenu = \App\Models\Menu::menuForLocation('footer');
                    $footerEcosystemMenu = \App\Models\Menu::menuForLocation('footer_ecosystem');
                    $footerPagesMenu = \App\Models\Menu::menuForLocation('footer_pages');
                    try {
                        $footerPages = \App\Models\Page::active()->orderBy('id')->take(6)->get();
                    } catch (\Throwable $e) {
                        $footerPages = collect();
                    }
                @endphp

                @include('blog.partials.footer-menu-column', [
                    'menu' => $footerPrimaryMenu,
                    'defaultTitle' => 'Platform',
                    'fallbackLinks' => [
                        ['title' => 'Home', 'url' => url('/')],
                        ['title' => 'About Us', 'url' => route('page.show', 'about-us')],
                        ['title' => 'Services', 'url' => url('/services')],
                        ['title' => 'Pricing Plans', 'url' => url('/pricing')],
                        ['title' => 'Portfolio', 'url' => url('/project')],
                    ],
                ])

                @include('blog.partials.footer-menu-column', [
                    'menu' => $footerEcosystemMenu,
                    'defaultTitle' => 'Ecosystem',
                    'fallbackLinks' => [
                        ['title' => 'Blog Magazine', 'url' => url('/blog-list')],
                        ['title' => 'All Articles', 'url' => url('/blog')],
                        ['title' => 'Commerce Store', 'url' => url('/ecomm')],
                        ['title' => 'Contact Support', 'url' => url('/contact')],
                        ['title' => 'Admin Portal', 'url' => url('/admin/login')],
                    ],
                ])

                @if ($footerPagesMenu)
                    @include('blog.partials.footer-menu-column', [
                        'menu' => $footerPagesMenu,
                        'defaultTitle' => 'Pages',
                        'fallbackLinks' => [],
                    ])
                @else
                    <div class="col-lg-2 col-md-6 col-sm-6 mb-4 mb-lg-0 bg-transparent border-0 shadow-none">
                        <div class="footer-widget bg-transparent border-0 shadow-none p-0">
                            <h4 class="footer-title mb-3">Pages</h4>
                            <ul class="list-unstyled footer-menu lh-35">
                                @forelse ($footerPages as $fPage)
                                    <li><a href="{{ route('page.show', $fPage->slug) }}">{{ $fPage->title }}</a></li>
                                @empty
                                    <li><a href="{{ route('page.show', 'about-us') }}">About Us</a></li>
                                    <li><a href="{{ url('/contact') }}">Contact</a></li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                @endif
                <div class="col-lg-3 col-md-6 col-sm-6 mb-4 mb-lg-0 bg-transparent border-0 shadow-none">
                    <div class="footer-widget bg-transparent border-0 shadow-none p-0">
                        <h4 class="footer-title mb-3">Stay Ahead</h4>
                        <p class="text-muted">Subscribe for architecture insights, software engineering masterclasses, and releases.</p>

                        <form action="{{ route('newsletter.subscribe') }}" method="POST" class="sub-form mt-3 bg-transparent border-0 shadow-none">
                            @csrf
                            <input type="hidden" name="source" value="website_footer">
                            <div class="position-relative w-100">
                                <input type="email" name="email" class="form-control form-control-elite" placeholder="Enter your email" style="border-radius: 999px; padding-right: 115px; height: 46px;" required>
                                <button type="submit" class="btn-elite btn-elite-primary btn-sm position-absolute" style="top: 50%; right: 5px; transform: translateY(-50%); border-radius: 999px; height: 36px; padding: 0 18px; display: inline-flex; align-items: center; justify-content: center; z-index: 5;">
                                    Subscribe
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <div class="footer-btm pt-4 mt-4">
            <div class="row align-items-center">
                <div class="col-lg-6 text-center text-lg-start text-lg-left mb-3 mb-lg-0">
                    <div class="copyright text-muted small">
                        &copy; {{ date('Y') }} <a href="{{ url('/') }}" class="footer-brand-link font-weight-bold">Xivig</a>. All rights reserved. Crafted for high performance.
                    </div>
                </div>
                <div class="col-lg-6 text-center text-lg-end text-lg-right">
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item">
                            <a href="https://facebook.com/xivig" class="footer-social-btn" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="https://twitter.com/xivig" class="footer-social-btn" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="https://github.com/xivig/" class="footer-social-btn" aria-label="GitHub"><i class="bi bi-github"></i></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="https://linkedin.com/company/xivig" class="footer-social-btn" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>

</div><!-- /.boxed-layout-wrapper -->

<!-- Essential Scripts -->
<script src="{{ asset('xivig-public/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('xivig-public/plugins/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('xivig-public/plugins/slick/slick.min.js') }}"></script>
<script src="{{ asset('xivig-public/plugins/magnific-popup/jquery.magnific-popup.min.js') }}"></script>
@vite(['resources/js/frontend/main.js'])
</body>
</html>
