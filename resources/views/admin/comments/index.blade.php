@extends('layouts.admin')

@section('title', 'Comments Moderation - XivigAdmin PRO')

@section('content')
<div class="page-comments">

  <!-- Header -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">Discussions &amp; Reader Comments</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <a href="{{ route('admin.posts.index') }}" class="breadcrumb-item">Editorial</a>
        <span class="breadcrumb-item active">Comments</span>
      </nav>
    </div>
    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
      <strong>{{ count($comments) }}</strong> Total Responses
    </span>
  </div>

  <div class="card">
    <div class="card-header py-3">
      <h5 class="card-title mb-0">Reader Community Feedback</h5>
      <small class="text-muted">Review, moderate, and manage discussion threads on blog articles</small>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table id="commentsTable" class="table table-hover align-middle">
          <thead>
            <tr>
              <th style="width: 50px;">#</th>
              <th>Reader</th>
              <th>Comment Content</th>
              <th>Article</th>
              <th>Replies</th>
              <th>Submitted</th>
              <th data-sortable="false" class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($comments as $comment)
              <tr>
                <td>{{ $comment->id }}</td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="avatar-initials bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 34px; height: 34px; font-size: 13px;">
                      {{ strtoupper(substr($comment->user->name ?? 'A', 0, 1)) }}
                    </div>
                    <div>
                      <div class="fw-semibold small">{{ $comment->user->name ?? 'Anonymous User' }}</div>
                      <div class="text-muted small" style="font-size: 11px;">{{ $comment->user->email ?? '' }}</div>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="text-break" style="max-width: 320px;">
                    {{ $comment->comment }}
                  </div>
                </td>
                <td>
                  @if ($comment->post)
                    <a href="{{ route('single-post', $comment->post->slug) }}" target="_blank" class="fw-semibold text-decoration-none small">
                      {{ Str::limit($comment->post->title, 35) }} <i class="bi bi-box-arrow-up-right small"></i>
                    </a>
                  @else
                    <span class="text-muted small">Orphaned Comment</span>
                  @endif
                </td>
                <td>
                  <span class="badge bg-secondary-subtle text-secondary">
                    {{ $comment->replies ? $comment->replies->count() : 0 }}
                  </span>
                </td>
                <td>
                  <span class="small text-muted">{{ $comment->created_at ? $comment->created_at->diffForHumans() : 'N/A' }}</span>
                </td>
                <td class="text-end">
                  <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteComment('{{ $comment->id }}')" title="Remove Comment & Replies">
                    <i class="bi bi-trash"></i> Delete
                  </button>

                  <form id="delete-comment-{{ $comment->id }}" action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

@push('scripts')
<script>
  function deleteComment(id) {
    if (confirm('Are you sure you want to delete this comment and all nested replies?')) {
      document.getElementById('delete-comment-' + id).submit();
    }
  }
</script>
@endpush
@endsection
