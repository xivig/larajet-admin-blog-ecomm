<style>
    /* Absolute reset for footer column cards/boxes */
    footer.footer,
    footer.footer .container,
    footer.footer .row,
    footer.footer [class*="col-"],
    footer.footer .footer-widget,
    footer.footer .widget,
    [data-bs-theme="dark"] footer.footer [class*="col-"],
    [data-bs-theme="dark"] footer.footer .footer-widget,
    [data-bs-theme="dark"] footer.footer .widget,
    [data-theme="dark"] footer.footer [class*="col-"],
    [data-theme="dark"] footer.footer .footer-widget,
    [data-theme="dark"] footer.footer .widget,
    [data-bs-theme="light"] footer.footer [class*="col-"],
    [data-bs-theme="light"] footer.footer .footer-widget,
    [data-bs-theme="light"] footer.footer .widget,
    [data-theme="light"] footer.footer [class*="col-"],
    [data-theme="light"] footer.footer .footer-widget,
    [data-theme="light"] footer.footer .widget {
        background: transparent !important;
        background-color: transparent !important;
        border: none !important;
        box-shadow: none !important;
        outline: none !important;
    }
</style>
<footer class="footer section bg-transparent border-0 shadow-none">
    <div class="container bg-transparent border-0 shadow-none">
        <div class="row bg-transparent border-0 shadow-none">
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4 mb-lg-0 bg-transparent border-0 shadow-none">
                <div class="footer-widget bg-transparent border-0 shadow-none p-0">
                    <h4 class="footer-title mb-4">Platform</h4>
                    <ul class="list-unstyled footer-menu lh-35">
                        <li><a href="{{ url('/') }}">Home</a></li>
                        <li><a href="{{ url('/about') }}">About Us</a></li>
                        <li><a href="{{ url('/services') }}">Services & Capabilities</a></li>
                        <li><a href="{{ url('/pricing') }}">Pricing Plans</a></li>
                        <li><a href="{{ url('/project') }}">Portfolio Showcases</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 col-sm-6 mb-4 mb-lg-0 bg-transparent border-0 shadow-none">
                <div class="footer-widget bg-transparent border-0 shadow-none p-0">
                    <h4 class="footer-title mb-4">Ecosystem</h4>
                    <ul class="list-unstyled footer-menu lh-35">
                        <li><a href="{{ url('/blog-list') }}">Blog Magazine</a></li>
                        <li><a href="{{ url('/blog') }}">All Articles</a></li>
                        <li><a href="{{ url('/ecomm') }}">Commerce Store</a></li>
                        <li><a href="{{ url('/contact') }}">Contact Support</a></li>
                        <li><a href="{{ url('/admin/login') }}">Admin Portal</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 mb-4 mb-lg-0 bg-transparent border-0 shadow-none">
                <div class="footer-widget bg-transparent border-0 shadow-none p-0">
                    <h4 class="footer-title mb-4">Stay Ahead</h4>
                    <p class="text-muted">Subscribe for architecture insights, software engineering masterclasses, and releases.</p>

                    <form action="#" class="sub-form mt-3 bg-transparent border-0 shadow-none" onsubmit="event.preventDefault(); alert('Thank you for subscribing to Xivig insights!');">
                        <div class="input-group">
                            <input type="email" class="form-control form-control-elite" placeholder="Enter your email" required>
                            <div class="input-group-append ml-2">
                                <button type="submit" class="btn-elite btn-elite-primary btn-sm">Subscribe</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

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
        </div>

        <div class="footer-btm pt-5 mt-5">
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
<script src="{{ asset('xivig-blog/plugins/jquery/jquery.js') }}"></script>
<script src="{{ asset('xivig-blog/plugins/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('xivig-blog/plugins/slick/slick.min.js') }}"></script>
<script src="{{ asset('xivig-blog/plugins/magnific-popup/magnific-popup.js') }}"></script>
<script src="{{ asset('xivig-blog/js/script.js') }}"></script>
<script src="{{ asset('xivig-blog/js/theme-switcher.js') }}"></script>
</body>
</html>
