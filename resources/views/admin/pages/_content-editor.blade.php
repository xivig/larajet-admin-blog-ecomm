@php
  $initialContent = $content ?? '';
@endphp

<div class="mb-3">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-2 gap-2">
    <label for="pageContentInput" class="form-label fw-semibold mb-0">Page Content</label>
    <div class="btn-group btn-group-sm" role="group" aria-label="Editor Mode">
      <button type="button" class="btn btn-primary active" id="pageVisualModeBtn">
        <i class="bi bi-eye me-1"></i> Visual Editor
      </button>
      <button type="button" class="btn btn-outline-secondary" id="pageSourceModeBtn">
        <i class="bi bi-code-slash me-1"></i> HTML Source
      </button>
    </div>
  </div>

  <div id="pageContentVisualWrap" class="rounded overflow-hidden border">
    <div id="pageContentEditor" style="min-height: 360px;">{!! $initialContent !!}</div>
  </div>

  <textarea name="content" id="pageContentInput" rows="14" class="form-control font-monospace d-none" placeholder="Write page content or raw HTML...">{{ $initialContent }}</textarea>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const editorEl = document.getElementById('pageContentEditor');
  const visualWrap = document.getElementById('pageContentVisualWrap');
  const textarea = document.getElementById('pageContentInput');
  const visualBtn = document.getElementById('pageVisualModeBtn');
  const sourceBtn = document.getElementById('pageSourceModeBtn');

  if (!editorEl || !textarea) return;

  let quill = null;
  let isSourceMode = false;

  if (typeof Quill !== 'undefined') {
    quill = new Quill('#pageContentEditor', {
      theme: 'snow',
      placeholder: 'Compose rich page layout, headings, lists, and links...',
      modules: {
        toolbar: [
          [{ header: [1, 2, 3, 4, false] }],
          ['bold', 'italic', 'underline', 'strike'],
          [{ color: [] }, { background: [] }],
          [{ list: 'ordered' }, { list: 'bullet' }],
          [{ align: [] }],
          ['blockquote', 'code-block'],
          ['link', 'image', 'video'],
          ['clean'],
        ],
      },
    });

    quill.on('text-change', function () {
      if (!isSourceMode) {
        textarea.value = quill.root.innerHTML;
      }
    });
  } else {
    visualWrap?.classList.add('d-none');
    textarea.classList.remove('d-none');
    isSourceMode = true;
  }

  const setMode = (source) => {
    if (!quill) return;
    isSourceMode = source;

    if (source) {
      textarea.value = quill.root.innerHTML;
      visualWrap.classList.add('d-none');
      textarea.classList.remove('d-none');
      sourceBtn?.classList.replace('btn-outline-secondary', 'btn-primary');
      sourceBtn?.classList.add('active');
      visualBtn?.classList.replace('btn-primary', 'btn-outline-secondary');
      visualBtn?.classList.remove('active');
    } else {
      quill.clipboard.dangerouslyPasteHTML(textarea.value || '');
      textarea.classList.add('d-none');
      visualWrap.classList.remove('d-none');
      visualBtn?.classList.replace('btn-outline-secondary', 'btn-primary');
      visualBtn?.classList.add('active');
      sourceBtn?.classList.replace('btn-primary', 'btn-outline-secondary');
      sourceBtn?.classList.remove('active');
    }
  };

  visualBtn?.addEventListener('click', () => setMode(false));
  sourceBtn?.addEventListener('click', () => setMode(true));

  const parentForm = textarea.closest('form');
  if (parentForm) {
    parentForm.addEventListener('submit', function () {
      if (quill && !isSourceMode) {
        textarea.value = quill.root.innerHTML;
      }
    });
  }
});
</script>
@endpush
