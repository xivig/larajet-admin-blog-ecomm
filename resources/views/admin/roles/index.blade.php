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
            <!-- Roles Navigation List -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">System Roles</h5>
                    <span class="badge bg-primary-subtle text-primary">{{ $roles->count() }} Defined</span>
                </div>
                <div class="card-body p-0">
                    <div class="roles-list list-group list-group-flush">
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
                               class="roles-item list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3 px-3 border-start {{ $isActive ? 'active border-start-primary border-4 bg-light-subtle' : 'border-0' }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="roles-item-icon rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px; background: {{ $c['bg'] }}; color: {{ $c['color'] }}; font-size: 1.15rem;">
                                        <i class="bi {{ $c['icon'] }}"></i>
                                    </div>
                                    <div class="roles-item-info">
                                        <h6 class="roles-item-name mb-0 fw-semibold">{{ $role->name }}</h6>
                                        <small class="roles-item-count text-muted">{{ $role->users->count() }} assigned users</small>
                                    </div>
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
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
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
            <!-- Permissions Matrix Form -->
            <form action="{{ route('admin.roles.update', $currentRole->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="name" value="{{ $currentRole->name }}">
                <input type="hidden" name="description" value="{{ $currentRole->description }}">
                <input type="hidden" name="color" value="{{ $currentRole->color }}">

                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-transparent d-flex align-items-center justify-content-between py-3">
                        <div>
                            <h5 class="card-title mb-0 fw-bold">Permissions Matrix</h5>
                            <small class="text-muted">Configure access rights for <strong>{{ $currentRole->name }}</strong></small>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnSelectAll">
                                <i class="bi bi-check-all me-1"></i> Select All
                            </button>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table roles-perm-table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4" style="width: 35%;">Module</th>
                                        <th style="width: 35%;">Permission Description</th>
                                        <th style="width: 15%;">Key</th>
                                        <th class="text-center pe-4" style="width: 15%;">Allowed</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $moduleIcons = [
                                            'posts' => 'bi-file-earmark-post',
                                            'categories' => 'bi-folder',
                                            'tags' => 'bi-tags',
                                            'comments' => 'bi-chat-dots',
                                            'users' => 'bi-people',
                                            'roles' => 'bi-shield-lock',
                                            'chat' => 'bi-chat-text',
                                            'email' => 'bi-envelope',
                                            'files' => 'bi-folder2-open',
                                            'settings' => 'bi-gear',
                                        ];
                                    @endphp
                                    @forelse($permissions as $module => $modulePermissions)
                                        <tr class="table-active bg-light">
                                            <td colspan="4" class="ps-4 fw-bold text-uppercase small text-secondary">
                                                <i class="bi {{ $moduleIcons[strtolower($module)] ?? 'bi-grid' }} me-2 text-primary"></i>
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
                                                    <div class="form-check form-switch d-inline-block">
                                                        <input class="form-check-input perm-checkbox" 
                                                               type="checkbox" 
                                                               name="permissions[]" 
                                                               value="{{ $perm->id }}"
                                                               id="perm_{{ $perm->id }}"
                                                               {{ $hasPerm ? 'checked' : '' }}>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                <i class="bi bi-info-circle fs-3 d-block mb-2"></i>
                                                No permissions registered in system.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent d-flex justify-content-between align-items-center py-3">
                        <span class="text-muted small">Checked permissions will be assigned to {{ $currentRole->name }}.</span>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-save me-1"></i> Save Changes
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
                                                     onerror="this.src='{{ asset('admin-dist/img/avatar-1-K2kaIzYo.webp') }}'">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnSelectAll = document.getElementById('btnSelectAll');
    if (btnSelectAll) {
        let allSelected = false;
        btnSelectAll.addEventListener('click', function() {
            allSelected = !allSelected;
            document.querySelectorAll('.perm-checkbox').forEach(cb => {
                cb.checked = allSelected;
            });
            btnSelectAll.innerHTML = allSelected 
                ? '<i class="bi bi-x-circle me-1"></i> Deselect All' 
                : '<i class="bi bi-check-all me-1"></i> Select All';
        });
    }
});
</script>
@endsection