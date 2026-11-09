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
        <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Pages
        </a>
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

                        <div class="mb-3">
                            <label for="content" class="form-label fw-semibold">Page Content</label>
                            <textarea name="content" id="content" rows="12" class="form-control font-monospace">{{ old('content', $page->content) }}</textarea>
                        </div>

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
                            <label for="template" class="form-label fw-semibold">Layout Template</label>
                            <select name="template" id="template" class="form-select">
                                <option value="default" {{ $page->template === 'default' ? 'selected' : '' }}>Default Page</option>
                                <option value="fullwidth" {{ $page->template === 'fullwidth' ? 'selected' : '' }}>Full Width Canvas</option>
                                <option value="landing" {{ $page->template === 'landing' ? 'selected' : '' }}>Landing Showcase</option>
                                <option value="legal" {{ $page->template === 'legal' ? 'selected' : '' }}>Legal &amp; Policies</option>
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
