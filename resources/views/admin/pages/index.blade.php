@extends('layouts.admin')

@section('title', 'Pages Management - Xivig Admin')

@section('content')
<div class="page-pages">
    <!-- Header -->
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="page-title mb-1">Static Pages Management</h1>
            <nav class="breadcrumb mb-0">
                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
                <span class="breadcrumb-item active">Pages</span>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ url('/') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-box-arrow-up-right me-1"></i> Front Page
            </a>
            <a href="{{ route('admin.menu.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-list-nested me-1"></i> Menu Builder
            </a>
            <a href="{{ route('admin.pages.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Create New Page
            </a>
        </div>
    </div>

    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-2 py-2 px-3 mb-4 small">
        <i class="bi bi-link-45deg fs-5 text-primary"></i>
        <span>Published pages live at <code>/page/{slug}</code> and automatically appear in the front-end <strong>Pages</strong> navbar dropdown &amp; Footer.</span>
    </div>

    <!-- Filter / Search Card -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body py-3">
            <form action="{{ route('admin.pages.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search pages by title or slug..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Published</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('admin.pages.index') }}" class="btn btn-sm btn-light">Clear</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Pages Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Title &amp; URL</th>
                            <th>Template</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pages as $page)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-semibold text-body">{{ $page->title }}</div>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        @if ($page->status)
                                            <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="small text-primary text-decoration-none">
                                                <code>/page/{{ $page->slug }}</code> <i class="bi bi-box-arrow-up-right" style="font-size: 0.7rem;"></i>
                                            </a>
                                        @else
                                            <small class="text-muted"><code>/page/{{ $page->slug }}</code> (unpublished)</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $page->template_name }}</span>
                                </td>
                                <td>
                                    @if ($page->status)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Published</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Draft</span>
                                    @endif
                                </td>
                                <td class="small text-muted">
                                    {{ $page->updated_at->format('M d, Y') }}
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group btn-group-sm">
                                        @if ($page->status)
                                            <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="btn btn-outline-success" title="View on Front Site">
                                                <i class="bi bi-box-arrow-up-right"></i>
                                            </a>
                                        @else
                                            <button type="button" class="btn btn-outline-secondary" disabled title="Publish page first to view on front site">
                                                <i class="bi bi-eye-slash"></i>
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-outline-secondary copy-page-link-btn" data-url="{{ route('page.show', $page->slug) }}" title="Copy Page URL">
                                            <i class="bi bi-link-45deg"></i>
                                        </button>
                                        <a href="{{ route('admin.pages.edit', $page->id) }}" class="btn btn-outline-primary" title="Edit Page">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.pages.destroy', $page->id) }}"
                                              method="POST"
                                              class="d-inline js-swal-confirm"
                                              data-confirm-title="Delete Page?"
                                              data-confirm-text="Move page &quot;{{ $page->title }}&quot; to Trash?"
                                              data-confirm-button="Yes, Delete Page">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete Page">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-file-earmark-text fs-2 d-block mb-2 text-secondary"></i>
                                    No pages found. Click <strong>Create New Page</strong> to add your first static page.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($pages->hasPages())
                <div class="p-3 border-top">
                    {{ $pages->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.copy-page-link-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const url = btn.dataset.url;
        if (!url) return;
        navigator.clipboard?.writeText(url).then(function () {
            const icon = btn.querySelector('i');
            if (!icon) return;
            const prev = icon.className;
            icon.className = 'bi bi-check2 text-success';
            setTimeout(function () { icon.className = prev; }, 1600);
        });
    });
});
</script>
@endpush
