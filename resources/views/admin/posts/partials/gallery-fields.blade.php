{{-- Unlimited Images / Photo Gallery Suite --}}
<div class="card mb-4 border-0 shadow-sm">
  <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
    <div>
      <h5 class="card-title mb-0 fw-bold">
        <i class="bi bi-images text-primary me-2"></i> Photo Gallery &amp; Visual Architecture
      </h5>
      <small class="text-muted">Attach unlimited supplementary images, architectural diagrams, and photo spreads</small>
    </div>
    <span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1">
      Unlimited Images
    </span>
  </div>
  <div class="card-body">
    <!-- Multi-file uploader -->
    <div class="mb-3">
      <label class="form-label fw-semibold">Upload Additional Gallery Images</label>
      <input type="file" name="gallery_images[]" id="galleryImagesInput" class="form-control" multiple accept="image/*" onchange="previewGalleryUploads(this)">
      <div class="form-text small">Select multiple images at once (JPG, PNG, WEBP, GIF, SVG). Each up to 5MB.</div>
    </div>

    <!-- Live Upload Preview Container -->
    <div id="newGalleryPreviewBox" class="row g-2 mb-3 d-none">
      <div class="col-12"><small class="text-muted fw-semibold">Pending Uploads Preview:</small></div>
      <div id="newGalleryThumbnails" class="d-flex flex-wrap gap-2"></div>
    </div>

    <!-- Existing Gallery Images (for edit mode) -->
    @if(isset($post) && $post->images && $post->images->count() > 0)
      <div class="mt-4 pt-3 border-top">
        <h6 class="fw-bold mb-3 d-flex align-items-center">
          <span>Current Gallery Images ({{ $post->images->count() }})</span>
        </h6>
        <div class="row g-3">
          @foreach($post->images as $img)
            <div class="col-md-4 col-sm-6" id="gallery-card-{{ $img->id }}">
              <div class="card h-100 border shadow-sm overflow-hidden">
                <div class="position-relative" style="height: 140px; background-color: #f1f5f9;">
                  <img src="{{ $img->url }}" alt="{{ $img->alt_text }}" class="w-100 h-100 object-fit-cover" loading="lazy">
                  <button type="button" 
                          class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 rounded-circle p-1 d-flex align-items-center justify-content-center" 
                          style="width: 26px; height: 26px;" 
                          title="Remove Image"
                          onclick="removeExistingGalleryImage({{ $post->id }}, {{ $img->id }})">
                    <i class="bi bi-trash" style="font-size: 12px;"></i>
                  </button>
                </div>
                <div class="p-2 bg-light">
                  <input type="text" 
                         name="gallery_captions[{{ $img->id }}]" 
                         class="form-control form-control-sm" 
                         placeholder="Caption..." 
                         value="{{ $img->caption }}">
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endif
  </div>
</div>

<script>
function previewGalleryUploads(input) {
    const previewBox = document.getElementById('newGalleryPreviewBox');
    const thumbContainer = document.getElementById('newGalleryThumbnails');
    thumbContainer.innerHTML = '';

    if (input.files && input.files.length > 0) {
        previewBox.classList.remove('d-none');
        Array.from(input.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'rounded border shadow-sm';
                img.style.width = '80px';
                img.style.height = '80px';
                img.style.objectFit = 'cover';
                thumbContainer.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    } else {
        previewBox.classList.add('d-none');
    }
}

function removeExistingGalleryImage(postId, imageId) {
    if (!confirm('Are you sure you want to remove this gallery image?')) return;

    fetch(`/admin/posts/${postId}/gallery/${imageId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const card = document.getElementById(`gallery-card-${imageId}`);
            if (card) card.remove();
        }
    })
    .catch(() => {
        // Fallback form submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/posts/${postId}/gallery/${imageId}`;
        form.innerHTML = `
            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        document.body.appendChild(form);
        form.submit();
    });
}
</script>
