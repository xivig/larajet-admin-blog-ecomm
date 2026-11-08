@extends('layouts.admin')

@section('title', 'Image Cropper Studio - Xivig Admin')

@section('content')
<!-- Cropper.js Styles -->
<link rel="stylesheet" href="{{ asset('admin-dist/vendor/cropperjs/cropper.min.css') }}">

<div class="page-image-cropper">
    <!-- Header Hero Section -->
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h1 class="page-title h3 mb-0">Image Cropper Studio</h1>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill small fw-bold px-2.5 py-1">Cropper.js v1.6.2</span>
            </div>
            <nav class="breadcrumb mb-0">
                <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item text-decoration-none">Dashboard</a>
                <a href="{{ route('admin.media.index') }}" class="breadcrumb-item text-decoration-none">Media Library</a>
                <span class="breadcrumb-item active">Image Cropper</span>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-2">
            <label class="btn btn-outline-primary btn-sm rounded-pill px-3.5 mb-0 cursor-pointer" for="inputImage">
                <i class="bi bi-upload me-1.5"></i> Upload from PC
            </label>
            <input type="file" class="d-none" id="inputImage" name="file" accept="image/*">

            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#mediaPickerModal">
                <i class="bi bi-images me-1.5"></i> Choose from Library
            </button>

            <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm" data-method="getCroppedCanvas">
                <i class="bi bi-scissors me-1.5"></i> Render &amp; Export
            </button>
        </div>
    </div>

    <!-- Active Media Notice -->
    @if($selectedMedia)
        <div class="alert alert-info alert-dismissible fade show d-flex align-items-center justify-content-between py-2 px-3 mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-info-circle-fill"></i>
                <span>Editing Media Asset: <strong>{{ $selectedMedia->name }}</strong> (ID #{{ $selectedMedia->id }}, {{ $selectedMedia->dimensions ?: 'original' }})</span>
            </div>
            <a href="{{ route('admin.image-cropper.index') }}" class="small text-decoration-none fw-semibold">Reset to Default</a>
        </div>
    @endif

    <div class="row g-4">
        <!-- Main Workspace (8 columns) -->
        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 mb-4">
                <!-- Image Viewport Canvas -->
                <div class="rounded-3 overflow-hidden border bg-light-subtle mb-3 position-relative d-flex align-items-center justify-content-center" 
                     style="min-height: 440px; max-height: 540px; background-image: radial-gradient(rgba(0,0,0,0.12) 1px, transparent 1px); background-size: 20px 20px;">
                    <img id="image" src="{{ $defaultImageUrl }}" alt="Crop Target" class="img-fluid" style="max-height: 520px; max-width: 100%;">
                </div>

                <!-- Cropper Toolbar Controls -->
                <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center justify-content-between pt-3 border-top" id="actions">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <!-- Mode Group -->
                        <div class="cropper-toolbar-group shadow-sm">
                            <button type="button" class="btn" data-method="setDragMode" data-option="move" title="Move Canvas">
                                <i class="bi bi-arrows-move"></i>
                            </button>
                            <button type="button" class="btn active" data-method="setDragMode" data-option="crop" title="Crop Selection Box">
                                <i class="bi bi-crop"></i>
                            </button>
                        </div>

                        <!-- Zoom Controls -->
                        <div class="cropper-toolbar-group shadow-sm">
                            <button type="button" class="btn" data-method="zoom" data-option="0.1" title="Zoom In">
                                <i class="bi bi-zoom-in"></i>
                            </button>
                            <button type="button" class="btn" data-method="zoom" data-option="-0.1" title="Zoom Out">
                                <i class="bi bi-zoom-out"></i>
                            </button>
                        </div>

                        <!-- Rotation & Flips -->
                        <div class="cropper-toolbar-group shadow-sm">
                            <button type="button" class="btn" data-method="rotate" data-option="-45" title="Rotate -45°">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                            <button type="button" class="btn" data-method="rotate" data-option="45" title="Rotate +45°">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                            <button type="button" class="btn" data-method="scaleX" data-option="-1" title="Flip Horizontal">
                                <i class="bi bi-arrow-left-right"></i>
                            </button>
                            <button type="button" class="btn" data-method="scaleY" data-option="-1" title="Flip Vertical">
                                <i class="bi bi-arrow-down-up"></i>
                            </button>
                            <button type="button" class="btn" data-method="reset" title="Reset Frame">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                        </div>

                        <!-- Aspect Ratio Presets -->
                        <div class="cropper-toolbar-group shadow-sm" id="aspectRatioGroup">
                            <button type="button" class="btn active fw-semibold" data-method="setAspectRatio" data-option="1.7777777777777777" title="16:9 Banner">16:9</button>
                            <button type="button" class="btn fw-semibold" data-method="setAspectRatio" data-option="1.3333333333333333" title="4:3 Card">4:3</button>
                            <button type="button" class="btn fw-semibold" data-method="setAspectRatio" data-option="1" title="1:1 Square">1:1</button>
                            <button type="button" class="btn fw-semibold" data-method="setAspectRatio" data-option="0.6666666666666666" title="2:3 Portrait">2:3</button>
                            <button type="button" class="btn fw-semibold" data-method="setAspectRatio" data-option="NaN" title="Free Form">Free</button>
                        </div>
                    </div>

                    <!-- Direct Action Button -->
                    <div>
                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3.5 py-1.5 shadow-sm fw-bold d-inline-flex align-items-center" data-method="getCroppedCanvas">
                            <i class="bi bi-scissors me-1.5"></i> Render Canvas
                        </button>
                    </div>
                </div>
            </div>

            <!-- Recent Media Switcher Bar -->
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0 small text-uppercase text-secondary">
                        <i class="bi bi-clock-history me-1"></i> Quick Switch from Recent Library Assets
                    </h6>
                    <a href="{{ route('admin.media.index') }}" class="small text-decoration-none">Open Media Library &rarr;</a>
                </div>
                <div class="d-flex gap-2 overflow-auto py-1">
                    @foreach($recentAssets as $rec)
                        <div class="flex-shrink-0 cursor-pointer rounded border p-1 bg-light-subtle hover-shadow" 
                             style="width: 80px; height: 60px; cursor: pointer;"
                             onclick="switchCropperImage('{{ $rec->url }}', '{{ addslashes($rec->name) }}')"
                             title="{{ $rec->name }}">
                            <img src="{{ $rec->url }}" alt="{{ $rec->name }}" class="w-100 h-100 object-fit-cover rounded" onerror="this.src='{{ asset('admin-dist/img/blog-1-PTDVKJDt.webp') }}'">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Sidebar (4 columns) -->
        <div class="col-xl-4 col-lg-5">
            <!-- Responsive Previews Pod -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 text-center">
                <h6 class="fw-bold mb-3 small text-uppercase text-secondary">Live Responsive Previews</h6>
                <div class="d-flex flex-column align-items-center gap-3">
                    <!-- 16:9 Banner Preview -->
                    <div>
                        <div class="img-preview rounded-3 overflow-hidden border shadow-sm mx-auto" style="width: 240px; height: 135px;"></div>
                        <span class="text-muted small d-block mt-1">16:9 Blog Banner Preview</span>
                    </div>

                    <div class="d-flex gap-3 align-items-center justify-content-center">
                        <!-- Square Card Preview -->
                        <div>
                            <div class="img-preview rounded-3 overflow-hidden border shadow-sm" style="width: 90px; height: 90px;"></div>
                            <span class="text-muted small d-block mt-1">1:1 Square</span>
                        </div>

                        <!-- Circular Avatar Preview -->
                        <div>
                            <div class="img-preview rounded-circle overflow-hidden border border-2 border-primary shadow-sm" style="width: 80px; height: 80px;"></div>
                            <span class="text-muted small d-block mt-1">Avatar Circle</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coordinate Telemetry Matrix -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h6 class="fw-bold mb-3 small text-uppercase text-secondary">
                    <i class="bi bi-cpu me-1"></i> Boundary Telemetry
                </h6>
                <div class="row g-2 font-monospace small">
                    <div class="col-6">
                        <label class="form-label text-muted mb-0 small">X (px)</label>
                        <input type="text" class="form-control form-control-sm text-center" id="dataX" readonly value="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted mb-0 small">Y (px)</label>
                        <input type="text" class="form-control form-control-sm text-center" id="dataY" readonly value="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted mb-0 small">Width (px)</label>
                        <input type="text" class="form-control form-control-sm text-center" id="dataWidth" readonly value="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label text-muted mb-0 small">Height (px)</label>
                        <input type="text" class="form-control form-control-sm text-center" id="dataHeight" readonly value="0">
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted mb-0 small">Rotation Angle</label>
                        <input type="text" class="form-control form-control-sm text-center" id="dataRotate" readonly value="0°">
                    </div>
                </div>
            </div>

            <!-- Export & Storage Actions Card -->
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
                <h6 class="fw-bold mb-3 small text-uppercase text-secondary">Rendered Canvas &amp; Export</h6>
                
                <div id="croppedResult" class="rounded-3 border p-2 bg-light-subtle mb-3 d-flex align-items-center justify-content-center overflow-hidden" style="min-height: 140px;">
                    <span class="text-muted small">Click "Render Canvas" to generate export</span>
                </div>

                <div class="d-grid gap-2">
                    <a href="javascript:;" id="download" download="xivig-cropped.jpg" class="btn btn-outline-success btn-sm rounded-pill fw-semibold py-2">
                        <i class="bi bi-download me-1.5"></i> Download Cropped Image
                    </a>

                    <button type="button" class="btn btn-primary btn-sm rounded-pill fw-semibold py-2 shadow-sm" id="btnSaveToLibrary">
                        <i class="bi bi-cloud-arrow-up me-1.5"></i> Save Directly to Media Library
                    </button>
                </div>

                <div id="saveLibraryStatus" class="small mt-2" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Cropped Result Modal -->
<div class="modal fade" id="getCroppedCanvasModal" tabindex="-1" aria-labelledby="getCroppedCanvasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header border-0 bg-light p-3 px-4">
                <h5 class="modal-title fw-bold" id="getCroppedCanvasModalLabel">
                    <i class="bi bi-scissors me-2 text-primary"></i> Cropped Export Preview
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center bg-white" id="modalCroppedResult">
                <!-- Rendered Canvas will be cloned here -->
            </div>
            <div class="modal-footer border-0 bg-light p-3 px-4 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Close</button>
                <div class="d-flex gap-2">
                    <a class="btn btn-outline-success btn-sm rounded-pill px-4 fw-semibold" id="modalDownload" href="javascript:void(0);" download="xivig-cropped.jpg">
                        <i class="bi bi-download me-1"></i> Download
                    </a>
                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold" id="btnModalSaveToLibrary">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Save to Library
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Media Picker Modal Component -->
@include('admin.partials.media-picker-modal')

<!-- Cropper.js Script -->
<script src="{{ asset('admin-dist/vendor/cropperjs/cropper.min.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const image = document.getElementById('image');
    if (!image) return;

    const dataX = document.getElementById('dataX');
    const dataY = document.getElementById('dataY');
    const dataWidth = document.getElementById('dataWidth');
    const dataHeight = document.getElementById('dataHeight');
    const dataRotate = document.getElementById('dataRotate');
    const resultContainer = document.getElementById('croppedResult');
    const modalResultContainer = document.getElementById('modalCroppedResult');
    const downloadBtn = document.getElementById('download');
    const modalDownloadBtn = document.getElementById('modalDownload');
    const btnSaveToLibrary = document.getElementById('btnSaveToLibrary');
    const btnModalSaveToLibrary = document.getElementById('btnModalSaveToLibrary');
    const saveLibraryStatus = document.getElementById('saveLibraryStatus');

    let cropper = null;

    const cropperOptions = {
        aspectRatio: 16 / 9,
        preview: '.img-preview',
        viewMode: 2,
        dragMode: 'crop',
        autoCropArea: 0.85,
        restore: false,
        guides: true,
        center: true,
        highlight: false,
        cropBoxMovable: true,
        cropBoxResizable: true,
        toggleDragModeOnDblclick: true,
        crop(event) {
            if (dataX) dataX.value = Math.round(event.detail.x);
            if (dataY) dataY.value = Math.round(event.detail.y);
            if (dataWidth) dataWidth.value = Math.round(event.detail.width);
            if (dataHeight) dataHeight.value = Math.round(event.detail.height);
            if (dataRotate) dataRotate.value = Math.round(event.detail.rotate || 0) + '°';
        },
    };

    function initCropper() {
        if (cropper) {
            cropper.destroy();
        }
        cropper = new Cropper(image, cropperOptions);
    }

    if (image.complete && image.naturalWidth > 0) {
        initCropper();
    } else {
        image.addEventListener('load', initCropper, { once: true });
    }

    // Switch image helper
    window.switchCropperImage = function(url, title = 'Selected Image') {
        if (!url || !cropper) return;
        cropper.replace(url);
    };

    // Override picker modal selection to load image directly into cropper
    window.selectPickerAsset = function(id, url, name) {
        switchCropperImage(url, name);
        const modalEl = document.getElementById('mediaPickerModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        }
    };

    // Global action dispatcher for all [data-method] buttons
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('[data-method]');
        if (!btn || !cropper) return;

        const method = btn.getAttribute('data-method');
        let option = btn.getAttribute('data-option');
        let secondOption = btn.getAttribute('data-second-option');

        if (option && !isNaN(option)) {
            option = parseFloat(option);
        } else if (option === 'NaN') {
            option = NaN;
        }

        if (secondOption && !isNaN(secondOption)) {
            secondOption = parseFloat(secondOption);
        }

        switch (method) {
            case 'getCroppedCanvas':
                const canvas = cropper.getCroppedCanvas({
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                });
                if (canvas) {
                    canvas.style.maxWidth = '100%';
                    canvas.style.height = 'auto';
                    canvas.style.borderRadius = '8px';

                    // Update side widget
                    if (resultContainer) {
                        resultContainer.innerHTML = '';
                        resultContainer.appendChild(canvas);
                    }

                    const dataUrl = canvas.toDataURL('image/jpeg', 0.92);

                    if (downloadBtn) {
                        downloadBtn.href = dataUrl;
                    }

                    // Populate and open modal
                    if (modalResultContainer) {
                        modalResultContainer.innerHTML = '';
                        const modalCanvas = document.createElement('img');
                        modalCanvas.src = dataUrl;
                        modalCanvas.className = 'img-fluid rounded shadow-sm';
                        modalCanvas.style.maxHeight = '420px';
                        modalResultContainer.appendChild(modalCanvas);
                    }

                    if (modalDownloadBtn) {
                        modalDownloadBtn.href = dataUrl;
                    }

                    const modalEl = document.getElementById('getCroppedCanvasModal');
                    if (modalEl && window.bootstrap && window.bootstrap.Modal) {
                        const modal = window.bootstrap.Modal.getOrCreateInstance(modalEl);
                        modal.show();
                    }
                }
                break;

            case 'scaleX':
                cropper.scaleX(option || -1);
                btn.setAttribute('data-option', -(option || 1));
                break;

            case 'scaleY':
                cropper.scaleY(option || -1);
                btn.setAttribute('data-option', -(option || 1));
                break;

            case 'move':
                cropper.move(option || 0, secondOption || 0);
                break;

            case 'setAspectRatio':
                cropper.setAspectRatio(option);
                const ratioGroup = btn.closest('#aspectRatioGroup, .cropper-toolbar-group');
                if (ratioGroup) {
                    ratioGroup.querySelectorAll('[data-method="setAspectRatio"]').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                }
                break;

            default:
                if (typeof cropper[method] === 'function') {
                    cropper[method](option);
                }
        }
    });

    // Local file upload input handler
    const inputImage = document.getElementById('inputImage');
    if (inputImage) {
        const URL = window.URL || window.webkitURL;
        inputImage.addEventListener('change', function () {
            const files = this.files;
            if (files && files.length && cropper) {
                const file = files[0];
                if (/^image\/\w+$/.test(file.type)) {
                    const uploadedImageURL = URL.createObjectURL(file);
                    cropper.replace(uploadedImageURL);
                    inputImage.value = null;
                } else {
                    alert('Please select a valid image file.');
                }
            }
        });
    }

    // Save directly to Media Library handler
    function saveCroppedToMediaLibrary(triggerBtn) {
        if (!cropper) return;
        const canvas = cropper.getCroppedCanvas({
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });
        if (!canvas) {
            alert('Failed to generate cropped canvas.');
            return;
        }

        const dataUrl = canvas.toDataURL('image/jpeg', 0.92);
        const originalText = triggerBtn.innerHTML;
        triggerBtn.disabled = true;
        triggerBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        fetch('{{ route("admin.image-cropper.save") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                image_data: dataUrl,
                name: 'Cropped Asset ' + new Date().toISOString().slice(0,10),
                folder: 'cropped'
            })
        })
        .then(res => res.json())
        .then(data => {
            triggerBtn.disabled = false;
            triggerBtn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Saved!';
            if (saveLibraryStatus) {
                saveLibraryStatus.style.display = 'block';
                saveLibraryStatus.className = 'small mt-2 text-success fw-semibold';
                saveLibraryStatus.innerHTML = '<i class="bi bi-check-circle me-1"></i> ' + data.message + ' <a href="{{ route("admin.media.index") }}" class="text-decoration-underline ms-1">View in Library</a>';
            }
            alert('Asset successfully saved into Media Library!');
            setTimeout(() => {
                triggerBtn.innerHTML = originalText;
            }, 3000);
        })
        .catch(err => {
            triggerBtn.disabled = false;
            triggerBtn.innerHTML = originalText;
            alert('Failed to save cropped image to Media Library.');
        });
    }

    if (btnSaveToLibrary) {
        btnSaveToLibrary.addEventListener('click', function() {
            saveCroppedToMediaLibrary(this);
        });
    }

    if (btnModalSaveToLibrary) {
        btnModalSaveToLibrary.addEventListener('click', function() {
            saveCroppedToMediaLibrary(this);
        });
    }
});
</script>

