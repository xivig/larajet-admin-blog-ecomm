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
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}" href="{{ route('admin.notifications.index') }}">
            <span class="nav-icon"><i class="ph-light ph-bell"></i></span>
            <span class="nav-text">Notifications</span>
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

        <li class="nav-heading"><span>Admin Account</span></li>

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
              : asset('admin-dist/img/1-DZLDOIIi.jpg');
          @endphp
          <img src="{{ $avatarUrl }}" alt="{{ $adminUser->name ?? 'Admin' }}" class="sidebar-account-avatar">
          <div class="sidebar-account-meta">
            <div class="sidebar-account-name">{{ $adminUser->name ?? 'Executive Admin' }}</div>
            <div class="sidebar-account-role">ENTERPRISE ADMIN</div>
          </div>
        </a>
        <div class="sidebar-account-actions">
          <a href="{{ route('admin.profile') }}" class="sidebar-account-action" title="Settings" aria-label="Settings">
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
