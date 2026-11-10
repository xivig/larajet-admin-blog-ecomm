<aside class="sidebar">
  <div class="sidebar-shell">
    <button class="sidebar-close" type="button" aria-label="Close sidebar">
      <i class="bi bi-x-lg"></i>
    </button>

    <!-- Sidebar Navigation -->
    <nav class="sidebar-nav">
      <ul class="nav-menu">

        <!-- Core Dashboard -->
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
            <span class="nav-icon"><i class="ph-light ph-squares-four"></i></span>
            <span class="nav-text">Dashboard</span>
            <span class="nav-meta">Overview</span>
          </a>
        </li>

        <li class="nav-heading"><span>Editorial &amp; Content</span></li>

        <!-- Posts Management -->
        <li class="nav-item has-submenu {{ request()->routeIs('admin.posts.*') ? 'open' : '' }}">
          <a class="nav-link {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}" href="#" aria-expanded="{{ request()->routeIs('admin.posts.*') ? 'true' : 'false' }}">
            <span class="nav-icon"><i class="ph-light ph-article"></i></span>
            <span class="nav-text">Articles</span>
            <span class="nav-arrow"><i class="bi bi-chevron-right"></i></span>
          </a>
          <ul class="nav-submenu {{ request()->routeIs('admin.posts.*') ? 'show' : '' }}">
            <li>
              <a class="nav-link {{ request()->routeIs('admin.posts.index') ? 'active' : '' }}" href="{{ route('admin.posts.index') }}">
                <span class="nav-dot"></span>All Articles
              </a>
            </li>
            <li>
              <a class="nav-link {{ request()->routeIs('admin.posts.create') ? 'active' : '' }}" href="{{ route('admin.posts.create') }}">
                <span class="nav-dot"></span>Create New Post
              </a>
            </li>
          </ul>
        </li>

        <!-- Static Pages -->
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}" href="{{ route('admin.pages.index') }}">
            <span class="nav-icon"><i class="ph-light ph-file-text"></i></span>
            <span class="nav-text">Pages</span>
          </a>
        </li>

        <!-- Categories -->
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
            <span class="nav-icon"><i class="ph-light ph-folder-notch-open"></i></span>
            <span class="nav-text">Categories</span>
          </a>
        </li>

        <!-- Tags -->
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.tags.*') ? 'active' : '' }}" href="{{ route('admin.tags.index') }}">
            <span class="nav-icon"><i class="ph-light ph-tag"></i></span>
            <span class="nav-text">Tags</span>
          </a>
        </li>

        <!-- Comments Moderation -->
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.comments.*') ? 'active' : '' }}" href="{{ route('admin.comments.index') }}">
            <span class="nav-icon"><i class="ph-light ph-chat-centered-text"></i></span>
            <span class="nav-text">Comments</span>
          </a>
        </li>

        <!-- Navigation Menu Builder -->
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.menu.*') ? 'active' : '' }}" href="{{ route('admin.menu.index') }}">
            <span class="nav-icon"><i class="ph-light ph-list-dashes"></i></span>
            <span class="nav-text">Navigation Menu</span>
          </a>
        </li>

        <!-- Modular Widgets -->
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.widgets.*') ? 'active' : '' }}" href="{{ route('admin.widgets.index') }}">
            <span class="nav-icon"><i class="ph-light ph-squares-four"></i></span>
            <span class="nav-text">Widgets</span>
          </a>
        </li>

        <!-- URL Management & 301 Redirects -->
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.url-management.*') ? 'active' : '' }}" href="{{ route('admin.url-management.index') }}">
            <span class="nav-icon"><i class="ph-light ph-link"></i></span>
            <span class="nav-text">URL Management</span>
            <span class="badge bg-success-subtle text-success rounded-pill ms-auto" style="font-size: 0.65rem;">301</span>
          </a>
        </li>

        <!-- Search Analytics -->
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.search-analytics.*') ? 'active' : '' }}" href="{{ route('admin.search-analytics.index') }}">
            <span class="nav-icon"><i class="ph-light ph-magnifying-glass"></i></span>
            <span class="nav-text">Search Intelligence</span>
          </a>
        </li>

        <!-- Newsletter Audience -->
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.newsletter.*') ? 'active' : '' }}" href="{{ route('admin.newsletter.index') }}">
            <span class="nav-icon"><i class="ph-light ph-paper-plane-tilt"></i></span>
            <span class="nav-text">Newsletter</span>
          </a>
        </li>

        <li class="nav-heading"><span>Commerce &amp; Gateways</span></li>

        <!-- E-Commerce Store & Gateways -->
        <li class="nav-item has-submenu {{ request()->routeIs('admin.store.*') ? 'open' : '' }}">
          <a class="nav-link {{ request()->routeIs('admin.store.*') ? 'active' : '' }}" href="#" aria-expanded="{{ request()->routeIs('admin.store.*') ? 'true' : 'false' }}">
            <span class="nav-icon"><i class="ph-light ph-storefront"></i></span>
            <span class="nav-text">E-Commerce Store</span>
            <span class="nav-arrow"><i class="bi bi-chevron-right"></i></span>
          </a>
          <ul class="nav-submenu {{ request()->routeIs('admin.store.*') ? 'show' : '' }}">
            <li>
              <a class="nav-link {{ request()->routeIs('admin.store.products') ? 'active' : '' }}" href="{{ route('admin.store.products') }}">
                <span class="nav-dot"></span>Products &amp; Catalog
              </a>
            </li>
            <li>
              <a class="nav-link {{ request()->routeIs('admin.store.orders') ? 'active' : '' }}" href="{{ route('admin.store.orders') }}">
                <span class="nav-dot"></span>Orders &amp; Fulfillment
              </a>
            </li>
            <li>
              <a class="nav-link {{ request()->routeIs('admin.store.gateways') ? 'active' : '' }}" href="{{ route('admin.store.gateways') }}">
                <span class="nav-dot"></span>Payment Gateways
              </a>
            </li>
            <li>
              <a class="nav-link {{ request()->routeIs('admin.store.coupons') ? 'active' : '' }}" href="{{ route('admin.store.coupons') }}">
                <span class="nav-dot"></span>Promo Coupons
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-heading"><span>Applications &amp; Workspace</span></li>

        <!-- Chat -->
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.chat.*') ? 'active' : '' }}" href="{{ route('admin.chat.index') }}">
            <span class="nav-icon"><i class="ph-light ph-chats-circle"></i></span>
            <span class="nav-text">Team Chat</span>
          </a>
        </li>

        <!-- Email / Inbox -->
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.email.*') ? 'active' : '' }}" href="{{ route('admin.email.index') }}">
            <span class="nav-icon"><i class="ph-light ph-envelope-simple"></i></span>
            <span class="nav-text">Mail Center</span>
          </a>
        </li>

        <!-- File Manager -->
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.file-manager.*') ? 'active' : '' }}" href="{{ route('admin.file-manager.index') }}">
            <span class="nav-icon"><i class="ph-light ph-folder-simple-star"></i></span>
            <span class="nav-text">File Manager</span>
          </a>
        </li>

        <!-- Notifications Center -->
        @php
          $sidebarUnreadCount = \App\Models\AdminNotification::where('is_read', false)->count();
        @endphp
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}" href="{{ route('admin.notifications.index') }}">
            <span class="nav-icon"><i class="ph-light ph-bell"></i></span>
            <span class="nav-text">Notifications</span>
            <span id="sidebarNotifBadge" class="badge rounded-pill bg-danger ms-auto {{ $sidebarUnreadCount > 0 ? '' : 'd-none' }}" style="font-size: 0.65rem;">{{ $sidebarUnreadCount > 99 ? '99+' : $sidebarUnreadCount }}</span>
          </a>
        </li>

        <li class="nav-heading"><span>Assets &amp; Media</span></li>

        <!-- Media Library -->
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.media.*') ? 'active' : '' }}" href="{{ route('admin.media.index') }}">
            <span class="nav-icon"><i class="ph-light ph-image"></i></span>
            <span class="nav-text">Media Library</span>
          </a>
        </li>

        <!-- Image Cropper Studio -->
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.image-cropper.*') ? 'active' : '' }}" href="{{ route('admin.image-cropper.index') }}">
            <span class="nav-icon"><i class="ph-light ph-crop"></i></span>
            <span class="nav-text">Image Cropper</span>
          </a>
        </li>

        <!-- Gallery Management -->
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.galleries.*') ? 'active' : '' }}" href="{{ route('admin.galleries.index') }}">
            <span class="nav-icon"><i class="ph-light ph-images"></i></span>
            <span class="nav-text">Galleries</span>
          </a>
        </li>

        <li class="nav-heading"><span>User Management</span></li>

        <!-- Users Directory -->
        <li class="nav-item has-submenu {{ request()->routeIs('admin.users.*') ? 'open' : '' }}">
          <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="#" aria-expanded="{{ request()->routeIs('admin.users.*') ? 'true' : 'false' }}">
            <span class="nav-icon"><i class="ph-light ph-users-three"></i></span>
            <span class="nav-text">Users</span>
            <span class="nav-arrow"><i class="bi bi-chevron-right"></i></span>
          </a>
          <ul class="nav-submenu {{ request()->routeIs('admin.users.*') ? 'show' : '' }}">
            <li>
              <a class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                <span class="nav-dot"></span>Users Directory
              </a>
            </li>
            <li>
              <a class="nav-link {{ request()->routeIs('admin.users.create') ? 'active' : '' }}" href="{{ route('admin.users.create') }}">
                <span class="nav-dot"></span>Add New User
              </a>
            </li>
          </ul>
        </li>

        <!-- Roles & Permissions -->
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">
            <span class="nav-icon"><i class="ph-light ph-shield-check"></i></span>
            <span class="nav-text">Roles &amp; Permissions</span>
          </a>
        </li>

        <li class="nav-heading"><span>Ecosystem &amp; Live Site</span></li>

        <li class="nav-item">
          <a class="nav-link" href="{{ url('/blog') }}" target="_blank">
            <span class="nav-icon"><i class="ph-light ph-globe"></i></span>
            <span class="nav-text">Live Blog</span>
            <span class="nav-meta"><i class="bi bi-box-arrow-up-right small"></i></span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="{{ url('/') }}" target="_blank">
            <span class="nav-icon"><i class="ph-light ph-browser"></i></span>
            <span class="nav-text">Front Website</span>
            <span class="nav-meta"><i class="bi bi-box-arrow-up-right small"></i></span>
          </a>
        </li>

        <li class="nav-heading"><span>Admin &amp; Settings</span></li>

        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.settings.index') || request()->routeIs('admin.settings.maintenance') || request()->routeIs('admin.settings.integrations') || request()->routeIs('admin.settings.editorial') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
            <span class="nav-icon"><i class="ph-light ph-gear"></i></span>
            <span class="nav-text">Site Settings</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.settings.seo*') ? 'active' : '' }}" href="{{ route('admin.settings.seo') }}">
            <span class="nav-icon"><i class="ph-light ph-globe"></i></span>
            <span class="nav-text">SEO Suite</span>
            <span class="badge bg-primary-subtle text-primary rounded-pill ms-auto" style="font-size: 0.65rem;">PRO</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}" href="{{ route('admin.activity-logs.index') }}">
            <span class="nav-icon"><i class="ph-light ph-clock-counter-clockwise"></i></span>
            <span class="nav-text">Activity Log</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.backups.*') ? 'active' : '' }}" href="{{ route('admin.backups.index') }}">
            <span class="nav-icon"><i class="ph-light ph-hard-drive"></i></span>
            <span class="nav-text">System Backups</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}" href="{{ route('admin.profile') }}">
            <span class="nav-icon"><i class="ph-light ph-user-circle"></i></span>
            <span class="nav-text">Profile &amp; Security</span>
          </a>
        </li>

      </ul>
    </nav>

    <!-- Sidebar Bottom Card (XivigAdmin PRO) -->
    <div class="sidebar-footer">
      <div class="sidebar-account">
        <a href="{{ route('admin.profile') }}" class="sidebar-account-main">
          @php
            $adminUser = Auth::guard('admin')->user();
            $avatarUrl = ($adminUser && $adminUser->profile_photo_path) 
              ? asset($adminUser->profile_photo_path) 
              : asset('xivig-admin/img/1-DZLDOIIi.jpg');
          @endphp
          <img src="{{ $avatarUrl }}" alt="{{ $adminUser->name ?? 'Admin' }}" class="sidebar-account-avatar">
          <div class="sidebar-account-meta">
            <div class="sidebar-account-name">{{ $adminUser->name ?? 'Executive Admin' }}</div>
            <div class="sidebar-account-role">ENTERPRISE ADMIN</div>
          </div>
        </a>
        <div class="sidebar-account-actions">
          <a href="{{ route('admin.settings.index') }}" class="sidebar-account-action" title="Site Settings" aria-label="Site Settings">
            <i class="bi bi-gear"></i>
          </a>
          <a href="#" class="sidebar-account-action sidebar-account-logout" title="Sign Out" aria-label="Sign Out" onclick="event.preventDefault(); document.getElementById('adminSidebarLogoutForm').submit();">
            <i class="bi bi-box-arrow-right"></i>
          </a>
        </div>
      </div>

      <div class="sidebar-footer-links">
        <a href="{{ url('/blog') }}" target="_blank" class="sidebar-footer-link">
          <i class="bi bi-globe"></i>
          <span>Live Blog</span>
        </a>
        <a href="{{ route('admin.comments.index') }}" class="sidebar-footer-link">
          <i class="bi bi-bell"></i>
          <span>Alerts</span>
        </a>
      </div>
    </div>

    <form id="adminSidebarLogoutForm" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
      @csrf
    </form>
  </div>
</aside>
