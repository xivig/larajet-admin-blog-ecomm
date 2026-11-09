@extends('layouts.admin')

@section('title', 'Roles & Permissions - Xivig Admin')

@section('content')
<div class="page-roles">
    <!-- Page Header -->
    <div class="page-header d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="page-title h3 mb-1">Roles & Permissions</h1>
            <nav class="breadcrumb mb-0">
                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item text-decoration-none">Dashboard</a>
                <span class="breadcrumb-item active">Roles & Permissions</span>
            </nav>
        </div>
        <div class="page-header-actions d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#assignUserModal">
                <i class="bi bi-person-plus me-1"></i> Assign User
            </button>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                <i class="bi bi-plus-lg me-1"></i> Add Role
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

    <div class="row g-4">
        <!-- Left Column: Roles List & Active Role Details -->
        <div class="col-xl-4 col-lg-5">
            <!-- Roles Navigation Card -->
            <div class="card mb-4 border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                    <h5 class="card-title mb-0 fw-bold">Roles</h5>
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1">{{ $roles->count() }} Defined</span>
                </div>
                <div class="card-body p-0">
                    <div class="roles-list">
                        @foreach($roles as $role)
                            @php
                                $isActive = ($currentRole && $currentRole->id === $role->id);
                                $colorMap = [
                                    'danger' => ['bg' => 'rgba(239, 68, 68, 0.12)', 'color' => '#ef4444', 'icon' => 'bi-shield-fill'],
                                    'warning' => ['bg' => 'rgba(245, 158, 11, 0.12)', 'color' => '#f59e0b', 'icon' => 'bi-person-badge'],
                                    'info' => ['bg' => 'rgba(6, 182, 212, 0.12)', 'color' => '#06b6d4', 'icon' => 'bi-pencil-square'],
                                    'primary' => ['bg' => 'rgba(99, 102, 241, 0.12)', 'color' => '#6366f1', 'icon' => 'bi-shield-check'],
                                    'success' => ['bg' => 'rgba(16, 185, 129, 0.12)', 'color' => '#10b981', 'icon' => 'bi-person-check'],
                                    'secondary' => ['bg' => 'rgba(148, 163, 184, 0.12)', 'color' => '#94a3b8', 'icon' => 'bi-person'],
                                ];
                                $c = $colorMap[$role->color ?? 'primary'] ?? $colorMap['primary'];
                            @endphp
                            <a href="{{ route('admin.roles.index', ['role' => $role->id]) }}" 
                               class="roles-item {{ $isActive ? 'active' : '' }} text-decoration-none">
                                <div class="roles-item-icon" style="background: {{ $c['bg'] }}; color: {{ $c['color'] }};">
                                    <i class="bi {{ $c['icon'] }}"></i>
                                </div>
                                <div class="roles-item-info">
                                    <span class="roles-item-name">{{ $role->name }}</span>
                                    <span class="roles-item-count">{{ $role->users->count() }} {{ Str::plural('user', $role->users->count()) }}</span>
                                </div>
                                <span class="badge bg-{{ $role->color ?? 'secondary' }}-subtle text-{{ $role->color ?? 'secondary' }} rounded-pill px-2">
                                    {{ $role->slug }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Active Role Details Card -->
            @if($currentRole)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                    <h5 class="card-title mb-0 fw-bold">Role Details</h5>
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editRoleModal">
                        <i class="bi bi-pencil me-1"></i> Edit Role
                    </button>
                </div>
                <div class="card-body">
                    <div class="roles-detail-list d-flex flex-column gap-3">
                        <div class="roles-detail pb-2 border-bottom">
                            <span class="roles-detail-label text-muted d-block small text-uppercase fw-semibold">Role Name</span>
                            <span class="roles-detail-value fw-bold fs-6">{{ $currentRole->name }}</span>
                        </div>
                        <div class="roles-detail pb-2 border-bottom">
                            <span class="roles-detail-label text-muted d-block small text-uppercase fw-semibold">Slug Identifier</span>
                            <code class="roles-detail-value text-primary fs-6">{{ $currentRole->slug }}</code>
                        </div>
                        <div class="roles-detail pb-2 border-bottom">
                            <span class="roles-detail-label text-muted d-block small text-uppercase fw-semibold">Description</span>
                            <span class="roles-detail-value text-body">{{ $currentRole->description ?: 'No description provided for this role.' }}</span>
                        </div>
                        <div class="roles-detail pb-2 border-bottom">
                            <span class="roles-detail-label text-muted d-block small text-uppercase fw-semibold">Permissions Count</span>
                            <span class="roles-detail-value fw-semibold text-success">{{ $currentRole->permissions->count() }} active permissions</span>
                        </div>
                        <div class="roles-detail">
                            <span class="roles-detail-label text-muted d-block small text-uppercase fw-semibold">Created Date</span>
                            <span class="roles-detail-value text-muted small">{{ $currentRole->created_at ? $currentRole->created_at->format('M d, Y h:i A') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column: Permissions Matrix & Assigned Users -->
        <div class="col-xl-8 col-lg-7">
            @if($currentRole)
            @php
                // Map permissions by slug for lightning fast lookup
                $permsBySlug = [];
                foreach ($permissions as $module => $modulePerms) {
                    foreach ($modulePerms as $p) {
                        $permsBySlug[$p->slug] = $p;
                    }
                }
                $currentPermIds = $currentRole->permissions->pluck('id')->flip()->toArray();
                $hasPermSlug = fn($slug) => isset($permsBySlug[$slug]) && isset($currentPermIds[$permsBySlug[$slug]->id]);
                $getPermId = fn($slug) => $permsBySlug[$slug]->id ?? null;

                // Matrix structure definition
                $matrixGroups = [
                    'Content Management' => [
                        'icon' => 'bi-file-earmark-text',
                        'rows' => [
                            [
                                'module' => 'Blog Posts',
                                'row_id' => 'posts',
                                'view' => 'posts.view',
                                'create' => 'posts.create',
                                'edit' => 'posts.edit',
                                'delete' => 'posts.delete',
                                'special' => ['slug' => 'posts.publish', 'label' => 'Publish'],
                            ],
                            [
                                'module' => 'Pages',
                                'row_id' => 'pages',
                                'view' => 'pages.view',
                                'create' => 'pages.create',
                                'edit' => 'pages.edit',
                                'delete' => 'pages.delete',
                                'special' => null,
                            ],
                            [
                                'module' => 'Categories',
                                'row_id' => 'categories',
                                'view' => 'categories.view',
                                'create' => 'categories.create',
                                'edit' => 'categories.edit',
                                'delete' => 'categories.delete',
                                'special' => null,
                            ],
                            [
                                'module' => 'Media Library',
                                'row_id' => 'media',
                                'view' => 'media.view',
                                'create' => 'media.upload',
                                'edit' => 'media.edit',
                                'delete' => 'media.delete',
                                'special' => null,
                            ],
                        ],
                    ],
                    'Audience & Community' => [
                        'icon' => 'bi-chat-dots',
                        'rows' => [
                            [
                                'module' => 'Comments',
                                'row_id' => 'comments',
                                'view' => 'comments.view',
                                'create' => 'comments.reply',
                                'edit' => 'comments.moderate',
                                'delete' => 'comments.delete',
                                'special' => null,
                            ],
                            [
                                'module' => 'Newsletter',
                                'row_id' => 'newsletter',
                                'view' => 'newsletter.view',
                                'create' => 'newsletter.send',
                                'edit' => 'newsletter.export',
                                'delete' => 'newsletter.delete',
                                'special' => null,
                            ],
                        ],
                    ],
                    'System & Administration' => [
                        'icon' => 'bi-shield-lock',
                        'rows' => [
                            [
                                'module' => 'User Management',
                                'row_id' => 'users',
                                'view' => 'users.view',
                                'create' => 'users.create',
                                'edit' => 'users.edit',
                                'delete' => 'users.delete',
                                'special' => ['slug' => 'users.roles', 'label' => 'Roles'],
                            ],
                            [
                                'module' => 'Navigation Menu',
                                'row_id' => 'menu',
                                'view' => 'menu.view',
                                'create' => null,
                                'edit' => 'menu.manage',
                                'delete' => null,
                                'special' => null,
                            ],
                            [
                                'module' => 'SEO Intelligence',
                                'row_id' => 'seo',
                                'view' => 'seo.view',
                                'create' => null,
                                'edit' => 'seo.edit',
                                'delete' => null,
                                'special' => null,
                            ],
                            [
                                'module' => 'Site Settings',
                                'row_id' => 'settings',
                                'view' => 'settings.view',
                                'create' => null,
                                'edit' => 'settings.edit',
                                'delete' => null,
                                'special' => null,
                            ],
                        ],
                    ],
                ];
            @endphp

            <!-- Permissions Matrix Form -->
            <form id="permMatrixForm" action="{{ route('admin.roles.update', $currentRole->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="name" value="{{ $currentRole->name }}">
                <input type="hidden" name="description" value="{{ $currentRole->description }}">
                <input type="hidden" name="color" value="{{ $currentRole->color }}">

                <div class="card mb-4 border-0 shadow-sm overflow-hidden">
                    <div class="card-header bg-transparent d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                        <div>
                            <h5 class="card-title mb-0 fw-bold">Permissions Matrix</h5>
                            <span class="roles-subtitle text-muted small">Configure access for <strong>{{ $currentRole->name }}</strong> role</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-secondary active" id="btnViewMatrix" onclick="setPermView('matrix')">
                                    <i class="bi bi-grid-3x3 me-1"></i> Matrix
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="btnViewList" onclick="setPermView('list')">
                                    <i class="bi bi-list-ul me-1"></i> Detailed
                                </button>
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnSelectAll">
                                <i class="bi bi-check-all me-1"></i> Select All
                            </button>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-check-lg me-1"></i> Save Changes
                            </button>
                        </div>
                    </div>

                    <!-- 1. Matrix View (Ref Image Matching Layout) -->
                    <div id="matrixViewContainer" class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table roles-perm-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4" style="width: 28%;">MODULE</th>
                                        <th class="text-center" style="width: 12%;">VIEW</th>
                                        <th class="text-center" style="width: 12%;">CREATE</th>
                                        <th class="text-center" style="width: 12%;">EDIT</th>
                                        <th class="text-center" style="width: 12%;">DELETE</th>
                                        <th class="text-center" style="width: 12%;">SPECIAL</th>
                                        <th class="text-center pe-4" style="width: 12%;">ALL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($matrixGroups as $groupName => $groupData)
                                        <tr class="roles-perm-group">
                                            <td colspan="7" class="ps-4 py-2">
                                                <i class="bi {{ $groupData['icon'] }} me-2 text-primary"></i>
                                                {{ $groupName }}
                                            </td>
                                        </tr>
                                        @foreach($groupData['rows'] as $r)
                                            <tr class="perm-row" data-row-id="{{ $r['row_id'] }}">
                                                <td class="roles-perm-module ps-4 fw-medium text-body">
                                                    {{ $r['module'] }}
                                                </td>

                                                {{-- View Action --}}
                                                <td class="text-center">
                                                    @if($r['view'] && ($id = $getPermId($r['view'])))
                                                        <input type="checkbox" 
                                                               class="perm-checkbox row-perm" 
                                                               name="permissions[]" 
                                                               value="{{ $id }}"
                                                               data-slug="{{ $r['view'] }}"
                                                               data-bs-toggle="tooltip"
                                                               title="Grants {{ $r['view'] }}"
                                                               {{ $hasPermSlug($r['view']) ? 'checked' : '' }}>
                                                    @else
                                                        <span class="roles-perm-na">--</span>
                                                    @endif
                                                </td>

                                                {{-- Create Action --}}
                                                <td class="text-center">
                                                    @if($r['create'] && ($id = $getPermId($r['create'])))
                                                        <input type="checkbox" 
                                                               class="perm-checkbox row-perm" 
                                                               name="permissions[]" 
                                                               value="{{ $id }}"
                                                               data-slug="{{ $r['create'] }}"
                                                               data-bs-toggle="tooltip"
                                                               title="Grants {{ $r['create'] }}"
                                                               {{ $hasPermSlug($r['create']) ? 'checked' : '' }}>
                                                    @else
                                                        <span class="roles-perm-na">--</span>
                                                    @endif
                                                </td>

                                                {{-- Edit Action --}}
                                                <td class="text-center">
                                                    @if($r['edit'] && ($id = $getPermId($r['edit'])))
                                                        <input type="checkbox" 
                                                               class="perm-checkbox row-perm" 
                                                               name="permissions[]" 
                                                               value="{{ $id }}"
                                                               data-slug="{{ $r['edit'] }}"
                                                               data-bs-toggle="tooltip"
                                                               title="Grants {{ $r['edit'] }}"
                                                               {{ $hasPermSlug($r['edit']) ? 'checked' : '' }}>
                                                    @else
                                                        <span class="roles-perm-na">--</span>
                                                    @endif
                                                </td>

                                                {{-- Delete Action --}}
                                                <td class="text-center">
                                                    @if($r['delete'] && ($id = $getPermId($r['delete'])))
                                                        <input type="checkbox" 
                                                               class="perm-checkbox row-perm" 
                                                               name="permissions[]" 
                                                               value="{{ $id }}"
                                                               data-slug="{{ $r['delete'] }}"
                                                               data-bs-toggle="tooltip"
                                                               title="Grants {{ $r['delete'] }}"
                                                               {{ $hasPermSlug($r['delete']) ? 'checked' : '' }}>
                                                    @else
                                                        <span class="roles-perm-na">--</span>
                                                    @endif
                                                </td>

                                                {{-- Special Action --}}
                                                <td class="text-center">
                                                    @if($r['special'] && ($id = $getPermId($r['special']['slug'])))
                                                        <div class="d-inline-flex align-items-center gap-1">
                                                            <input type="checkbox" 
                                                                   class="perm-checkbox row-perm" 
                                                                   name="permissions[]" 
                                                                   value="{{ $id }}"
                                                                   data-slug="{{ $r['special']['slug'] }}"
                                                                   data-bs-toggle="tooltip"
                                                                   title="Grants {{ $r['special']['slug'] }}"
                                                                   {{ $hasPermSlug($r['special']['slug']) ? 'checked' : '' }}>
                                                            <span class="badge bg-light text-muted border px-1" style="font-size: 0.65rem;">{{ $r['special']['label'] }}</span>
                                                        </div>
                                                    @else
                                                        <span class="roles-perm-na">--</span>
                                                    @endif
                                                </td>

                                                {{-- Row All Action --}}
                                                <td class="text-center pe-4">
                                                    <input type="checkbox" 
                                                           class="perm-checkbox row-select-all" 
                                                           title="Toggle all for {{ $r['module'] }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 2. Detailed List View (Optional Toggle) -->
                    <div id="listViewContainer" class="card-body p-0 d-none">
                        <div class="table-responsive">
                            <table class="table roles-perm-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4" style="width: 30%;">MODULE & PERMISSION</th>
                                        <th style="width: 38%;">DESCRIPTION</th>
                                        <th style="width: 18%;">KEY</th>
                                        <th class="text-center pe-4" style="width: 14%;">ALLOWED</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($permissions as $module => $modulePermissions)
                                        <tr class="roles-perm-group">
                                            <td colspan="4" class="ps-4 py-2">
                                                <i class="bi bi-grid me-2 text-primary"></i>
                                                {{ ucfirst($module) }} Management
                                            </td>
                                        </tr>
                                        @foreach($modulePermissions as $perm)
                                            @php
                                                $hasPerm = $currentRole->permissions->contains('id', $perm->id);
                                            @endphp
                                            <tr>
                                                <td class="ps-4 fw-semibold text-body">
                                                    {{ $perm->name }}
                                                </td>
                                                <td class="text-muted small">
                                                    {{ $perm->description ?: 'Grants access to execute ' . $perm->slug . ' within ' . $module }}
                                                </td>
                                                <td>
                                                    <code class="small text-muted">{{ $perm->slug }}</code>
                                                </td>
                                                <td class="text-center pe-4">
                                                    <input type="checkbox" 
                                                           class="perm-checkbox list-perm" 
                                                           data-sync-id="{{ $perm->id }}"
                                                           value="{{ $perm->id }}"
                                                           id="list_perm_{{ $perm->id }}"
                                                           {{ $hasPerm ? 'checked' : '' }}>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center py-3">
                        <span class="text-muted small">Checked permissions will be assigned to <strong>{{ $currentRole->name }}</strong>.</span>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-check-lg me-1"></i> Save Changes
                        </button>
                    </div>
                </div>
            </form>

            <!-- Users Assigned to this Role Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between py-3">
                    <div>
                        <h5 class="card-title mb-0 fw-bold">Users with {{ $currentRole->name }} Role</h5>
                        <small class="text-muted">Total {{ $currentRole->users->count() }} active staff assigned</small>
                    </div>
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assignUserModal">
                        <i class="bi bi-person-plus me-1"></i> Add User
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">User</th>
                                    <th>Role Details</th>
                                    <th>Joined</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($currentRole->users as $u)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="{{ $u->profile_photo_url ?? asset('admin-dist/img/avatar-1-K2kaIzYo.webp') }}" 
                                                     alt="{{ $u->name }}" 
                                                     class="rounded-circle object-fit-cover" 
                                                     width="38" height="38"
                                                     onerror="this.onerror=null; this.src='{{ asset('admin-dist/img/avatar-1-K2kaIzYo.webp') }}'">
                                                <div>
                                                    <div class="fw-semibold text-body">{{ $u->name }}</div>
                                                    <small class="text-muted">{{ $u->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $currentRole->color ?? 'primary' }}-subtle text-{{ $currentRole->color ?? 'primary' }}">
                                                {{ $currentRole->name }}
                                            </span>
                                        </td>
                                        <td class="text-muted small">
                                            {{ $u->created_at ? $u->created_at->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.users.show', $u->id) }}" class="btn btn-outline-info" title="View Profile & Authority">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.users.edit', $u->id) }}" class="btn btn-outline-primary" title="Edit & Reassign Authority">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger" title="Remove role from user" onclick="showConfirmDialog({ title: 'Remove Role Assignment?', text: 'Remove {{ $u->name }} from {{ $currentRole->name }} role?' }, () => document.getElementById('removeUserForm-{{ $u->id }}').submit())">
                                                    <i class="bi bi-person-x"></i>
                                                </button>
                                            </div>
                                            <form id="removeUserForm-{{ $u->id }}" action="{{ route('admin.roles.remove-user', $currentRole->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $u->id }}">
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="bi bi-people fs-3 d-block mb-2 text-muted"></i>
                                            No users currently assigned to this role.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- ================= MODALS ================= -->

<!-- Add Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="addRoleModalLabel">Create New Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Content Reviewer" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Describe the authority and scope of this role..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Badge Color Theme</label>
                        <div class="d-flex gap-3">
                            @foreach(['primary' => '#6366f1', 'danger' => '#ef4444', 'warning' => '#f59e0b', 'info' => '#06b6d4', 'success' => '#10b981', 'secondary' => '#94a3b8'] as $col => $hex)
                                <label class="form-check form-check-inline cursor-pointer">
                                    <input class="form-check-input" type="radio" name="color" value="{{ $col }}" {{ $col === 'primary' ? 'checked' : '' }}>
                                    <span class="d-inline-block rounded-circle align-middle ms-1" style="width: 18px; height: 18px; background-color: {{ $hex }};"></span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($currentRole)
<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.roles.update', $currentRole->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="editRoleModalLabel">Edit Role: {{ $currentRole->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ $currentRole->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ $currentRole->description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Badge Color Theme</label>
                        <div class="d-flex gap-3">
                            @foreach(['primary' => '#6366f1', 'danger' => '#ef4444', 'warning' => '#f59e0b', 'info' => '#06b6d4', 'success' => '#10b981', 'secondary' => '#94a3b8'] as $col => $hex)
                                <label class="form-check form-check-inline cursor-pointer">
                                    <input class="form-check-input" type="radio" name="color" value="{{ $col }}" {{ $currentRole->color === $col ? 'checked' : '' }}>
                                    <span class="d-inline-block rounded-circle align-middle ms-1" style="width: 18px; height: 18px; background-color: {{ $hex }};"></span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @if($currentRole->slug !== 'super-admin')
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-danger small">Danger Zone: Permanently remove this role</span>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="showConfirmDialog({ title: 'Delete Role?', text: 'Are you sure you want to delete this role? Users will lose their role assignments.', icon: 'error', confirmButtonText: 'Delete Role' }, () => document.getElementById('delete-role-form').submit())">
                            <i class="bi bi-trash me-1"></i> Delete Role
                        </button>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
            @if($currentRole->slug !== 'super-admin')
            <form id="delete-role-form" action="{{ route('admin.roles.destroy', $currentRole->id) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
            @endif
        </div>
    </div>
</div>
@endif

<!-- Assign User to Role Modal -->
<div class="modal fade" id="assignUserModal" tabindex="-1" aria-labelledby="assignUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.roles.assign') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="assignUserModalLabel">Assign User to Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select User <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select" required>
                            <option value="">-- Choose User --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Role <span class="text-danger">*</span></label>
                        <select name="role_id" class="form-select" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ ($currentRole && $currentRole->id === $role->id) ? 'selected' : '' }}>
                                    {{ $role->name }} ({{ $role->slug }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* ============================================================
   Roles & Permissions UI - Exact Match to Design Template
   ============================================================ */

/* Left Navigation Roles List */
.page-roles .roles-list {
    display: flex;
    flex-direction: column;
}

.page-roles .roles-item {
    display: flex;
    align-items: center;
    gap: 0.875rem;
    padding: 0.875rem 1.25rem;
    border: none !important;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
    border-left: 3.5px solid transparent !important;
    cursor: pointer;
    background: transparent;
    color: inherit;
    text-decoration: none;
    transition: background 0.15s ease-in-out, border-color 0.15s ease-in-out;
}

.page-roles .roles-item:last-child {
    border-bottom: none !important;
}

.page-roles .roles-item:hover {
    background: rgba(99, 102, 241, 0.03);
}

/* Active Role Item: ONLY Left Border, NO top or right border */
.page-roles .roles-item.active {
    background: #f8f9ff !important;
    border-top: none !important;
    border-right: none !important;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
    border-left: 3.5px solid #6366f1 !important;
    padding-left: calc(1.25rem - 0.5px) !important;
}

[data-bs-theme="dark"] .page-roles .roles-item.active {
    background: rgba(99, 102, 241, 0.12) !important;
    border-left-color: #818cf8 !important;
}

.page-roles .roles-item.active .roles-item-name {
    color: #4f46e5 !important;
    font-weight: 700;
}

[data-bs-theme="dark"] .page-roles .roles-item.active .roles-item-name {
    color: #818cf8 !important;
}

.page-roles .roles-item-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
}

.page-roles .roles-item-info {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
}

.page-roles .roles-item-name {
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--heading-color, #1e293b);
}

.page-roles .roles-item-count {
    font-size: 0.8125rem;
    color: var(--muted-color, #64748b);
}

/* ============================================================
   Permissions Matrix Table & Custom Checkbox Styling
   ============================================================ */

.page-roles .roles-perm-table {
    font-size: 0.875rem;
}

.page-roles .roles-perm-table thead th {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--muted-color, #64748b);
    border-bottom-width: 1px;
    background-color: var(--bs-light-bg-subtle, #f8faff);
    padding: 0.75rem 1rem;
}

.page-roles .roles-perm-table td {
    vertical-align: middle;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid var(--border-color-light, rgba(0,0,0,0.05));
}

.page-roles .roles-perm-group td {
    background: var(--bs-light-bg-subtle, #f8fafc) !important;
    font-weight: 600;
    font-size: 0.8125rem;
    color: var(--heading-color, #334155);
    text-transform: uppercase;
    letter-spacing: 0.03em;
    border-bottom: 1px solid var(--border-color, #e2e8f0);
}

.page-roles .roles-perm-module {
    color: var(--default-color, #1e293b);
}

.page-roles .roles-perm-na {
    color: #94a3b8;
    font-weight: 500;
    font-size: 0.875rem;
    user-select: none;
}

/* Custom Checkbox - Crisp Rounded Square with White Checkmark */
.page-roles .perm-checkbox {
    width: 20px !important;
    height: 20px !important;
    min-width: 20px !important;
    border-radius: 6px !important;
    border: 1.5px solid #cbd5e1 !important;
    background-color: var(--bs-body-bg, #ffffff) !important;
    cursor: pointer !important;
    vertical-align: middle !important;
    margin: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: all 0.15s ease-in-out !important;
    -webkit-appearance: none !important;
    appearance: none !important;
    position: relative !important;
    outline: none !important;
}

.page-roles .perm-checkbox:hover {
    border-color: #6366f1 !important;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15) !important;
}

.page-roles .perm-checkbox:checked {
    background-color: #6366f1 !important;
    border-color: #6366f1 !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23ffffff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='m5.5 10.5 3 3 6-6'/%3e%3c/svg%3e") !important;
    background-repeat: no-repeat !important;
    background-position: center !important;
    background-size: 70% !important;
}

[data-bs-theme="dark"] .page-roles .perm-checkbox {
    border-color: #475569 !important;
    background-color: #1e293b !important;
}

[data-bs-theme="dark"] .page-roles .perm-checkbox:checked {
    background-color: #6366f1 !important;
    border-color: #6366f1 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Initialize Bootstrap Tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(el) {
        return new bootstrap.Tooltip(el);
    });

    // 2. Row All Checkboxes logic
    function updateRowSelectAll(row) {
        const rowAllCheckbox = row.querySelector('.row-select-all');
        if (!rowAllCheckbox) return;

        const rowPerms = row.querySelectorAll('.row-perm');
        if (rowPerms.length === 0) return;

        const allChecked = Array.from(rowPerms).every(cb => cb.checked);
        rowAllCheckbox.checked = allChecked;
    }

    // Initialize all rows on load
    document.querySelectorAll('.perm-row').forEach(row => {
        updateRowSelectAll(row);

        // When any permission in the row changes
        row.querySelectorAll('.row-perm').forEach(cb => {
            cb.addEventListener('change', function() {
                updateRowSelectAll(row);
                syncMatrixToList(cb.value, cb.checked);
                updateGlobalSelectAllState();
            });
        });

        // When row-select-all is toggled
        const rowAllCheckbox = row.querySelector('.row-select-all');
        if (rowAllCheckbox) {
            rowAllCheckbox.addEventListener('change', function() {
                const isChecked = rowAllCheckbox.checked;
                row.querySelectorAll('.row-perm').forEach(cb => {
                    cb.checked = isChecked;
                    syncMatrixToList(cb.value, isChecked);
                });
                updateGlobalSelectAllState();
            });
        }
    });

    // 3. Global Select All Button
    const btnSelectAll = document.getElementById('btnSelectAll');
    if (btnSelectAll) {
        btnSelectAll.addEventListener('click', function() {
            const allPerms = document.querySelectorAll('.perm-checkbox.row-perm');
            const someUnchecked = Array.from(allPerms).some(cb => !cb.checked);
            const newState = someUnchecked;

            allPerms.forEach(cb => {
                cb.checked = newState;
                syncMatrixToList(cb.value, newState);
            });

            document.querySelectorAll('.row-select-all').forEach(cb => {
                cb.checked = newState;
            });

            updateGlobalSelectAllState();
        });
    }

    function updateGlobalSelectAllState() {
        if (!btnSelectAll) return;
        const allPerms = document.querySelectorAll('.perm-checkbox.row-perm');
        const allChecked = Array.from(allPerms).every(cb => cb.checked);
        btnSelectAll.innerHTML = allChecked 
            ? '<i class="bi bi-x-circle me-1"></i> Deselect All' 
            : '<i class="bi bi-check-all me-1"></i> Select All';
    }

    // 4. Synchronization between Matrix View and Detailed List View
    function syncMatrixToList(permId, isChecked) {
        const listCheckbox = document.querySelector(`.list-perm[data-sync-id="${permId}"]`);
        if (listCheckbox) {
            listCheckbox.checked = isChecked;
        }
    }

    document.querySelectorAll('.list-perm').forEach(cb => {
        cb.addEventListener('change', function() {
            const permId = cb.getAttribute('data-sync-id');
            const matrixCheckbox = document.querySelector(`.row-perm[value="${permId}"]`);
            if (matrixCheckbox) {
                matrixCheckbox.checked = cb.checked;
                const row = matrixCheckbox.closest('.perm-row');
                if (row) updateRowSelectAll(row);
            }
            updateGlobalSelectAllState();
        });
    });

    // 5. View Switcher (Matrix vs Detailed List)
    window.setPermView = function(view) {
        const matrixContainer = document.getElementById('matrixViewContainer');
        const listContainer = document.getElementById('listViewContainer');
        const btnMatrix = document.getElementById('btnViewMatrix');
        const btnList = document.getElementById('btnViewList');

        if (view === 'matrix') {
            matrixContainer.classList.remove('d-none');
            listContainer.classList.add('d-none');
            btnMatrix.classList.add('active', 'btn-primary');
            btnMatrix.classList.remove('btn-outline-secondary');
            btnList.classList.remove('active', 'btn-primary');
            btnList.classList.add('btn-outline-secondary');
        } else {
            matrixContainer.classList.add('d-none');
            listContainer.classList.remove('d-none');
            btnList.classList.add('active', 'btn-primary');
            btnList.classList.remove('btn-outline-secondary');
            btnMatrix.classList.remove('active', 'btn-primary');
            btnMatrix.classList.add('btn-outline-secondary');
        }
    };

    updateGlobalSelectAllState();
});
</script>
@endsection
