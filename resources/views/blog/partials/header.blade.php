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

                        <!-- Dark/Light Mode Switcher -->
                        <button type="button" class="theme-toggle-btn me-3 mr-3" id="themeToggleBtn" aria-label="Toggle dark/light mode" title="Toggle dark/light mode">
                            <i class="bi bi-moon-stars-fill theme-icon-dark"></i>
                            <i class="bi bi-sun-fill theme-icon-light"></i>
                        </button>

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
                            <a href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('header-user-logout').submit();" class="top-auth-link top-auth-logout">
                                <i class="bi bi-box-arrow-right me-1 mr-1"></i> Logout
                            </a>
                            <form id="header-user-logout" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        @else
                            <a href="{{ route('admin.login') }}" class="top-auth-link me-3 mr-3" title="Admin Control Center">
                                <i class="bi bi-shield-lock me-1 mr-1"></i> Admin Sign In
                            </a>
                            <a href="{{ route('login') }}" class="top-auth-link top-auth-pill me-2 mr-2">
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

                        @php
                            $headerMenu = \App\Models\Menu::menuForLocation('header');
                            try {
                                $frontPages = \App\Models\Page::active()->orderBy('id')->get();
                            } catch (\Throwable $e) {
                                $frontPages = collect();
                            }
                        @endphp

                        <div class="collapse navbar-collapse text-center" id="navbarsMain">
                            <ul class="navbar-nav ms-auto ml-auto align-items-center">
                                @if ($headerMenu)
                                    @foreach ($headerMenu->rootItems as $navItem)
                                        @php
                                            $target = $navItem->target ?? '_self';
                                            $isActive = $navItem->isActive();
                                        @endphp

                                        @if ($navItem->isBlogMegaTrigger())
                                            <li class="nav-item dropdown blog-dropdown {{ $isActive ? 'active' : '' }}">
                                                <a class="nav-link dropdown-toggle" href="#" id="dropdownBlog-{{ $navItem->id }}"
                                                   data-bs-toggle="dropdown" data-bs-display="static" aria-haspopup="true" aria-expanded="false">
                                                    {{ $navItem->title }} <i class="bi bi-chevron-down"></i>
                                                </a>
                                                @include('blog.partials.blog-mega-menu', ['toggleId' => 'dropdownBlog-' . $navItem->id])
                                            </li>
                                        @elseif ($navItem->isPagesPlaceholder())
                                            @if ($frontPages->isNotEmpty() || $navItem->children->isNotEmpty())
                                                <li class="nav-item dropdown pages-dropdown {{ $isActive ? 'active' : '' }}">
                                                    <a class="nav-link dropdown-toggle" href="#" id="dropdownPages-{{ $navItem->id }}"
                                                       data-bs-toggle="dropdown" data-bs-display="static" aria-haspopup="true" aria-expanded="false">
                                                        {{ $navItem->title }} <i class="bi bi-chevron-down"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-elite pages-dropdown-menu shadow-lg" aria-labelledby="dropdownPages-{{ $navItem->id }}">
                                                        <div class="dropdown-header blog-mega-heading text-uppercase fw-bold px-2 pt-1 pb-2" style="color: #6366f1;">
                                                            <i class="bi bi-file-earmark-richtext me-1"></i> Site Pages
                                                        </div>
                                                        @foreach ($frontPages as $frontPage)
                                                            <a class="dropdown-item py-2 px-2 rounded-3 d-flex align-items-center {{ request()->is('page/' . $frontPage->slug) ? 'active' : '' }}" href="{{ route('page.show', $frontPage->slug) }}">
                                                                <i class="bi bi-file-earmark-text text-primary me-3" style="font-size: 14px; width: 18px; text-align: center;"></i>
                                                                <div>
                                                                    <span class="d-block fw-bold" style="font-size: 13.5px;">{{ $frontPage->title }}</span>
                                                                    <small class="text-muted" style="font-size: 11px;">/page/{{ $frontPage->slug }}</small>
                                                                </div>
                                                            </a>
                                                        @endforeach
                                                        @foreach ($navItem->children as $childItem)
                                                            @php $childTarget = $childItem->target ?? '_self'; @endphp
                                                            <a class="dropdown-item py-2 px-2 rounded-3 d-flex align-items-center {{ $childItem->isActive() ? 'active' : '' }}"
                                                               href="{{ $childItem->resolvedUrl() }}"
                                                               target="{{ $childTarget }}"
                                                               @if($childTarget === '_blank') rel="noopener noreferrer" @endif>
                                                                <i class="bi bi-link-45deg text-primary me-3" style="font-size: 14px; width: 18px; text-align: center;"></i>
                                                                <div>
                                                                    <span class="d-block fw-bold" style="font-size: 13.5px;">{{ $childItem->title }}</span>
                                                                    <small class="text-muted" style="font-size: 11px;">{{ $childItem->url }}</small>
                                                                </div>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </li>
                                            @endif
                                        @elseif ($navItem->children->isNotEmpty())
                                            <li class="nav-item dropdown pages-dropdown {{ $isActive ? 'active' : '' }}">
                                                <a class="nav-link dropdown-toggle" href="{{ $navItem->resolvedUrl() }}" id="dropdownMenuItem-{{ $navItem->id }}"
                                                   data-bs-toggle="dropdown" data-bs-display="static" aria-haspopup="true" aria-expanded="false">
                                                    {{ $navItem->title }} <i class="bi bi-chevron-down"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-elite pages-dropdown-menu shadow-lg" aria-labelledby="dropdownMenuItem-{{ $navItem->id }}">
                                                    <div class="dropdown-header blog-mega-heading text-uppercase fw-bold px-2 pt-1 pb-2" style="color: #6366f1;">
                                                        <i class="bi bi-diagram-3 me-1"></i> {{ $navItem->title }}
                                                    </div>
                                                    @if (!in_array(trim((string) $navItem->url), ['', '#'], true))
                                                        <a class="dropdown-item py-2 px-2 rounded-3 d-flex align-items-center {{ $navItem->isActive() ? 'active' : '' }}"
                                                           href="{{ $navItem->resolvedUrl() }}"
                                                           target="{{ $target }}"
                                                           @if($target === '_blank') rel="noopener noreferrer" @endif>
                                                            <i class="bi bi-compass text-primary me-3" style="font-size: 14px; width: 18px; text-align: center;"></i>
                                                            <div>
                                                                <span class="d-block fw-bold" style="font-size: 13.5px;">All {{ $navItem->title }}</span>
                                                                <small class="text-muted" style="font-size: 11px;">{{ $navItem->url }}</small>
                                                            </div>
                                                        </a>
                                                    @endif
                                                    @foreach ($navItem->children as $childItem)
                                                        @php $childTarget = $childItem->target ?? '_self'; @endphp
                                                        <a class="dropdown-item py-2 px-2 rounded-3 d-flex align-items-center {{ $childItem->isActive() ? 'active' : '' }}"
                                                           href="{{ $childItem->resolvedUrl() }}"
                                                           target="{{ $childTarget }}"
                                                           @if($childTarget === '_blank') rel="noopener noreferrer" @endif>
                                                            <i class="bi bi-arrow-right-short text-primary me-3" style="font-size: 15px; width: 18px; text-align: center;"></i>
                                                            <div>
                                                                <span class="d-block fw-bold" style="font-size: 13.5px;">{{ $childItem->title }}</span>
                                                                <small class="text-muted" style="font-size: 11px;">{{ $childItem->url }}</small>
                                                            </div>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </li>
                                        @else
                                            <li class="nav-item {{ $isActive ? 'active' : '' }}">
                                                <a class="nav-link"
                                                   href="{{ $navItem->resolvedUrl() }}"
                                                   target="{{ $target }}"
                                                   @if($target === '_blank') rel="noopener noreferrer" @endif>
                                                    {{ $navItem->title }}
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                @else
                                    <li class="nav-item {{ request()->is('/') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ url('/') }}">Home</a>
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
                                    <li class="nav-item dropdown blog-dropdown {{ request()->is('blog*') ? 'active' : '' }}">
                                        <a class="nav-link dropdown-toggle" href="#" id="dropdownBlog"
                                           data-bs-toggle="dropdown" data-bs-display="static" aria-haspopup="true" aria-expanded="false">
                                            Blog <i class="bi bi-chevron-down"></i>
                                        </a>
                                        @include('blog.partials.blog-mega-menu', ['toggleId' => 'dropdownBlog'])
                                    </li>
                                    @if ($frontPages->isNotEmpty())
                                        <li class="nav-item dropdown pages-dropdown {{ request()->is('page*') ? 'active' : '' }}">
                                            <a class="nav-link dropdown-toggle" href="#" id="dropdownPages"
                                               data-bs-toggle="dropdown" data-bs-display="static" aria-haspopup="true" aria-expanded="false">
                                                Pages <i class="bi bi-chevron-down"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-elite pages-dropdown-menu shadow-lg" aria-labelledby="dropdownPages">
                                                <div class="dropdown-header blog-mega-heading text-uppercase fw-bold px-2 pt-1 pb-2" style="color: #6366f1;">
                                                    <i class="bi bi-file-earmark-richtext me-1"></i> Site Pages
                                                </div>
                                                @foreach ($frontPages as $frontPage)
                                                    <a class="dropdown-item py-2 px-2 rounded-3 d-flex align-items-center {{ request()->is('page/' . $frontPage->slug) ? 'active' : '' }}" href="{{ route('page.show', $frontPage->slug) }}">
                                                        <i class="bi bi-file-earmark-text text-primary me-3" style="font-size: 14px; width: 18px; text-align: center;"></i>
                                                        <div>
                                                            <span class="d-block fw-bold" style="font-size: 13.5px;">{{ $frontPage->title }}</span>
                                                            <small class="text-muted" style="font-size: 11px;">/page/{{ $frontPage->slug }}</small>
                                                        </div>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </li>
                                    @endif
                                    <li class="nav-item {{ request()->is('ecomm') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ url('/ecomm') }}">Store</a>
                                    </li>
                                    <li class="nav-item {{ request()->is('contact') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ url('/contact') }}">Contact</a>
                                    </li>
                                @endif
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
