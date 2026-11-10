@extends('layouts.admin')

@section('title', 'Visitor & Audience Analytics Dashboard - XivigAdmin PRO')

@section('content')
<div class="page-analytics-dashboard">

  <!-- Header -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">Visitor &amp; Audience Analytics</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <span class="breadcrumb-item active">Analytics</span>
      </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
      <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1.5 small">
        <span class="spinner-grow spinner-grow-sm text-success" role="status" style="width: 8px; height: 8px;"></span>
        <span>Live Telemetry Active</span>
      </span>
      <a href="{{ route('admin.search-analytics.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1.5 small shadow-sm">
        <i class="bi bi-search"></i>
        <span>Search Analytics</span>
      </a>
    </div>
  </div>

  <!-- KPI Metrics Grid -->
  <div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold">Total Visitors</span>
          <div class="p-2 rounded-3" style="background: rgba(85, 81, 255, 0.1); color: #5551ff;">
            <i class="bi bi-people-fill fs-5"></i>
          </div>
        </div>
        <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalVisitors) }}</h3>
        <small class="text-success fw-semibold"><i class="bi bi-arrow-up-short"></i> {{ number_format($uniqueVisitorsToday) }} active today</small>
      </div>
    </div>

    <div class="col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold">Total Page Views</span>
          <div class="p-2 rounded-3" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
            <i class="bi bi-eye-fill fs-5"></i>
          </div>
        </div>
        <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalPageViews) }}</h3>
        <small class="text-muted">{{ number_format($pageViewsToday) }} impressions today</small>
      </div>
    </div>

    <div class="col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold">Engagement Depth</span>
          <div class="p-2 rounded-3" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
            <i class="bi bi-bar-chart-steps fs-5"></i>
          </div>
        </div>
        <h3 class="fw-bold mb-0 text-dark">
          {{ $totalVisitors > 0 ? number_format($totalPageViews / max($totalVisitors, 1), 1) : '1.0' }}
        </h3>
        <small class="text-muted">Avg. pages per session</small>
      </div>
    </div>

    <div class="col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold">Audience Reach</span>
          <div class="p-2 rounded-3" style="background: rgba(6, 182, 212, 0.1); color: #06b6d4;">
            <i class="bi bi-globe-americas fs-5"></i>
          </div>
        </div>
        <h3 class="fw-bold mb-0 text-dark">{{ $countries->count() }}</h3>
        <small class="text-muted">Global territories detected</small>
      </div>
    </div>
  </div>

  <!-- Row 1: Top Posts & Traffic Breakdown -->
  <div class="row g-4 mb-4">
    <!-- Top Posts -->
    <div class="col-lg-7">
      <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="fw-bold mb-0"><i class="bi bi-file-earmark-post text-primary me-2"></i> Top Performing Articles</h5>
          <a href="{{ route('admin.posts.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">View All</a>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase text-muted">
              <tr>
                <th>Article Title</th>
                <th>Category</th>
                <th class="text-end">Impressions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($topPosts as $p)
                <tr>
                  <td>
                    <div class="d-flex align-items-center gap-2.5">
                      <img src="{{ $p->image?->webp_url ?? $p->image?->url ?? asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}" class="rounded-2 flex-shrink-0" style="width: 38px; height: 38px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('admin-dist/img/avatar.png') }}';">
                      <div class="min-w-0">
                        <a href="{{ url('/blog/' . $p->slug) }}" target="_blank" class="fw-bold text-dark text-decoration-none small text-truncate d-block" style="max-width: 280px;">
                          {{ $p->title }}
                        </a>
                        <small class="text-muted">{{ $p->published_at ? $p->published_at->format('M d, Y') : 'Recent' }} · {{ $p->reading_time ?? 1 }}m read</small>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="badge bg-light text-primary border small font-weight-normal">{{ $p->category?->name ?? 'Article' }}</span>
                  </td>
                  <td class="text-end fw-bold text-dark">
                    {{ number_format($p->views_count) }}
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="3" class="text-center py-4 text-muted">No post traffic recorded yet.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Country Geography Distribution -->
    <div class="col-lg-5">
      <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
        <h5 class="fw-bold mb-3"><i class="bi bi-geo-alt text-danger me-2"></i> Top Audience Countries</h5>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase text-muted">
              <tr>
                <th>Country</th>
                <th class="text-end">Impressions</th>
                <th class="text-end">Share</th>
              </tr>
            </thead>
            <tbody>
              @php $totalCountryViews = $countries->sum('views') ?: 1; @endphp
              @forelse($countries as $c)
                @php $share = round(($c->views / $totalCountryViews) * 100, 1); @endphp
                <tr>
                  <td class="fw-bold text-dark">
                    <i class="bi bi-globe2 text-muted me-1.5"></i> {{ $c->country ?: 'United States' }}
                  </td>
                  <td class="text-end text-muted small font-monospace">
                    {{ number_format($c->views) }}
                  </td>
                  <td class="text-end">
                    <div class="d-inline-flex align-items-center gap-1.5">
                      <div class="progress" style="width: 50px; height: 6px;">
                        <div class="progress-bar bg-primary" style="width: {{ $share }}%"></div>
                      </div>
                      <small class="fw-semibold text-muted">{{ $share }}%</small>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="3" class="text-center py-4 text-muted">Awaiting location telemetry.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Row 2: Devices, Browsers & Referrers -->
  <div class="row g-4 mb-4">
    <!-- Devices -->
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
        <h5 class="fw-bold mb-3"><i class="bi bi-phone text-warning me-2"></i> Device Breakdown</h5>
        <ul class="list-group list-group-flush mb-0">
          @forelse($devices as $dev)
            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5">
              <span class="d-flex align-items-center gap-2">
                @if($dev->device === 'Desktop')
                  <i class="bi bi-laptop text-primary fs-5"></i>
                @elseif($dev->device === 'Mobile')
                  <i class="bi bi-phone text-success fs-5"></i>
                @else
                  <i class="bi bi-tablet text-info fs-5"></i>
                @endif
                <span class="fw-semibold text-dark">{{ $dev->device }}</span>
              </span>
              <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill">{{ number_format($dev->count) }}</span>
            </li>
          @empty
            <li class="list-group-item px-0 text-muted">No device records yet.</li>
          @endforelse
        </ul>
      </div>
    </div>

    <!-- Browsers -->
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
        <h5 class="fw-bold mb-3"><i class="bi bi-browser-chrome text-info me-2"></i> Browsers</h5>
        <ul class="list-group list-group-flush mb-0">
          @forelse($browsers as $br)
            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5">
              <span class="fw-semibold text-dark">{{ $br->browser }}</span>
              <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill">{{ number_format($br->count) }}</span>
            </li>
          @empty
            <li class="list-group-item px-0 text-muted">No browser telemetry.</li>
          @endforelse
        </ul>
      </div>
    </div>

    <!-- Referrers -->
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
        <h5 class="fw-bold mb-3"><i class="bi bi-box-arrow-up-right text-success me-2"></i> Top Referrers</h5>
        <ul class="list-group list-group-flush mb-0">
          @forelse($referrers as $ref)
            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5">
              <span class="small fw-semibold text-dark text-truncate" style="max-width: 180px;">
                {{ parse_url($ref->referer, PHP_URL_HOST) ?: $ref->referer }}
              </span>
              <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-pill">{{ number_format($ref->count) }}</span>
            </li>
          @empty
            <li class="list-group-item px-0 text-muted small">Most visitors originate from direct or organic links.</li>
          @endforelse
        </ul>
      </div>
    </div>
  </div>

  <!-- Row 3: Popular Categories & Search Keywords -->
  <div class="row g-4 mb-4">
    <!-- Popular Categories -->
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
        <h5 class="fw-bold mb-3"><i class="bi bi-folder2-open text-primary me-2"></i> Popular Categories</h5>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase text-muted">
              <tr>
                <th>Category</th>
                <th class="text-end">Articles Count</th>
                <th class="text-end">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($popularCategories as $cat)
                <tr>
                  <td class="fw-bold text-dark">{{ $cat->name }}</td>
                  <td class="text-end fw-semibold">{{ $cat->posts_count }}</td>
                  <td class="text-end">
                    <a href="{{ url('/category/' . $cat->slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 py-0.5 small">
                      View <i class="bi bi-arrow-up-right" style="font-size: 10px;"></i>
                    </a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Search Keywords -->
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="fw-bold mb-0"><i class="bi bi-search text-warning me-2"></i> Leading Search Keywords</h5>
          <a href="{{ route('admin.search-analytics.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Full Analytics</a>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase text-muted">
              <tr>
                <th>Keyword</th>
                <th>Search Count</th>
                <th class="text-end">Results</th>
              </tr>
            </thead>
            <tbody>
              @forelse($searchKeywords as $sk)
                <tr>
                  <td><code class="fw-bold text-dark">{{ $sk->query }}</code></td>
                  <td><span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1">{{ $sk->hits_count }}</span></td>
                  <td class="text-end">
                    @if($sk->results_count > 0)
                      <span class="text-success small fw-semibold">{{ $sk->results_count }} matched</span>
                    @else
                      <span class="badge bg-danger-subtle text-danger rounded-pill">0</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="3" class="text-center py-4 text-muted">No search keywords recorded.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
