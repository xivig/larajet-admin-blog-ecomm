<div class="col-lg-2 col-md-6 col-sm-6 mb-4 mb-lg-0 bg-transparent border-0 shadow-none">
    <div class="footer-widget bg-transparent border-0 shadow-none p-0">
        <h4 class="footer-title mb-3">{{ $menu?->name ?? $defaultTitle }}</h4>
        @include('blog.partials.footer-menu-list', [
            'menu' => $menu,
            'fallbackLinks' => $fallbackLinks ?? [],
        ])
    </div>
</div>
