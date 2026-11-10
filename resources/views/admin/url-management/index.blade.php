@extends('layouts.admin')

@section('title', 'URL Management, 301 Redirects & 404 Logger - XivigAdmin PRO')

@section('content')
<div class="page-url-management">

  <!-- Header -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">URL Management &amp; Redirect Center</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <span class="breadcrumb-item active">URL Management</span>
      </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
      <form action="{{ route('admin.url-management.sitemap.generate') }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2 small shadow-sm">
          <i class="bi bi-arrow-repeat"></i>
          <span>Generate Sitemap</span>
        </button>
      </form>
      <a href="{{ url('/sitemap.xml') }}" target="_blank" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1.5 small">
        <i class="bi bi-box-arrow-up-right"></i>
        <span>View XML</span>
      </a>
      <a href="{{ url('/robots.txt') }}" target="_blank" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1.5 small">
        <i class="bi bi-robot"></i>
        <span>robots.txt</span>
      </a>
      <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm" style="background-color: #5551ff; border-color: #5551ff;" data-bs-toggle="modal" data-bs-target="#createRedirectModal">
        <i class="bi bi-plus-lg fs-5"></i>
        <span>Add 301 Redirect</span>
      </button>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <!-- Telemetry Overview Cards -->
  <div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold">Active Redirects</span>
          <div class="p-2 rounded-3" style="background: rgba(85, 81, 255, 0.1); color: #5551ff;">
            <i class="bi bi-arrow-left-right fs-5"></i>
          </div>
        </div>
        <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['active_redirects']) }}</h3>
        <small class="text-muted">{{ number_format($stats['total_redirects']) }} total rules configured</small>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold">Redirect Traffic</span>
          <div class="p-2 rounded-3" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
            <i class="bi bi-lightning-charge fs-5"></i>
          </div>
        </div>
        <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['total_redirect_hits']) }}</h3>
        <small class="text-muted">Total redirects served to visitors</small>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold">404 Errors Logged</span>
          <div class="p-2 rounded-3" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
            <i class="bi bi-exclamation-octagon fs-5"></i>
          </div>
        </div>
        <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['total_404_errors']) }}</h3>
        <small class="text-danger fw-semibold">{{ number_format($stats['unresolved_404s']) }} unresolved paths</small>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold">Slug Transitions</span>
          <div class="p-2 rounded-3" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
            <i class="bi bi-shield-check fs-5"></i>
          </div>
        </div>
        <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['total_slug_changes']) }}</h3>
        <small class="text-muted">Automatic 301 history points</small>
      </div>
    </div>
  </div>

  <!-- Navigation Tabs -->
  <ul class="nav nav-pills gap-2 mb-4 p-1.5 bg-white rounded-pill shadow-sm d-inline-flex border" role="tablist">
    <li class="nav-item">
      <a class="nav-link rounded-pill px-4 py-2 fw-semibold {{ $tab === 'redirects' ? 'active text-white' : 'text-secondary' }}" style="{{ $tab === 'redirects' ? 'background: #5551ff;' : '' }}" href="{{ route('admin.url-management.index', ['tab' => 'redirects']) }}">
        <i class="bi bi-arrow-left-right me-1"></i> 301/302 Redirect Rules ({{ $stats['total_redirects'] }})
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link rounded-pill px-4 py-2 fw-semibold {{ $tab === 'notfound' ? 'active text-white' : 'text-secondary' }}" style="{{ $tab === 'notfound' ? 'background: #5551ff;' : '' }}" href="{{ route('admin.url-management.index', ['tab' => 'notfound']) }}">
        <i class="bi bi-bug me-1"></i> 404 Error Logger
        @if($stats['unresolved_404s'] > 0)
          <span class="badge bg-danger rounded-pill ms-1">{{ $stats['unresolved_404s'] }}</span>
        @endif
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link rounded-pill px-4 py-2 fw-semibold {{ $tab === 'slugs' ? 'active text-white' : 'text-secondary' }}" style="{{ $tab === 'slugs' ? 'background: #5551ff;' : '' }}" href="{{ route('admin.url-management.index', ['tab' => 'slugs']) }}">
        <i class="bi bi-clock-history me-1"></i> Slug History Audit
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link rounded-pill px-4 py-2 fw-semibold {{ $tab === 'robots' ? 'active text-white' : 'text-secondary' }}" style="{{ $tab === 'robots' ? 'background: #5551ff;' : '' }}" href="{{ route('admin.url-management.index', ['tab' => 'robots']) }}">
        <i class="bi bi-robot me-1"></i> Robots &amp; Sitemap
      </a>
    </li>
  </ul>

  <!-- Tab 1: 301/302 Redirect Rules -->
  @if($tab === 'redirects')
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
      <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h5 class="fw-bold mb-0">Configured Redirect Rules</h5>
        <form action="{{ route('admin.url-management.index') }}" method="GET" class="d-flex align-items-center gap-2">
          <input type="hidden" name="tab" value="redirects">
          <input type="text" name="search_redirect" value="{{ request('search_redirect') }}" placeholder="Search source or target path..." class="form-control form-control-sm rounded-pill px-3" style="width: 260px;">
          <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3">Search</button>
        </form>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light small text-uppercase text-muted">
            <tr>
              <th class="ps-4">Status</th>
              <th>Source URL / Path</th>
              <th>Target Destination</th>
              <th>Code</th>
              <th>Hits</th>
              <th>Last Used</th>
              <th class="pe-4 text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($redirectRules as $rule)
              <tr>
                <td class="ps-4">
                  <form action="{{ route('admin.url-management.redirects.toggle', $rule->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm p-0 border-0" title="Click to toggle">
                      @if($rule->is_active)
                        <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">Active</span>
                      @else
                        <span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill">Paused</span>
                      @endif
                    </button>
                  </form>
                </td>
                <td>
                  <code class="fw-bold text-dark">{{ $rule->source_path }}</code>
                  @if($rule->description)
                    <div class="small text-muted">{{ $rule->description }}</div>
                  @endif
                </td>
                <td>
                  <a href="{{ str_starts_with($rule->target_path, 'http') ? $rule->target_path : url($rule->target_path) }}" target="_blank" class="text-primary text-decoration-none small">
                    {{ $rule->target_path }} <i class="bi bi-box-arrow-up-right ms-1" style="font-size: 11px;"></i>
                  </a>
                </td>
                <td>
                  <span class="badge {{ $rule->status_code === 301 ? 'bg-primary' : 'bg-warning text-dark' }} rounded-pill px-2.5">
                    {{ $rule->status_code }}
                  </span>
                </td>
                <td>
                  <span class="fw-bold">{{ number_format($rule->hits_count) }}</span>
                </td>
                <td>
                  <small class="text-muted">{{ $rule->last_accessed_at ? $rule->last_accessed_at->diffForHumans() : 'Never' }}</small>
                </td>
                <td class="pe-4 text-end">
                  <div class="btn-group">
                    <form action="{{ route('admin.url-management.redirects.destroy', $rule->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this redirect rule permanently?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                  <i class="bi bi-arrow-left-right fs-1 d-block mb-2 text-muted opacity-50"></i>
                  No redirect rules created yet. Click "Add 301 Redirect" to configure your first rewrite.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if($redirectRules->hasPages())
        <div class="card-footer bg-white py-3 px-4 border-top">
          {{ $redirectRules->links() }}
        </div>
      @endif
    </div>
  @endif

  <!-- Tab 2: 404 Error Logger -->
  @if($tab === 'notfound')
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
      <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
          <h5 class="fw-bold mb-0">Captured 404 Error Requests</h5>
          <small class="text-muted">Real-time error requests tracked automatically across the platform</small>
        </div>
        <div class="d-flex align-items-center gap-2">
          <a href="{{ route('admin.url-management.404.export') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-download me-1"></i> Export CSV
          </a>
          <form action="{{ route('admin.url-management.404.clear-resolved') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
              <i class="bi bi-check2-all me-1"></i> Clear Resolved
            </button>
          </form>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light small text-uppercase text-muted">
            <tr>
              <th class="ps-4">404 URL</th>
              <th>Referer / Origin</th>
              <th>Hits Count</th>
              <th>Last Seen</th>
              <th>Status</th>
              <th class="pe-4 text-end">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($notFoundLogs as $nLog)
              <tr>
                <td class="ps-4">
                  <div class="fw-bold text-dark font-monospace small" style="max-width: 380px; word-break: break-all;">
                    {{ $nLog->url }}
                  </div>
                  <small class="text-muted">IP: {{ $nLog->ip ?? 'Unknown' }}</small>
                </td>
                <td>
                  <small class="text-muted" style="max-width: 250px; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    {{ $nLog->referer ?: 'Direct Access' }}
                  </small>
                </td>
                <td>
                  <span class="badge bg-danger-subtle text-danger fw-bold rounded-pill px-2.5">
                    {{ $nLog->hits_count }} hits
                  </span>
                </td>
                <td>
                  <small class="text-muted">{{ $nLog->last_seen_at ? $nLog->last_seen_at->diffForHumans() : 'Recent' }}</small>
                </td>
                <td>
                  @if($nLog->is_resolved)
                    <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1">Resolved</span>
                  @else
                    <span class="badge bg-warning-subtle text-warning rounded-pill px-2 py-1">Unresolved</span>
                  @endif
                </td>
                <td class="pe-4 text-end">
                  <div class="btn-group gap-1">
                    {{-- Quick Create 301 Redirect button --}}
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-2.5 py-1" onclick="openQuickRedirectModal('{{ parse_url($nLog->url, PHP_URL_PATH) }}')" title="Redirect this 404 to a live page">
                      <i class="bi bi-arrow-right-circle me-1"></i> Redirect 301
                    </button>
                    @if(!$nLog->is_resolved)
                      <form action="{{ route('admin.url-management.404.resolve', $nLog->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2.5 py-1" title="Mark Resolved">
                          <i class="bi bi-check-lg"></i>
                        </button>
                      </form>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-5 text-muted">
                  <i class="bi bi-check-circle fs-1 d-block mb-2 text-success opacity-50"></i>
                  No 404 errors recorded! All system endpoints are healthy.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if($notFoundLogs->hasPages())
        <div class="card-footer bg-white py-3 px-4 border-top">
          {{ $notFoundLogs->links() }}
        </div>
      @endif
    </div>
  @endif

  <!-- Tab 3: Slug History Audit -->
  @if($tab === 'slugs')
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
      <div class="card-header bg-white py-3 px-4 border-bottom">
        <h5 class="fw-bold mb-0">Slug Transition &amp; SEO Redirection Audit</h5>
        <small class="text-muted">Whenever an editor updates an article or page slug, a permanent 301 history entry is automatically recorded so external backlinks never break.</small>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light small text-uppercase text-muted">
            <tr>
              <th class="ps-4">Entity</th>
              <th>Original Slug (Old)</th>
              <th>Redirection Destination (New)</th>
              <th>Status</th>
              <th class="pe-4">Recorded At</th>
            </tr>
          </thead>
          <tbody>
            @forelse($slugHistories as $sh)
              <tr>
                <td class="ps-4">
                  <span class="badge bg-light text-dark border">{{ $sh->model_type }} #{{ $sh->model_id }}</span>
                </td>
                <td>
                  <code class="text-danger fw-bold">/{{ $sh->old_slug }}</code>
                </td>
                <td>
                  <i class="bi bi-arrow-right text-muted mx-2"></i>
                  <a href="{{ url('/blog/' . $sh->new_slug) }}" target="_blank" class="fw-bold text-success text-decoration-none">
                    /{{ $sh->new_slug }}
                  </a>
                </td>
                <td>
                  <span class="badge bg-success-subtle text-success rounded-pill px-2.5">Auto 301 Redirection</span>
                </td>
                <td class="pe-4">
                  <small class="text-muted">{{ $sh->created_at ? $sh->created_at->format('M d, Y h:i A') : 'Recorded' }}</small>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center py-5 text-muted">
                  <i class="bi bi-clock-history fs-1 d-block mb-2 text-muted opacity-50"></i>
                  No slug changes recorded yet. Modifying any post or page slug will automatically log here.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if($slugHistories->hasPages())
        <div class="card-footer bg-white py-3 px-4 border-top">
          {{ $slugHistories->links() }}
        </div>
      @endif
    </div>
  @endif

  <!-- Tab 4: Robots & Sitemap -->
  @if($tab === 'robots')
    <div class="row g-4 mb-4">
      <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-robot text-primary me-2"></i> Dynamic robots.txt</h5>
            <a href="{{ url('/robots.txt') }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
              Open <i class="bi bi-box-arrow-up-right ms-1"></i>
            </a>
          </div>
          <p class="text-muted small mb-3">Robots.txt controls search engine crawlers (Googlebot, Bingbot, Yandex). It is dynamically served and automatically links to your sitemap.</p>
          <pre class="p-3 bg-light rounded-3 font-monospace small mb-3" style="max-height: 250px; overflow-y: auto;">{{ $robotsTxt }}</pre>
          <div class="d-flex gap-2">
            <a href="{{ route('admin.settings.seo') }}" class="btn btn-primary rounded-pill px-4 fw-semibold" style="background-color: #5551ff; border-color: #5551ff;">
              <i class="bi bi-pencil-square me-1"></i> Edit in SEO Suite
            </a>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-diagram-3 text-success me-2"></i> XML Sitemap System</h5>
            <a href="{{ url('/sitemap.xml') }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
              Inspect <i class="bi bi-box-arrow-up-right ms-1"></i>
            </a>
          </div>
          <p class="text-muted small mb-3">Full Google Image Sitemap compliant XML file covering all published Posts, Pages, Categories, Tags, Authors, and Media Assets. Generated daily by automated cron routine.</p>
          <div class="p-3 bg-light rounded-3 mb-3 small">
            <div class="d-flex justify-content-between py-1 border-bottom">
              <span class="text-muted">Target Path:</span>
              <code class="fw-bold">/sitemap.xml</code>
            </div>
            <div class="d-flex justify-content-between py-1 border-bottom">
              <span class="text-muted">Cron Schedule:</span>
              <span class="badge bg-success-subtle text-success">Daily (00:00 UTC)</span>
            </div>
            <div class="d-flex justify-content-between py-1">
              <span class="text-muted">Image Sitemaps:</span>
              <span class="badge bg-primary-subtle text-primary">Enabled (xmlns:image)</span>
            </div>
          </div>
          <form action="{{ route('admin.url-management.sitemap.generate') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold w-100">
              <i class="bi bi-lightning-charge-fill me-1"></i> Re-Generate &amp; Write Sitemap Now
            </button>
          </form>
        </div>
      </div>
    </div>
  @endif

