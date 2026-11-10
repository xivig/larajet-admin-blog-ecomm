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
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newMenuModal">
                <i class="bi bi-plus-lg me-1"></i> Create Menu
            </button>
        </div>
    </div>

    <!-- Menu Selector Toolbar -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="fw-semibold small text-muted text-uppercase">Select Menu:</span>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle fw-bold" type="button" data-bs-toggle="dropdown">
                        {{ $currentMenu ? $currentMenu->name : 'Choose Menu' }}
                    </button>
                    <ul class="dropdown-menu shadow">
                        @foreach ($menus as $m)
                            <li>
                                <a class="dropdown-item d-flex justify-content-between align-items-center {{ $currentMenu && $currentMenu->id === $m->id ? 'active' : '' }}" href="{{ route('admin.menu.index', ['menu' => $m->id]) }}">
                                    <span>{{ $m->name }}</span>
                                    <small class="badge bg-light text-dark ms-2">{{ $m->location }}</small>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            @if($currentMenu)
                <div class="d-flex align-items-center gap-2">
                    <span class="small text-muted">Location: <code>{{ $currentMenu->location }}</code></span>
                    <form action="{{ route('admin.menu.destroy', $currentMenu->id) }}" method="POST" onsubmit="return confirm('Delete this menu?')">
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
                                    <input type="text" name="url" class="form-control form-control-sm" placeholder="https:// or /path" required>
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
                                        @foreach($currentMenu->items as $parentOpt)
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
                <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0 fw-bold">Menu Structure ({{ $currentMenu->name }})</h5>
                        <small class="text-muted">Drag or configure items in your navigation bar</small>
                    </div>
                    <span class="badge bg-primary-subtle text-primary">{{ $currentMenu->items->count() }} Items</span>
                </div>
                <div class="card-body">
                    @if($currentMenu->items->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-list-nested fs-1 d-block mb-2 text-secondary"></i>
                            This menu has no navigation links yet. Use the left panel to add custom links, pages, or categories.
                        </div>
                    @else
                        <div class="list-group" id="menuItemsList">
                            @foreach ($currentMenu->items as $item)
                                <div class="list-group-item d-flex flex-wrap justify-content-between align-items-center py-3 {{ $item->parent_id ? 'ms-4 border-start border-primary border-3 bg-light' : '' }}">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="bi bi-grip-vertical text-muted fs-5 cursor-grab"></i>
                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="fw-bold">{{ $item->title }}</span>
                                                @if($item->parent_id)
                                                    <span class="badge bg-primary-subtle text-primary rounded-pill small" style="font-size: 10px;">Sub-link</span>
                                                @endif
                                            </div>
                                            <small class="text-muted">
                                                <code>{{ $item->url }}</code> &bull; <span class="badge bg-light text-dark border">{{ $item->target }}</span>
                                            </small>
                                        </div>
                                    </div>

                                    <form action="{{ route('admin.menu.items.delete', $item->id) }}" method="POST" onsubmit="return confirm('Remove link?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove Item">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
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
                            <input type="text" name="name" id="menuName" class="form-control" placeholder="e.g. Footer Quick Links" required>
                        </div>
                        <div class="mb-3">
                            <label for="menuLocation" class="form-label fw-semibold">Display Location</label>
                            <select name="location" id="menuLocation" class="form-select">
                                <option value="header">Main Header</option>
                                <option value="footer">Footer Columns</option>
                                <option value="sidebar">Sidebar Widget</option>
                                <option value="mobile">Mobile Drawer</option>
                            </select>
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
