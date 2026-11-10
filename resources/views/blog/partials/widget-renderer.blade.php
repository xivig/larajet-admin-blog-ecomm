@php
    $areaName = $area ?? 'sidebar';
    $renderedWidgets = \App\Models\Widget::getAreaWidgets($areaName);
@endphp

@foreach($renderedWidgets as $widget)
    <div class="sidebar-box widget-card-item mb-4 p-4 rounded-lg bg-white border shadow-sm" id="widget-{{ $widget->id }}">
        @if(!empty($widget->title))
            <h5 class="widget-title mb-3 font-weight-bold text-dark d-flex align-items-center">
                <span class="widget-indicator mr-2" style="width: 4px; height: 18px; background: linear-gradient(135deg, #4f46e5, #06b6d4); border-radius: 2px; display: inline-block;"></span>
                {{ $widget->title }}
            </h5>
        @endif

        @switch($widget->widget_type)
            {{-- 1. Popular Posts --}}
            @case('popular')
                @php
                    $limit = $widget->settings['limit'] ?? 4;
                    $popPosts = \App\Models\Post::published()->orderBy('views_count', 'desc')->take($limit)->get();
                @endphp
                <div class="widget-posts-list">
                    @foreach($popPosts as $pItem)
                        <div class="d-flex align-items-center mb-3">
                            <a href="{{ url('/blog/' . $pItem->slug) }}" class="flex-shrink-0 mr-3">
                                <img src="{{ $pItem->image?->webp_url ?? $pItem->image?->url ?? asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}" 
                                     alt="{{ $pItem->title }}" 
                                     loading="lazy"
                                     class="rounded" 
                                     style="width: 65px; height: 55px; object-fit: cover;"
                                     onerror="this.onerror=null;this.src='{{ asset('admin-dist/img/avatar.png') }}';">
                            </a>
                            <div class="min-w-0 flex-grow-1" style="max-width: calc(100% - 80px);">
                                <span class="badge badge-light text-primary font-weight-bold text-truncate d-inline-block" style="max-width: 130px; font-size: 11px;">
                                    {{ $pItem->category?->name ?? 'Article' }}
                                </span>
                                <h6 class="mb-0 mt-1 font-weight-bold" style="font-size: 13px; line-height: 1.35;">
                                    <a href="{{ url('/blog/' . $pItem->slug) }}" class="text-dark elite-title-clamp text-decoration-none">
                                        {{ $pItem->title }}
                                    </a>
                                </h6>
                                <div class="text-muted small mt-1" style="font-size: 11px;">
                                    <i class="far fa-eye mr-1"></i> {{ $pItem->views_count ?? 0 }} · {{ $pItem->reading_time ?? 1 }}m
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @break

            {{-- 2. Recent Posts --}}
            @case('recent_posts')
                @php
                    $limit = $widget->settings['limit'] ?? 4;
                    $recPosts = \App\Models\Post::published()->latest('published_at')->take($limit)->get();
                @endphp
                <div class="widget-posts-list">
                    @foreach($recPosts as $pItem)
                        <div class="d-flex align-items-center mb-3">
                            <a href="{{ url('/blog/' . $pItem->slug) }}" class="flex-shrink-0 mr-3">
                                <img src="{{ $pItem->image?->webp_url ?? $pItem->image?->url ?? asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}" 
                                     alt="{{ $pItem->title }}" 
                                     loading="lazy"
                                     class="rounded" 
                                     style="width: 65px; height: 55px; object-fit: cover;"
                                     onerror="this.onerror=null;this.src='{{ asset('admin-dist/img/avatar.png') }}';">
                            </a>
                            <div class="min-w-0 flex-grow-1" style="max-width: calc(100% - 80px);">
                                <span class="badge badge-light text-info font-weight-bold text-truncate d-inline-block" style="max-width: 130px; font-size: 11px;">
                                    {{ $pItem->category?->name ?? 'Article' }}
                                </span>
                                <h6 class="mb-0 mt-1 font-weight-bold" style="font-size: 13px; line-height: 1.35;">
                                    <a href="{{ url('/blog/' . $pItem->slug) }}" class="text-dark elite-title-clamp text-decoration-none">
                                        {{ $pItem->title }}
                                    </a>
                                </h6>
                                <div class="text-muted small mt-1" style="font-size: 11px;">
                                    <i class="far fa-clock mr-1"></i> {{ $pItem->published_at ? $pItem->published_at->format('M d') : 'Recent' }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @break

            {{-- 3. Trending Posts --}}
            @case('trending')
                @php
                    $limit = $widget->settings['limit'] ?? 4;
                    $trendPosts = \App\Models\Post::published()->where('is_featured', 1)->take($limit)->get();
                @endphp
                <div class="widget-posts-list">
                    @foreach($trendPosts as $pItem)
                        <div class="d-flex align-items-center mb-3">
                            <a href="{{ url('/blog/' . $pItem->slug) }}" class="flex-shrink-0 mr-3">
                                <img src="{{ $pItem->image?->webp_url ?? $pItem->image?->url ?? asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}" 
                                     alt="{{ $pItem->title }}" 
                                     loading="lazy"
                                     class="rounded" 
                                     style="width: 65px; height: 55px; object-fit: cover;"
                                     onerror="this.onerror=null;this.src='{{ asset('admin-dist/img/avatar.png') }}';">
                            </a>
                            <div class="min-w-0 flex-grow-1" style="max-width: calc(100% - 80px);">
                                <span class="badge badge-warning text-dark font-weight-bold text-truncate d-inline-block" style="max-width: 130px; font-size: 11px;">
                                    <i class="fas fa-bolt mr-1"></i> Trending
                                </span>
                                <h6 class="mb-0 mt-1 font-weight-bold" style="font-size: 13px; line-height: 1.35;">
                                    <a href="{{ url('/blog/' . $pItem->slug) }}" class="text-dark elite-title-clamp text-decoration-none">
                                        {{ $pItem->title }}
                                    </a>
                                </h6>
                            </div>
                        </div>
                    @endforeach
                </div>
                @break

            {{-- 4. Newsletter Subscribe --}}
            @case('newsletter')
                <div class="widget-newsletter">
                    @if(!empty($widget->content))
                        <p class="text-muted small mb-3">{{ $widget->content }}</p>
                    @endif
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
                </div>
                @break

            {{-- 5. Categories (Limited to 10) --}}
            @case('categories')
                @php
                    $limit = min($widget->settings['limit'] ?? 10, 10);
                    $wCats = \App\Models\Category::withCount('posts')->take($limit)->get();
                @endphp
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
                @break

            {{-- 6. Tags Cloud (Limited to 25) --}}
            @case('tags')
                @php
                    $limit = min($widget->settings['limit'] ?? 25, 25);
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

            {{-- 7. Monthly Archive --}}
            @case('archive')
                @php
                    $archives = \App\Models\Post::published()
                        ->selectRaw("strftime('%Y-%m', published_at) as month, count(*) as count")
                        ->groupBy('month')
                        ->orderBy('month', 'desc')
                        ->take(6)
                        ->get();
                @endphp
                <ul class="list-unstyled mb-0">
                    @foreach($archives as $arc)
                        <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <a href="{{ url('/blog?archive=' . $arc->month) }}" class="text-dark font-weight-bold small text-decoration-none">
                                <i class="far fa-calendar-alt text-primary mr-2"></i> {{ \Carbon\Carbon::createFromFormat('Y-m', $arc->month)->format('F Y') }}
                            </a>
                            <span class="badge badge-light badge-pill text-muted small">{{ $arc->count }}</span>
                        </li>
                    @endforeach
                </ul>
                @break

            {{-- 8. Advertisement Banner / Code --}}
            @case('ads')
                <div class="widget-ad-container text-center">
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

            {{-- 9. Custom HTML --}}
            @case('custom_html')
                <div class="widget-custom-html">
                    {!! $widget->content !!}
                </div>
                @break

        @endswitch
    </div>
@endforeach
