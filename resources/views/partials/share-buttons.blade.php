@php
    $shareUrl = $shareUrl ?? url()->current();
    $shareTitle = $shareTitle ?? ($post->title ?? config('app.name'));
    $encodedUrl = urlencode($shareUrl);
    $encodedTitle = urlencode($shareTitle);
@endphp

<div class="share-buttons-suite d-flex align-items-center flex-wrap gap-2 py-2">
    <span class="share-label fw-bold text-muted small me-2 text-uppercase letter-spacing-1">
        <i class="bi bi-share me-1 text-primary"></i> Share:
    </span>

    {{-- Facebook --}}
    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedUrl }}" 
       target="_blank" 
       rel="noopener noreferrer" 
       class="btn btn-sm btn-outline-primary rounded-circle d-inline-flex align-items-center justify-content-center share-btn share-facebook"
       style="width: 36px; height: 36px;"
       title="Share on Facebook"
       aria-label="Share on Facebook">
        <i class="bi bi-facebook"></i>
    </a>

    {{-- Twitter / X --}}
    <a href="https://twitter.com/intent/tweet?text={{ $encodedTitle }}&url={{ $encodedUrl }}" 
       target="_blank" 
       rel="noopener noreferrer" 
       class="btn btn-sm btn-outline-dark rounded-circle d-inline-flex align-items-center justify-content-center share-btn share-twitter"
       style="width: 36px; height: 36px;"
       title="Share on X"
       aria-label="Share on X">
        <i class="bi bi-twitter-x"></i>
    </a>

    {{-- LinkedIn --}}
    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $encodedUrl }}" 
       target="_blank" 
       rel="noopener noreferrer" 
       class="btn btn-sm btn-outline-info rounded-circle d-inline-flex align-items-center justify-content-center share-btn share-linkedin"
       style="width: 36px; height: 36px;"
       title="Share on LinkedIn"
       aria-label="Share on LinkedIn">
        <i class="bi bi-linkedin"></i>
    </a>

    {{-- WhatsApp --}}
    <a href="https://api.whatsapp.com/send?text={{ $encodedTitle }}%20{{ $encodedUrl }}" 
       target="_blank" 
       rel="noopener noreferrer" 
       class="btn btn-sm btn-outline-success rounded-circle d-inline-flex align-items-center justify-content-center share-btn share-whatsapp"
       style="width: 36px; height: 36px;"
       title="Share via WhatsApp"
       aria-label="Share via WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>

    {{-- Telegram --}}
    <a href="https://t.me/share/url?url={{ $encodedUrl }}&text={{ $encodedTitle }}" 
       target="_blank" 
       rel="noopener noreferrer" 
       class="btn btn-sm btn-outline-primary rounded-circle d-inline-flex align-items-center justify-content-center share-btn share-telegram"
       style="width: 36px; height: 36px;"
       title="Share on Telegram"
       aria-label="Share on Telegram">
        <i class="bi bi-telegram"></i>
    </a>

    {{-- Copy Link Button --}}
    <button type="button" 
            class="btn btn-sm btn-outline-secondary rounded-pill px-3 d-inline-flex align-items-center gap-1 copy-link-btn" 
            onclick="copyCurrentArticleUrl(this, '{{ $shareUrl }}')"
            title="Copy Article Link">
        <i class="bi bi-link-45deg fs-6"></i>
        <span class="copy-text small">Copy Link</span>
    </button>
</div>

<script>
function copyCurrentArticleUrl(btn, url) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(() => showCopiedFeedback(btn));
    } else {
        const tempInput = document.createElement('input');
        tempInput.value = url;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);
        showCopiedFeedback(btn);
    }
}

function showCopiedFeedback(btn) {
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-check2 text-success fs-6"></i> <span class="small text-success fw-bold">Copied!</span>';
    btn.classList.add('border-success');
    setTimeout(() => {
        btn.innerHTML = originalHtml;
        btn.classList.remove('border-success');
    }, 2200);
}
</script>
