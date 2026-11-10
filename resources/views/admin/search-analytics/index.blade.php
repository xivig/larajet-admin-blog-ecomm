@extends('layouts.admin')

@section('title', 'Search Intelligence & Analytics - XivigAdmin PRO')

@section('content')
<div class="page-search-analytics">

  <!-- Header -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">Search Intelligence &amp; Analytics</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <span class="breadcrumb-item active">Search Analytics</span>
      </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
      <form action="{{ route('admin.search-analytics.clear') }}" method="POST" onsubmit="return confirm('Reset all search query records?');">
        @csrf
        <button type="submit" class="btn btn-outline-danger rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm small">
          <i class="bi bi-trash"></i>
          <span>Clear Analytics</span>
        </button>
      </form>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <!-- Metrics Cards -->
  <div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold">Total Queries</span>
          <div class="p-2 rounded-3" style="background: rgba(85, 81, 255, 0.1); color: #5551ff;">
            <i class="bi bi-search fs-5"></i>
          </div>
        </div>
        <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['total_searches']) }}</h3>
        <small class="text-muted">Total user searches performed</small>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold">Distinct Queries</span>
          <div class="p-2 rounded-3" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
            <i class="bi bi-lightbulb fs-5"></i>
          </div>
        </div>
        <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['unique_queries']) }}</h3>
        <small class="text-muted">Unique topics searched</small>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold">Zero-Result Queries</span>
          <div class="p-2 rounded-3" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
            <i class="bi bi-question-circle fs-5"></i>
          </div>
        </div>
        <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['zero_results_count']) }}</h3>
        <small class="text-danger fw-semibold">Editorial content opportunities</small>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted small fw-semibold">Most Popular Query</span>
          <div class="p-2 rounded-3" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
            <i class="bi bi-fire fs-5"></i>
          </div>
        </div>
        <h3 class="fw-bold mb-0 text-dark text-truncate">{{ $stats['top_query'] }}</h3>
        <small class="text-muted">Leading interest keyword</small>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <!-- Top 10 Popular Queries -->
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
        <h5 class="fw-bold mb-3"><i class="bi bi-graph-up-arrow text-primary me-2"></i> Top Search Keywords</h5>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase text-muted">
              <tr>
                <th>Keyword / Query</th>
                <th>Search Count</th>
                <th>Results Found</th>
              </tr>
            </thead>
            <tbody>
              @forelse($topQueries as $tq)
                <tr>
                  <td>
                    <code class="fw-bold text-dark fs-6">{{ $tq->query }}</code>
                  </td>
                  <td>
                    <span class="badge bg-primary rounded-pill px-2.5 py-1">{{ number_format($tq->hits_count) }} searches</span>
                  </td>
                  <td>
                    @if($tq->results_count > 0)
                      <span class="text-success fw-semibold">{{ $tq->results_count }} articles</span>
                    @else
                      <span class="badge bg-danger-subtle text-danger rounded-pill">0 (No Match)</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="3" class="text-center py-4 text-muted">No searches logged yet.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Recent Search Stream -->
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
        <h5 class="fw-bold mb-3"><i class="bi bi-clock-history text-success me-2"></i> Real-Time Recent Activity</h5>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase text-muted">
              <tr>
                <th>Query</th>
                <th>Origin IP</th>
                <th>Timestamp</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentQueries as $rq)
                <tr>
                  <td>
                    <span class="fw-semibold text-dark">{{ $rq->query }}</span>
                  </td>
                  <td>
                    <small class="text-muted font-monospace">{{ $rq->ip ?: '127.0.0.1' }}</small>
                  </td>
                  <td>
                    <small class="text-muted">{{ $rq->last_searched_at ? $rq->last_searched_at->diffForHumans() : 'Recent' }}</small>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="3" class="text-center py-4 text-muted">No recent queries.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Detailed Full Query Database -->
  <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
    <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
      <h5 class="fw-bold mb-0">All Search Logs &amp; Audit Trail</h5>
      <form action="{{ route('admin.search-analytics.index') }}" method="GET" class="d-flex align-items-center gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Filter keyword..." class="form-control form-control-sm rounded-pill px-3" style="width: 200px;">
        <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3">Filter</button>
      </form>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light small text-uppercase text-muted">
          <tr>
            <th class="ps-4">Query String</th>
            <th>Search Frequency</th>
            <th>Live Results Matched</th>
            <th>Client IP</th>
            <th class="pe-4">Last Searched</th>
          </tr>
        </thead>
        <tbody>
          @forelse($logs as $log)
            <tr>
              <td class="ps-4">
                <span class="fw-bold text-dark">{{ $log->query }}</span>
              </td>
              <td>
                <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill">{{ $log->hits_count }}</span>
              </td>
              <td>
                @if($log->results_count > 0)
                  <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">{{ $log->results_count }} articles</span>
                @else
                  <span class="badge bg-danger-subtle text-danger rounded-pill px-2.5 py-1">0 results</span>
                @endif
              </td>
              <td>
                <small class="text-muted font-monospace">{{ $log->ip ?? 'N/A' }}</small>
              </td>
              <td class="pe-4">
                <small class="text-muted">{{ $log->last_searched_at ? $log->last_searched_at->format('M d, Y h:i A') : 'Recorded' }}</small>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center py-5 text-muted">No query records found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($logs->hasPages())
      <div class="card-footer bg-white py-3 px-4 border-top">
        {{ $logs->links() }}
      </div>
    @endif
  </div>

</div>
@endsection