<style>
/* Cropper Toolbar styling from xivig.github.io */
.cropper-toolbar-group {
    border: 1px solid rgba(0, 0, 0, 0.12);
    border-radius: 50rem !important;
    overflow: hidden;
    display: inline-flex;
    background: #ffffff;
}

.cropper-toolbar-group .btn {
    border: none !important;
    border-right: 1px solid rgba(0, 0, 0, 0.08) !important;
    border-radius: 0 !important;
    background: transparent;
    padding: 0.45rem 0.85rem;
    font-size: 0.8125rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s ease;
    color: #212529;
}

.cropper-toolbar-group .btn:last-child {
    border-right: none !important;
}

.cropper-toolbar-group .btn:hover {
    background: rgba(0, 0, 0, 0.05);
    color: var(--bs-primary);
}

.cropper-toolbar-group .btn.active {
    background: var(--bs-primary) !important;
    color: #ffffff !important;
}

[data-bs-theme="dark"] .cropper-toolbar-group {
    border-color: rgba(255, 255, 255, 0.12);
    background: rgba(255, 255, 255, 0.04);
}

[data-bs-theme="dark"] .cropper-toolbar-group .btn {
    border-right-color: rgba(255, 255, 255, 0.12) !important;
    color: #F2F5FA;
}

[data-bs-theme="dark"] .cropper-toolbar-group .btn:hover {
    background: rgba(255, 255, 255, 0.08);
    color: #ffffff;
}

[data-bs-theme="dark"] .cropper-toolbar-group .btn.active {
    background: var(--bs-primary) !important;
    color: #ffffff !important;
}

.img-preview {
    overflow: hidden;
}

#croppedResult canvas {
    max-width: 100% !important;
    height: auto !important;
    border-radius: 8px;
}

.hover-shadow:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    border-color: var(--bs-primary) !important;
}
</style>
@endsection
