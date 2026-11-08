@extends('layouts.admin')

@section('title', 'Media Library & Digital Assets - Xivig Admin')

@section('content')
<div class="page-media">
    <!-- Page Header -->
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="page-title h3 mb-1">Media Library</h1>
            <nav class="breadcrumb mb-0">
                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item text-decoration-none">Dashboard</a>
                <span class="breadcrumb-item active">Media Library</span>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-2">
            <!-- View Mode Switch -->
            <div class="btn-group" role="group" aria-label="View switch">
                <a href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}" 
                   class="btn btn-sm {{ $activeView === 'grid' ? 'btn-primary' : 'btn-outline-secondary' }}" 
                   title="Grid View">
                    <i class="bi bi-grid-fill"></i>
                </a>
                <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}" 
                   class="btn btn-sm {{ $activeView === 'list' ? 'btn-primary' : 'btn-outline-secondary' }}" 
                   title="List View">
                    <i class="bi bi-list-ul"></i>
                </a>
            </div>

            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#uploaderCard">
                <i class="bi bi-cloud-arrow-up me-1"></i> Upload Assets
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Storage Metrics Cards Row -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary-subtle text-primary" style="width: 48px; height: 48px; font-size: 1.35rem;">
                        <i class="bi bi-images"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total Assets</div>
                        <h4 class="mb-0 fw-bold">{{ number_format($totalCount) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-info-subtle text-info" style="width: 48px; height: 48px; font-size: 1.35rem;">
                        <i class="bi bi-hdd-network"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Storage Used</div>
                        <h4 class="mb-0 fw-bold">{{ $formattedTotalSize }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-success-subtle text-success" style="width: 48px; height: 48px; font-size: 1.35rem;">
                        <i class="bi bi-link-45deg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Attached to Posts</div>
                        <h4 class="mb-0 fw-bold">{{ number_format($usedInPostsCount) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning-subtle text-warning" style="width: 48px; height: 48px; font-size: 1.35rem;">
                        <i class="bi bi-trash3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Unlinked / Standalone</div>
                        <h4 class="mb-0 fw-bold">{{ number_format($unusedCount) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Drag & Drop Uploader (Collapsible) -->
    <div class="collapse {{ request('upload') ? 'show' : '' }} mb-4" id="uploaderCard">
        <div class="card border-primary border-opacity-25 shadow-sm">
            <div class="card-header bg-primary-subtle py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title text-primary mb-0 fw-bold">
                    <i class="bi bi-cloud-arrow-up me-2"></i> Multi-File Media Uploader
                </h5>
                <span class="badge bg-primary text-white">Max 20MB per file</span>
            </div>
            <div class="card-body">
                <form id="mediaUploadForm" action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="dropzoneContainer" class="border border-2 border-dashed rounded-3 p-5 text-center cursor-pointer bg-light-subtle transition-all" style="cursor: pointer;">
                        <input type="file" name="media_files[]" id="fileInput" multiple accept="image/*,application/pdf" class="d-none">
                        <i class="bi bi-cloud-upload text-primary display-4 d-block mb-3"></i>
                        <h5 class="fw-bold mb-1">Drag and drop files here, or click to browse</h5>
                        <p class="text-muted small mb-3">Supports JPG, PNG, WebP, GIF, SVG, and PDF documents</p>
                        <button type="button" class="btn btn-outline-primary btn-sm px-4" onclick="document.getElementById('fileInput').click()">
                            Browse Files
                        </button>
                    </div>

                    <!-- Selected Files Queue Display -->
                    <div id="fileQueue" class="mt-3" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold small" id="queueCount">0 files selected</span>
                            <div class="d-flex gap-2">
                                <select name="folder" class="form-select form-select-sm" style="width: 160px;">
                                    <option value="general">Folder: General</option>
                                    <option value="blog">Folder: Blog</option>
                                    <option value="showcase">Folder: Showcase</option>
                                    <option value="banners">Folder: Banners</option>
                                </select>
                                <button type="submit" id="btnUploadSubmit" class="btn btn-primary btn-sm px-3">
                                    <i class="bi bi-upload me-1"></i> Upload Selected
                                </button>
                            </div>
                        </div>
                        <ul class="list-group list-group-flush border rounded-2" id="queueList" style="max-height: 200px; overflow-y: auto;"></ul>
                    </div>

                    <!-- Progress Bar for AJAX -->
                    <div id="uploadProgressBarWrap" class="progress mt-3 d-none" style="height: 10px;">
                        <div id="uploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Media View Filter Tabs -->
    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <a href="{{ route('admin.media.index') }}" class="btn btn-sm rounded-pill {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}">
            <i class="bi bi-images me-1"></i> Active Media <span class="badge {{ !request('status') ? 'bg-white text-primary' : 'bg-secondary' }} ms-1">{{ number_format($totalCount) }}</span>
        </a>
        <a href="{{ route('admin.media.index', ['status' => 'trash']) }}" class="btn btn-sm rounded-pill {{ request('status') === 'trash' ? 'btn-danger' : 'btn-outline-danger' }}">
            <i class="bi bi-trash me-1"></i> Trash <span class="badge {{ request('status') === 'trash' ? 'bg-white text-danger' : 'bg-danger' }} ms-1">{{ number_format($trashCount ?? 0) }}</span>
        </a>
    </div>

    <!-- Controls & Filters Bar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="{{ route('admin.media.index') }}" method="GET" class="row g-2 align-items-center">
                <input type="hidden" name="view" value="{{ $activeView }}">

                <!-- Search Input -->
                <div class="col-lg-4 col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="search" name="q" class="form-control border-start-0" placeholder="Search by name, alt text..." value="{{ request('q') }}">
                        @if(request('q'))
                            <a href="{{ route('admin.media.index', array_merge(request()->except('q'), ['view' => $activeView])) }}" class="input-group-text bg-transparent text-muted" title="Clear search">
                                <i class="bi bi-x"></i>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Type Filter -->
                <div class="col-lg-2 col-md-3 col-6">
                    <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" {{ request('type') === 'all' || !request('type') ? 'selected' : '' }}>All Asset Types</option>
                        <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>Images (Raster)</option>
                        <option value="svg" {{ request('type') === 'svg' ? 'selected' : '' }}>Vectors / SVG</option>
                        <option value="document" {{ request('type') === 'document' ? 'selected' : '' }}>Documents / PDF</option>
                    </select>
                </div>

                <!-- Folder Filter -->
                <div class="col-lg-2 col-md-3 col-6">
                    <select name="folder" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" {{ request('folder') === 'all' || !request('folder') ? 'selected' : '' }}>All Folders</option>
                        @foreach($availableFolders as $fld)
                            <option value="{{ $fld }}" {{ request('folder') === $fld ? 'selected' : '' }}>Folder: {{ ucfirst($fld) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Usage Filter -->
                <div class="col-lg-2 col-md-6 col-6">
                    <select name="usage" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" {{ request('usage') === 'all' || !request('usage') ? 'selected' : '' }}>All Usage</option>
                        <option value="used" {{ request('usage') === 'used' ? 'selected' : '' }}>Used in Posts</option>
                        <option value="unused" {{ request('usage') === 'unused' ? 'selected' : '' }}>Unlinked Only</option>
                    </select>
                </div>

                <!-- Sort Order -->
                <div class="col-lg-2 col-md-6 col-6">
                    <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                        <option value="size_desc" {{ request('sort') === 'size_desc' ? 'selected' : '' }}>Largest Size</option>
                        <option value="size_asc" {{ request('sort') === 'size_asc' ? 'selected' : '' }}>Smallest Size</option>
                        <option value="most_used" {{ request('sort') === 'most_used' ? 'selected' : '' }}>Most Used</option>
                    </select>
                </div>
            </form>

            <!-- Bulk Selection Action Bar (Hidden until items selected) -->
            <div id="bulkActionBar" class="alert alert-primary py-2 px-3 mt-3 mb-0 d-none align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary text-white" id="selectedCountBadge">0</span>
                    <span class="fw-semibold small">asset(s) selected</span>
                </div>
                <div class="d-flex gap-2">
                    @if(request('status') === 'trash')
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="executeBulkAction('restore')">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Restore Selected
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="executeBulkAction('force_delete')">
                            <i class="bi bi-x-octagon me-1"></i> Delete Permanently
                        </button>
                    @else
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="executeBulkAction('delete')">
                            <i class="bi bi-trash me-1"></i> Move to Trash
                        </button>
                    @endif
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearBulkSelection()">
                        Clear Selection
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content: Grid View or List View -->
    @if($activeView === 'grid')
        <!-- GRID VIEW (4 COLUMNS) -->
        <div class="row g-3 mb-4">
            @forelse($pictures as $pic)
                <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6">
                    <div class="card h-100 border-0 shadow-sm media-card position-relative overflow-hidden group-hover">
                        <!-- Select Checkbox -->
                        <div class="position-absolute top-0 start-0 m-2" style="z-index: 10;">
                            <input type="checkbox" class="form-check-input media-checkbox shadow-sm" value="{{ $pic->id }}" onchange="updateBulkBar()">
                        </div>

                        <!-- ID & Extension Badge -->
                        <div class="position-absolute top-0 end-0 m-2 d-flex gap-1" style="z-index: 10;">
                            @php
                                $ext = strtoupper(pathinfo($pic->link, PATHINFO_EXTENSION));
                            @endphp
                            <span class="badge bg-dark bg-opacity-75 font-monospace small px-1.5">{{ $ext ?: 'IMG' }}</span>
                        </div>

                        <!-- Asset Image Thumbnail / File Representation -->
                        <div class="media-thumb-wrap position-relative cursor-pointer" onclick="openInspectorModal({{ $pic->id }})" style="height: 195px; background: rgba(0,0,0,0.04); overflow: hidden; display: flex; align-items: center; justify-content: center;">
                            @if($pic->is_image)
                                <img src="{{ $pic->url }}" alt="{{ $pic->alt_text }}" id="asset-card-img-{{ $pic->id }}" class="w-100 h-100 object-fit-cover transition-transform" onerror="this.src='{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}'">
                            @else
                                <div class="text-center p-3 text-secondary">
                                    <i class="bi bi-file-earmark-pdf display-4 text-danger d-block mb-1"></i>
                                    <span class="small fw-bold">{{ $ext }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Card Body -->
                        <div class="card-body p-2.5 d-flex flex-column justify-content-between">
                            <div>
                                <h6 class="card-title mb-1 text-truncate fw-semibold small cursor-pointer" id="asset-card-title-{{ $pic->id }}" data-asset-title-id="{{ $pic->id }}" onclick="openInspectorModal({{ $pic->id }})" title="{{ $pic->name }}">
                                    {{ $pic->name ?: 'Asset #' . $pic->id }}
                                </h6>
                                <div class="d-flex justify-content-between align-items-center text-muted" style="font-size: 0.75rem;">
                                    <span>{{ $pic->dimensions ?: $pic->formatted_size }}</span>
                                    <span class="badge bg-light text-secondary border px-1.5 py-0.5" id="asset-card-folder-{{ $pic->id }}">{{ ucfirst($pic->folder ?: 'general') }}</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between pt-2 mt-2 border-top">
                                @if($pic->posts_count > 0)
                                    <span class="badge bg-success-subtle text-success small" style="font-size: 0.7rem;">
                                        <i class="bi bi-link-45deg"></i> {{ $pic->posts_count }} {{ Str::plural('post', $pic->posts_count) }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary small" style="font-size: 0.7rem;">
                                        Unlinked
                                    </span>
                                @endif

                                <div class="btn-group btn-group-sm">
                                    @if($pic->trashed())
                                        <button type="button" class="btn btn-outline-success btn-sm p-1 px-1.5" onclick="restoreSingleAsset({{ $pic->id }})" title="Restore from Trash">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm p-1 px-1.5" onclick="forceDeleteSingleAsset({{ $pic->id }}, '{{ addslashes($pic->name) }}')" title="Delete Permanently">
                                            <i class="bi bi-x-octagon"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-outline-secondary btn-sm p-1 px-1.5" onclick="copyAssetUrl('{{ $pic->url }}')" title="Copy URL">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-success btn-sm p-1 px-1.5" onclick="duplicateSingleAsset({{ $pic->id }})" title="Duplicate Asset">
                                            <i class="bi bi-copy"></i>
                                        </button>
                                        <a href="{{ route('admin.image-cropper.index', ['media_id' => $pic->id]) }}" class="btn btn-outline-warning btn-sm p-1 px-1.5" title="Crop & Resize in Studio">
                                            <i class="bi bi-crop"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-primary btn-sm p-1 px-1.5" onclick="openInspectorModal({{ $pic->id }})" title="Inspect & Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm p-1 px-1.5" onclick="deleteSingleAsset({{ $pic->id }}, '{{ addslashes($pic->name) }}')" title="Move to Trash">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 py-5 text-center text-muted">
                    <i class="bi bi-images display-3 d-block mb-3 text-secondary opacity-50"></i>
                    <h5 class="fw-bold">No media assets match your query</h5>
                    <p class="small text-muted mb-3">Try adjusting your search terms or upload new images using the button above.</p>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('uploaderCard').classList.add('show')">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Upload First Asset
                    </button>
                </div>
            @endforelse
        </div>
    @else
        <!-- LIST / TABLE VIEW -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="width: 40px;">
                                    <input type="checkbox" class="form-check-input" id="checkAllList" onchange="toggleSelectAll(this)">
                                </th>
                                <th style="width: 80px;">Thumbnail</th>
                                <th>Name &amp; Alt Text</th>
                                <th>Folder</th>
                                <th>Dimensions</th>
                                <th>File Size</th>
                                <th>MIME Type</th>
                                <th>Usage</th>
                                <th>Uploaded</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pictures as $pic)
                                <tr>
                                    <td class="ps-3">
                                        <input type="checkbox" class="form-check-input media-checkbox" value="{{ $pic->id }}" onchange="updateBulkBar()">
                                    </td>
                                    <td>
                                        <div class="rounded overflow-hidden cursor-pointer" onclick="openInspectorModal({{ $pic->id }})" style="width: 54px; height: 54px; background: rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: center;">
                                            @if($pic->is_image)
                                                <img src="{{ $pic->url }}" alt="{{ $pic->alt_text }}" id="asset-list-img-{{ $pic->id }}" class="w-100 h-100 object-fit-cover" onerror="this.src='{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}'">
                                            @else
                                                <i class="bi bi-file-earmark-pdf fs-3 text-danger"></i>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <a href="javascript:void(0)" onclick="openInspectorModal({{ $pic->id }})" class="fw-semibold text-body text-decoration-none d-block" id="asset-list-title-{{ $pic->id }}" data-asset-title-id="{{ $pic->id }}">
                                            {{ $pic->name ?: 'Asset #' . $pic->id }}
                                        </a>
                                        <small class="text-muted d-block text-truncate" style="max-width: 240px;" id="asset-list-alt-{{ $pic->id }}">{{ $pic->alt_text ?: 'No alt text set' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border" id="asset-list-folder-{{ $pic->id }}">{{ $pic->folder ?: 'general' }}</span>
                                    </td>
                                    <td><code class="small text-muted">{{ $pic->dimensions ?: 'N/A' }}</code></td>
                                    <td class="small">{{ $pic->formatted_size }}</td>
                                    <td><span class="badge bg-secondary-subtle text-secondary font-monospace" style="font-size: 0.72rem;">{{ $pic->mime_type ?: 'image' }}</span></td>
                                    <td>
                                        @if($pic->posts_count > 0)
                                            <span class="badge bg-success-subtle text-success">
                                                {{ $pic->posts_count }} {{ Str::plural('post', $pic->posts_count) }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted border">Unused</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">
                                        {{ $pic->created_at ? $pic->created_at->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="btn-group btn-group-sm">
                                            @if($pic->trashed())
                                                <button type="button" class="btn btn-outline-success" onclick="restoreSingleAsset({{ $pic->id }})" title="Restore Asset">
                                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                                                </button>
                                                <button type="button" class="btn btn-outline-danger" onclick="forceDeleteSingleAsset({{ $pic->id }}, '{{ addslashes($pic->name) }}')" title="Delete Permanently">
                                                    <i class="bi bi-x-octagon me-1"></i> Delete
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-outline-secondary" onclick="copyAssetUrl('{{ $pic->url }}')" title="Copy URL">
                                                    <i class="bi bi-clipboard"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-success" onclick="duplicateSingleAsset({{ $pic->id }})" title="Duplicate Asset">
                                                    <i class="bi bi-copy"></i>
                                                </button>
                                                <a href="{{ route('admin.image-cropper.index', ['media_id' => $pic->id]) }}" class="btn btn-outline-warning" title="Crop & Resize in Studio">
                                                    <i class="bi bi-crop"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-primary" onclick="openInspectorModal({{ $pic->id }})" title="Edit Details">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger" onclick="deleteSingleAsset({{ $pic->id }}, '{{ addslashes($pic->name) }}')" title="Move to Trash">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        No media files found matching your criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Pagination -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mt-3 gap-2">
        <span class="text-muted small">
            Showing {{ $pictures->firstItem() ?? 0 }} to {{ $pictures->lastItem() ?? 0 }} of {{ $pictures->total() }} assets
        </span>
        <div>
            {{ $pictures->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- ================= ASSET INSPECTOR MODAL ================= -->
<div class="modal fade" id="mediaInspectorModal" tabindex="-1" aria-labelledby="mediaInspectorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-image text-primary fs-5"></i>
                    <h5 class="modal-title fw-bold" id="mediaInspectorModalLabel">Asset Details &amp; Metadata</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <!-- Left: Large Preview & Links -->
                    <div class="col-lg-7">
                        <div class="bg-dark bg-opacity-10 rounded-3 p-3 text-center mb-3 d-flex align-items-center justify-content-center" style="min-height: 340px; max-height: 480px; overflow: hidden;">
                            <img id="inspectorImagePreview" src="" alt="Preview" class="img-fluid rounded shadow-sm" style="max-height: 420px; object-fit: contain;">
                            <div id="inspectorNonImagePreview" class="d-none text-center py-5">
                                <i class="bi bi-file-earmark-pdf display-1 text-danger d-block mb-2"></i>
                                <h5 class="text-muted fw-bold" id="inspectorDocTitle">Document Asset</h5>
                            </div>
                        </div>

                        <!-- Direct URL Copy -->
                        <label class="form-label small fw-semibold text-muted text-uppercase mb-1">Direct Asset URL</label>
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" id="inspectorAssetUrl" class="form-control" readonly>
                            <button type="button" class="btn btn-primary" onclick="copyAssetUrl(document.getElementById('inspectorAssetUrl').value)">
                                <i class="bi bi-clipboard me-1"></i> Copy Link
                            </button>
                            <a id="inspectorOpenTab" href="#" target="_blank" class="btn btn-outline-secondary" title="Open full asset in new tab">
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        </div>

                        <!-- Attached Posts List -->
                        <div class="card bg-light border-0">
                            <div class="card-body p-3">
                                <h6 class="fw-bold small text-uppercase mb-2 text-secondary">
                                    <i class="bi bi-link-45deg me-1"></i> Blog Posts Using this Asset (<span id="inspectorPostsCount">0</span>)
                                </h6>
                                <div id="inspectorPostsList" class="small text-muted" style="max-height: 120px; overflow-y: auto;">
                                    No posts currently attached to this image.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Editable Form & Specs -->
                    <div class="col-lg-5">
                        <form id="inspectorMetadataForm">
                            <input type="hidden" id="inspectorAssetId">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Asset Title / Name <span class="text-danger">*</span></label>
                                <input type="text" id="inspectorName" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Alt Text (Crucial for SEO &amp; Accessibility)</label>
                                <input type="text" id="inspectorAltText" class="form-control" placeholder="Describe the image content...">
                                <small class="text-muted" style="font-size: 0.75rem;">Search engines and screen-readers use this to understand your asset.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Caption</label>
                                <textarea id="inspectorCaption" class="form-control" rows="2" placeholder="Optional caption displayed under the image"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Description / Notes</label>
                                <textarea id="inspectorDescription" class="form-control" rows="2" placeholder="Internal editorial notes"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Folder Category</label>
                                <select id="inspectorFolder" class="form-select form-select-sm">
                                    <option value="general">General</option>
                                    <option value="blog">Blog</option>
                                    <option value="showcase">Showcase</option>
                                    <option value="banners">Banners</option>
                                    <option value="avatars">Avatars</option>
                                </select>
                            </div>

                            <!-- Technical Specs Table -->
                            <div class="border rounded-2 p-3 bg-light-subtle mb-4 small">
                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted">MIME Type:</span>
                                    <code class="fw-semibold" id="inspectorMimeType">-</code>
                                </div>
                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted">Dimensions:</span>
                                    <span class="fw-semibold" id="inspectorDimensions">-</span>
                                </div>
                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted">File Size:</span>
                                    <span class="fw-semibold" id="inspectorFileSize">-</span>
                                </div>
                                <div class="d-flex justify-content-between py-1">
                                    <span class="text-muted">Uploaded At:</span>
                                    <span class="fw-semibold" id="inspectorCreatedAt">-</span>
                                </div>
                            </div>

                            <!-- Action Toolbar (Quick Tools + Save) -->
                            <div class="pt-3 border-top mt-3">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                    <span class="small text-muted fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                        <i class="bi bi-tools me-1"></i> Quick Actions
                                    </span>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-success" id="btnInspectorDuplicate" title="Duplicate this asset">
                                            <i class="bi bi-copy me-1"></i> Duplicate
                                        </button>
                                        <a href="#" id="inspectorCropBtn" class="btn btn-outline-warning" title="Crop & Resize in Studio">
                                            <i class="bi bi-crop me-1"></i> Crop
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" id="btnInspectorDelete" title="Move to Trash">
                                            <i class="bi bi-trash me-1"></i> Trash
                                        </button>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
                                    <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-pill" data-bs-dismiss="modal">
                                        Cancel
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-sm px-4 rounded-pill fw-semibold shadow-sm text-nowrap" id="btnInspectorSave">
                                        <i class="bi bi-check-lg me-1"></i> Save Changes
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Bulk Action Form -->
<form id="bulkActionForm" action="{{ route('admin.media.bulk') }}" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="action" id="bulkActionType" value="delete">
    <div id="bulkIdsContainer"></div>
</form>

<!-- Single Delete Form -->
<form id="singleDeleteForm" action="" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

<!-- Single Restore Form -->
<form id="singleRestoreForm" action="" method="POST" class="d-none">
    @csrf
</form>

<!-- Single Force Delete Form -->
<form id="singleForceDeleteForm" action="" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

<!-- Single Duplicate Form -->
<form id="singleDuplicateForm" action="" method="POST" class="d-none">
    @csrf
</form>
@endsection

@push('styles')
<style>
.media-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.media-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1) !important;
}
.media-card:hover .object-fit-cover {
    transform: scale(1.04);
}
.transition-transform {
    transition: transform 0.3s ease;
}
.cursor-pointer {
    cursor: pointer;
}
</style>
@endpush

@push('scripts')
<script>
// Copy URL Helper
function copyAssetUrl(url) {
    if (!url) return;
    navigator.clipboard.writeText(url).then(() => {
        if (window.showToast) {
            window.showToast('Public asset URL copied to clipboard!', 'success');
        } else {
            alert('Public asset URL copied to clipboard!');
        }
    }).catch(err => {
        prompt('Copy the asset link:', url);
    });
}

// Single asset soft deletion
function deleteSingleAsset(id, name) {
    showConfirmDialog({
        title: 'Move to Trash?',
        text: 'Move media asset "' + name + '" to Trash?',
        icon: 'warning',
        confirmButtonText: 'Yes, Move'
    }, function() {
        const form = document.getElementById('singleDeleteForm');
        form.action = '{{ url("admin/media") }}/' + id;
        form.submit();
    });
}

// Single asset restore
function restoreSingleAsset(id) {
    const form = document.getElementById('singleRestoreForm');
    form.action = '{{ url("admin/media") }}/' + id + '/restore';
    form.submit();
}

// Single asset force delete
function forceDeleteSingleAsset(id, name) {
    showConfirmDialog({
        title: 'Permanent Deletion',
        text: 'Are you sure you want to permanently delete media asset "' + name + '"? This will remove the file from storage and cannot be undone.',
        icon: 'error',
        confirmButtonText: 'Delete Permanently'
    }, function() {
        const form = document.getElementById('singleForceDeleteForm');
        form.action = '{{ url("admin/media") }}/' + id + '/force-delete';
        form.submit();
    });
}

// Single asset duplicate
function duplicateSingleAsset(id) {
    const form = document.getElementById('singleDuplicateForm');
    form.action = '{{ url("admin/media") }}/' + id + '/duplicate';
    form.submit();
}

// Bulk Selection Management
function toggleSelectAll(masterCb) {
    document.querySelectorAll('.media-checkbox').forEach(cb => {
        cb.checked = masterCb.checked;
    });
    updateBulkBar();
}

function updateBulkBar() {
    const selected = document.querySelectorAll('.media-checkbox:checked');
    const bar = document.getElementById('bulkActionBar');
    const badge = document.getElementById('selectedCountBadge');
    
    if (selected.length > 0) {
        bar.classList.remove('d-none');
        bar.classList.add('d-flex');
        badge.innerText = selected.length;
    } else {
        bar.classList.add('d-none');
        bar.classList.remove('d-flex');
    }
}

function clearBulkSelection() {
    document.querySelectorAll('.media-checkbox').forEach(cb => cb.checked = false);
    const checkAll = document.getElementById('checkAllList');
    if (checkAll) checkAll.checked = false;
    updateBulkBar();
}

function executeBulkAction(action) {
    const selected = Array.from(document.querySelectorAll('.media-checkbox:checked')).map(cb => cb.value);
    if (selected.length === 0) return;

    const proceed = () => {
        const form = document.getElementById('bulkActionForm');
        const container = document.getElementById('bulkIdsContainer');
        document.getElementById('bulkActionType').value = action;
        container.innerHTML = '';

        selected.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            container.appendChild(input);
        });

        form.submit();
    };

    if (action === 'delete') {
        showConfirmDialog({
            title: 'Move Selected to Trash?',
            text: 'Move ' + selected.length + ' selected media asset(s) to Trash?',
            icon: 'warning',
            confirmButtonText: 'Move to Trash'
        }, proceed);
    } else if (action === 'force_delete') {
        showConfirmDialog({
            title: 'Permanently Delete Selected?',
            text: 'Are you sure you want to permanently delete ' + selected.length + ' media asset(s)? Files will be unlinked from storage.',
            icon: 'error',
            confirmButtonText: 'Delete Permanently'
        }, proceed);
    } else {
        proceed();
    }
}

// Drag & Drop Queue Handling
document.addEventListener('DOMContentLoaded', function() {
    const dropzone = document.getElementById('dropzoneContainer');
    const fileInput = document.getElementById('fileInput');
    const fileQueue = document.getElementById('fileQueue');
    const queueList = document.getElementById('queueList');
    const queueCount = document.getElementById('queueCount');

    if (dropzone && fileInput) {
        ['dragenter', 'dragover'].forEach(evt => {
            dropzone.addEventListener(evt, (e) => {
                e.preventDefault();
                dropzone.classList.add('border-primary', 'bg-primary-subtle');
            });
        });

        ['dragleave', 'drop'].forEach(evt => {
            dropzone.addEventListener(evt, (e) => {
                e.preventDefault();
                dropzone.classList.remove('border-primary', 'bg-primary-subtle');
            });
        });

        dropzone.addEventListener('drop', (e) => {
            if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                renderQueue(e.dataTransfer.files);
            }
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files && fileInput.files.length > 0) {
                renderQueue(fileInput.files);
            }
        });
    }

    function renderQueue(files) {
        queueList.innerHTML = '';
        queueCount.innerText = files.length + ' file(s) selected';
        fileQueue.style.display = 'block';

        Array.from(files).forEach((f, idx) => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center py-2 px-3 small';
            li.innerHTML = `
                <div class="d-flex align-items-center gap-2 text-truncate">
                    <i class="bi bi-file-earmark-image text-primary"></i>
                    <span class="fw-semibold text-truncate" style="max-width: 320px;">${f.name}</span>
                </div>
                <span class="badge bg-secondary-subtle text-secondary">${(f.size / 1024).toFixed(1)} KB</span>
            `;
            queueList.appendChild(li);
        });
    }

    // Inspector Modal AJAX loader
    window.openInspectorModal = function(id) {
        fetch('{{ url("admin/media") }}/' + id, {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.asset) {
                const a = data.asset;
                document.getElementById('inspectorAssetId').value = a.id;
                document.getElementById('inspectorName').value = a.name || '';
                document.getElementById('inspectorAltText').value = a.alt_text || '';
                document.getElementById('inspectorCaption').value = a.caption || '';
                document.getElementById('inspectorDescription').value = a.description || '';
                
                // Case-insensitive folder selector matching
                const folderSelect = document.getElementById('inspectorFolder');
                const rawFolder = a.folder || 'general';
                let matchedFolder = false;
                for (let i = 0; i < folderSelect.options.length; i++) {
                    if (folderSelect.options[i].value.toLowerCase() === rawFolder.toLowerCase()) {
                        folderSelect.selectedIndex = i;
                        matchedFolder = true;
                        break;
                    }
                }
                if (!matchedFolder && a.folder) {
                    const customOpt = new Option(a.folder, a.folder, true, true);
                    folderSelect.add(customOpt);
                }

                document.getElementById('inspectorAssetUrl').value = a.url;
                document.getElementById('inspectorOpenTab').href = a.url;

                document.getElementById('inspectorMimeType').innerText = a.mime_type || 'image';
                document.getElementById('inspectorDimensions').innerText = a.dimensions || 'N/A';
                document.getElementById('inspectorFileSize').innerText = a.formatted_size || 'N/A';
                document.getElementById('inspectorCreatedAt').innerText = a.created_at || 'N/A';

                const imgPreview = document.getElementById('inspectorImagePreview');
                const docPreview = document.getElementById('inspectorNonImagePreview');

                if (a.is_image) {
                    imgPreview.src = a.url;
                    imgPreview.classList.remove('d-none');
                    docPreview.classList.add('d-none');
                } else {
                    imgPreview.classList.add('d-none');
                    docPreview.classList.remove('d-none');
                    document.getElementById('inspectorDocTitle').innerText = a.name;
                }

                // Render attached blog posts
                const postsList = document.getElementById('inspectorPostsList');
                document.getElementById('inspectorPostsCount').innerText = a.posts_count || 0;
                if (a.posts && a.posts.length > 0) {
                    postsList.innerHTML = '<ul class="list-unstyled mb-0">' + 
                        a.posts.map(p => `
                            <li class="mb-1 text-truncate">
                                <a href="{{ url('admin/posts') }}/${p.id}/edit" target="_blank" class="text-decoration-none">
                                    <i class="bi bi-box-arrow-up-right me-1 small"></i> ${p.title}
                                </a>
                            </li>
                        `).join('') + '</ul>';
                } else {
                    postsList.innerHTML = '<span class="text-muted">No blog posts currently use this image.</span>';
                }

                // Bind crop button
                const cropBtn = document.getElementById('inspectorCropBtn');
                if (cropBtn) {
                    cropBtn.href = '{{ url("admin/image-cropper") }}?media_id=' + a.id;
                }

                // Bind duplicate button
                const dupBtn = document.getElementById('btnInspectorDuplicate');
                if (dupBtn) {
                    dupBtn.onclick = function() {
                        const modalInstance = bootstrap.Modal.getInstance(document.getElementById('mediaInspectorModal'));
                        if (modalInstance) modalInstance.hide();
                        duplicateSingleAsset(a.id);
                    };
                }

                // Bind delete button
                const delBtn = document.getElementById('btnInspectorDelete');
                if (delBtn) {
                    delBtn.onclick = function() {
                        const modalInstance = bootstrap.Modal.getInstance(document.getElementById('mediaInspectorModal'));
                        if (modalInstance) modalInstance.hide();
                        deleteSingleAsset(a.id, a.name);
                    };
                }

                const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('mediaInspectorModal'));
                modal.show();
            }
        })
        .catch(err => {
            console.error('Error fetching asset details:', err);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Load Failed',
                    text: 'Failed to load asset details from server.',
                    customClass: { confirmButton: 'btn btn-primary px-4 rounded-pill' },
                    buttonsStyling: false
                });
            } else {
                alert('Failed to load asset details.');
            }
        });
    };

    // Inspector Metadata Form Submit
    const metadataForm = document.getElementById('inspectorMetadataForm');
    if (metadataForm) {
        metadataForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('inspectorAssetId').value;
            const btn = document.getElementById('btnInspectorSave');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

            const newName = document.getElementById('inspectorName').value;
            const newAlt = document.getElementById('inspectorAltText').value;
            const newCaption = document.getElementById('inspectorCaption').value;
            const newDescription = document.getElementById('inspectorDescription').value;
            const newFolder = document.getElementById('inspectorFolder').value;

            const payload = {
                _token: '{{ csrf_token() }}',
                _method: 'PUT',
                name: newName,
                alt_text: newAlt,
                caption: newCaption,
                description: newDescription,
                folder: newFolder,
            };

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            fetch('{{ url("admin/media") }}/' + id, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-HTTP-Method-Override': 'PUT'
                },
                body: JSON.stringify(payload)
            })
            .then(async res => {
                let data = null;
                try {
                    data = await res.json();
                } catch (e) {
                    // non-json response
                }
                if (!res.ok) {
                    const error = new Error((data && data.message) ? data.message : `Server returned status ${res.status}`);
                    error.data = data;
                    throw error;
                }
                return data || {};
            })
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Save Changes';

                // Immediately dismiss modal so user directly sees the updated Media Library
                const modalEl = document.getElementById('mediaInspectorModal');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) {
                    modalInstance.hide();
                }

                // 1. Immediately update all titles in Grid view & List view
                const finalName = (data.asset && data.asset.name) ? data.asset.name : newName;
                const finalAlt = (data.asset && data.asset.alt_text !== undefined) ? data.asset.alt_text : newAlt;
                const finalFolder = (data.asset && data.asset.folder) ? data.asset.folder : newFolder;

                document.querySelectorAll('[data-asset-title-id="' + id + '"]').forEach(el => {
                    el.textContent = finalName;
                    el.setAttribute('title', finalName);
                });

                // 2. Immediately update alt text in List view
                const listAltEl = document.getElementById('asset-list-alt-' + id);
                if (listAltEl) {
                    listAltEl.textContent = finalAlt || 'No alt text set';
                }

                // 3. Immediately update thumbnail alt attributes in Grid and List views
                const cardImg = document.getElementById('asset-card-img-' + id);
                if (cardImg) cardImg.setAttribute('alt', finalAlt || '');
                const listImg = document.getElementById('asset-list-img-' + id);
                if (listImg) listImg.setAttribute('alt', finalAlt || '');

                // 4. Immediately update Folder badges in List and Grid views
                const listFolderEl = document.getElementById('asset-list-folder-' + id);
                if (listFolderEl) {
                    listFolderEl.textContent = finalFolder || 'general';
                }
                const cardFolderEl = document.getElementById('asset-card-folder-' + id);
                if (cardFolderEl) {
                    cardFolderEl.textContent = (finalFolder ? (finalFolder.charAt(0).toUpperCase() + finalFolder.slice(1)) : 'General');
                }

                // 5. Display clear success toast notification
                const msg = data.message || 'Media metadata updated successfully!';
                if (window.showToast) {
                    window.showToast(msg, 'success');
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: msg,
                        timer: 2500,
                        showConfirmButton: false
                    });
                } else {
                    alert(msg);
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Save Changes';
                let errMsg = 'Failed to save metadata updates.';
                if (err && err.data && err.data.errors) {
                    errMsg = Object.values(err.data.errors).flat().join('<br>');
                } else if (err && err.data && err.data.message) {
                    errMsg = err.data.message;
                } else if (err && err.message) {
                    errMsg = err.message;
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Save Failed',
                        html: errMsg,
                        customClass: { confirmButton: 'btn btn-primary px-4 rounded-pill' },
                        buttonsStyling: false
                    });
                } else {
                    alert(errMsg);
                }
            });
        });
    }

    // Suppress chrome extension background disconnect errors
    window.addEventListener('error', function(e) {
        if (e.message && e.message.includes('Could not establish connection')) {
            e.preventDefault();
        }
    });
});
</script>
@endpush
