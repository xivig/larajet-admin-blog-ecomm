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

        <li class="nav-heading"><span>Assets &amp; Media</span></li>

        <!-- Media Library -->
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.media.*') ? 'active' : '' }}" href="{{ route('admin.media.index') }}">
            <span class="nav-icon"><i class="ph-light ph-image"></i></span>
            <span class="nav-text">Media Library</span>
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

        <li class="nav-item">
          <a class="nav-link text-danger" href="#" onclick="event.preventDefault(); document.getElementById('adminSidebarLogoutForm').submit();">
            <span class="nav-icon"><i class="ph-light ph-sign-out text-danger"></i></span>
            <span class="nav-text">Sign Out</span>
          </a>
          <form id="adminSidebarLogoutForm" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
            @csrf
          </form>
        </li>

      </ul>
    </nav>
  </div>
</aside>
