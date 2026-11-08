<header class="header">
  <div class="header-left">
    <a href="{{ route('admin.dashboard') }}" class="header-brand" aria-label="XivigAdmin home">
      <span class="header-logo">
        <img src="{{ asset('admin-dist/img/xivig-logo-CDpcaf-E.png') }}" alt="XivigAdmin" class="brand-logo-img">
      </span>
      <span class="header-context">
        <strong class="header-context-title">Xivig<span class="brand-gradient">Admin</span></strong>
        <span class="badge-elite">PRO</span>
      </span>
    </a>

    <button class="sidebar-toggle" type="button" title="Toggle Sidebar" aria-label="Toggle sidebar">
      <span class="menu-lines" aria-hidden="true">
        <span></span>
        <span></span>
        <span></span>
      </span>
    </button>
  </div>

  <div class="header-search-wrap">
    <form class="search-form" action="{{ route('admin.posts.index') }}" method="GET">
      <i class="bi bi-search search-icon"></i>
      <input type="search" name="q" placeholder="Search blog posts, categories, or keywords..." autocomplete="off" value="{{ request('q') }}">
      <kbd class="search-shortcut">⌘K</kbd>
    </form>
  </div>

  <div class="header-right">
    <div class="header-actions-desktop">

      <!-- Front Quick Links -->
      <div class="header-action-wrap dropdown quickaccess-dropdown">
        <button class="header-action dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Quick Links">
          <i class="ph-light ph-squares-four"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-end quickaccess-menu">
          <div class="menu-title">Shortcuts</div>
          <a href="{{ route('admin.posts.create') }}" class="quickaccess-featured">
            <span class="quickaccess-featured-icon"><i class="bi bi-pencil-square"></i></span>
            <span class="quickaccess-featured-copy">
              <strong>Compose Article</strong>
              <small>Open rich Quill editor &amp; publish</small>
            </span>
          </a>
          <div class="quickaccess-grid">
            <a href="{{ url('/blog') }}" target="_blank" class="quickaccess-tile">
              <i class="bi bi-globe"></i>
              <span>Live Blog</span>
            </a>
            <a href="{{ url('/') }}" target="_blank" class="quickaccess-tile">
              <i class="bi bi-box-arrow-up-right"></i>
              <span>Home Site</span>
            </a>
            <a href="{{ route('admin.categories.index') }}" class="quickaccess-tile">
              <i class="bi bi-folder2"></i>
              <span>Categories</span>
            </a>
            <a href="{{ route('admin.media.index') }}" class="quickaccess-tile">
              <i class="bi bi-images"></i>
              <span>Media</span>
            </a>
          </div>
        </div>
      </div>

      <!-- Notifications -->
      @php
        $navNotifications = \App\Models\AdminNotification::latest()->take(5)->get();
        $navUnreadCount = \App\Models\AdminNotification::where('is_read', false)->count();
      @endphp
      <div class="header-action-wrap dropdown notifications-dropdown">
        <button class="header-action dropdown-toggle position-relative" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
          <i class="bi bi-bell"></i>
          @if($navUnreadCount > 0)
            <span class="header-badge">{{ $navUnreadCount }}</span>
          @endif
        </button>
        <div class="dropdown-menu dropdown-menu-end notifications-menu" style="min-width: 320px;">
          <div class="notification-header d-flex justify-content-between align-items-center p-3 border-bottom">
            <div>
              <h6 class="mb-0 fw-bold">Notifications</h6>
              <span class="small text-muted">{{ $navUnreadCount }} unread alert{{ $navUnreadCount === 1 ? '' : 's' }}</span>
            </div>
            <a href="{{ route('admin.notifications.index') }}" class="small text-primary text-decoration-none fw-semibold">View Center</a>
          </div>
          <div class="notification-list" style="max-height: 280px; overflow-y: auto;">
            @forelse($navNotifications as $notif)
              @php
                $iconClass = match($notif->type) {
                  'comment' => 'bi-chat-dots text-primary',
                  'user' => 'bi-person-check text-success',
                  'system' => 'bi-shield-check text-info',
                  'warning' => 'bi-exclamation-triangle text-warning',
                  default => 'bi-bell text-secondary',
                };
              @endphp
              <a href="{{ $notif->link ?: route('admin.notifications.index') }}" class="dropdown-item d-flex gap-2 p-2.5 border-bottom text-wrap {{ !$notif->is_read ? 'bg-light-subtle' : '' }}">
                <div class="flex-shrink-0 mt-1">
                  <i class="bi {{ $iconClass }} fs-5"></i>
                </div>
                <div class="flex-grow-1">
                  <div class="fw-semibold small text-body">{{ $notif->title }}</div>
                  <div class="text-muted small lh-sm">{{ Str::limit($notif->message, 60) }}</div>
                  <small class="text-muted" style="font-size: 0.72rem;">{{ $notif->created_at ? $notif->created_at->diffForHumans() : 'Just now' }}</small>
                </div>
              </a>
            @empty
              <div class="p-3 text-center text-muted small">
                <i class="bi bi-bell-slash fs-4 d-block mb-1 text-muted"></i>
                No notifications right now.
              </div>
            @endforelse
          </div>
          <div class="p-2 text-center border-top bg-light-subtle">
            <a href="{{ route('admin.notifications.index') }}" class="small text-decoration-none fw-semibold">Go to Notification Center &rarr;</a>
          </div>
        </div>
      </div>

      <!-- Theme Mode Toggle -->
      <button class="header-action theme-toggle" type="button" title="Toggle Theme (Dark / Light)">
        <i class="ph-light ph-moon-stars theme-icon-dark"></i>
        <i class="ph-light ph-sun theme-icon-light"></i>
      </button>

      <!-- Authenticated Admin Profile Dropdown -->
      @php
        $headerAdmin = Auth::guard('admin')->user();
        $headerAvatar = ($headerAdmin && $headerAdmin->profile_photo_path) 
          ? asset($headerAdmin->profile_photo_path) 
          : asset('admin-dist/img/1-DZLDOIIi.jpg');
      @endphp
      <div class="header-action-wrap dropdown user-dropdown">
        <button class="dropdown-toggle user-trigger" data-bs-toggle="dropdown" aria-expanded="false">
          <img src="{{ $headerAvatar }}" alt="User" class="user-avatar">
          <div class="user-brief">
            <span class="user-name">{{ $headerAdmin->name ?? 'Executive Admin' }}</span>
            <span class="user-role">ENTERPRISE ADMIN</span>
          </div>
        </button>

        <div class="dropdown-menu dropdown-menu-end user-menu">
          <div class="user-menu-header">
            <img src="{{ $headerAvatar }}" alt="User" class="user-menu-avatar">
            <div class="user-menu-info">
              <div class="user-menu-name">{{ $headerAdmin->name ?? 'Executive Admin' }}</div>
              <div class="user-menu-email">
                <a href="mailto:{{ $headerAdmin->email ?? 'admin@xivig.com' }}">
                  {{ $headerAdmin->email ?? 'admin@xivig.com' }}
                </a>
              </div>
            </div>
          </div>
          <div class="user-menu-body">
            <a class="user-menu-item" href="{{ route('admin.profile') }}">
              <i class="bi bi-person"></i>
              <span>Admin Profile</span>
            </a>
            <a class="user-menu-item" href="{{ route('admin.posts.index') }}">
              <i class="bi bi-journal-richtext"></i>
              <span>All Articles</span>
            </a>
            <a class="user-menu-item" href="{{ url('/blog') }}" target="_blank">
              <i class="bi bi-box-arrow-up-right"></i>
              <span>View Frontend</span>
            </a>
          </div>
          <div class="user-menu-footer">
            <form action="{{ route('admin.logout') }}" method="POST" id="adminHeaderLogoutForm" class="d-inline w-100">
              @csrf
              <button type="submit" class="user-menu-logout btn border-0 text-start w-100 bg-transparent">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </button>
            </form>
          </div>
        </div>
      </div>

    </div>

    <div class="header-actions-mobile">
      <button class="header-action theme-toggle" type="button" title="Toggle Theme">
        <i class="ph-light ph-moon-stars theme-icon-dark"></i>
        <i class="ph-light ph-sun theme-icon-light"></i>
      </button>
      <button class="header-action sidebar-toggle" type="button" title="Toggle Navigation">
        <i class="bi bi-list fs-4"></i>
      </button>
    </div>
  </div>
</header>
