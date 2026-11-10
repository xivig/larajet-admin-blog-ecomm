@extends('layouts.admin')

@section('title', 'Image Cropper Studio - Xivig Admin')

@push('styles')
<!-- Cropper.js CSS (Local + CDN Fallback) -->
<link rel="stylesheet" href="{{ asset('xivig-admin/vendor/cropperjs/cropper.min.css') }}?v={{ file_exists(public_path('xivig-admin/vendor/cropperjs/cropper.min.css')) ? filemtime(public_path('xivig-admin/vendor/cropperjs/cropper.min.css')) : time() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
@endpush

@section('content')
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
                <i class="bi bi-upload me-1.5"></i> Upload Image
            </label>
            <input type="file" class="d-none" id="inputImage" name="file" accept="image/*">

            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#mediaPickerModal">
                <i class="bi bi-images me-1.5"></i> Select from Library
            </button>

            <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm" data-method="getCroppedCanvas">
                <i class="bi bi-crop me-1.5"></i> Crop &amp; Preview
            </button>
        </div>
    </div>

    <!-- Active Media Notice -->
    @if(!empty($selectedMedia))
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
                <div class="img-container rounded-3 overflow-hidden border bg-light-subtle mb-3 position-relative" 
                     style="height: 480px; max-height: 520px; background-image: radial-gradient(rgba(0,0,0,0.12) 1px, transparent 1px); background-size: 20px 20px;">
                    <img id="image" src="{{ $defaultImageUrl ?? asset('xivig-admin/img/cropper-sample.jpg') }}" alt="Crop Target" style="max-width: 100%;">
                </div>

                <!-- Cropper Toolbar Controls -->
                <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center justify-content-between pt-3 border-top" id="actions">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <!-- Mode Group -->
                        <div class="cropper-toolbar-group shadow-sm" id="dragModeGroup">
                            <button type="button" class="btn active" data-method="setDragMode" data-option="crop" title="Crop Mode: Draw or Resize Crop Selection Box">
                                <i class="bi bi-crop me-1"></i> Crop
                            </button>
                            <button type="button" class="btn" data-method="setDragMode" data-option="move" title="Pan Mode: Move Canvas">
                                <i class="bi bi-arrows-move me-1"></i> Move
                            </button>
                        </div>

                        <!-- Selection Quick Controls -->
                        <div class="cropper-toolbar-group shadow-sm">
                            <button type="button" class="btn" data-method="crop" title="Show / Restore Selection Box">
                                <i class="bi bi-check2-square"></i>
                            </button>
                            <button type="button" class="btn" data-method="clear" title="Clear Selection Box">
                                <i class="bi bi-x-square"></i>
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
                    @foreach($recentAssets ?? [] as $rec)
                        <div class="flex-shrink-0 cursor-pointer rounded border p-1 bg-light-subtle hover-shadow" 
                             style="width: 80px; height: 60px; cursor: pointer;"
                             onclick="switchCropperImage('{{ $rec->url }}', '{{ addslashes($rec->name) }}')"
                             title="{{ $rec->name }}">
                            <img src="{{ $rec->url }}" alt="{{ $rec->name }}" class="w-100 h-100 object-fit-cover rounded" onerror="this.onerror=null; this.src='{{ asset('xivig-admin/img/blog-1-PTDVKJDt.webp') }}'">
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
                        <div class="img-preview rounded-3 overflow-hidden border shadow-sm mx-auto" style="width: 220px; height: 124px;"></div>
                        <span class="text-muted small d-block mt-1">16:9 Banner Preview</span>
                    </div>

                    <div class="d-flex gap-3 align-items-center justify-content-center">
                        <!-- Square Card Preview -->
                        <div>
                            <div class="img-preview rounded-3 overflow-hidden border shadow-sm" style="width: 80px; height: 80px;"></div>
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

<!-- Cropper.js Script (Local with cache busting + CDN Fallback) -->
<script src="{{ asset('xivig-admin/vendor/cropperjs/cropper.min.js') }}?v={{ file_exists(public_path('xivig-admin/vendor/cropperjs/cropper.min.js')) ? filemtime(public_path('xivig-admin/vendor/cropperjs/cropper.min.js')) : time() }}"></script>

