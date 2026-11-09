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
        <div>
            <a href="{{ route('admin.pages.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Create New Page
            </a>
        </div>
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
                                    <div class="fw-semibold text-dark">{{ $page->title }}</div>
                                    <small class="text-muted"><code>/page/{{ $page->slug }}</code></small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ ucfirst($page->template) }}</span>
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
                                        <a href="{{ route('admin.pages.edit', $page->id) }}" class="btn btn-outline-primary" title="Edit Page">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.pages.destroy', $page->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this page?')">
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
