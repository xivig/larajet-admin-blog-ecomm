@extends('layouts.admin')

@section('title', 'Modular Widgets Management - XivigAdmin PRO')

@section('content')
<div class="page-widgets-management">

  <!-- Header -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">Modular Widgets &amp; Sidebars</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <span class="breadcrumb-item active">Widgets</span>
      </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
      <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm" style="background-color: #5551ff; border-color: #5551ff;" data-bs-toggle="modal" data-bs-target="#addWidgetModal">
        <i class="bi bi-plus-lg fs-5"></i>
        <span>Add Widget</span>
      </button>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <!-- Placement Area Selector Pills -->
  <div class="d-flex flex-wrap gap-2 mb-4 p-2 bg-white rounded-4 shadow-sm border">
    @foreach($allAreas as $key => $areaLabel)
      <a href="{{ route('admin.widgets.index', ['area' => $key]) }}" class="btn rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2 {{ $selectedArea === $key ? 'btn-primary text-white shadow-sm' : 'btn-light text-secondary' }}" style="{{ $selectedArea === $key ? 'background: #5551ff; border-color: #5551ff;' : '' }}">
        @if($key === 'sidebar')
          <i class="bi bi-layout-sidebar-inset"></i>
        @elseif(str_starts_with($key, 'footer'))
          <i class="bi bi-layout-three-columns"></i>
        @else
          <i class="bi bi-window-dock"></i>
        @endif
        <span>{{ $areaLabel }}</span>
        <span class="badge {{ $selectedArea === $key ? 'bg-white text-dark' : 'bg-secondary-subtle text-secondary' }} rounded-pill ms-1">
          {{ \App\Models\Widget::where('area', $key)->count() }}
        </span>
      </a>
    @endforeach
  </div>

  <!-- Active Widgets List in Current Area -->
  <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
      <div>
        <h5 class="fw-bold mb-0">{{ $allAreas[$selectedArea] ?? 'Area' }} Widgets</h5>
        <small class="text-muted">Widgets render sequentially according to display order</small>
      </div>
      <span class="badge bg-light text-dark border px-3 py-1.5 rounded-pill fw-semibold">
        {{ $widgets->count() }} active in this slot
      </span>
    </div>

    <div class="p-4">
      <div class="row g-3" id="widgetsSortableList">
        @forelse($widgets as $widget)
          @php
            $typeMeta = $widgetTypes[$widget->widget_type] ?? ['label' => ucfirst($widget->widget_type), 'icon' => 'fas fa-puzzle-piece'];
          @endphp
          <div class="col-12" id="widget-card-{{ $widget->id }}">
            <div class="card border rounded-4 shadow-none p-3 d-flex flex-md-row justify-content-between align-items-md-center gap-3 {{ $widget->status ? 'bg-white' : 'bg-light opacity-75' }}">
              <div class="d-flex align-items-center gap-3">
                <div class="p-3 rounded-3 text-white d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #4f46e5, #06b6d4); width: 48px; height: 48px;">
                  <i class="{{ $typeMeta['icon'] }} fs-5"></i>
                </div>
                <div>
                  <div class="d-flex align-items-center gap-2 mb-1">
                    <h6 class="fw-bold mb-0 text-dark">{{ $widget->title }}</h6>
                    <span class="badge bg-primary-subtle text-primary px-2.5 py-1 rounded-pill small font-weight-normal">
                      {{ $typeMeta['label'] }}
                    </span>
                    @if($widget->status)
                      <span class="badge bg-success-subtle text-success rounded-pill small">Visible</span>
                    @else
                      <span class="badge bg-secondary-subtle text-secondary rounded-pill small">Hidden</span>
                    @endif
                  </div>
                  <small class="text-muted">
                    Order: <strong class="text-dark">#{{ $widget->order }}</strong>
                    @if(!empty($widget->settings['limit']))
                      · Limit: {{ $widget->settings['limit'] }} items
                    @endif
                    @if(!empty($widget->content))
                      · {{ Str::limit(strip_tags($widget->content), 60) }}
                    @endif
                  </small>
                </div>
              </div>

              <div class="d-flex align-items-center gap-2">
                {{-- Toggle Status --}}
                <form action="{{ route('admin.widgets.toggle', $widget->id) }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-sm {{ $widget->status ? 'btn-outline-secondary' : 'btn-outline-success' }} rounded-pill px-3" title="Toggle visibility">
                    {{ $widget->status ? 'Hide' : 'Show' }}
                  </button>
                </form>

                {{-- Delete --}}
                <form action="{{ route('admin.widgets.destroy', $widget->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove widget \'{{ $widget->title }}\'?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12 text-center py-5">
            <i class="bi bi-layout-sidebar fs-1 text-muted opacity-50 d-block mb-2"></i>
            <h6 class="fw-bold text-muted">No widgets configured in {{ $allAreas[$selectedArea] }}.</h6>
            <p class="text-muted small">Click "Add Widget" to place interactive modules in this slot.</p>
          </div>
        @endforelse
      </div>
    </div>
  </div>

