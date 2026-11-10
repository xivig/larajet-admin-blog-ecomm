@extends('layouts.admin')

@section('title', 'Navigation Menu Builder - Xivig Admin')

@section('content')
<div class="page-menu-builder">
    <!-- Header -->
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="page-title mb-1">Navigation Menu Builder</h1>
            <nav class="breadcrumb mb-0">
                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
                <span class="breadcrumb-item active">Navigation Menus</span>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ url('/') }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-box-arrow-up-right me-1"></i> Preview Site
            </a>
            @if (!$slotMenus['header'] || !$slotMenus['footer'] || !$slotMenus['footer_ecosystem'])
                <form action="{{ route('admin.menu.sync-defaults') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-arrow-repeat me-1"></i> Sync Default Slots
                    </button>
                </form>
            @endif
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newMenuModal">
                <i class="bi bi-plus-lg me-1"></i> Create Menu
            </button>
        </div>
    </div>

    <!-- Front-End Sync Slots & Menu Selector Toolbar -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="fw-semibold small text-muted text-uppercase me-1">Live Sync Slots:</span>
                @foreach ([
                    'header' => ['label' => 'Header Navbar', 'icon' => 'bi-layout-navbar'],
                    'footer' => ['label' => 'Footer Platform', 'icon' => 'bi-layout-text-window-reverse'],
                    'footer_ecosystem' => ['label' => 'Footer Ecosystem', 'icon' => 'bi-columns-gap'],
                ] as $slotKey => $slotMeta)
                    @php $slotMenu = $slotMenus[$slotKey] ?? null; @endphp
                    @if ($slotMenu)
                        <a href="{{ route('admin.menu.index', ['menu' => $slotMenu->id]) }}"
                           class="btn btn-sm {{ $currentMenu && $currentMenu->id === $slotMenu->id ? 'btn-primary' : 'btn-outline-secondary' }}">
                            <i class="bi {{ $slotMeta['icon'] }} me-1"></i>
                            {{ $slotMeta['label'] }}
                            <span class="badge {{ $currentMenu && $currentMenu->id === $slotMenu->id ? 'bg-white text-primary' : 'bg-light text-dark border' }} ms-1">{{ $slotMenu->items->count() }}</span>
                        </a>
                    @else
                        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#newMenuModal"
                                onclick="document.getElementById('menuLocation').value='{{ $slotKey }}'">
                            <i class="bi bi-plus-circle me-1"></i> Bind {{ $slotMeta['label'] }}
                        </button>
                    @endif
                @endforeach

                <div class="dropdown ms-1">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle fw-semibold" type="button" data-bs-toggle="dropdown">
                        All Menus ({{ $menus->count() }})
                    </button>
                    <ul class="dropdown-menu shadow">
                        @foreach ($menus as $m)
                            <li>
                                <a class="dropdown-item d-flex justify-content-between align-items-center gap-3 {{ $currentMenu && $currentMenu->id === $m->id ? 'active' : '' }}" href="{{ route('admin.menu.index', ['menu' => $m->id]) }}">
                                    <span>{{ $m->name }}</span>
                                    <small class="badge bg-light text-dark">{{ \App\Models\Menu::locationLabel($m->location) }}</small>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            @if($currentMenu)
                @php
                    $isActiveSlotMenu = ($slotMenus[$currentMenu->location] ?? null)?->id === $currentMenu->id;
                @endphp
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="badge {{ $isActiveSlotMenu ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-light text-dark border' }} py-2 px-2">
                        <i class="bi {{ $isActiveSlotMenu ? 'bi-broadcast' : 'bi-geo-alt' }} me-1"></i>
                        {{ \App\Models\Menu::locationLabel($currentMenu->location) }} (<code>{{ $currentMenu->location }}</code>)
                    </span>
                    <form action="{{ route('admin.menu.destroy', $currentMenu->id) }}"
                          method="POST"
                          class="js-swal-confirm"
                          data-confirm-title="Delete Navigation Menu?"
                          data-confirm-text="Delete menu &quot;{{ $currentMenu->name }}&quot; and all its links? This action cannot be undone."
                          data-confirm-button="Yes, Delete Menu">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash me-1"></i> Delete Menu
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    @if($currentMenu)
    <div class="row g-4">
        <!-- Left Column: Add Links -->
        <div class="col-lg-4">
            <div class="accordion border-0 shadow-sm rounded overflow-hidden" id="menuAddAccordion">

                <!-- Custom Link Card -->
                <div class="accordion-item border-0 border-bottom">
                    <h2 class="accordion-header" id="headingCustom">
                        <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCustom" aria-expanded="true">
                            <i class="bi bi-link-45deg me-2 text-primary fs-5"></i> Custom Link
                        </button>
                    </h2>
                    <div id="collapseCustom" class="accordion-collapse collapse show" data-bs-parent="#menuAddAccordion">
                        <div class="accordion-body">
                            <form action="{{ route('admin.menu.items.add', $currentMenu->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">URL <span class="text-danger">*</span></label>
                                    <input type="text" name="url" class="form-control form-control-sm" placeholder="https:// or /path (use #pages for auto Pages)" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Link Text <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control form-control-sm" placeholder="e.g. Documentation" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Target</label>
                                    <select name="target" class="form-select form-select-sm">
                                        <option value="_self">Same Window (_self)</option>
                                        <option value="_blank">New Tab (_blank)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Parent Level (Hierarchy)</label>
                                    <select name="parent_id" class="form-select form-select-sm">
                                        <option value="">Top Level (Root)</option>
                                        @foreach($rootItems as $parentOpt)
                                            <option value="{{ $parentOpt->id }}">— Sub-item of: {{ $parentOpt->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                    <i class="bi bi-plus-circle me-1"></i> Add to Menu
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Built-in Platform Routes Quick Add -->
                <div class="accordion-item border-0 border-bottom">
                    <h2 class="accordion-header" id="headingRoutes">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRoutes">
                            <i class="bi bi-compass me-2 text-info fs-5"></i> Platform Routes
                        </button>
                    </h2>
                    <div id="collapseRoutes" class="accordion-collapse collapse" data-bs-parent="#menuAddAccordion">
                        <div class="accordion-body p-0">
                            <div class="list-group list-group-flush small" style="max-height: 260px; overflow-y: auto;">
                                @foreach ([
                                    ['title' => 'Home', 'url' => '/'],
                                    ['title' => 'Services', 'url' => '/services'],
                                    ['title' => 'Pricing Plans', 'url' => '/pricing'],
                                    ['title' => 'Portfolio', 'url' => '/project'],
                                    ['title' => 'Blog (Mega-Menu)', 'url' => '/blog'],
                                    ['title' => 'Blog Magazine', 'url' => '/blog-list'],
                                    ['title' => 'Pages (Auto Dropdown)', 'url' => '#pages'],
                                    ['title' => 'Commerce Store', 'url' => '/ecomm'],
                                    ['title' => 'Contact Support', 'url' => '/contact'],
                                    ['title' => 'Admin Portal', 'url' => '/admin/login'],
                                ] as $presetRoute)
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <div>
                                            <span class="d-block fw-semibold">{{ $presetRoute['title'] }}</span>
                                            <small class="text-muted"><code>{{ $presetRoute['url'] }}</code></small>
                                        </div>
                                        <form action="{{ route('admin.menu.items.add', $currentMenu->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="title" value="{{ $presetRoute['title'] }}">
                                            <input type="hidden" name="url" value="{{ $presetRoute['url'] }}">
                                            <button type="submit" class="btn btn-sm btn-outline-info py-0 px-2">Add</button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Static Pages Quick Add -->
                <div class="accordion-item border-0 border-bottom">
                    <h2 class="accordion-header" id="headingPages">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePages">
                            <i class="bi bi-file-earmark-text me-2 text-success fs-5"></i> Static Pages
                        </button>
                    </h2>
                    <div id="collapsePages" class="accordion-collapse collapse" data-bs-parent="#menuAddAccordion">
                        <div class="accordion-body p-0">
                            <div class="list-group list-group-flush small" style="max-height: 250px; overflow-y: auto;">
                                @forelse ($pages as $p)
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <span>{{ $p->title }}</span>
                                        <form action="{{ route('admin.menu.items.add', $currentMenu->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="title" value="{{ $p->title }}">
                                            <input type="hidden" name="url" value="/page/{{ $p->slug }}">
                                            <button type="submit" class="btn btn-sm btn-outline-success py-0 px-2">
                                                Add
                                            </button>
                                        </form>
                                    </div>
                                @empty
                                    <div class="list-group-item text-muted text-center py-3">No static pages available.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Categories Quick Add -->
                <div class="accordion-item border-0">
                    <h2 class="accordion-header" id="headingCats">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCats">
                            <i class="bi bi-folder2 me-2 text-warning fs-5"></i> Categories
                        </button>
                    </h2>
                    <div id="collapseCats" class="accordion-collapse collapse" data-bs-parent="#menuAddAccordion">
                        <div class="accordion-body p-0">
                            <div class="list-group list-group-flush small" style="max-height: 250px; overflow-y: auto;">
                                @forelse ($categories as $cat)
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <span>{{ $cat->name }}</span>
                                        <form action="{{ route('admin.menu.items.add', $currentMenu->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="title" value="{{ $cat->name }}">
                                            <input type="hidden" name="url" value="/blog?category={{ urlencode($cat->name) }}">
                                            <button type="submit" class="btn btn-sm btn-outline-warning py-0 px-2">
                                                Add
                                            </button>
                                        </form>
                                    </div>
                                @empty
                                    <div class="list-group-item text-muted text-center py-3">No categories found.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Right Column: Menu Structure -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="card-title mb-0 fw-bold">Menu Structure ({{ $currentMenu->name }})</h5>
                        <small class="text-muted">Reorder, edit, or nest links — changes sync immediately with the live front-end</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span id="menuOrderSyncStatus" class="badge bg-success-subtle text-success d-none">Order synchronized</span>
                        <span class="badge bg-primary-subtle text-primary">{{ $hierarchicalItems->count() }} Items</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($hierarchicalItems->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-list-nested fs-1 d-block mb-2 text-secondary"></i>
                            This menu has no navigation links yet. Use the left panel to add custom links, routes, pages, or categories.
                        </div>
                    @else
                        <div class="list-group" id="menuItemsList" data-menu-id="{{ $currentMenu->id }}">
                            @foreach ($hierarchicalItems as $item)
                                <div class="list-group-item menu-builder-row d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 {{ $item->parent_id ? 'ms-4 border-start border-primary border-3 bg-light' : '' }}"
                                     data-id="{{ $item->id }}"
                                     data-parent-id="{{ $item->parent_id ?? '' }}">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="btn-group-vertical btn-group-sm" role="group" aria-label="Reorder {{ $item->title }}">
                                            <button type="button" class="btn btn-outline-secondary py-0 px-1 js-move-up" title="Move Up">
                                                <i class="bi bi-chevron-up" style="font-size: 11px;"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary py-0 px-1 js-move-down" title="Move Down">
                                                <i class="bi bi-chevron-down" style="font-size: 11px;"></i>
                                            </button>
                                        </div>
                                        <div>
                                            <div class="d-flex flex-wrap align-items-center gap-2">
                                                <span class="fw-bold">{{ $item->title }}</span>
                                                @if($item->parent_id)
                                                    <span class="badge bg-primary-subtle text-primary rounded-pill small" style="font-size: 10px;">Sub-link</span>
                                                @endif
                                                @if($currentMenu->location === 'header' && !$item->parent_id && $item->isPagesPlaceholder())
                                                    <span class="badge bg-info-subtle text-info rounded-pill small" style="font-size: 10px;">Auto-syncs Active Pages</span>
                                                @elseif($currentMenu->location === 'header' && !$item->parent_id && $item->isBlogMegaTrigger())
                                                    <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill small" style="font-size: 10px;">Mega-Menu Dropdown</span>
                                                @endif
                                            </div>
                                            <small class="text-muted">
                                                <code>{{ $item->url }}</code> &bull; <span class="badge bg-light text-dark border">{{ $item->target }}</span>
                                            </small>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary js-edit-menu-item"
                                                title="Edit Item"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editMenuItemModal"
                                                data-action="{{ route('admin.menu.items.update', $item->id) }}"
                                                data-id="{{ $item->id }}"
                                                data-title="{{ $item->title }}"
                                                data-url="{{ $item->url }}"
                                                data-target="{{ $item->target ?? '_self' }}"
                                                data-parent-id="{{ $item->parent_id ?? '' }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('admin.menu.items.delete', $item->id) }}"
                                              method="POST"
                                              class="js-swal-confirm"
                                              data-confirm-title="Remove Menu Link?"
                                              data-confirm-text="Remove &quot;{{ $item->title }}&quot; from {{ $currentMenu->name }}? Any sub-links will remain available."
                                              data-confirm-button="Yes, Remove Link">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove Item">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Edit Menu Item Modal -->
    @if($currentMenu)
    <div class="modal fade" id="editMenuItemModal" tabindex="-1" aria-labelledby="editMenuItemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editMenuItemForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editMenuItemModalLabel">Edit Navigation Link</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editItemTitle" class="form-label fw-semibold">Link Text <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="editItemTitle" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="editItemUrl" class="form-label fw-semibold">URL <span class="text-danger">*</span></label>
                            <input type="text" name="url" id="editItemUrl" class="form-control" required>
                            <div class="form-text">Use <code>/path</code>, full <code>https://</code> URL, or <code>#pages</code> for dynamic site pages.</div>
                        </div>
                        <div class="mb-3">
                            <label for="editItemTarget" class="form-label fw-semibold">Target</label>
                            <select name="target" id="editItemTarget" class="form-select">
                                <option value="_self">Same Window (_self)</option>
                                <option value="_blank">New Tab (_blank)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editItemParent" class="form-label fw-semibold">Parent Level</label>
                            <select name="parent_id" id="editItemParent" class="form-select">
                                <option value="">Top Level (Root)</option>
                                @foreach($rootItems as $parentOpt)
                                    <option value="{{ $parentOpt->id }}">— Sub-item of: {{ $parentOpt->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- New Menu Modal -->
    <div class="modal fade" id="newMenuModal" tabindex="-1" aria-labelledby="newMenuModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.menu.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="newMenuModalLabel">Create New Navigation Menu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="menuName" class="form-label fw-semibold">Menu Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="menuName" class="form-control" placeholder="e.g. Footer Platform Links" required>
                        </div>
                        <div class="mb-3">
                            <label for="menuLocation" class="form-label fw-semibold">Display Location</label>
                            <select name="location" id="menuLocation" class="form-select">
                                @foreach($locations as $locKey => $locLabel)
                                    <option value="{{ $locKey }}">{{ $locLabel }} ({{ $locKey }})</option>
                                @endforeach
                            </select>
                            <div class="form-text">Selecting Header Navbar or Footer locations automatically synchronizes with the live site.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Menu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const editForm = document.getElementById('editMenuItemForm');
    const titleInput = document.getElementById('editItemTitle');
    const urlInput = document.getElementById('editItemUrl');
    const targetSelect = document.getElementById('editItemTarget');
    const parentSelect = document.getElementById('editItemParent');

    document.querySelectorAll('.js-edit-menu-item').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (!editForm) return;
            const currentId = btn.dataset.id || '';
            editForm.action = btn.dataset.action || '';
            titleInput.value = btn.dataset.title || '';
            urlInput.value = btn.dataset.url || '';
            targetSelect.value = btn.dataset.target || '_self';
            parentSelect.value = btn.dataset.parentId || '';

            Array.from(parentSelect.options).forEach((opt) => {
                opt.disabled = opt.value !== '' && opt.value === currentId;
            });
        });
    });

    const listEl = document.getElementById('menuItemsList');
    const syncStatusEl = document.getElementById('menuOrderSyncStatus');
    if (!listEl) return;

    const getBlock = (row) => {
        const id = row.dataset.id;
        const parentId = row.dataset.parentId;
        if (parentId) return [row];

        const block = [row];
        let next = row.nextElementSibling;
        while (next && next.dataset.parentId === id) {
            block.push(next);
            next = next.nextElementSibling;
        }
        return block;
    };

    const saveOrder = async () => {
        const rows = Array.from(listEl.querySelectorAll('.menu-builder-row'));
        const items = rows.map((row) => ({
            id: Number(row.dataset.id),
            parent_id: row.dataset.parentId ? Number(row.dataset.parentId) : null,
        }));

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
        const res = await fetch('{{ route('admin.menu.items.reorder') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                menu_id: Number(listEl.dataset.menuId),
                items,
            }),
        });

        if (res.ok && syncStatusEl) {
            syncStatusEl.classList.remove('d-none');
            clearTimeout(window.__menuSyncBadgeTimer);
            window.__menuSyncBadgeTimer = setTimeout(() => syncStatusEl.classList.add('d-none'), 2200);
        }
    };

    listEl.addEventListener('click', (e) => {
        const upBtn = e.target.closest('.js-move-up');
        const downBtn = e.target.closest('.js-move-down');
        if (!upBtn && !downBtn) return;

        const row = e.target.closest('.menu-builder-row');
        if (!row) return;

        const parentId = row.dataset.parentId || '';
        const block = getBlock(row);

        if (upBtn) {
            let prev = row.previousElementSibling;
            while (prev && (prev.dataset.parentId || '') !== parentId) {
                if (parentId && !prev.dataset.parentId) return;
                prev = prev.previousElementSibling;
            }
            if (!prev) return;
            block.forEach((node) => listEl.insertBefore(node, prev));
        } else {
            const tail = block[block.length - 1];
            let next = tail.nextElementSibling;
            if (parentId) {
                if (!next || next.dataset.parentId !== parentId) return;
                listEl.insertBefore(next, row);
            } else {
                while (next && next.dataset.parentId) {
                    next = next.nextElementSibling;
                }
                if (!next) return;
                const nextBlock = getBlock(next);
                const anchor = nextBlock[nextBlock.length - 1].nextSibling;
                block.forEach((node) => listEl.insertBefore(node, anchor));
            }
        }

        saveOrder();
    });
});
</script>
@endpush
