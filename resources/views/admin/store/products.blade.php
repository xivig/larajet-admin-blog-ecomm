@extends('layouts.admin')

@section('title', 'Store Products & Catalog - XivigAdmin PRO')

@section('content')
<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
  <div>
    <h1 class="page-title mb-1">Storefront Products &amp; Catalog</h1>
    <nav class="breadcrumb mb-0">
      <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
      <span class="breadcrumb-item">E-Commerce Store</span>
      <span class="breadcrumb-item active">Products</span>
    </nav>
  </div>
  <button type="button" class="btn btn-primary rounded-pill px-4 fw-semibold" data-bs-toggle="modal" data-bs-target="#createProductModal">
    <i class="bi bi-plus-lg me-1"></i> Add New Product
  </button>
</div>

@include('admin.store.partials.nav')

<!-- KPI Summary Strip -->
<div class="row g-3 mb-4">
  <div class="col-xl-3 col-sm-6">
    <div class="card border-0 shadow-sm rounded-4 h-100">
      <div class="card-body p-3 d-flex align-items-center gap-3">
        <div class="rounded-3 bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
          <i class="bi bi-currency-dollar fs-4"></i>
        </div>
        <div>
          <div class="text-muted small fw-semibold">Store Revenue</div>
          <div class="fs-4 fw-bold">{{ $currencySymbol }}{{ number_format($stats['total_revenue'], 2) }}</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-sm-6">
    <div class="card border-0 shadow-sm rounded-4 h-100">
      <div class="card-body p-3 d-flex align-items-center gap-3">
        <div class="rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
          <i class="bi bi-bag-check-fill fs-4"></i>
        </div>
        <div>
          <div class="text-muted small fw-semibold">Paid Orders</div>
          <div class="fs-4 fw-bold">{{ $stats['paid_orders'] }}</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-sm-6">
    <div class="card border-0 shadow-sm rounded-4 h-100">
      <div class="card-body p-3 d-flex align-items-center gap-3">
        <div class="rounded-3 bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
          <i class="bi bi-hourglass-split fs-4"></i>
        </div>
        <div>
          <div class="text-muted small fw-semibold">Pending Orders</div>
          <div class="fs-4 fw-bold">{{ $stats['pending_orders'] }}</div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-sm-6">
    <div class="card border-0 shadow-sm rounded-4 h-100">
      <div class="card-body p-3 d-flex align-items-center gap-3">
        <div class="rounded-3 bg-info-subtle text-info d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
          <i class="bi bi-box-seam fs-4"></i>
        </div>
        <div>
          <div class="text-muted small fw-semibold">Active Products</div>
          <div class="fs-4 fw-bold">{{ $stats['active_products'] }}</div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Filters & Search Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
  <div class="card-body p-3">
    <form action="{{ route('admin.store.products') }}" method="GET" class="row g-2 align-items-center">
      <div class="col-md-4">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control rounded-pill px-3" placeholder="Search title, SKU, or category...">
      </div>
      <div class="col-md-3">
        <select name="category" class="form-select rounded-pill px-3">
          <option value="">All Categories</option>
          @foreach($categories as $cat)
            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-5 d-flex flex-wrap gap-2 justify-content-md-end">
        <button type="submit" class="btn btn-primary rounded-pill px-3 fw-semibold">Filter</button>
        <a href="{{ route('admin.store.products') }}" class="btn btn-outline-secondary rounded-pill px-3">Active</a>
        <a href="{{ route('admin.store.products', ['view' => 'trash']) }}" class="btn {{ request('view') === 'trash' ? 'btn-danger' : 'btn-outline-danger' }} rounded-pill px-3">
          Trash ({{ $stats['trash_count'] }})
        </a>
      </div>
    </form>
  </div>
</div>

