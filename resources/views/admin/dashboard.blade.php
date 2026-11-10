@extends('layouts.admin')

@section('title', 'Dashboard - XivigAdmin PRO')

@section('content')
<div class="page-dashboard nd-dashboard">

  <!-- Executive Topbar -->
  <section class="nd-topbar d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="nd-title mb-1">Executive Blog Command Center</h1>
      <p class="nd-subtitle text-muted mb-0">Unified editorial workflows, readership performance, and publishing operations.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('admin.posts.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 px-3 py-2">
        <i class="bi bi-pencil-square"></i>
        <span>Compose Article</span>
      </a>
      <a href="{{ url('/blog') }}" target="_blank" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 px-3 py-2">
        <i class="bi bi-box-arrow-up-right"></i>
        <span>Live Blog</span>
      </a>
    </div>
  </section>

  <!-- KPI Metric Cards (Using XivigAdmin PRO Grid) -->
  <section class="row g-3 mb-4 nd-kpi-rail">
    <!-- Total Articles -->
    <div class="col-xxl-3 col-md-6">
      <article class="nd-kpi-card nd-kpi-revenue">
        <span class="nd-kpi-icon"><i class="bi bi-journal-richtext"></i></span>
        <span class="nd-kpi-label">Total Articles</span>
        <strong class="nd-kpi-value">{{ $totalPosts }}</strong>
        <span class="nd-kpi-trend up"><i class="bi bi-arrow-up-right"></i> Full Catalog</span>
      </article>
    </div>

    <!-- Published Posts -->
    <div class="col-xxl-3 col-md-6">
      <article class="nd-kpi-card nd-kpi-leads">
        <span class="nd-kpi-icon"><i class="bi bi-check-circle"></i></span>
        <span class="nd-kpi-label">Published Live</span>
        <strong class="nd-kpi-value">{{ $publishedPosts }}</strong>
        <span class="nd-kpi-trend up"><i class="bi bi-broadcast"></i> {{ $totalPosts > 0 ? round(($publishedPosts / $totalPosts) * 100) : 0 }}% active</span>
      </article>
    </div>

    <!-- Drafts / In Review -->
    <div class="col-xxl-3 col-md-6">
      <article class="nd-kpi-card nd-kpi-conversion">
        <span class="nd-kpi-icon"><i class="bi bi-file-earmark-diff"></i></span>
        <span class="nd-kpi-label">Draft Articles</span>
        <strong class="nd-kpi-value">{{ $draftPosts }}</strong>
        <span class="nd-kpi-trend"><i class="bi bi-hourglass-split"></i> In Pipeline</span>
      </article>
    </div>

    <!-- Comments & Discussions -->
    <div class="col-xxl-3 col-md-6">
      <article class="nd-kpi-card nd-kpi-active">
        <span class="nd-kpi-icon"><i class="bi bi-chat-square-quote"></i></span>
        <span class="nd-kpi-label">Total Discussions</span>
        <strong class="nd-kpi-value">{{ $totalComments }}</strong>
        <span class="nd-kpi-trend up"><i class="bi bi-people"></i> Moderated</span>
      </article>
    </div>
  </section>

  <!-- Editorial Main Grid -->
  <div class="row g-4">
    <!-- Left Column: Recent Articles & Quick Draft -->
    <div class="col-xl-8">

      <!-- Recent Articles Card -->
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
          <div>
            <h5 class="card-title mb-0">Recent Editorial Articles</h5>
            <small class="text-muted">Latest posts created across categories</small>
          </div>
          <a href="{{ route('admin.posts.index') }}" class="btn btn-sm btn-outline-primary">
            View All Posts <i class="bi bi-arrow-right ms-1"></i>
          </a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th style="width: 50px;">Img</th>
                  <th>Title &amp; Category</th>
                  <th>Author</th>
                  <th>Status</th>
                  <th>Date</th>
                  <th class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($recentPosts as $post)
                  <tr>
                    <td>
                      @if ($post->image && $post->image->link)
                        <img src="{{ Str::startsWith($post->image->link, ['http', 'https', 'uploads/', 'xivig-admin/', 'xivig-public/']) ? asset($post->image->link) : asset('xivig-public/images/blog/' . $post->image->link) }}" alt="" class="post-thumb-sm" onerror="this.onerror=null; this.src='{{ asset('xivig-admin/img/blog-1-PTDVKJDt.webp') }}'">
                      @else
                        <img src="{{ asset('xivig-admin/img/blog-1-PTDVKJDt.webp') }}" alt="" class="post-thumb-sm">
                      @endif
                    </td>
                    <td>
                      <div class="fw-semibold text-truncate" style="max-width: 280px;">
                        <a href="{{ route('admin.posts.edit', $post->id) }}" class="text-decoration-none text-reset">
                          {{ $post->title }}
                        </a>
                      </div>
                      <span class="badge bg-secondary-subtle text-secondary px-2 py-0.5 mt-1 small">
                        {{ $post->category->name ?? 'General' }}
                      </span>
                    </td>
                    <td>
                      <span class="small">{{ $post->author->nickname ?? ($post->author->user->name ?? 'Admin') }}</span>
                    </td>
                    <td>
                      @if ($post->status)
                        <span class="badge badge-published px-2.5 py-1">Published</span>
                      @else
                        <span class="badge badge-draft px-2.5 py-1">Draft</span>
                      @endif
                    </td>
                    <td>
                      <span class="text-muted small">{{ $post->created_at ? $post->created_at->format('M d, Y') : 'N/A' }}</span>
                    </td>
                    <td class="text-end">
                      <div class="btn-group btn-group-sm">
                        <a href="{{ route('single-post', $post->slug) }}" target="_blank" class="btn btn-outline-secondary" title="View on site">
                          <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                        <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-outline-primary" title="Edit Post">
                          <i class="bi bi-pencil"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                      No blog posts recorded yet. Click "Compose Article" to get started!
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Quick Draft Card -->
      <div class="card mb-4">
        <div class="card-header py-3">
          <h5 class="card-title mb-0">Quick Idea &amp; Draft Note</h5>
          <small class="text-muted">Jot down article concepts directly into your editorial pipeline</small>
        </div>
        <div class="card-body">
          <form action="{{ route('admin.posts.quick-draft') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label for="draftTitle" class="form-label fw-semibold">Draft Headline</label>
              <input type="text" name="title" id="draftTitle" class="form-control" placeholder="E.g., Microservices patterns with Laravel and Kafka..." required>
            </div>
            <div class="mb-3">
              <label for="draftContent" class="form-label fw-semibold">Outline &amp; Notes</label>
              <textarea name="content" id="draftContent" class="form-control" rows="4" placeholder="Brief outline, references, key bullet points..." required></textarea>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-muted small"><i class="bi bi-info-circle me-1"></i> Saves directly as an unpublished draft article.</span>
              <button type="submit" class="btn btn-primary btn-sm px-3">
                <i class="bi bi-save me-1"></i> Save Draft
              </button>
            </div>
          </form>
        </div>
      </div>

    </div>

    <!-- Right Column: Top Categories & Comments -->
    <div class="col-xl-4">

      <!-- Category Distribution -->
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
          <div>
            <h5 class="card-title mb-0">Categories</h5>
            <small class="text-muted">Post volume by topic</small>
          </div>
          <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-link p-0 text-decoration-none">Manage</a>
        </div>
        <div class="card-body">
          <ul class="list-group list-group-flush">
            @forelse ($topCategories as $cat)
              <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5">
                <div class="d-flex align-items-center">
                  <span class="me-2 text-primary"><i class="bi bi-folder-fill"></i></span>
                  <span class="fw-semibold">{{ $cat->name }}</span>
                </div>
                <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5">
                  {{ $cat->posts_count }} {{ Str::plural('post', $cat->posts_count) }}
                </span>
              </li>
            @empty
              <li class="list-group-item text-center text-muted px-0">No categories found.</li>
            @endforelse
          </ul>
        </div>
      </div>

      <!-- Recent Discussions -->
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
          <div>
            <h5 class="card-title mb-0">Recent Comments</h5>
            <small class="text-muted">Latest community reader interactions</small>
          </div>
          <a href="{{ route('admin.comments.index') }}" class="btn btn-sm btn-link p-0 text-decoration-none">Moderate</a>
        </div>
        <div class="card-body p-0">
          <div class="list-group list-group-flush">
            @forelse ($recentComments as $comment)
              <div class="list-group-item p-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <strong class="small text-truncate" style="max-width: 180px;">
                    {{ $comment->user->name ?? 'Anonymous Reader' }}
                  </strong>
                  <span class="text-muted small">{{ $comment->created_at ? $comment->created_at->diffForHumans() : '' }}</span>
                </div>
                <p class="text-muted small mb-1 text-truncate">{{ $comment->comment }}</p>
                <div class="small">
                  <span class="text-secondary">On: </span>
                  <a href="{{ route('single-post', $comment->post->slug ?? '#') }}" target="_blank" class="text-decoration-none fw-semibold">
                    {{ Str::limit($comment->post->title ?? 'Post', 30) }}
                  </a>
                </div>
              </div>
            @empty
              <div class="p-4 text-center text-muted small">No comments recorded yet.</div>
            @endforelse
          </div>
        </div>
      </div>

      <!-- Quick Platform Status -->
      <div class="card">
        <div class="card-header py-3">
          <h5 class="card-title mb-0">System &amp; Engine</h5>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between py-1.5 border-bottom">
            <span class="text-muted small">Framework</span>
            <span class="fw-semibold small">Laravel 13 Compatible</span>
          </div>
          <div class="d-flex justify-content-between py-1.5 border-bottom">
            <span class="text-muted small">Admin Guard</span>
            <span class="badge bg-success-subtle text-success small">Auth::guard('admin')</span>
          </div>
          <div class="d-flex justify-content-between py-1.5 border-bottom">
            <span class="text-muted small">WYSIWYG Engine</span>
            <span class="fw-semibold small">Quill v2 Snow</span>
          </div>
          <div class="d-flex justify-content-between py-1.5">
            <span class="text-muted small">Datatables</span>
            <span class="fw-semibold small">simple-datatables</span>
          </div>
        </div>
      </div>

    </div>
  </div>

</div>
@endsection
