@php
    $sidebarPopular = $sidebarPopular ?? \App\Models\Post::published()->with(['category', 'image'])->orderByDesc('views_count')->take(4)->get();
    $sidebarRecent = $sidebarRecent ?? \App\Models\Post::published()->with(['category', 'image'])->latest('published_at')->take(4)->get();
    $sidebarComments = $sidebarComments ?? \App\Models\Comment::with(['user', 'post'])->latest()->take(4)->get();
    $sidebarCategories = $sidebarCategories ?? ($categories ?? \App\Models\Category::withCount('posts')->orderByDesc('posts_count')->take(10)->get());
    $sidebarTags = $sidebarTags ?? ($tags ?? \App\Models\Tag::withCount('posts')->orderByDesc('posts_count')->take(25)->get());
@endphp

<aside class="blog-sidebar-elite">
    <!-- Search Widget -->
    <div class="sidebar-widget-card mb-4">
        <h5 class="sidebar-card-title">
            <i class="fas fa-search mr-2 text-primary"></i> Search Insights
        </h5>
        <form action="{{ url('/blog-list') }}" method="GET" class="position-relative w-100">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-elite pr-5" style="border-radius: 999px; padding-right: 52px; height: 46px;" placeholder="Search articles, guides...">
            <button type="submit" class="btn-elite btn-elite-primary btn-sm position-absolute" style="top: 50%; right: 5px; transform: translateY(-50%); border-radius: 999px; height: 36px; width: 36px; padding: 0; display: inline-flex; align-items: center; justify-content: center; z-index: 5;" title="Search">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <!-- Interactive Tabs Widget: Popular / Recent / Comments -->
    <div class="sidebar-widget-card mb-4">
        <ul class="nav nav-tabs nav-tabs-elite" id="sidebarStoryTabs" role="tablist">
            <li class="nav-item flex-fill text-center" role="presentation">
                <button class="nav-link active w-100" id="tab-popular-btn" data-bs-toggle="tab" data-toggle="tab" data-bs-target="#tab-popular" data-target="#tab-popular" type="button" role="tab" aria-controls="tab-popular" aria-selected="true">
                    <i class="fas fa-fire mr-1 text-danger"></i> Popular
                </button>
            </li>
            <li class="nav-item flex-fill text-center" role="presentation">
                <button class="nav-link w-100" id="tab-recent-btn" data-bs-toggle="tab" data-toggle="tab" data-bs-target="#tab-recent" data-target="#tab-recent" type="button" role="tab" aria-controls="tab-recent" aria-selected="false">
                    <i class="far fa-clock mr-1 text-primary"></i> Recent
                </button>
            </li>
            <li class="nav-item flex-fill text-center" role="presentation">
                <button class="nav-link w-100" id="tab-comments-btn" data-bs-toggle="tab" data-toggle="tab" data-bs-target="#tab-comments" data-target="#tab-comments" type="button" role="tab" aria-controls="tab-comments" aria-selected="false">
                    <i class="far fa-comments mr-1 text-success"></i> Comments
                </button>
            </li>
        </ul>

        <div class="tab-content pt-3" id="sidebarStoryTabsContent">
            <!-- Popular Tab -->
            <div class="tab-pane fade show active" id="tab-popular" role="tabpanel" aria-labelledby="tab-popular-btn">
                <div class="d-flex flex-column" style="gap: 14px;">
                    @forelse($sidebarPopular as $pItem)
                        @php
                            $pImg = $pItem->image->link ?? 'admin-dist/img/blog-1-PTDVKJDt.webp';
                            $pSrc = Str::startsWith($pImg, ['http://', 'https://']) ? $pImg : asset($pImg);
                        @endphp
                        <div class="sidebar-mini-article d-flex align-items-center">
                            <a href="{{ route('single-post', $pItem->slug) }}" class="flex-shrink-0 mr-3 me-3" style="width: 72px; height: 60px; border-radius: 10px; overflow: hidden; display: block;">
                                <img src="{{ $pSrc }}" alt="{{ $pItem->title }}" class="w-100 h-100 object-fit-cover" onerror="this.src='{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}'">
                            </a>
                            <div class="flex-grow-1 min-w-0">
                                <span class="badge badge-light text-primary small py-1 px-2 font-weight-bold" style="font-size: 11px;">
                                    {{ $pItem->category->name ?? 'Article' }}
                                </span>
                                <h6 class="mb-1 mt-1 font-weight-bold" style="font-size: 13.5px; line-height: 1.35;">
                                    <a href="{{ route('single-post', $pItem->slug) }}" class="text-body text-decoration-none d-block text-truncate" title="{{ $pItem->title }}">
                                        {{ $pItem->title }}
                                    </a>
                                </h6>
                                <div class="small text-muted d-flex align-items-center" style="gap: 10px; font-size: 11.5px;">
                                    <span><i class="far fa-eye mr-1"></i> {{ number_format($pItem->views_count ?? 0) }}</span>
                                    <span><i class="far fa-clock mr-1"></i> {{ $pItem->reading_time }}m</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small mb-0 py-2">No trending stories available.</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Tab -->
            <div class="tab-pane fade" id="tab-recent" role="tabpanel" aria-labelledby="tab-recent-btn">
                <div class="d-flex flex-column" style="gap: 14px;">
                    @forelse($sidebarRecent as $rItem)
                        @php
                            $rImg = $rItem->image->link ?? 'admin-dist/img/blog-1-PTDVKJDt.webp';
                            $rSrc = Str::startsWith($rImg, ['http://', 'https://']) ? $rImg : asset($rImg);
                        @endphp
                        <div class="sidebar-mini-article d-flex align-items-center">
                            <a href="{{ route('single-post', $rItem->slug) }}" class="flex-shrink-0 mr-3 me-3" style="width: 72px; height: 60px; border-radius: 10px; overflow: hidden; display: block;">
                                <img src="{{ $rSrc }}" alt="{{ $rItem->title }}" class="w-100 h-100 object-fit-cover" onerror="this.src='{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}'">
                            </a>
                            <div class="flex-grow-1 min-w-0">
                                <h6 class="mb-1 font-weight-bold" style="font-size: 13.5px; line-height: 1.35;">
                                    <a href="{{ route('single-post', $rItem->slug) }}" class="text-body text-decoration-none d-block text-truncate" title="{{ $rItem->title }}">
                                        {{ $rItem->title }}
                                    </a>
                                </h6>
                                <div class="small text-muted d-flex align-items-center" style="gap: 10px; font-size: 11.5px;">
                                    <span><i class="far fa-calendar-alt mr-1"></i> {{ ($rItem->published_at ?? $rItem->created_at)->format('M d, Y') }}</span>
                                    <span><i class="far fa-clock mr-1"></i> {{ $rItem->reading_time }}m</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small mb-0 py-2">No recent articles found.</p>
                    @endforelse
                </div>
            </div>

            <!-- Comments Tab -->
            <div class="tab-pane fade" id="tab-comments" role="tabpanel" aria-labelledby="tab-comments-btn">
                <div class="d-flex flex-column" style="gap: 12px;">
                    @forelse($sidebarComments as $cItem)
                        <div class="p-2 rounded bg-light-subtle border border-light-subtle">
                            <div class="d-flex align-items-center mb-1">
                                <span class="badge badge-secondary mr-2 me-2 py-0 px-2" style="font-size: 10px;">
                                    <i class="far fa-user mr-1"></i> {{ $cItem->user->name ?? 'Reader' }}
                                </span>
                                <small class="text-muted" style="font-size: 11px;">{{ $cItem->created_at ? $cItem->created_at->diffForHumans() : 'Recently' }}</small>
                            </div>
                            <p class="mb-1 small text-body font-italic" style="font-size: 12.5px; line-height: 1.4;">
                                "{{ Str::limit($cItem->comment, 75) }}"
                            </p>
                            @if($cItem->post)
                                <a href="{{ route('single-post', $cItem->post->slug) }}" class="small text-primary font-weight-bold text-decoration-none" style="font-size: 11.5px;">
                                    on {{ Str::limit($cItem->post->title, 32) }} <i class="fas fa-angle-right"></i>
                                </a>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted small mb-0 py-2">No community comments yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Widget -->
    <div class="sidebar-widget-card mb-4">
        <h5 class="sidebar-card-title">
            <i class="fas fa-folder-open mr-2 text-primary"></i> Topic Categories
        </h5>
        <div class="d-flex flex-column" style="gap: 8px;">
            @foreach($sidebarCategories->take(10) as $cat)
                <a href="{{ url('/blog-list?category=' . ($cat->name ?? $cat->id)) }}" class="sidebar-cat-row d-flex align-items-center justify-content-between p-2 rounded text-decoration-none">
                    <span class="d-inline-flex align-items-center font-weight-bold" style="font-size: 13.5px; color: inherit;">
                        <i class="fas fa-chevron-right mr-2 me-2 text-muted" style="font-size: 10px;"></i> {{ $cat->name }}
                    </span>
                    <span class="badge badge-elite-count">{{ $cat->posts_count ?? 0 }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Trending Tags Cloud -->
    <div class="sidebar-widget-card mb-4">
        <h5 class="sidebar-card-title">
            <i class="fas fa-tags mr-2 text-primary"></i> Trending Tags
        </h5>
        <div class="d-flex flex-wrap" style="gap: 8px;">
            @foreach($sidebarTags->take(25) as $tItem)
                <a href="{{ url('/blog/tag/' . $tItem->id) }}" class="sidebar-tag-chip">
                    #{{ $tItem->name }}
                    @if(!empty($tItem->posts_count))
                        <span class="tag-chip-count">{{ $tItem->posts_count }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    <!-- Newsletter Widget -->
    <div class="sidebar-widget-card sidebar-newsletter-card mb-4 text-center">
        <div class="mb-3">
            <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle" style="width: 52px; height: 52px; box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4);">
                <i class="fas fa-paper-plane" style="font-size: 1.25rem;"></i>
            </div>
        </div>
        <h5 class="font-weight-bold mb-2 text-body">Engineering Dispatch</h5>
        <p class="text-muted small mb-3">Join 15,000+ developers receiving weekly system blueprints and security updates.</p>
        <form action="{{ route('newsletter.subscribe') }}" method="POST">
            @csrf
            <div class="input-group mb-2">
                <input type="email" name="email" class="form-control form-control-elite" placeholder="name@company.com" required style="border-radius: 999px 0 0 999px; height: 44px; font-size: 13.5px;">
                <div class="input-group-append">
                    <button class="btn-elite btn-elite-primary btn-sm px-3 font-weight-bold" type="submit" style="border-radius: 0 999px 999px 0; height: 44px;">
                        Join
                    </button>
                </div>
            </div>
            <small class="text-muted" style="font-size: 11px;">Zero spam. Unsubscribe anytime with one click.</small>
        </form>
    </div>

    <!-- Stay Connected Social -->
    <div class="sidebar-widget-card mb-4">
        <h5 class="sidebar-card-title">
            <i class="fas fa-share-alt mr-2 text-primary"></i> Connect
        </h5>
        <div class="d-flex align-items-center justify-content-around py-2">
            <a href="https://twitter.com/xivig" target="_blank" class="share-circle-btn share-twitter" title="Follow on X">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </a>
            <a href="https://github.com/xivig" target="_blank" class="share-circle-btn share-twitter" title="Explore GitHub">
                <i class="fab fa-github" style="font-size: 16px;"></i>
            </a>
            <a href="https://linkedin.com/company/xivig" target="_blank" class="share-circle-btn share-linkedin" title="LinkedIn Network">
                <i class="fab fa-linkedin-in"></i>
            </a>
            <a href="https://facebook.com/xivig" target="_blank" class="share-circle-btn share-facebook" title="Facebook Page">
                <i class="fab fa-facebook-f"></i>
            </a>
            <a href="{{ url('/blog/feed') }}" class="share-circle-btn" style="color: #f97316; background: rgba(249, 115, 22, 0.1); border-color: rgba(249, 115, 22, 0.25);" title="RSS / Web Feed">
                <i class="fas fa-rss"></i>
            </a>
        </div>
    </div>
</aside>