<!-- Products Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th class="ps-4">Product</th>
          <th>SKU &amp; Category</th>
          <th>Pricing</th>
          <th>Inventory &amp; Sales</th>
          <th>Status</th>
          <th class="text-end pe-4">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($products as $product)
          <tr>
            <td class="ps-4">
              <div class="d-flex align-items-center gap-3">
                <img src="{{ $product->resolved_image_url }}" alt="{{ $product->title }}" class="rounded-3 border flex-shrink-0" style="width: 52px; height: 42px; object-fit: cover;">
                <div>
                  <div class="fw-bold">{{ $product->title }}</div>
                  <div class="small text-muted text-truncate" style="max-width: 280px;">{{ $product->short_description }}</div>
                </div>
              </div>
            </td>
            <td>
              <div class="font-monospace small fw-semibold">{{ $product->sku }}</div>
              <span class="badge bg-primary-subtle text-primary rounded-pill">{{ $product->category }}</span>
              @if($product->is_featured)
                <span class="badge bg-warning-subtle text-warning rounded-pill">Featured</span>
              @endif
            </td>
            <td>
              @if($product->has_discount)
                <div class="fw-bold text-success">{{ $currencySymbol }}{{ number_format((float) $product->sale_price, 2) }}</div>
                <small class="text-muted text-decoration-line-through">{{ $currencySymbol }}{{ number_format((float) $product->price, 2) }}</small>
              @else
                <div class="fw-bold">{{ $currencySymbol }}{{ number_format((float) $product->price, 2) }}</div>
              @endif
            </td>
            <td>
              <div class="small fw-semibold">
                {{ $product->stock_qty === -1 ? 'Unlimited (Digital)' : $product->stock_qty . ' units' }}
              </div>
              <small class="text-muted">{{ $product->sales_count }} total sales</small>
            </td>
            <td>
              @if(request('view') !== 'trash')
                <form action="{{ route('admin.store.products.toggle', $product) }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-sm rounded-pill px-3 {{ $product->is_active ? 'btn-success-subtle text-success' : 'btn-secondary-subtle text-secondary' }}">
                    {{ $product->is_active ? 'Active' : 'Hidden' }}
                  </button>
                </form>
              @else
                <span class="badge bg-danger-subtle text-danger rounded-pill">Trashed</span>
              @endif
            </td>
            <td class="text-end pe-4">
              @if(request('view') === 'trash')
                <form action="{{ route('admin.store.products.restore', $product->id) }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3">Restore</button>
                </form>
                <form action="{{ route('admin.store.products.force-delete', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this product?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">Delete Forever</button>
                </form>
              @else
                <button type="button"
                        class="btn btn-sm btn-outline-primary rounded-pill px-3 edit-product-btn"
                        data-product="{{ json_encode([
                            'id' => $product->id,
                            'update_url' => route('admin.store.products.update', $product),
                            'title' => $product->title,
                            'slug' => $product->slug,
                            'sku' => $product->sku,
                            'category' => $product->category,
                            'badge_label' => $product->badge_label,
                            'badge_color' => $product->badge_color,
                            'price' => $product->price,
                            'sale_price' => $product->sale_price,
                            'stock_qty' => $product->stock_qty,
                            'download_url' => $product->download_url,
                            'image_url' => $product->image_url,
                            'short_description' => $product->short_description,
                            'description' => $product->description,
                            'features_text' => implode("\n", $product->features ?? []),
                            'sort_order' => $product->sort_order,
                            'is_featured' => $product->is_featured,
                            'is_active' => $product->is_active,
                            'is_digital' => $product->is_digital,
                        ]) }}">
                  <i class="bi bi-pencil-square"></i> Edit
                </button>
                <form action="{{ route('admin.store.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Move this product to trash?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2" title="Move to Trash">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center py-5 text-muted">No products found in this view.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($products->hasPages())
    <div class="card-footer bg-transparent border-top px-4 py-3">
      {{ $products->links() }}
    </div>
  @endif
</div>

