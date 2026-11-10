<div class="dropdown-menu dropdown-menu-elite blog-mega-menu shadow-lg" aria-labelledby="{{ $toggleId ?? 'dropdownBlog' }}">
    <div class="row g-3 text-start">
        <!-- Column 1: Portals & Catalogs -->
        <div class="col-lg-4 blog-mega-col">
            <div class="dropdown-header blog-mega-heading text-uppercase fw-bold px-2 pt-1 pb-2" style="color: #6366f1;">
                <i class="fas fa-newspaper me-1"></i> Portals &amp; Catalogs
            </div>
            <a class="dropdown-item py-2 px-2 rounded-3 d-flex align-items-center" href="{{ route('blog.magazine') }}">
                <i class="fas fa-bolt text-danger me-3" style="font-size: 14px; width: 18px; text-align: center;"></i>
                <div>
                    <span class="d-block fw-bold" style="font-size: 13.5px;">Magazine Portal</span>
                    <small class="text-muted" style="font-size: 11px;">Elite editorial &amp; breaking ticker</small>
                </div>
            </a>
            <a class="dropdown-item py-2 px-2 rounded-3 d-flex align-items-center" href="{{ url('/blog-list') }}">
                <i class="fas fa-th-large text-primary me-3" style="font-size: 14px; width: 18px; text-align: center;"></i>
                <div>
                    <span class="d-block fw-bold" style="font-size: 13.5px;">4-Column Catalog</span>
                    <small class="text-muted" style="font-size: 11px;">Grid view with quick filters</small>
                </div>
            </a>
            <a class="dropdown-item py-2 px-2 rounded-3 d-flex align-items-center" href="{{ route('blog.feed') }}">
                <i class="fas fa-rss text-warning me-3" style="font-size: 14px; width: 18px; text-align: center;"></i>
                <div>
                    <span class="d-block fw-bold" style="font-size: 13.5px;">Live Feed Stream</span>
                    <small class="text-muted" style="font-size: 11px;">Continuous chronologic stories</small>
                </div>
            </a>
        </div>

        <!-- Column 2: Visual & Media Formats -->
        <div class="col-lg-4 blog-mega-col">
            <div class="dropdown-header blog-mega-heading text-uppercase fw-bold px-2 pt-1 pb-2" style="color: #10b981;">
                <i class="fas fa-photo-video me-1"></i> Visual &amp; Media Formats
            </div>
            <a class="dropdown-item py-2 px-2 rounded-3 d-flex align-items-center" href="{{ route('blog.gallery') }}">
                <i class="fas fa-images text-info me-3" style="font-size: 14px; width: 18px; text-align: center;"></i>
                <div>
                    <span class="d-block fw-bold" style="font-size: 13.5px;">Visual Living Gallery</span>
                    <small class="text-muted" style="font-size: 11px;">Masonry tiles &amp; full lightbox</small>
                </div>
            </a>
            <a class="dropdown-item py-2 px-2 rounded-3 d-flex align-items-center" href="{{ route('blog.videos') }}">
                <i class="fas fa-play-circle text-danger me-3" style="font-size: 14px; width: 18px; text-align: center;"></i>
                <div>
                    <span class="d-block fw-bold" style="font-size: 13.5px;">Video Insights</span>
                    <small class="text-muted" style="font-size: 11px;">Cinema player &amp; tech playlist</small>
                </div>
            </a>
            <a class="dropdown-item py-2 px-2 rounded-3 d-flex align-items-center" href="{{ route('blog.single-magazine') }}">
                <i class="fas fa-file-alt text-primary me-3" style="font-size: 14px; width: 18px; text-align: center;"></i>
                <div>
                    <span class="d-block fw-bold" style="font-size: 13.5px;">Magazine Article Single</span>
                    <small class="text-muted" style="font-size: 11px;">Full editorial single post layout</small>
                </div>
            </a>
            <a class="dropdown-item py-2 px-2 rounded-3 d-flex align-items-center" href="{{ route('blog.format.image') }}">
                <i class="fas fa-image me-3" style="font-size: 14px; width: 18px; text-align: center; color: #8b5cf6;"></i>
                <div>
                    <span class="d-block fw-bold" style="font-size: 13.5px;">Immersive Hero Post</span>
                    <small class="text-muted" style="font-size: 11px;">Fullscreen visual presentation</small>
                </div>
            </a>
            <a class="dropdown-item py-2 px-2 rounded-3 d-flex align-items-center" href="{{ route('blog.format.link') }}">
                <i class="fas fa-bookmark text-danger me-3" style="font-size: 14px; width: 18px; text-align: center;"></i>
                <div>
                    <span class="d-block fw-bold" style="font-size: 13.5px;">Curated Bookmark Post</span>
                    <small class="text-muted" style="font-size: 11px;">External dispatch &amp; citations</small>
                </div>
            </a>
        </div>

        <!-- Column 3: Taxonomies & Directory -->
        <div class="col-lg-4 blog-mega-col">
            <div class="dropdown-header blog-mega-heading text-uppercase fw-bold px-2 pt-1 pb-2" style="color: #f59e0b;">
                <i class="fas fa-sitemap me-1"></i> Taxonomies &amp; Directory
            </div>
            <a class="dropdown-item py-2 px-2 rounded-3 d-flex align-items-center" href="{{ url('/blog/category/technology') }}">
                <i class="fas fa-folder-open text-warning me-3" style="font-size: 14px; width: 18px; text-align: center;"></i>
                <div>
                    <span class="d-block fw-bold" style="font-size: 13.5px;">Category Archives</span>
                    <small class="text-muted" style="font-size: 11px;">Tech, Business, Politics &amp; more</small>
                </div>
            </a>
            <a class="dropdown-item py-2 px-2 rounded-3 d-flex align-items-center" href="{{ route('blog.tags') }}">
                <i class="fas fa-tags text-success me-3" style="font-size: 14px; width: 18px; text-align: center;"></i>
                <div>
                    <span class="d-block fw-bold" style="font-size: 13.5px;">All Tags Directory</span>
                    <small class="text-muted" style="font-size: 11px;">A-Z topic index &amp; clouds</small>
                </div>
            </a>
            <a class="dropdown-item py-2 px-2 rounded-3 d-flex align-items-center" href="{{ route('blog.authors') }}">
                <i class="fas fa-users text-primary me-3" style="font-size: 14px; width: 18px; text-align: center;"></i>
                <div>
                    <span class="d-block fw-bold" style="font-size: 13.5px;">Authors Directory</span>
                    <small class="text-muted" style="font-size: 11px;">Writers &amp; engineers roster</small>
                </div>
            </a>
        </div>
    </div>
</div>
