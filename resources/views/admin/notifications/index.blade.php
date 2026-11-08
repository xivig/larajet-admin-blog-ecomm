@extends('layouts.admin')

@section('title', 'Notification Center - XivigAdmin PRO')

@section('content')
<div class="page-notifications">

  <!-- Header -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">Notification Center</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <span class="breadcrumb-item active">Notifications</span>
      </nav>
    </div>
    <div class="d-flex gap-2">
      <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline-primary btn-sm">
          <i class="bi bi-check2-all me-1"></i> Mark All as Read
        </button>
      </form>
      <form action="{{ route('admin.notifications.clear-read') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-trash3 me-1"></i> Clear Read
        </button>
      </form>
    </div>
  </div>

  <!-- KPI Badges -->
  <div class="row g-3 mb-4">
    <div class="col-xl-4 col-sm-6">
      <div class="card border-0 shadow-sm">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="rounded-3 p-3 bg-primary-subtle text-primary fs-3">
            <i class="bi bi-bell-fill"></i>
          </div>
          <div>
            <h3 class="fw-bold mb-0">{{ $unreadCount }}</h3>
            <span class="text-muted small">Unread Alerts</span>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-4 col-sm-6">
      <div class="card border-0 shadow-sm">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="rounded-3 p-3 bg-success-subtle text-success fs-3">
            <i class="bi bi-chat-heart-fill"></i>
          </div>
          <div>
            <h3 class="fw-bold mb-0">{{ \App\Models\AdminNotification::where('type', 'comment')->count() }}</h3>
            <span class="text-muted small">Discussion Signals</span>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-4 col-sm-6">
      <div class="card border-0 shadow-sm">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="rounded-3 p-3 bg-info-subtle text-info fs-3">
            <i class="bi bi-shield-check"></i>
          </div>
          <div>
            <h3 class="fw-bold mb-0">{{ \App\Models\AdminNotification::where('type', 'alert')->count() }}</h3>
            <span class="text-muted small">Security &amp; System</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Notifications List Card -->
  <div class="card">
    <div class="card-header py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
      <h5 class="card-title mb-0">System Activity &amp; Reader Alerts</h5>
      <ul class="nav nav-pills gap-1">
        <li class="nav-item">
          <a class="nav-link py-1 px-2.5 small {{ !request('filter') && !request('type') ? 'active' : '' }}" href="{{ route('admin.notifications.index') }}">All</a>
        </li>
        <li class="nav-item">
          <a class="nav-link py-1 px-2.5 small {{ request('filter') === 'unread' ? 'active' : '' }}" href="{{ route('admin.notifications.index', ['filter' => 'unread']) }}">Unread</a>
        </li>
        <li class="nav-item">
          <a class="nav-link py-1 px-2.5 small {{ request('type') === 'comment' ? 'active' : '' }}" href="{{ route('admin.notifications.index', ['type' => 'comment']) }}">Comments</a>
        </li>
        <li class="nav-item">
          <a class="nav-link py-1 px-2.5 small {{ request('type') === 'user' ? 'active' : '' }}" href="{{ route('admin.notifications.index', ['type' => 'user']) }}">Users</a>
        </li>
        <li class="nav-item">
          <a class="nav-link py-1 px-2.5 small {{ request('type') === 'alert' ? 'active' : '' }}" href="{{ route('admin.notifications.index', ['type' => 'alert']) }}">Security</a>
        </li>
      </ul>
    </div>
    <div class="card-body p-0">
      <div class="list-group list-group-flush">
        @forelse ($notifications as $n)
          <div class="list-group-item list-group-item-action p-3 d-flex align-items-start gap-3 {{ !$n->is_read ? 'bg-body-tertiary fw-semibold' : '' }}">
            <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;
              background-color: {{ $n->type === 'comment' ? 'rgba(13, 110, 253, 0.12)' : ($n->type === 'user' ? 'rgba(25, 135, 84, 0.12)' : ($n->type === 'alert' ? 'rgba(220, 53, 69, 0.12)' : 'rgba(108, 117, 125, 0.12)')) }};
              color: {{ $n->type === 'comment' ? '#0d6efd' : ($n->type === 'user' ? '#198754' : ($n->type === 'alert' ? '#dc3545' : '#6c757d')) }};">
              @if ($n->type === 'comment')
                <i class="bi bi-chat-dots-fill fs-5"></i>
              @elseif ($n->type === 'user')
                <i class="bi bi-person-fill-add fs-5"></i>
              @elseif ($n->type === 'alert')
                <i class="bi bi-exclamation-octagon-fill fs-5"></i>
              @else
                <i class="bi bi-info-circle-fill fs-5"></i>
              @endif
            </div>

            <div class="flex-grow-1 min-w-0">
              <div class="d-flex align-items-center justify-content-between mb-1">
                <h6 class="mb-0 fs-6 {{ !$n->is_read ? 'fw-bold' : 'fw-semibold' }}">{{ $n->title }}</h6>
                <small class="text-muted">{{ $n->created_at ? $n->created_at->diffForHumans() : '' }}</small>
              </div>
              <p class="mb-2 text-muted small">{{ $n->message }}</p>
              <div class="d-flex align-items-center gap-2">
                @if ($n->link)
                  <a href="{{ route('admin.notifications.read', $n->id) }}" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 12px;">
                    View Details <i class="bi bi-arrow-right ms-1"></i>
                  </a>
                @endif
                @if (!$n->is_read)
                  <form action="{{ route('admin.notifications.read', $n->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-link text-decoration-none py-0 px-2 text-muted" style="font-size: 12px;">
                      Mark as read
                    </button>
                  </form>
                @endif
              </div>
            </div>

            <form action="{{ route('admin.notifications.destroy', $n->id) }}" method="POST">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm text-muted p-1" title="Dismiss">
                <i class="bi bi-x-lg"></i>
              </button>
            </form>
          </div>
        @empty
          <div class="p-5 text-center text-muted">
            <i class="bi bi-bell-slash fs-1 d-block mb-2"></i>
            No notifications matching this filter.
          </div>
        @endforelse
      </div>

      @if ($notifications->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
          {{ $notifications->links() }}
        </div>
      @endif
    </div>
  </div>

</div>
@endsection