<!-- Create Product Modal -->
<div class="modal fade" id="createProductModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <form action="{{ route('admin.store.products.store') }}" method="POST" enctype="multipart/form-data" class="modal-content rounded-4 border-0 shadow">
      @csrf
      <div class="modal-header border-bottom px-4 py-3">
        <h5 class="modal-title fw-bold">Add Storefront Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="row g-3">
          <div class="col-md-8">
            <label class="form-label small fw-bold">Product Title *</label>
            <input type="text" name="title" class="form-control" required placeholder="e.g. Enterprise API Rate Guard">
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">SKU Code *</label>
            <input type="text" name="sku" class="form-control font-monospace" required placeholder="XIV-NEW-007">
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">Category *</label>
            <input type="text" name="category" list="storeCategoriesDatalist" class="form-control" required value="Starters">
            <datalist id="storeCategoriesDatalist">
              <option value="Starters">
              <option value="Blueprints">
              <option value="UI Suites">
              <option value="Security">
              <option value="API & SDK">
            </datalist>
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">Corner Badge Label</label>
            <input type="text" name="badge_label" class="form-control" placeholder="e.g. Laravel 13 Starter">
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">Stock Qty (-1 = Unlimited)</label>
            <input type="number" name="stock_qty" class="form-control" value="-1" required>
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">Price ({{ $currencySymbol }}) *</label>
            <input type="number" step="0.01" min="0" name="price" class="form-control" required value="99.00">
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">Sale Price (Optional)</label>
            <input type="number" step="0.01" min="0" name="sale_price" class="form-control" placeholder="Leave blank for none">
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">Sort Order</label>
            <input type="number" name="sort_order" class="form-control" value="0">
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-bold">Image Upload (JPG/PNG/WEBP)</label>
            <input type="file" name="image_file" class="form-control" accept="image/*">
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-bold">Or Image Path / URL</label>
            <input type="text" name="image_url" class="form-control" value="xivig-public/images/portfolio/project-nexus-core.webp">
          </div>
          <div class="col-12">
            <label class="form-label small fw-bold">Private Download / Package URL (Unlocked on Paid Orders)</label>
            <input type="text" name="download_url" class="form-control" value="https://github.com/xivig/laravel-blog-admin-template/releases">
          </div>
          <div class="col-12">
            <label class="form-label small fw-bold">Short Summary</label>
            <textarea name="short_description" rows="2" class="form-control" placeholder="Concise summary displayed on product cards..."></textarea>
          </div>
          <div class="col-12">
            <label class="form-label small fw-bold">Full Technical Description</label>
            <textarea name="description" rows="3" class="form-control"></textarea>
          </div>
          <div class="col-12">
            <label class="form-label small fw-bold">Key Features (One per line)</label>
            <textarea name="features_text" rows="3" class="form-control" placeholder="Production Dockerfiles&#10;Automated Feature Tests"></textarea>
          </div>
          <div class="col-12 d-flex flex-wrap gap-4 pt-2">
            <input type="hidden" name="is_active" value="0">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="is_active" id="createActive" value="1" checked>
              <label class="form-check-label fw-semibold" for="createActive">Visible on Storefront</label>
            </div>
            <input type="hidden" name="is_featured" value="0">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="is_featured" id="createFeatured" value="1">
              <label class="form-check-label fw-semibold" for="createFeatured">Featured Item</label>
            </div>
            <input type="hidden" name="is_digital" value="0">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="is_digital" id="createDigital" value="1" checked>
              <label class="form-check-label fw-semibold" for="createDigital">Instant License Delivery</label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer border-top px-4 py-3">
        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">Save Product</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <form id="editProductForm" method="POST" enctype="multipart/form-data" class="modal-content rounded-4 border-0 shadow">
      @csrf
      @method('PUT')
      <div class="modal-header border-bottom px-4 py-3">
        <h5 class="modal-title fw-bold">Edit Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="row g-3">
          <div class="col-md-8">
            <label class="form-label small fw-bold">Product Title *</label>
            <input type="text" name="title" id="editProdTitle" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">SKU Code *</label>
            <input type="text" name="sku" id="editProdSku" class="form-control font-monospace" required>
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">Category *</label>
            <input type="text" name="category" id="editProdCategory" list="storeCategoriesDatalist" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">Corner Badge Label</label>
            <input type="text" name="badge_label" id="editProdBadge" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">Stock Qty (-1 = Unlimited)</label>
            <input type="number" name="stock_qty" id="editProdStock" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">Price *</label>
            <input type="number" step="0.01" min="0" name="price" id="editProdPrice" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">Sale Price</label>
            <input type="number" step="0.01" min="0" name="sale_price" id="editProdSalePrice" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">Sort Order</label>
            <input type="number" name="sort_order" id="editProdSort" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-bold">Upload Replacement Image</label>
            <input type="file" name="image_file" class="form-control" accept="image/*">
          </div>
          <div class="col-md-6">
            <label class="form-label small fw-bold">Or Image Path / URL</label>
            <input type="text" name="image_url" id="editProdImageUrl" class="form-control">
          </div>
          <div class="col-12">
            <label class="form-label small fw-bold">Download / Repository URL</label>
            <input type="text" name="download_url" id="editProdDownloadUrl" class="form-control">
          </div>
          <div class="col-12">
            <label class="form-label small fw-bold">Short Summary</label>
            <textarea name="short_description" id="editProdShortDesc" rows="2" class="form-control"></textarea>
          </div>
          <div class="col-12">
            <label class="form-label small fw-bold">Full Technical Description</label>
            <textarea name="description" id="editProdDesc" rows="3" class="form-control"></textarea>
          </div>
          <div class="col-12">
            <label class="form-label small fw-bold">Key Features (One per line)</label>
            <textarea name="features_text" id="editProdFeatures" rows="3" class="form-control"></textarea>
          </div>
          <div class="col-12 d-flex flex-wrap gap-4 pt-2">
            <input type="hidden" name="is_active" value="0">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="is_active" id="editProdActive" value="1">
              <label class="form-check-label fw-semibold" for="editProdActive">Visible on Storefront</label>
            </div>
            <input type="hidden" name="is_featured" value="0">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="is_featured" id="editProdFeatured" value="1">
              <label class="form-check-label fw-semibold" for="editProdFeatured">Featured Item</label>
            </div>
            <input type="hidden" name="is_digital" value="0">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="is_digital" id="editProdDigital" value="1">
              <label class="form-check-label fw-semibold" for="editProdDigital">Instant License Delivery</label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer border-top px-4 py-3">
        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">Update Product</button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var editModalEl = document.getElementById('editProductModal');
  var editForm = document.getElementById('editProductForm');
  var editModal = null;

  document.querySelectorAll('.edit-product-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var p = JSON.parse(this.getAttribute('data-product') || '{}');
      if (!p.update_url) return;
      editForm.action = p.update_url;
      document.getElementById('editProdTitle').value = p.title || '';
      document.getElementById('editProdSku').value = p.sku || '';
      document.getElementById('editProdCategory').value = p.category || '';
      document.getElementById('editProdBadge').value = p.badge_label || '';
      document.getElementById('editProdStock').value = p.stock_qty ?? -1;
      document.getElementById('editProdPrice').value = p.price || 0;
      document.getElementById('editProdSalePrice').value = p.sale_price || '';
      document.getElementById('editProdSort').value = p.sort_order ?? 0;
      document.getElementById('editProdImageUrl').value = p.image_url || '';
      document.getElementById('editProdDownloadUrl').value = p.download_url || '';
      document.getElementById('editProdShortDesc').value = p.short_description || '';
      document.getElementById('editProdDesc').value = p.description || '';
      document.getElementById('editProdFeatures').value = p.features_text || '';
      document.getElementById('editProdActive').checked = !!p.is_active;
      document.getElementById('editProdFeatured').checked = !!p.is_featured;
      document.getElementById('editProdDigital').checked = !!p.is_digital;

      if (window.bootstrap && window.bootstrap.Modal) {
        editModal = editModal || window.bootstrap.Modal.getOrCreateInstance(editModalEl);
        editModal.show();
      }
    });
  });
});
</script>
@endsection
