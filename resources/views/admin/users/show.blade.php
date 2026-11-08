@extends('layouts.admin')

@section('title', 'User Authority & Profile: ' . $user->name . ' - XivigAdmin PRO')

@section('content')
<div class="page-users-show">

  <!-- Header -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">User Profile &amp; Authority</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <a href="{{ route('admin.users.index') }}" class="breadcrumb-item">Users</a>
        <span class="breadcrumb-item active">{{ $user->name }}</span>
      </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
      <button type="button" class="btn btn-warning d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#showReassignModal">
        <i class="bi bi-arrow-left-right"></i>
        <span>Reassign Authority</span>
      </button>
      <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
        <i class="bi bi-pencil"></i>
        <span>Edit Account</span>
      </a>
      <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Directory
      </a>
    </div>
  </div>

  <div class="row g-4">
    <!-- Left Column: User Card & Authority Matrix -->
    <div class="col-lg-4">
      <!-- Profile Summary -->
      <div class="card mb-4">
        <div class="card-body text-center p-4">
          @if ($user->profile_photo_path)
            <img src="{{ asset($user->profile_photo_path) }}" alt="{{ $user->name }}" class="rounded-circle mb-3 border shadow-sm" style="width: 88px; height: 88px; object-fit: cover;">
          @else
            <div class="avatar-initials bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold mx-auto mb-3 shadow-sm" style="width: 88px; height: 88px; font-size: 32px;">
              {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
          @endif
          <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
          <p class="text-muted small mb-2">{{ $user->email }}</p>
          <div class="d-flex justify-content-center gap-1 flex-wrap mb-3">
            @forelse ($user->roles as $role)
              <span class="badge bg-{{ $role->color }}">{{ $role->name }}</span>
            @empty
              <span class="badge bg-secondary-subtle text-secondary">Standard Reader</span>
            @endforelse
            @if ($user->author)
              <span class="badge bg-success-subtle text-success">Author ({{ $user->author->nickname }})</span>
            @endif
          </div>

          <div class="border-top pt-3 mt-3 d-flex justify-content-around text-center">
            <div>
              <div class="fs-4 fw-bold text-primary">{{ $user->posts_count }}</div>
              <small class="text-muted">Articles</small>
            </div>
            <div class="border-start"></div>
            <div>
              <div class="fs-4 fw-bold text-info">{{ $user->comments_count }}</div>
              <small class="text-muted">Discussions</small>
            </div>
            <div class="border-start"></div>
            <div>
              <div class="fs-4 fw-bold text-success">{{ $user->created_at ? $user->created_at->format('Y') : '-' }}</div>
              <small class="text-muted">Member Since</small>
            </div>
          </div>
        </div>
      </div>

      <!-- Authority & Roles Card -->
      <div class="card mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
          <h6 class="card-title mb-0 fw-bold">
            <i class="bi bi-shield-check text-primary me-1"></i> Security Authority
          </h6>
          <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#showRolesModal">
            Edit Roles
          </button>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label class="text-muted small text-uppercase fw-semibold mb-1 d-block">Assigned Roles</label>
            @if ($user->roles->count() > 0)
              <ul class="list-unstyled mb-0">
                @foreach ($user->roles as $r)
                  <li class="d-flex align-items-center justify-content-between py-1.5 border-bottom">
                    <div>
                      <span class="badge bg-{{ $r->color }}-subtle text-{{ $r->color }} fw-semibold">{{ $r->name }}</span>
                      <small class="text-muted d-block" style="font-size: 11px;">{{ $r->description }}</small>
                    </div>
                    <i class="bi bi-check-circle-fill text-success"></i>
                  </li>
                @endforeach
              </ul>
            @else
              <p class="text-muted small mb-0">No elevated security roles assigned. Has standard reader permissions.</p>
            @endif
          </div>

          <div>
            <label class="text-muted small text-uppercase fw-semibold mb-1 d-block">Granted Capabilities</label>
            <div class="d-flex flex-wrap gap-1">
              @php
                $allPerms = $user->roles->flatMap->permissions->unique('id');
              @endphp
              @forelse ($allPerms as $perm)
                <span class="badge bg-light text-dark border" style="font-size: 11px;">{{ $perm->name }}</span>
              @empty
                <span class="text-muted small">Standard authenticated permissions.</span>
              @endforelse
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Column: Articles & Discussions Activity -->
    <div class="col-lg-8">
      <!-- Authored Articles -->
      <div class="card mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
          <div>
            <h5 class="card-title mb-0">Authored Publications</h5>
            <small class="text-muted">Articles under this user's authority</small>
          </div>
          <span class="badge bg-primary-subtle text-primary">{{ $user->posts_count }} Total Articles</span>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Title</th>
                  <th>Category</th>
                  <th>Status</th>
                  <th>Published</th>
                  <th class="text-end">Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($user->posts as $post)
                  <tr>
                    <td>
                      <div class="fw-semibold text-truncate" style="max-width: 280px;">{{ $post->title }}</div>
                      <small class="text-muted">#{{ $post->id }} &bull; {{ $post->slug }}</small>
                    </td>
                    <td>
                      <span class="badge bg-light text-dark border">{{ $post->category?->name ?? 'Uncategorized' }}</span>
                    </td>
                    <td>
                      @if ($post->status)
                        <span class="badge bg-success-subtle text-success">Published</span>
                      @else
                        <span class="badge bg-warning-subtle text-warning">Draft</span>
                      @endif
                    </td>
                    <td>
                      <small class="text-muted">{{ $post->created_at ? $post->created_at->format('M d, Y') : '-' }}</small>
                    </td>
                    <td class="text-end">
                      <div class="btn-group btn-group-sm">
                        <a href="{{ route('admin.posts.show', $post->id) }}" class="btn btn-outline-secondary" title="View Article">
                          <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-outline-primary" title="Edit Article">
                          <i class="bi bi-pencil"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                      <i class="bi bi-journal-text fs-2 d-block mb-1"></i>
                      No publications authored yet.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Discussions & Comments Activity -->
      <div class="card">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
          <div>
            <h5 class="card-title mb-0">Recent Discussion Activity</h5>
            <small class="text-muted">Comments and community replies</small>
          </div>
          <span class="badge bg-info-subtle text-info">{{ $user->comments_count }} Comments</span>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Comment Snippet</th>
                  <th>Article</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($user->comments as $comment)
                  <tr>
                    <td>
                      <div class="text-truncate" style="max-width: 320px;">"{{ Str::limit($comment->comment, 80) }}"</div>
                    </td>
                    <td>
                      <span class="text-muted small">{{ $comment->post?->title ? Str::limit($comment->post->title, 35) : 'Deleted Post' }}</span>
                    </td>
                    <td>
                      <small class="text-muted">{{ $comment->created_at ? $comment->created_at->format('M d, Y') : '-' }}</small>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="3" class="text-center py-4 text-muted">
                      <i class="bi bi-chat-left-dots fs-2 d-block mb-1"></i>
                      No discussion comments recorded.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<!-- Reassign Authority Modal -->
<div class="modal fade" id="showReassignModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-warning-subtle py-3">
        <h5 class="modal-title fw-bold text-warning-emphasis d-flex align-items-center gap-2">
          <i class="bi bi-arrow-left-right"></i>
          <span>Reassign User Authority &amp; Ownership</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.users.reassign-authority', $user->id) }}" method="POST" id="showModalReassignForm">
        @csrf
        <input type="hidden" name="action_type" value="transfer">
        <div class="modal-body">
          <div class="alert alert-warning d-flex align-items-start gap-2 mb-4">
            <i class="bi bi-info-circle-fill fs-5 mt-0.5"></i>
            <div>
              Reassigning authority transfers all publications ({{ $user->posts_count }} articles) and discussion comments ({{ $user->comments_count }} comments) authored by <strong>{{ $user->name }}</strong> to a replacement user.
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Target Recipient User <span class="text-danger">*</span></label>
            <select name="target_user_id" class="form-select" required>
              <option value="">-- Choose recipient user to take over authority --</option>
              @foreach ($otherUsers as $ou)
                <option value="{{ $ou->id }}">#{{ $ou->id }} - {{ $ou->name }} ({{ $ou->email }})</option>
              @endforeach
            </select>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="transfer_posts" value="1" id="show_tp" checked>
                <label class="form-check-label fw-semibold small" for="show_tp">
                  Transfer all {{ $user->posts_count }} authored article(s)
                </label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="transfer_comments" value="1" id="show_tc" checked>
                <label class="form-check-label fw-semibold small" for="show_tc">
                  Transfer all {{ $user->comments_count }} discussion comment(s)
                </label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="copy_roles" value="1" id="show_cr">
                <label class="form-check-label fw-semibold small" for="show_cr">
                  Copy {{ $user->name }}'s security roles to recipient
                </label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="delete_source_user" value="1" id="show_ds">
                <label class="form-check-label fw-semibold small text-danger" for="show_ds">
                  Decommission &amp; delete this user after transfer
                </label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-warning">
            <i class="bi bi-arrow-left-right me-1"></i> Execute Authority Transfer
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Roles Modal -->
<div class="modal fade" id="showRolesModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header py-3">
        <h5 class="modal-title fw-bold">Update Security Roles</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.users.reassign-authority', $user->id) }}" method="POST">
        @csrf
        <input type="hidden" name="action_type" value="roles_only">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold d-block">Assigned Roles</label>
            @foreach ($roles as $r)
              <div class="border rounded p-2 mb-2">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $r->id }}" id="sr_{{ $r->id }}"
                    {{ $user->roles->contains('id', $r->id) ? 'checked' : '' }}>
                  <label class="form-check-label fw-semibold" for="sr_{{ $r->id }}">
                    <span class="badge bg-{{ $r->color }}-subtle text-{{ $r->color }}">{{ $r->name }}</span>
                  </label>
                  <small class="text-muted d-block mt-0.5" style="font-size: 11px;">{{ $r->description }}</small>
                </div>
              </div>
            @endforeach
          </div>

          <div class="form-check form-switch mt-3">
            <input class="form-check-input" type="checkbox" name="is_author" id="sr_isAuthor" value="1" {{ $user->author ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="sr_isAuthor">
              Author Contributor Status
            </label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Roles</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection