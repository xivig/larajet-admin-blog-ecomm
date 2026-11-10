<!-- Media Picker Modal Component -->
<div class="modal fade" id="mediaPickerModal" tabindex="-1" aria-labelledby="mediaPickerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-images text-primary fs-5"></i>
                    <h5 class="modal-title fw-bold" id="mediaPickerModalLabel">Choose from Media Library</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <!-- Search & Filters -->
                <div class="d-flex justify-content-between align-items-center mb-3 gap-2">
                    <div class="input-group input-group-sm" style="max-width: 350px;">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="pickerSearchInput" class="form-control border-start-0" placeholder="Filter media by name...">
                    </div>
                    <span class="small text-muted" id="pickerCountText">Loading assets...</span>
                </div>

                <!-- Grid of Library Assets -->
                <div class="row g-2" id="pickerGridContainer" style="max-height: 480px; overflow-y: auto;">
                    <!-- Dynamically populated via AJAX or initial render -->
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalEl = document.getElementById('mediaPickerModal');
    if (!modalEl) return;

    let pickerAssets = [];

    modalEl.addEventListener('show.bs.modal', function() {
        loadPickerAssets();
    });

    function loadPickerAssets(query = '') {
        const grid = document.getElementById('pickerGridContainer');
        const countText = document.getElementById('pickerCountText');
        grid.innerHTML = '<div class="col-12 text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Loading media catalog...</div>';

        fetch('{{ route("admin.media.picker") }}?q=' + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                if (data.success && data.items) {
                    pickerAssets = data.items;
                    countText.innerText = data.items.length + ' asset(s) found';
                    renderPickerGrid(data.items);
                } else {
                    grid.innerHTML = '<div class="col-12 text-center py-4 text-muted">No media items found.</div>';
                }
            })
            .catch(err => {
                console.error(err);
                grid.innerHTML = '<div class="col-12 text-center py-4 text-danger">Failed to load media assets.</div>';
            });
    }

    function renderPickerGrid(items) {
        const grid = document.getElementById('pickerGridContainer');
        if (items.length === 0) {
            grid.innerHTML = '<div class="col-12 text-center py-4 text-muted">No media matching your filter.</div>';
            return;
        }

        grid.innerHTML = items.map(item => `
            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
                <div class="card h-100 border p-1 picker-card cursor-pointer" onclick="selectPickerAsset(${item.id}, '${item.url}', '${item.name.replace(/'/g, "\\'")}')" style="cursor: pointer; transition: all 0.2s;">
                    <div style="height: 115px; overflow: hidden; background: #0000000a; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                        <img src="${item.url}" alt="${item.alt_text || ''}" class="w-100 h-100 object-fit-cover" onerror="this.onerror=null; this.src='{{ asset('xivig-admin/img/blog-1-PTDVKJDt.webp') }}'">
                    </div>
                    <div class="p-1.5 text-truncate">
                        <small class="fw-semibold text-truncate d-block" style="font-size: 0.75rem;">${item.name || 'Image #' + item.id}</small>
                        <span class="text-muted" style="font-size: 0.68rem;">${item.dimensions || item.formatted_size || ''}</span>
                    </div>
                </div>
            </div>
        `).join('');
    }

    const searchInput = document.getElementById('pickerSearchInput');
    if (searchInput) {
        let debounceTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                loadPickerAssets(searchInput.value);
            }, 300);
        });
    }

    window.selectPickerAsset = function(id, url, name) {
        // Set image_id in select
        const select = document.querySelector('select[name="image_id"]');
        if (select) {
            let option = select.querySelector(`option[value="${id}"]`);
            if (!option) {
                option = document.createElement('option');
                option.value = id;
                option.text = name + ' (' + id + ')';
                select.appendChild(option);
            }
            select.value = id;
        }

        // Update preview image
        const previewBox = document.getElementById('imagePreviewBox');
        const previewImg = document.getElementById('imagePreview') || document.getElementById('currentCoverImg');
        if (previewImg) {
            previewImg.src = url;
        }
        if (previewBox) {
            previewBox.style.display = 'block';
        }

        // Close modal
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) {
            modal.hide();
        }
    };
});
</script>

<style>
.picker-card:hover {
    border-color: var(--bs-primary) !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>
