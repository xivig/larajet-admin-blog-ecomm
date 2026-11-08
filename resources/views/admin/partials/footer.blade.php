<footer class="footer">
  <div class="footer-content">
    <div class="footer-credits">
      <div class="footer-copyright">
        &copy; {{ date('Y') }} <a href="{{ route('admin.dashboard') }}" class="fw-semibold">XivigAdmin</a>. Crafted for elite digital enterprises.
      </div>
      <div class="footer-version">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5">v2.0.0 Pro</span>
      </div>
    </div>

    <div class="footer-links">
      <a href="{{ url('/') }}" target="_blank">Public Site</a>
      <a href="{{ url('/blog') }}" target="_blank">Blog Magazine</a>
      <a href="{{ route('admin.posts.create') }}">Compose Post</a>
      <a href="{{ route('admin.comments.index') }}">Discussions</a>
      <a href="{{ route('admin.profile') }}">Security</a>
    </div>
  </div>
</footer>