</div>

<!-- Modal: Add 301/302 Redirect Rule -->
<div class="modal fade" id="createRedirectModal" tabindex="-1" aria-labelledby="createRedirectModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <form action="{{ route('admin.url-management.redirects.store') }}" method="POST">
        @csrf
        <div class="modal-header border-bottom py-3 px-4">
          <h5 class="modal-title fw-bold" id="createRedirectModalLabel">Create URL Redirect Rule</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label small fw-bold">Source URL / Old Path *</label>
            <input type="text" name="source_path" id="modalSourcePath" class="form-control rounded-3" placeholder="/old-category/my-article" required>
            <small class="text-muted">Relative path (e.g. <code>/old-page</code> or <code>/feed</code>)</small>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold">Target Destination *</label>
            <input type="text" name="target_path" class="form-control rounded-3" placeholder="/blog/new-article or https://..." required>
            <small class="text-muted">Relative path on this domain or absolute external URL</small>
          </div>
          <div class="row mb-3">
            <div class="col-6">
              <label class="form-label small fw-bold">Redirect Status Code</label>
              <select name="status_code" class="form-select rounded-3">
                <option value="301" selected>301 - Permanent (Recommended for SEO)</option>
                <option value="302">302 - Temporary</option>
              </select>
            </div>
            <div class="col-6">
              <label class="form-label small fw-bold">Rule Status</label>
              <div class="form-check form-switch mt-2">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="redirectActiveSwitch" checked>
                <label class="form-check-label small fw-semibold" for="redirectActiveSwitch">Enabled</label>
              </div>
            </div>
          </div>
          <div class="mb-2">
            <label class="form-label small fw-bold">Internal Note / Reason</label>
            <input type="text" name="description" class="form-control rounded-3" placeholder="e.g. Migration from old CMS or re-branded URL">
          </div>
        </div>
        <div class="modal-footer border-top py-3 px-4">
          <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold" style="background-color: #5551ff; border-color: #5551ff;">Save Redirect</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function openQuickRedirectModal(path) {
  var modalEl = document.getElementById('createRedirectModal');
  var inputEl = document.getElementById('modalSourcePath');
  if (inputEl) {
    inputEl.value = path;
  }
  var modal = new bootstrap.Modal(modalEl);
  modal.show();
}
</script>
@endsection