<script>
(function () {
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

    // Self-healing Cropper loader matching xivig.github.io
    function ensureCropper(callback, retries = 25) {
        if (typeof window.Cropper === 'function') {
            callback(window.Cropper);
            return;
        }

        if (retries > 0) {
            setTimeout(function() { ensureCropper(callback, retries - 1); }, 100);
            return;
        }

        console.warn("Local Cropper.js not detected, attempting dynamic CDN fallback...");
        if (!document.getElementById('cropper-cdn-fallback')) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css';
            document.head.appendChild(link);

            const script = document.createElement('script');
            script.id = 'cropper-cdn-fallback';
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js';
            script.onload = function() {
                if (typeof window.Cropper === 'function') {
                    callback(window.Cropper);
                }
            };
            script.onerror = function() {
                console.error("Cropper.js library could not be loaded from local or CDN.");
            };
            document.head.appendChild(script);
        }
    }

    const cropperOptions = {
        aspectRatio: 16 / 9,
        preview: '.img-preview',
        viewMode: 1, // Restrict crop box to stay within canvas boundaries
        dragMode: 'crop', // Default to crop selection mode
        autoCrop: true, // Auto-create crop selection box on load
        autoCropArea: 0.85,
        restore: false,
        checkCrossOrigin: true,
        checkOrientation: true,
        modal: true,
        guides: true,
        center: true,
        highlight: true,
        background: true,
        cropBoxMovable: true, // Allow moving crop selection box
        cropBoxResizable: true, // Allow resizing crop selection box
        toggleDragModeOnDblclick: true,
        minCropBoxWidth: 20,
        minCropBoxHeight: 20,
        crop(event) {
            if (dataX) dataX.value = Math.round(event.detail.x);
            if (dataY) dataY.value = Math.round(event.detail.y);
            if (dataWidth) dataWidth.value = Math.round(event.detail.width);
            if (dataHeight) dataHeight.value = Math.round(event.detail.height);
            if (dataRotate) dataRotate.value = Math.round(event.detail.rotate || 0) + '°';
        },
    };

    ensureCropper(function (CropperClass) {
        function startCropper() {
            if (cropper) {
                try { cropper.destroy(); } catch (e) {}
            }
            cropper = new CropperClass(image, cropperOptions);
            image.__cropper = cropper;
            window.__cropperInstance = cropper;
            image.setAttribute('data-cropper-initialized', 'true');
        }

        function initCropperWhenReady() {
            if (image.complete) {
                if (typeof image.decode === 'function') {
                    image.decode().then(startCropper).catch(function () {
                        startCropper();
                    });
                } else {
                    startCropper();
                }
            } else {
                image.addEventListener('load', startCropper, { once: true });
                image.addEventListener('error', function () {
                    console.warn('Image failed to load in cropper, attempting fallback sample');
                }, { once: true });
            }
        }

        initCropperWhenReady();

        // Safety timeout to ensure cropper is created even if events desynced
        setTimeout(function () {
            if (!cropper && !image.getAttribute('data-cropper-initialized')) {
                startCropper();
            }
        }, 300);

        // Switch image helper
        window.switchCropperImage = function(url, title = 'Selected Image') {
            if (!url) return;
            if (cropper) {
                cropper.replace(url);
            } else {
                image.src = url;
                startCropper();
            }
            const alertBox = document.querySelector('.page-image-cropper .alert-info');
            if (alertBox) {
                const titleSpan = alertBox.querySelector('span');
                if (titleSpan) titleSpan.innerHTML = 'Editing Media Asset: <strong>' + title + '</strong>';
            }
        };

        // Override picker modal selection to load image directly into cropper
        window.selectPickerAsset = function(id, url, name) {
            switchCropperImage(url, name);
            const modalEl = document.getElementById('mediaPickerModal');
            if (modalEl && window.bootstrap && window.bootstrap.Modal) {
                const modal = window.bootstrap.Modal.getInstance(modalEl);
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

                case 'setDragMode':
                    cropper.setDragMode(option);
                    const modeGroup = btn.closest('#dragModeGroup, .cropper-toolbar-group');
                    if (modeGroup) {
                        modeGroup.querySelectorAll('[data-method="setDragMode"]').forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');
                    }
                    break;

                case 'crop':
                    cropper.crop();
                    const cropModeBtn = document.querySelector('#dragModeGroup [data-option="crop"]');
                    if (cropModeBtn) {
                        const grp = cropModeBtn.closest('.cropper-toolbar-group');
                        if (grp) grp.querySelectorAll('[data-method="setDragMode"]').forEach(b => b.classList.remove('active'));
                        cropModeBtn.classList.add('active');
                    }
                    break;

                case 'clear':
                    cropper.clear();
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
                        Swal.fire({
                            icon: 'warning',
                            title: 'Invalid File',
                            text: 'Please select a valid image file.',
                            customClass: { confirmButton: 'btn btn-primary px-4 rounded-pill' },
                            buttonsStyling: false
                        });
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
                Swal.fire({
                    icon: 'error',
                    title: 'Crop Error',
                    text: 'Failed to generate cropped canvas.',
                    customClass: { confirmButton: 'btn btn-primary px-4 rounded-pill' },
                    buttonsStyling: false
                });
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
                showToast('Asset successfully saved into Media Library!', 'success');
                setTimeout(() => {
                    triggerBtn.innerHTML = originalText;
                }, 3000);
            })
            .catch(err => {
                triggerBtn.disabled = false;
                triggerBtn.innerHTML = originalText;
                Swal.fire({
                    icon: 'error',
                    title: 'Save Failed',
                    text: 'Failed to save cropped image to Media Library.',
                    customClass: { confirmButton: 'btn btn-primary px-4 rounded-pill' },
                    buttonsStyling: false
                });
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
})();
</script>

<style>
/* Cropper Container & Viewport Fixes */
.img-container {
    width: 100%;
    height: 480px;
    max-height: 520px;
    text-align: center;
    position: relative;
    background-color: #f8fafc;
    background-image: radial-gradient(rgba(0,0,0,0.12) 1px, transparent 1px);
    background-size: 20px 20px;
    border-radius: 0.75rem;
    overflow: hidden;
}

[data-bs-theme="dark"] .img-container {
    background-color: #0f172a;
    background-image: radial-gradient(rgba(255,255,255,0.08) 1px, transparent 1px);
}

.img-container > img {
    max-width: 100%;
    display: block;
}

/* Ensure cropper-hidden hides the original target completely */
img.cropper-hidden, .cropper-hidden {
    display: none !important;
}

/* High-Visibility Crop Selection Box & Handles */
.cropper-view-box {
    outline: 2px solid #4059E8 !important;
    outline-color: #4059E8 !important;
    border-radius: 2px;
}

.cropper-modal {
    background-color: rgba(15, 23, 42, 0.6) !important;
}

.cropper-dashed {
    border-color: rgba(255, 255, 255, 0.8) !important;
    opacity: 0.85 !important;
}

.cropper-center:before, .cropper-center:after {
    background-color: #ffffff !important;
}

.cropper-face {
    background-color: rgba(64, 89, 232, 0.05) !important;
    cursor: move !important;
}

.cropper-line {
    background-color: #4059E8 !important;
    opacity: 0.3 !important;
}

.cropper-line:hover {
    opacity: 0.8 !important;
}

.cropper-line.line-e, .cropper-line.line-w {
    width: 8px !important;
    cursor: ew-resize !important;
}

.cropper-line.line-n, .cropper-line.line-s {
    height: 8px !important;
    cursor: ns-resize !important;
}

/* Modern Pill Selection Handles (Easy to click and drag) */
.cropper-point {
    background-color: #4059E8 !important;
    border: 2px solid #ffffff !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.35) !important;
    width: 12px !important;
    height: 12px !important;
    border-radius: 50% !important;
    opacity: 1 !important;
    z-index: 200 !important;
}

.cropper-point.point-e {
    margin-top: -6px !important;
    right: -6px !important;
    cursor: ew-resize !important;
}
.cropper-point.point-n {
    margin-left: -6px !important;
    top: -6px !important;
    cursor: ns-resize !important;
}
.cropper-point.point-w {
    margin-top: -6px !important;
    left: -6px !important;
    cursor: ew-resize !important;
}
.cropper-point.point-s {
    margin-left: -6px !important;
    bottom: -6px !important;
    cursor: s-resize !important;
}
.cropper-point.point-ne {
    top: -6px !important;
    right: -6px !important;
    cursor: nesw-resize !important;
}
.cropper-point.point-nw {
    top: -6px !important;
    left: -6px !important;
    cursor: nwse-resize !important;
}
.cropper-point.point-sw {
    bottom: -6px !important;
    left: -6px !important;
    cursor: nesw-resize !important;
}
.cropper-point.point-se {
    bottom: -6px !important;
    right: -6px !important;
    cursor: nwse-resize !important;
    width: 14px !important;
    height: 14px !important;
}

@media (min-width: 768px) {
    .cropper-point.point-se {
        width: 14px !important;
        height: 14px !important;
        opacity: 1 !important;
    }
}
@media (min-width: 1200px) {
    .cropper-point.point-se {
        width: 14px !important;
        height: 14px !important;
        opacity: 1 !important;
    }
}

.cropper-drag-box.cropper-crop {
    cursor: crosshair !important;
}

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
