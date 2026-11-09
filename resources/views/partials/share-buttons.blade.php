@php
    $shareUrl = $shareUrl ?? url()->current();
    $shareTitle = $shareTitle ?? ($post->title ?? config('app.name'));
    $encodedUrl = urlencode($shareUrl);
    $encodedTitle = urlencode($shareTitle);
@endphp

<div class="share-buttons-suite d-flex align-items-center flex-wrap py-2" style="gap: 12px;">
    <span class="share-label text-uppercase d-inline-flex align-items-center" style="font-weight: 700; font-size: 12.5px; letter-spacing: 0.06em; color: #64748b;">
        <i class="fas fa-share-alt mr-2" style="color: #6366f1;"></i> SHARE:
    </span>

    <div class="d-inline-flex align-items-center flex-wrap" style="gap: 8px;">
        {{-- Facebook --}}
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedUrl }}" 
           target="_blank" 
           rel="noopener noreferrer" 
           class="share-circle-btn share-facebook"
           title="Share on Facebook"
           aria-label="Share on Facebook">
            <i class="fab fa-facebook-f"></i>
        </a>

        {{-- Twitter / X --}}
        <a href="https://twitter.com/intent/tweet?text={{ $encodedTitle }}&url={{ $encodedUrl }}" 
           target="_blank" 
           rel="noopener noreferrer" 
           class="share-circle-btn share-twitter"
           title="Share on X"
           aria-label="Share on X">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
        </a>

        {{-- LinkedIn --}}
        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $encodedUrl }}" 
           target="_blank" 
           rel="noopener noreferrer" 
           class="share-circle-btn share-linkedin"
           title="Share on LinkedIn"
           aria-label="Share on LinkedIn">
            <i class="fab fa-linkedin-in"></i>
        </a>

        {{-- WhatsApp --}}
        <a href="https://api.whatsapp.com/send?text={{ $encodedTitle }}%20{{ $encodedUrl }}" 
           target="_blank" 
           rel="noopener noreferrer" 
           class="share-circle-btn share-whatsapp"
           title="Share via WhatsApp"
           aria-label="Share via WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>

        {{-- Telegram --}}
        <a href="https://t.me/share/url?url={{ $encodedUrl }}&text={{ $encodedTitle }}" 
           target="_blank" 
           rel="noopener noreferrer" 
           class="share-circle-btn share-telegram"
           title="Share on Telegram"
           aria-label="Share on Telegram">
            <i class="fab fa-telegram-plane"></i>
        </a>

        {{-- Copy Link Button --}}
        <button type="button" 
                class="share-copy-btn" 
                onclick="copyCurrentArticleUrl(this, '{{ $shareUrl }}')"
                title="Copy Article Link">
            <i class="fas fa-link"></i>
            <span class="copy-text">COPY LINK</span>
        </button>
    </div>
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
    btn.innerHTML = '<i class="fas fa-check text-success"></i> <span class="text-success fw-bold">COPIED!</span>';
    btn.classList.add('copied');
    setTimeout(() => {
        btn.innerHTML = originalHtml;
        btn.classList.remove('copied');
    }, 2200);
}
</script>
