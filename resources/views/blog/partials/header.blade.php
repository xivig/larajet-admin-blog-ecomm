<body>

<!-- Header Start -->
<header class="navigation">
    <div class="header-top">
        <div class="container">
            <div class="row justify-content-between align-items-center">
                <div class="col-lg-3 col-md-4">
                    <div class="header-top-socials text-center text-lg-left text-md-left">
                        <a href="https://www.facebook.com/xivig" aria-label="facebook" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://twitter.com/xivig" aria-label="twitter" target="_blank"><i class="fab fa-twitter"></i></a>
                        <a href="https://github.com/xivig/" aria-label="github" target="_blank"><i class="fab fa-github"></i></a>
                        <span class="badge-elite badge-elite-dark ml-2 py-0 px-2" style="font-size: 11px;">
                            <span class="badge-dot pulse"></span> v13 Engine
                        </span>
                    </div>
                </div>
                <div class="col-lg-9 col-md-8 text-center text-lg-right text-md-right">
                    <div class="header-top-info mb-2 mb-md-0 d-inline-block">
                        <a href="tel:+23-345-67890"><i class="fas fa-phone-alt mr-1"></i> <span>+23-345-67890</span></a>
                        <a href="mailto:support@xivig.com"><i class="fas fa-envelope mr-1"></i> <span>support@xivig.com</span></a>
                    </div>
                    <div class="d-inline-block ml-3">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-elite btn-elite-primary btn-sm py-1 px-3" style="font-size: 12px;">
                                <i class="fas fa-user-circle mr-1"></i> Dashboard
                            </a>
                            <a href="{{ route('logout') }}" class="text-white-50 ml-2" style="font-size: 12px;">Logout</a>
                        @else
                            <a href="{{ route('login') }}" class="text-white-50 mr-2" style="font-size: 12px;">
                                <i class="fas fa-sign-in-alt mr-1"></i> Sign In
                            </a>
                            <a href="{{ route('register') }}" class="btn-elite btn-elite-primary btn-sm py-1 px-3" style="font-size: 12px;">
                                Get Started
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="navbar">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <nav class="navbar navbar-expand-lg px-0 py-3">
                        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                            <span class="text-gradient font-weight-bold">Xivig</span>
                        </a>

                        <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarsMain"
                                aria-controls="navbarsMain" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="fa fa-bars text-dark"></span>
                        </button>

                        <div class="collapse navbar-collapse text-center" id="navbarsMain">
                            <ul class="navbar-nav ml-auto align-items-center">
                                <li class="nav-item {{ request()->is('/') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ url('/') }}">Home</a>
                                </li>
                                <li class="nav-item {{ request()->is('about') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ url('/about') }}">About</a>
                                </li>
                                <li class="nav-item {{ request()->is('services') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ url('/services') }}">Services</a>
                                </li>
                                <li class="nav-item {{ request()->is('pricing') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ url('/pricing') }}">Pricing</a>
                                </li>
                                <li class="nav-item {{ request()->is('project') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ url('/project') }}">Portfolio</a>
                                </li>
                                <li class="nav-item dropdown {{ request()->is('blog*') ? 'active' : '' }}">
                                    <a class="nav-link dropdown-toggle" href="#" id="dropdownBlog" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Blog <i class="fas fa-chevron-down small ml-1"></i>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownBlog">
                                        <li><a class="dropdown-item" href="{{ url('/blog-list') }}">Blog Magazine</a></li>
                                        <li><a class="dropdown-item" href="{{ url('/blog') }}">All Articles</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item {{ request()->is('ecomm') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ url('/ecomm') }}">Store</a>
                                </li>
                                <li class="nav-item {{ request()->is('contact') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ url('/contact') }}">Contact</a>
                                </li>
                            </ul>

                            <div class="my-2 my-md-0 ml-lg-4 text-center">
                                <a href="{{ url('/contact') }}" class="btn-elite btn-elite-primary btn-sm">Get a Quote</a>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- Header Close -->
