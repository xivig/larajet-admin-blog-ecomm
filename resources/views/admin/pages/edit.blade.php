@extends('layouts.admin')

@section('title', 'Edit Page - Xivig Admin')

@section('content')
<div class="page-pages-edit">
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title mb-1">Edit Static Page</h1>
            <nav class="breadcrumb mb-0">
                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
                <a href="{{ route('admin.pages.index') }}" class="breadcrumb-item">Pages</a>
                <span class="breadcrumb-item active">{{ $page->title }}</span>
            </nav>
        </div>
        <div class="d-flex gap-2">
            @if ($page->status)
                <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="btn btn-success btn-sm">
                    <i class="bi bi-box-arrow-up-right me-1"></i> View Live Page
                </a>
            @endif
            <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Pages
            </a>
        </div>
    </div>

    <form action="{{ route('admin.pages.update', $page->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">Page Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $page->title) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label fw-semibold">Custom Slug</label>
                            <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $page->slug) }}">
                        </div>

                        @include('admin.pages._content-editor', ['content' => old('content', $page->content)])

                        <div class="mb-3">
                            <label for="excerpt" class="form-label fw-semibold">Summary / Excerpt</label>
                            <textarea name="excerpt" id="excerpt" rows="2" class="form-control">{{ old('excerpt', $page->excerpt) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- SEO Panel -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent py-3">
                        <h6 class="card-title mb-0 fw-bold"><i class="bi bi-search me-1 text-primary"></i> Search Engine Optimization (SEO)</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="meta_title" class="form-label fw-semibold">Meta Title</label>
                            <input type="text" name="meta_title" id="meta_title" class="form-control" value="{{ old('meta_title', $page->meta_title) }}">
                        </div>
                        <div class="mb-3">
                            <label for="meta_description" class="form-label fw-semibold">Meta Description</label>
                            <textarea name="meta_description" id="meta_description" rows="2" class="form-control">{{ old('meta_description', $page->meta_description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent py-3">
                        <h6 class="card-title mb-0 fw-bold">Publishing Options</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ old('status', $page->status) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="status">Published Status</label>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-muted mb-1">Front Page URL</label>
                            <div class="p-2 rounded border bg-light-subtle small text-break">
                                @if ($page->status)
                                    <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="text-decoration-none">
                                        <code>{{ route('page.show', $page->slug) }}</code> <i class="bi bi-box-arrow-up-right ms-1"></i>
                                    </a>
                                @else
                                    <code class="text-muted">{{ route('page.show', $page->slug) }}</code>
                                    <div class="text-muted mt-1" style="font-size: 0.75rem;">Publish this page to link it in the Front Header &amp; Footer.</div>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="template" class="form-label fw-semibold">Layout Template</label>
                            <select name="template" id="template" class="form-select">
                                @foreach (\App\Models\Page::TEMPLATES as $tplKey => $tplLabel)
                                    <option value="{{ $tplKey }}" {{ old('template', $page->template) === $tplKey ? 'selected' : '' }}>{{ $tplLabel }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Update Page
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
