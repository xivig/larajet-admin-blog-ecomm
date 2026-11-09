{{-- Professional SEO Suite Card (Meta, Open Graph, JSON Schema) --}}
<div class="card mb-4 border-0 shadow-sm">
  <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
    <div>
      <h5 class="card-title mb-0 fw-bold">
        <i class="bi bi-search text-primary me-2"></i> Professional SEO &amp; Social Intelligence
      </h5>
      <small class="text-muted">SERP Meta, Open Graph (FB, X, LinkedIn, WhatsApp), and Schema.org JSON-LD</small>
    </div>
    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1">
      Rich Snippets Ready
    </span>
  </div>
  <div class="card-body">
    <!-- Nav Tabs for SEO Sub-Modules -->
    <ul class="nav nav-pills nav-fill mb-3 p-1 bg-light rounded" id="seoTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active py-2 fw-semibold" id="seo-meta-tab" data-bs-toggle="pill" data-bs-target="#seo-meta-content" type="button" role="tab">
          <i class="bi bi-card-text me-1"></i> Search Engine Meta
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link py-2 fw-semibold" id="seo-social-tab" data-bs-toggle="pill" data-bs-target="#seo-social-content" type="button" role="tab">
          <i class="bi bi-share me-1"></i> Open Graph &amp; Social
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link py-2 fw-semibold" id="seo-schema-tab" data-bs-toggle="pill" data-bs-target="#seo-schema-content" type="button" role="tab">
          <i class="bi bi-code-slash me-1"></i> JSON-LD Schema
        </button>
      </li>
    </ul>

    <div class="tab-content pt-2" id="seoTabsContent">
      <!-- 1. Meta Tab -->
      <div class="tab-pane fade show active" id="seo-meta-content" role="tabpanel">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">SEO Meta Title</label>
            <input type="text" name="meta_title" id="seoMetaTitle" class="form-control" placeholder="Defaults to Article Title | Site Name" value="{{ old('meta_title', $post->meta_title ?? '') }}" maxlength="255">
            <div class="d-flex justify-content-between form-text small">
              <span>Ideal length: 50-60 characters</span>
              <span id="metaTitleCount" class="text-muted">0 / 60</span>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Canonical URL</label>
            <input type="url" name="canonical_url" class="form-control" placeholder="https://yourdomain.com/blog/article-slug" value="{{ old('canonical_url', $post->canonical_url ?? '') }}">
            <div class="form-text small">Leave blank to use the standard canonical permalink.</div>
          </div>

          <div class="col-12">
            <label class="form-label fw-semibold">SEO Meta Description</label>
            <textarea name="meta_description" id="seoMetaDesc" class="form-control" rows="3" placeholder="Compelling 150-160 character snippet for Google SERP results...">{{ old('meta_description', $post->meta_description ?? '') }}</textarea>
            <div class="d-flex justify-content-between form-text small">
              <span>Ideal length: 150-160 characters</span>
              <span id="metaDescCount" class="text-muted">0 / 160</span>
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">Meta Keywords</label>
            <input type="text" name="meta_keywords" class="form-control" placeholder="laravel, architecture, cloud, php" value="{{ old('meta_keywords', $post->meta_keywords ?? '') }}">
            <div class="form-text small">Comma-separated target search keywords.</div>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Meta Author</label>
            <input type="text" name="meta_author" class="form-control" placeholder="Defaults to post author" value="{{ old('meta_author', $post->meta_author ?? '') }}">
            <div class="form-text small">Author signature for search engines.</div>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Robots Indexing</label>
            <select name="meta_robots" class="form-select">
              @php $curRobots = old('meta_robots', $post->meta_robots ?? 'index, follow'); @endphp
              <option value="index, follow" {{ $curRobots === 'index, follow' ? 'selected' : '' }}>Index, Follow (Recommended)</option>
              <option value="noindex, follow" {{ $curRobots === 'noindex, follow' ? 'selected' : '' }}>Noindex, Follow</option>
              <option value="index, nofollow" {{ $curRobots === 'index, nofollow' ? 'selected' : '' }}>Index, Nofollow</option>
              <option value="noindex, nofollow" {{ $curRobots === 'noindex, nofollow' ? 'selected' : '' }}>Noindex, Nofollow (Blocked)</option>
              <option value="noarchive, nosnippet" {{ $curRobots === 'noarchive, nosnippet' ? 'selected' : '' }}>Noarchive, Nosnippet</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Content Language</label>
            <input type="text" name="meta_language" class="form-control" placeholder="en" value="{{ old('meta_language', $post->meta_language ?? 'en') }}">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Content Rating</label>
            <select name="meta_rating" class="form-select">
              @php $curRating = old('meta_rating', $post->meta_rating ?? 'general'); @endphp
              <option value="general" {{ $curRating === 'general' ? 'selected' : '' }}>General (All Audiences)</option>
              <option value="14 years" {{ $curRating === '14 years' ? 'selected' : '' }}>14 Years</option>
              <option value="mature" {{ $curRating === 'mature' ? 'selected' : '' }}>Mature</option>
              <option value="restricted" {{ $curRating === 'restricted' ? 'selected' : '' }}>Restricted</option>
            </select>
          </div>

          <!-- Geo Target Metadata -->
          <div class="col-md-4">
            <label class="form-label fw-semibold">Geo Region</label>
            <input type="text" name="geo_region" class="form-control" placeholder="e.g. US-CA" value="{{ old('geo_region', $post->geo_region ?? '') }}">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Geo Placename</label>
            <input type="text" name="geo_placename" class="form-control" placeholder="e.g. San Francisco" value="{{ old('geo_placename', $post->geo_placename ?? '') }}">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Geo Coordinates (Position)</label>
            <input type="text" name="geo_position" class="form-control" placeholder="latitude;longitude (e.g. 37.77;-122.41)" value="{{ old('geo_position', $post->geo_position ?? '') }}">
          </div>

          <div class="col-12">
            <label class="form-label fw-semibold">Copyright Attribution</label>
            <input type="text" name="meta_copyright" class="form-control" placeholder="e.g. Copyright © 2026 XIVIG Maison Gazette. All rights reserved." value="{{ old('meta_copyright', $post->meta_copyright ?? '') }}">
          </div>
        </div>
      </div>

      <!-- 2. Open Graph & Social Tab -->
      <div class="tab-pane fade" id="seo-social-content" role="tabpanel">
        <div class="row g-3">
          <div class="col-12">
            <div class="alert alert-info py-2 px-3 small mb-2 d-flex align-items-center">
              <i class="bi bi-info-circle-fill me-2 fs-6"></i>
              <span>Open Graph tags power rich card unfurls across <strong>Facebook</strong>, <strong>LinkedIn</strong>, <strong>WhatsApp</strong>, and <strong>Twitter/X</strong>.</span>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Social Share Title (OG Title)</label>
            <input type="text" name="og_title" class="form-control" placeholder="Defaults to Article Title" value="{{ old('og_title', $post->og_title ?? '') }}">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Twitter Card Style</label>
            <select name="twitter_card" class="form-select">
              @php $curCard = old('twitter_card', $post->twitter_card ?? 'summary_large_image'); @endphp
              <option value="summary_large_image" {{ $curCard === 'summary_large_image' ? 'selected' : '' }}>Summary with Large Image (Recommended)</option>
              <option value="summary" {{ $curCard === 'summary' ? 'selected' : '' }}>Standard Summary</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Social Share Description</label>
            <textarea name="og_description" class="form-control" rows="2" placeholder="Defaults to Excerpt...">{{ old('og_description', $post->og_description ?? '') }}</textarea>
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Custom Social Card Image URL (1200x630)</label>
            <input type="text" name="og_image" class="form-control" placeholder="https://yourdomain.com/uploads/posts/og-cover.jpg (Defaults to Hero Cover)" value="{{ old('og_image', $post->og_image ?? '') }}">
            <div class="form-text small">Recommended resolution: 1200x630px for sharp retina cards on WhatsApp, Facebook &amp; X.</div>
          </div>
        </div>
      </div>

      <!-- 3. JSON-LD Schema Tab -->
      <div class="tab-pane fade" id="seo-schema-content" role="tabpanel">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Schema.org Article Type</label>
            <select name="schema_type" class="form-select">
              @php $curType = old('schema_type', $post->schema_type ?? 'Article'); @endphp
              <option value="Article" {{ $curType === 'Article' ? 'selected' : '' }}>Article (Standard Editorial)</option>
              <option value="NewsArticle" {{ $curType === 'NewsArticle' ? 'selected' : '' }}>NewsArticle (Time-Sensitive News / Press)</option>
              <option value="BlogPosting" {{ $curType === 'BlogPosting' ? 'selected' : '' }}>BlogPosting (Tech Blog &amp; Notes)</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Automatic Schemas Included</label>
            <div class="d-flex flex-wrap gap-1 mt-1">
              <span class="badge bg-success-subtle text-success">Organization</span>
              <span class="badge bg-success-subtle text-success">WebSite</span>
              <span class="badge bg-success-subtle text-success">BreadcrumbList</span>
              <span class="badge bg-success-subtle text-success">Person (Author)</span>
              <span class="badge bg-success-subtle text-success">ImageObject</span>
            </div>
          </div>

          <!-- Optional FAQ Schema Builder -->
          <div class="col-12 mt-3 pt-3 border-top">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <label class="form-label fw-semibold mb-0">
                <i class="bi bi-question-circle text-primary me-1"></i> FAQ Structured Data (FAQPage Schema)
              </label>
              <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2" onclick="addFaqRow()">
                <i class="bi bi-plus-lg me-1"></i> Add FAQ Item
              </button>
            </div>
            <p class="text-muted small mb-2">Google renders interactive collapsible Accordion questions in search results when FAQPage schema is attached.</p>
            
            <div id="faqItemsContainer" class="d-flex flex-column gap-2">
              @php
                $faqs = old('faq_questions') ? array_map(fn($q, $idx) => ['question' => $q, 'answer' => old('faq_answers')[$idx] ?? ''], old('faq_questions'), array_keys(old('faq_questions'))) : ($post->faq_schema ?? []);
              @endphp

              @forelse($faqs as $idx => $faq)
                <div class="faq-row card card-body p-3 bg-light border">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold small text-primary">Question #{{ $idx + 1 }}</span>
                    <button type="button" class="btn btn-sm btn-outline-danger py-0 px-1" onclick="this.closest('.faq-row').remove()">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                  <input type="text" name="faq_questions[]" class="form-control form-control-sm mb-2" placeholder="e.g. What is database queuing?" value="{{ $faq['question'] ?? '' }}">
                  <textarea name="faq_answers[]" class="form-control form-control-sm" rows="2" placeholder="e.g. Database queuing stores asynchronous jobs in a database table...">{{ $faq['answer'] ?? '' }}</textarea>
                </div>
              @empty
                <div class="text-muted small p-2 bg-light rounded text-center" id="emptyFaqNotice">
                  No FAQs attached. Click "+ Add FAQ Item" to include interactive rich snippet questions.
                </div>
              @endforelse
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function addFaqRow() {
    const container = document.getElementById('faqItemsContainer');
    const emptyNotice = document.getElementById('emptyFaqNotice');
    if (emptyNotice) emptyNotice.remove();

    const count = container.querySelectorAll('.faq-row').length + 1;
    const row = document.createElement('div');
    row.className = 'faq-row card card-body p-3 bg-light border';
    row.innerHTML = `
      <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="fw-semibold small text-primary">Question #${count}</span>
        <button type="button" class="btn btn-sm btn-outline-danger py-0 px-1" onclick="this.closest('.faq-row').remove()">
          <i class="bi bi-trash"></i>
        </button>
      </div>
      <input type="text" name="faq_questions[]" class="form-control form-control-sm mb-2" placeholder="e.g. How does 2FA work?" required>
      <textarea name="faq_answers[]" class="form-control form-control-sm" rows="2" placeholder="e.g. Two-factor authentication adds an extra TOTP security layer..." required></textarea>
    `;
    container.appendChild(row);
}

document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('seoMetaTitle');
    const descInput = document.getElementById('seoMetaDesc');
    const titleCount = document.getElementById('metaTitleCount');
    const descCount = document.getElementById('metaDescCount');

    if (titleInput && titleCount) {
        const updateTitle = () => {
            titleCount.textContent = `${titleInput.value.length} / 60`;
            titleCount.className = titleInput.value.length > 60 ? 'text-danger fw-bold' : 'text-muted';
        };
        titleInput.addEventListener('input', updateTitle);
        updateTitle();
    }

    if (descInput && descCount) {
        const updateDesc = () => {
            descCount.textContent = `${descInput.value.length} / 160`;
            descCount.className = descInput.value.length > 160 ? 'text-danger fw-bold' : 'text-muted';
        };
        descInput.addEventListener('input', updateDesc);
        updateDesc();
    }
});
</script>