</div>

<!-- Modal: Add Widget -->
<div class="modal fade" id="addWidgetModal" tabindex="-1" aria-labelledby="addWidgetModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <form action="{{ route('admin.widgets.store') }}" method="POST">
        @csrf
        <div class="modal-header border-bottom py-3 px-4">
          <h5 class="modal-title fw-bold" id="addWidgetModalLabel">Add Modular Widget</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold">Placement Area *</label>
              <select name="area" class="form-select rounded-3" required>
                @foreach($allAreas as $aKey => $aTitle)
                  <option value="{{ $aKey }}" {{ $selectedArea === $aKey ? 'selected' : '' }}>{{ $aTitle }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold">Widget Type *</label>
              <select name="widget_type" id="widgetTypeSelect" class="form-select rounded-3" required onchange="toggleWidgetFields(this.value)">
                @foreach($widgetTypes as $wKey => $wMeta)
                  <option value="{{ $wKey }}">{{ $wMeta['label'] }} - {{ $wMeta['desc'] }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-bold">Display Title *</label>
            <input type="text" name="title" class="form-control rounded-3" placeholder="e.g. Popular Articles or Stay Informed" required>
          </div>

          <div class="row g-3 mb-3" id="limitFieldGroup">
            <div class="col-md-6">
              <label class="form-label small fw-bold">Items Count Limit</label>
              <input type="number" name="limit" value="4" min="1" max="50" class="form-control rounded-3">
              <small class="text-muted">Maximum items rendered in list</small>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold">Initial State</label>
              <div class="form-check form-switch mt-2">
                <input class="form-check-input" type="checkbox" name="status" value="1" id="widgetStatusCheck" checked>
                <label class="form-check-label small fw-semibold" for="widgetStatusCheck">Enabled &amp; Visible</label>
              </div>
            </div>
          </div>

          <div class="mb-3" id="contentFieldGroup">
            <label class="form-label small fw-bold">Widget Description / Lead Text</label>
            <textarea name="content" rows="3" class="form-control rounded-3" placeholder="Optional descriptive paragraph shown inside the widget..."></textarea>
          </div>

          <div class="mb-3 d-none" id="adCodeFieldGroup">
            <label class="form-label small fw-bold">HTML / Banner Embed Code</label>
            <textarea name="ad_code" rows="4" class="form-control rounded-3 font-monospace small" placeholder="<a href='...'><img src='...' /></a> or Google AdSense snippet"></textarea>
          </div>
        </div>
        <div class="modal-footer border-top py-3 px-4">
          <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold" style="background-color: #5551ff; border-color: #5551ff;">Create Widget</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function toggleWidgetFields(type) {
  var adGroup = document.getElementById('adCodeFieldGroup');
  var limitGroup = document.getElementById('limitFieldGroup');
  if (type === 'ads' || type === 'custom_html') {
    adGroup.classList.remove('d-none');
  } else {
    adGroup.classList.add('d-none');
  }
}
</script>
@endsection
