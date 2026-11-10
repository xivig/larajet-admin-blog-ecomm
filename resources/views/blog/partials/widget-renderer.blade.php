@php
    $areaName = $area ?? 'sidebar';
    $isFooter = ($context ?? 'sidebar') === 'footer';
    $renderedWidgets = $preloadedWidgets ?? \App\Models\Widget::getAreaWidgets($areaName);
@endphp

@foreach($renderedWidgets as $widget)
    <div class="{{ $isFooter ? 'footer-widget bg-transparent border-0 shadow-none p-0' . (!$loop->last ? ' mb-4' : '') : 'sidebar-box widget-card-item mb-4 p-4 rounded-lg bg-white border shadow-sm' }}" id="widget-{{ $widget->id }}">
        @if(!empty($widget->title) && $widget->widget_type !== 'brand_info')
            @if($isFooter)
                <h4 class="footer-title mb-3">{{ $widget->title }}</h4>
            @else
                <h5 class="widget-title mb-3 font-weight-bold text-dark d-flex align-items-center">
                    <span class="widget-indicator mr-2" style="width: 4px; height: 18px; background: linear-gradient(135deg, #4f46e5, #06b6d4); border-radius: 2px; display: inline-block;"></span>
                    {{ $widget->title }}
                </h5>
            @endif
        @endif

        @switch($widget->widget_type)
            {{-- Brand & Contact Info --}}
            @case('brand_info')
                @php
                    $brandEmail = $widget->settings['email'] ?? null;
                    $brandPhone = $widget->settings['phone'] ?? null;
                @endphp
                <div class="widget-brand-info">
                    @if(!empty($widget->title))
                        <div class="logo mb-3">
                            <span class="text-gradient font-weight-bold h3">{{ $widget->title }}</span>
                        </div>
                    @endif
                    @if(!empty($widget->content))
                        <p class="text-muted small">{{ $widget->content }}</p>
                    @endif
                    @if(!empty($brandEmail))
                        <h6 class="mt-3">
                            <a href="mailto:{{ $brandEmail }}"><i class="bi bi-envelope me-1 mr-1 text-primary"></i> {{ $brandEmail }}</a>
                        </h6>
                    @endif
                    @if(!empty($brandPhone))
                        @php $telHref = preg_replace('/[^+\d]/', '', $brandPhone); @endphp
                        <a href="tel:{{ $telHref ?: $brandPhone }}">
                            <span class="text-gradient font-weight-bold h5">{{ $brandPhone }}</span>
                        </a>
                    @endif
                </div>
                @break

            {{-- Synchronized Menu Links (from Navigation Menu Builder) --}}
            @case('navigation_menu')
                @php
                    $menuLoc = $widget->settings['menu_location'] ?? 'footer';
                    $linkedMenu = \App\Models\Menu::menuForLocation($menuLoc);
                @endphp
                @include('blog.partials.footer-menu-list', [
                    'menu' => $linkedMenu,
                    'limit' => $widget->settings['limit'] ?? null,
                    'listClass' => $isFooter ? 'list-unstyled footer-menu lh-35 mb-0' : 'list-unstyled mb-0',
                ])
                @break

            {{-- Active Static Pages (with optional footer_pages menu override) --}}
            @case('pages_list')
                @php
                    $pagesLimit = (int) ($widget->settings['limit'] ?? 6);
                    $overrideLoc = $widget->settings['menu_location'] ?? 'footer_pages';
                    $overrideMenu = $overrideLoc ? \App\Models\Menu::menuForLocation($overrideLoc) : null;
                @endphp
                @if($overrideMenu)
                    @include('blog.partials.footer-menu-list', [
                        'menu' => $overrideMenu,
                        'limit' => $pagesLimit,
                        'listClass' => $isFooter ? 'list-unstyled footer-menu lh-35 mb-0' : 'list-unstyled mb-0',
                    ])
                @else
                    @php
                        try {
                            $widgetPages = \App\Models\Page::active()->orderBy('id')->take($pagesLimit)->get();
                        } catch (\Throwable) {
                            $widgetPages = collect();
                        }
                    @endphp
                    <ul class="{{ $isFooter ? 'list-unstyled footer-menu lh-35 mb-0' : 'list-unstyled mb-0' }}">
                        @forelse($widgetPages as $wPage)
                            <li><a href="{{ route('page.show', $wPage->slug) }}">{{ $wPage->title }}</a></li>
                        @empty
                            <li><a href="{{ url('/contact') }}">Contact</a></li>
                        @endforelse
                    </ul>
                @endif
                @break

            {{-- Popular, Recent, or Trending Posts --}}
            @case('popular')
            @case('recent_posts')
            @case('trending')
                @php
                    $limit = (int) ($widget->settings['limit'] ?? 4);
                    $postsQuery = \App\Models\Post::published();
                    if ($widget->widget_type === 'popular') {
                        $widgetPosts = $postsQuery->orderBy('views_count', 'desc')->take($limit)->get();
                    } elseif ($widget->widget_type === 'trending') {
                        $widgetPosts = $postsQuery->where('is_featured', 1)->take($limit)->get();
                    } else {
                        $widgetPosts = $postsQuery->latest('published_at')->take($limit)->get();
                    }
                @endphp
                @if($isFooter)
                    <ul class="list-unstyled footer-menu lh-35 mb-0">
                        @forelse($widgetPosts as $pItem)
                            <li><a href="{{ url('/blog/' . $pItem->slug) }}">{{ $pItem->title }}</a></li>
                        @empty
                            <li class="text-muted small">No articles published</li>
                        @endforelse
                    </ul>
                @else
                    <div class="widget-posts-list">
                        @foreach($widgetPosts as $pItem)
                            <div class="d-flex align-items-center mb-3">
                                <a href="{{ url('/blog/' . $pItem->slug) }}" class="flex-shrink-0 mr-3">
                                    <img src="{{ $pItem->image?->webp_url ?? $pItem->image?->url ?? asset('xivig-admin/img/blog-1-PTDVKJDt.webp') }}"
                                         alt="{{ $pItem->title }}"
                                         loading="lazy"
                                         class="rounded"
                                         style="width: 65px; height: 55px; object-fit: cover;"
                                         onerror="this.onerror=null;this.src='{{ asset('xivig-admin/img/avatar.png') }}';">
                                </a>
                                <div class="min-w-0 flex-grow-1" style="max-width: calc(100% - 80px);">
                                    @if($widget->widget_type === 'trending')
                                        <span class="badge badge-warning text-dark font-weight-bold text-truncate d-inline-block" style="max-width: 130px; font-size: 11px;">
                                            <i class="fas fa-bolt mr-1"></i> Trending
                                        </span>
                                    @else
                                        <span class="badge badge-light {{ $widget->widget_type === 'recent_posts' ? 'text-info' : 'text-primary' }} font-weight-bold text-truncate d-inline-block" style="max-width: 130px; font-size: 11px;">
                                            {{ $pItem->category?->name ?? 'Article' }}
                                        </span>
                                    @endif
                                    <h6 class="mb-0 mt-1 font-weight-bold" style="font-size: 13px; line-height: 1.35;">
                                        <a href="{{ url('/blog/' . $pItem->slug) }}" class="text-dark elite-title-clamp text-decoration-none">
                                            {{ $pItem->title }}
                                        </a>
                                    </h6>
                                    <div class="text-muted small mt-1" style="font-size: 11px;">
                                        @if($widget->widget_type === 'recent_posts')
                                            <i class="far fa-clock mr-1"></i> {{ $pItem->published_at ? $pItem->published_at->format('M d') : 'Recent' }}
                                        @elseif($widget->widget_type === 'popular')
                                            <i class="far fa-eye mr-1"></i> {{ $pItem->views_count ?? 0 }} · {{ $pItem->reading_time ?? 1 }}m
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                @break

            {{-- Newsletter Subscribe --}}
            @case('newsletter')
                <div class="widget-newsletter">
                    @if(!empty($widget->content))
                        <p class="text-muted {{ $isFooter ? '' : 'small mb-3' }}">{{ $widget->content }}</p>
                    @endif
                    @if($isFooter)
                        <form action="{{ route('newsletter.subscribe') }}" method="POST" class="sub-form mt-3 bg-transparent border-0 shadow-none">
                            @csrf
                            <input type="hidden" name="source" value="widget_{{ $areaName }}">
                            <div class="position-relative w-100">
                                <input type="email" name="email" class="form-control form-control-elite" placeholder="Enter your email" style="border-radius: 999px; padding-right: 115px; height: 46px;" required>
                                <button type="submit" class="btn-elite btn-elite-primary btn-sm position-absolute" style="top: 50%; right: 5px; transform: translateY(-50%); border-radius: 999px; height: 36px; padding: 0 18px; display: inline-flex; align-items: center; justify-content: center; z-index: 5;">
                                    Subscribe
                                </button>
                            </div>
                        </form>
                    @else
                        <form action="{{ route('newsletter.subscribe') }}" method="POST">
                            @csrf
                            <input type="hidden" name="source" value="widget_{{ $areaName }}">
                            <div class="form-group mb-2">
                                <input type="text" name="name" class="form-control form-control-sm" placeholder="Your name (optional)">
                            </div>
                            <div class="form-group mb-2">
                                <input type="email" name="email" class="form-control form-control-sm" placeholder="Enter work email..." required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm btn-block font-weight-bold w-100" style="background: linear-gradient(135deg, #4f46e5, #06b6d4); border: none;">
                                <i class="fas fa-paper-plane mr-1"></i> Subscribe
                            </button>
                        </form>
                    @endif
                </div>
                @break

            {{-- Categories --}}
            @case('categories')
                @php
                    $limit = min($widget->settings['limit'] ?? ($isFooter ? 6 : 10), 20);
                    $wCats = \App\Models\Category::withCount('posts')->take($limit)->get();
                @endphp
                @if($isFooter)
                    <ul class="list-unstyled footer-menu lh-35 mb-0">
                        @foreach($wCats as $cat)
                            <li>
                                <a href="{{ url('/category/' . $cat->slug) }}">{{ $cat->name }} ({{ $cat->posts_count }})</a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <ul class="list-unstyled mb-0 widget-categories-list">
                        @foreach($wCats as $cat)
                            <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <a href="{{ url('/category/' . $cat->slug) }}" class="text-dark font-weight-bold small text-decoration-none">
                                    <i class="far fa-folder text-primary mr-2"></i> {{ $cat->name }}
                                </a>
                                <span class="badge badge-light badge-pill text-muted small">{{ $cat->posts_count }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
                @break

            {{-- Tags Cloud --}}
            @case('tags')
                @php
                    $limit = min($widget->settings['limit'] ?? ($isFooter ? 12 : 25), 25);
                    $wTags = \App\Models\Tag::take($limit)->get();
                @endphp
                <div class="d-flex flex-wrap gap-2 widget-tags-cloud">
                    @foreach($wTags as $tag)
                        <a href="{{ url('/tag/' . $tag->id) }}" class="badge badge-light border text-muted px-2 py-1 mb-1 mr-1 small text-decoration-none" style="border-radius: 6px;">
                            #{{ $tag->name }}
                        </a>
                    @endforeach
                </div>
                @break

            {{-- Monthly Archive --}}
            @case('archive')
                @php
                    $limit = min($widget->settings['limit'] ?? 6, 12);
                    $monthExpr = \Illuminate\Support\Facades\DB::getDriverName() === 'sqlite'
                        ? "strftime('%Y-%m', published_at)"
                        : "DATE_FORMAT(published_at, '%Y-%m')";
                    $archives = \App\Models\Post::published()
                        ->whereNotNull('published_at')
                        ->selectRaw("{$monthExpr} as month, count(*) as count")
                        ->groupBy('month')
                        ->orderBy('month', 'desc')
                        ->take($limit)
                        ->get();
                @endphp
                <ul class="{{ $isFooter ? 'list-unstyled footer-menu lh-35 mb-0' : 'list-unstyled mb-0' }}">
                    @foreach($archives as $arc)
                        @if(!empty($arc->month))
                            <li class="{{ $isFooter ? '' : 'd-flex justify-content-between align-items-center py-2 border-bottom' }}">
                                <a href="{{ url('/blog?archive=' . $arc->month) }}" class="{{ $isFooter ? '' : 'text-dark font-weight-bold small text-decoration-none' }}">
                                    @if(!$isFooter)<i class="far fa-calendar-alt text-primary mr-2"></i>@endif
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $arc->month)->format('F Y') }}
                                    @if($isFooter) ({{ $arc->count }}) @endif
                                </a>
                                @if(!$isFooter)
                                    <span class="badge badge-light badge-pill text-muted small">{{ $arc->count }}</span>
                                @endif
                            </li>
                        @endif
                    @endforeach
                </ul>
                @break

            {{-- Advertisement Banner / Code --}}
            @case('ads')
                <div class="widget-ad-container {{ $isFooter ? '' : 'text-center' }}">
                    @if(!empty($widget->settings['ad_code']))
                        {!! $widget->settings['ad_code'] !!}
                    @elseif(!empty($widget->content))
                        {!! $widget->content !!}
                    @else
                        <div class="p-3 bg-light border rounded text-center">
                            <small class="text-uppercase text-muted font-weight-bold">Advertisement</small>
                            <div class="py-3 font-weight-bold text-primary">XIVIG High-Scale Partner</div>
                        </div>
                    @endif
                </div>
                @break

            {{-- Custom HTML --}}
            @case('custom_html')
                <div class="widget-custom-html">
                    {!! $widget->settings['ad_code'] ?? $widget->content !!}
                </div>
                @break
        @endswitch
    </div>
@endforeach
