<body class="boxed-layout-body">

<div class="boxed-layout-wrapper">
<!-- Header Start -->
<header class="navigation">
    <div class="header-top">
        <div class="container">
            <div class="row align-items-center">
                <!-- Left: Social Icons & Engine Badge -->
                <div class="col-lg-5 col-md-5 d-flex align-items-center">
                    <div class="header-top-socials">
                        <a href="https://www.facebook.com/xivig" aria-label="facebook" target="_blank"><i class="bi bi-facebook"></i></a>
                        <a href="https://twitter.com/xivig" aria-label="twitter" target="_blank"><i class="bi bi-twitter-x"></i></a>
                        <a href="https://github.com/xivig/" aria-label="github" target="_blank"><i class="bi bi-github"></i></a>
                    </div>
                    <span class="badge-v13 ms-3 ml-3">
                        <span class="badge-dot pulse"></span> v13 Engine
                    </span>
                </div>

                <!-- Right: Contact Info & Auth -->
                <div class="col-lg-7 col-md-7 text-end text-right">
                    <div class="d-inline-flex align-items-center header-top-right">
                        <a href="tel:+23-345-67890" class="top-util-link me-3 mr-3">
                            <i class="bi bi-telephone me-1 mr-1 text-primary-light"></i> +23-345-67890
                        </a>
                        <a href="mailto:support@xivig.com" class="top-util-link me-3 mr-3">
                            <i class="bi bi-envelope me-1 mr-1 text-primary-light"></i> support@xivig.com
                        </a>

                        <span class="top-util-divider me-3 mr-3"></span>

                        @if(Auth::guard('admin')->check())
                            <a href="{{ route('admin.dashboard') }}" class="top-auth-link me-3 mr-3">
                                <i class="bi bi-shield-lock me-1 mr-1"></i> Admin Console
                            </a>
                            <a href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('header-admin-logout').submit();" class="top-auth-link top-auth-logout">
                                <i class="bi bi-box-arrow-right me-1 mr-1"></i> Logout
                            </a>
                            <form id="header-admin-logout" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        @elseif(Auth::check())
                            <a href="{{ url('/dashboard') }}" class="top-auth-link me-3 mr-3">
                                <i class="bi bi-speedometer2 me-1 mr-1"></i> Dashboard
                            </a>
                            <a href="{{ route('logout') }}" class="top-auth-link top-auth-logout">
                                <i class="bi bi-box-arrow-right me-1 mr-1"></i> Logout
                            </a>
                        @else
                            <a href="{{ route('admin.login') }}" class="top-auth-link me-3 mr-3" title="Admin Control Center">
                                <i class="bi bi-shield-lock me-1 mr-1"></i> Admin Sign In
                            </a>
                            <a href="{{ route('login') }}" class="top-auth-link me-3 mr-3">
                                <i class="bi bi-box-arrow-in-right me-1 mr-1"></i> Sign In
                            </a>
                            <a href="{{ route('register') }}" class="top-auth-link top-auth-highlight">
                                <i class="bi bi-person-plus me-1 mr-1"></i> Register
                            </a>
                        @endif
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
                            <span class="brand-text-gradient font-weight-bold">Xivig</span>
                        </a>

                        <button class="navbar-toggler collapsed" type="button" 
                                data-bs-toggle="collapse" data-bs-target="#navbarsMain"
                                data-toggle="collapse" data-target="#navbarsMain"
                                aria-controls="navbarsMain" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="bi bi-list text-white" style="font-size: 1.5rem;"></span>
                        </button>

                        <div class="collapse navbar-collapse text-center" id="navbarsMain">
                            <ul class="navbar-nav ms-auto ml-auto align-items-center">
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
                                    <a class="nav-link dropdown-toggle" href="#" id="dropdownBlog" 
                                       data-bs-toggle="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Blog <i class="bi bi-chevron-down small ms-1 ml-1"></i>
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

                            <div class="my-2 my-md-0 ms-lg-4 ml-lg-4 text-center">
                                <a href="{{ url('/contact') }}" class="btn-elite btn-elite-primary btn-sm">
                                    <i class="bi bi-send me-2 mr-2" style="font-size: 11px;"></i> Get a Quote
                                </a>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- Header Close -->
