@extends('layouts.admin')

@section('title', 'SEO & Search Engine Intelligence Suite - XivigAdmin PRO')

@section('content')
<div class="page-seo-settings">

  <!-- Header -->
  <div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="page-title mb-1">SEO &amp; Search Engine Intelligence Suite</h1>
      <nav class="breadcrumb mb-0">
        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
        <a href="{{ route('admin.settings.index') }}" class="breadcrumb-item">Settings</a>
        <span class="breadcrumb-item active">SEO Suite</span>
      </nav>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="{{ url('/sitemap.xml') }}" target="_blank" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 small">
        <i class="bi bi-diagram-3"></i>
        <span>Sitemap</span>
      </a>
      <a href="{{ url('/robots.txt') }}" target="_blank" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 small">
        <i class="bi bi-robot"></i>
        <span>robots.txt</span>
      </a>
      <a href="{{ url('/feed.xml') }}" target="_blank" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 small">
        <i class="bi bi-rss"></i>
        <span>RSS Feed</span>
      </a>
      <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm" style="background-color: #5551ff; border-color: #5551ff;" onclick="document.getElementById('seoSettingsForm').submit();">
        <i class="bi bi-cloud-check-fill fs-5"></i>
        <span>Save All SEO Settings</span>
      </button>
    </div>
  </div>

  <!-- SEO Health & Audit Telemetry -->
  <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
    <div class="row align-items-center g-4">
      <div class="col-lg-4 border-end-lg">
        <div class="d-flex align-items-center gap-3">
          <div class="position-relative d-inline-flex align-items-center justify-content-center">
            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" 
                 style="width: 72px; height: 72px; font-size: 1.4rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
              {{ $score }}%
            </div>
          </div>
          <div>
            <h5 class="fw-bold mb-1 text-body">SEO Readiness Score</h5>
            <p class="text-muted small mb-0">
              @if($score >= 90)
                <span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i> Excellent:</span> Fully indexed and search optimized.
              @elseif($score >= 70)
                <span class="text-warning-emphasis fw-bold"><i class="bi bi-exclamation-triangle-fill me-1"></i> Good:</span> Minor optimizations recommended.
              @else
                <span class="text-danger fw-bold"><i class="bi bi-x-octagon-fill me-1"></i> Incomplete:</span> Important meta tags are missing.
              @endif
            </p>
          </div>
        </div>
      </div>

      <div class="col-lg-8">
        <div class="d-flex flex-wrap gap-2">
          <span class="badge {{ $checks['meta_title'] ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill px-3 py-1.5 small">
            <i class="bi {{ $checks['meta_title'] ? 'bi-check-lg' : 'bi-x-lg' }} me-1"></i> Meta Title
          </span>
          <span class="badge {{ $checks['meta_description'] ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill px-3 py-1.5 small">
            <i class="bi {{ $checks['meta_description'] ? 'bi-check-lg' : 'bi-x-lg' }} me-1"></i> Meta Description
          </span>
          <span class="badge {{ $checks['og_image'] ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning-emphasis' }} rounded-pill px-3 py-1.5 small">
            <i class="bi {{ $checks['og_image'] ? 'bi-check-lg' : 'bi-exclamation' }} me-1"></i> Social OG Banner
          </span>
          <span class="badge {{ $checks['schema_org'] ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning-emphasis' }} rounded-pill px-3 py-1.5 small">
            <i class="bi {{ $checks['schema_org'] ? 'bi-check-lg' : 'bi-exclamation' }} me-1"></i> Schema.org JSON-LD
          </span>
          <span class="badge {{ $checks['sitemap'] ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill px-3 py-1.5 small">
            <i class="bi {{ $checks['sitemap'] ? 'bi-check-lg' : 'bi-x-lg' }} me-1"></i> XML Sitemap
          </span>
          <span class="badge {{ $checks['robots_txt'] ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill px-3 py-1.5 small">
            <i class="bi {{ $checks['robots_txt'] ? 'bi-check-lg' : 'bi-x-lg' }} me-1"></i> robots.txt
          </span>
          <span class="badge {{ $checks['google_verification'] ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} rounded-pill px-3 py-1.5 small">
            <i class="bi {{ $checks['google_verification'] ? 'bi-check-lg' : 'bi-dash' }} me-1"></i> Google Search Console
          </span>
        </div>
      </div>
    </div>
  </div>

  <!-- Navigation Tabs -->
  <ul class="nav nav-pills mb-4 gap-2 bg-body-secondary p-1.5 rounded-4 shadow-2xs" id="seoTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active rounded-pill px-3 py-2 fw-semibold" id="tab-meta-btn" data-bs-toggle="pill" data-bs-target="#tab-meta" type="button" role="tab">
        <i class="bi bi-search me-1.5"></i> Meta SERP
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link rounded-pill px-3 py-2 fw-semibold" id="tab-social-btn" data-bs-toggle="pill" data-bs-target="#tab-social" type="button" role="tab">
        <i class="bi bi-share me-1.5"></i> Open Graph &amp; Twitter
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link rounded-pill px-3 py-2 fw-semibold" id="tab-robots-btn" data-bs-toggle="pill" data-bs-target="#tab-robots" type="button" role="tab">
        <i class="bi bi-robot me-1.5"></i> Robots &amp; Crawlers
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link rounded-pill px-3 py-2 fw-semibold" id="tab-schema-btn" data-bs-toggle="pill" data-bs-target="#tab-schema" type="button" role="tab">
        <i class="bi bi-code-slash me-1.5"></i> Structured Data (JSON-LD)
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link rounded-pill px-3 py-2 fw-semibold" id="tab-verify-btn" data-bs-toggle="pill" data-bs-target="#tab-verify" type="button" role="tab">
        <i class="bi bi-shield-check me-1.5"></i> Webmaster Verification
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link rounded-pill px-3 py-2 fw-semibold" id="tab-preview-btn" data-bs-toggle="pill" data-bs-target="#tab-preview" type="button" role="tab">
        <i class="bi bi-eye me-1.5"></i> Live SERP Simulator
      </button>
    </li>
  </ul>

  <!-- Main Settings Form -->
  <form action="{{ route('admin.settings.seo.update') }}" method="POST" id="seoSettingsForm">
    @csrf

    <div class="tab-content" id="seoTabsContent">

      <!-- ================= TAB 1: META SERP ================= -->
      <div class="tab-pane fade show active" id="tab-meta" role="tabpanel">
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
          <div class="d-flex align-items-center gap-2 mb-3">
            <div class="bg-primary-subtle text-primary p-2 rounded-3">
              <i class="bi bi-search fs-5"></i>
            </div>
            <div>
              <h5 class="fw-bold mb-0">Search Engine Optimization (SERP) Baseline</h5>
              <p class="text-muted small mb-0">Core baseline title, description, and keywords indexed by search crawlers.</p>
            </div>
          </div>

          <div class="row g-4">
            <!-- Meta Title -->
            <div class="col-md-9">
              <label for="seo_meta_title" class="form-label fw-semibold">Global Baseline Title <span class="text-danger">*</span></label>
              <input type="text" name="seo_meta_title" id="seo_meta_title" class="form-control rounded-3" 
                     value="{{ old('seo_meta_title', $settings->get('seo_meta_title', 'XIVIG Maison — Luxury Horology & Haute Couture Journal')) }}" required>
              <small class="text-muted">Displayed on the homepage and appended across publication records.</small>
            </div>

            <!-- Title Separator -->
            <div class="col-md-3">
              <label for="seo_title_separator" class="form-label fw-semibold">Title Separator <span class="text-danger">*</span></label>
              <select name="seo_title_separator" id="seo_title_separator" class="form-select rounded-3">
                @php $curSep = $settings->get('seo_title_separator', '—'); @endphp
                <option value="—" {{ $curSep === '—' ? 'selected' : '' }}>— (Em Dash)</option>
                <option value="|" {{ $curSep === '|' ? 'selected' : '' }}>| (Pipe)</option>
                <option value="•" {{ $curSep === '•' ? 'selected' : '' }}>• (Bullet)</option>
                <option value="-" {{ $curSep === '-' ? 'selected' : '' }}>- (Hyphen)</option>
                <option value="::" {{ $curSep === '::' ? 'selected' : '' }}>:: (Double Colon)</option>
              </select>
              <small class="text-muted">Separator between post title &amp; site.</small>
            </div>

            <!-- Meta Description -->
            <div class="col-12">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="seo_meta_description" class="form-label fw-semibold mb-0">Search Engine Meta Description <span class="text-danger">*</span></label>
                <span class="small font-monospace" id="descCharCounter">
                  <span id="charCount">0</span> / 160 characters
                </span>
              </div>
              <textarea name="seo_meta_description" id="seo_meta_description" class="form-control rounded-3" rows="3" required>{{ old('seo_meta_description', $settings->get('seo_meta_description', 'Chronicles of bespoke craftsmanship, luxury timepieces, and contemporary aesthetics. Read verified editorial deep-dives and engineering blueprints.')) }}</textarea>
              <div class="progress mt-1.5" style="height: 4px;">
                <div id="descProgressBar" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
              </div>
              <small class="text-muted mt-1 d-block">Recommended length is between 120 and 160 characters for optimal Google and Bing visibility.</small>
            </div>

            <!-- Focus Keywords -->
            <div class="col-md-6">
              <label for="seo_meta_keywords" class="form-label fw-semibold">Primary Meta Keywords</label>
              <input type="text" name="seo_meta_keywords" id="seo_meta_keywords" class="form-control rounded-3" 
                     value="{{ old('seo_meta_keywords', $settings->get('seo_meta_keywords', 'luxury, horology, haute couture, watches, bespoke, architecture, editorial, craftsmanship')) }}" 
                     placeholder="comma, separated, keywords">
              <small class="text-muted">Comma-separated focus terms for cataloging and legacy search tags.</small>
            </div>

            <!-- Canonical Base URL -->
            <div class="col-md-3">
              <label for="seo_canonical_url" class="form-label fw-semibold">Canonical Base URL</label>
              <input type="url" name="seo_canonical_url" id="seo_canonical_url" class="form-control rounded-3" 
                     value="{{ old('seo_canonical_url', $settings->get('seo_canonical_url', url('/'))) }}" placeholder="https://xivig.com">
              <small class="text-muted">Prevents duplicate content indexation.</small>
            </div>

            <!-- Author Attribution -->
            <div class="col-md-3">
              <label for="seo_author" class="form-label fw-semibold">Default Editorial Publisher</label>
              <input type="text" name="seo_author" id="seo_author" class="form-control rounded-3" 
                     value="{{ old('seo_author', $settings->get('seo_author', 'XIVIG Editorial Board')) }}">
              <small class="text-muted">Populates author meta tag.</small>
            </div>
          </div>
        </div>
      </div>

      <!-- ================= TAB 2: SOCIAL & OPEN GRAPH ================= -->
      <div class="tab-pane fade" id="tab-social" role="tabpanel">
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
          <div class="d-flex align-items-center gap-2 mb-3">
            <div class="bg-info-subtle text-info p-2 rounded-3">
              <i class="bi bi-share fs-5"></i>
            </div>
            <div>
              <h5 class="fw-bold mb-0">Open Graph &amp; Twitter Card Configuration</h5>
              <p class="text-muted small mb-0">Governs rich link previews on Facebook, LinkedIn, Twitter/X, Discord, and Slack.</p>
            </div>
          </div>

          <div class="row g-4">
            <!-- OG Title -->
            <div class="col-md-6">
              <label for="seo_og_title" class="form-label fw-semibold">Open Graph Share Title</label>
              <input type="text" name="seo_og_title" id="seo_og_title" class="form-control rounded-3" 
                     value="{{ old('seo_og_title', $settings->get('seo_og_title', 'XIVIG Maison Gazette')) }}">
              <small class="text-muted">Headline on social feeds (defaults to Meta Title if blank).</small>
            </div>

            <!-- OG Site Name -->
            <div class="col-md-3">
              <label for="seo_og_site_name" class="form-label fw-semibold">OG Site Name</label>
              <input type="text" name="seo_og_site_name" id="seo_og_site_name" class="form-control rounded-3" 
                     value="{{ old('seo_og_site_name', $settings->get('seo_og_site_name', 'XIVIG Maison')) }}">
            </div>

            <!-- OG Locale -->
            <div class="col-md-3">
              <label for="seo_og_locale" class="form-label fw-semibold">OG Locale <span class="text-danger">*</span></label>
              <input type="text" name="seo_og_locale" id="seo_og_locale" class="form-control rounded-3" 
                     value="{{ old('seo_og_locale', $settings->get('seo_og_locale', 'en_US')) }}" required>
              <small class="text-muted">e.g., en_US, fr_FR, de_DE</small>
            </div>

            <!-- OG Description -->
            <div class="col-12">
              <label for="seo_og_description" class="form-label fw-semibold">Open Graph Share Description</label>
              <textarea name="seo_og_description" id="seo_og_description" class="form-control rounded-3" rows="2">{{ old('seo_og_description', $settings->get('seo_og_description', 'Chronicles of bespoke craftsmanship, luxury timepieces, and contemporary aesthetics.')) }}</textarea>
            </div>

            <!-- Default Social Banner Image -->
            <div class="col-md-8">
              <label for="seo_og_image" class="form-label fw-semibold">Default Social Sharing Banner (OG:Image)</label>
              <div class="input-group">
                <input type="text" name="seo_og_image" id="seo_og_image" class="form-control rounded-start-3" 
                       value="{{ old('seo_og_image', $settings->get('seo_og_image', url('/xivig-public/images/blog/laravel-hyperscale-architecture.jpg'))) }}" 
                       placeholder="https://.../banner.jpg">
                <button type="button" class="btn btn-outline-secondary rounded-end-3 px-3" onclick="openMediaPickerModal('seo_og_image', 'ogImagePreview')">
                  <i class="bi bi-images me-1"></i> Choose from Media
                </button>
              </div>
              <small class="text-muted">Recommended resolution: 1200x630px. Used as fallback when articles don't specify a cover image.</small>
            </div>

            <!-- Preview Thumbnail -->
            <div class="col-md-4">
              <label class="form-label fw-semibold">Banner Preview</label>
              <div class="border rounded-3 p-1 text-center bg-body-secondary" style="height: 100px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                <img id="ogImagePreview" src="{{ $settings->get('seo_og_image', url('/xivig-public/images/blog/laravel-hyperscale-architecture.jpg')) }}" alt="OG Banner" style="max-height: 100%; max-width: 100%; object-fit: cover; border-radius: 6px;">
              </div>
            </div>

            <!-- Social Card Formats -->
            <div class="col-md-4">
              <label for="seo_og_type" class="form-label fw-semibold">OG Object Type <span class="text-danger">*</span></label>
              <select name="seo_og_type" id="seo_og_type" class="form-select rounded-3">
                @php $curType = $settings->get('seo_og_type', 'website'); @endphp
                <option value="website" {{ $curType === 'website' ? 'selected' : '' }}>website (General Site)</option>
                <option value="article" {{ $curType === 'article' ? 'selected' : '' }}>article (News &amp; Publications)</option>
                <option value="blog" {{ $curType === 'blog' ? 'selected' : '' }}>blog (Blog Journal)</option>
              </select>
            </div>

            <div class="col-md-4">
              <label for="seo_twitter_card" class="form-label fw-semibold">Twitter / X Card Format <span class="text-danger">*</span></label>
              <select name="seo_twitter_card" id="seo_twitter_card" class="form-select rounded-3">
                @php $curCard = $settings->get('seo_twitter_card', 'summary_large_image'); @endphp
                <option value="summary_large_image" {{ $curCard === 'summary_large_image' ? 'selected' : '' }}>summary_large_image (Recommended)</option>
                <option value="summary" {{ $curCard === 'summary' ? 'selected' : '' }}>summary (Small thumbnail card)</option>
              </select>
            </div>

            <div class="col-md-2">
              <label for="seo_twitter_site" class="form-label fw-semibold">Twitter Site Handle</label>
              <input type="text" name="seo_twitter_site" id="seo_twitter_site" class="form-control rounded-3" 
                     value="{{ old('seo_twitter_site', $settings->get('seo_twitter_site', '@xivigmaison')) }}" placeholder="@brand">
            </div>

            <div class="col-md-2">
              <label for="seo_twitter_creator" class="form-label fw-semibold">Creator Handle</label>
              <input type="text" name="seo_twitter_creator" id="seo_twitter_creator" class="form-control rounded-3" 
                     value="{{ old('seo_twitter_creator', $settings->get('seo_twitter_creator', '@xivigmaison')) }}" placeholder="@author">
            </div>
          </div>
        </div>
      </div>

      <!-- ================= TAB 3: ROBOTS & CRAWLERS ================= -->
      <div class="tab-pane fade" id="tab-robots" role="tabpanel">
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
          <div class="d-flex align-items-center gap-2 mb-3">
            <div class="bg-warning-subtle text-warning-emphasis p-2 rounded-3">
              <i class="bi bi-robot fs-5"></i>
            </div>
            <div>
              <h5 class="fw-bold mb-0">Robots &amp; Crawler Indexing Directives</h5>
              <p class="text-muted small mb-0">Control how Googlebot, Bingbot, and AI scrapers index your publications.</p>
            </div>
          </div>

          <div class="row g-4 mb-4">
            <!-- Index / NoIndex -->
            <div class="col-md-6">
              <label class="form-label fw-semibold">Robots Indexation <span class="text-danger">*</span></label>
              @php $curIndex = $settings->get('seo_robots_index', 'index'); @endphp
              <div class="d-flex gap-3">
                <label class="form-check p-3 border rounded-3 flex-fill cursor-pointer d-flex align-items-center gap-2">
                  <input class="form-check-input mt-0" type="radio" name="seo_robots_index" value="index" {{ $curIndex === 'index' ? 'checked' : '' }}>
                  <div>
                    <div class="fw-bold text-success">index</div>
                    <small class="text-muted">Allow search engines to index and rank site</small>
                  </div>
                </label>
                <label class="form-check p-3 border rounded-3 flex-fill cursor-pointer d-flex align-items-center gap-2">
                  <input class="form-check-input mt-0" type="radio" name="seo_robots_index" value="noindex" {{ $curIndex === 'noindex' ? 'checked' : '' }}>
                  <div>
                    <div class="fw-bold text-danger">noindex</div>
                    <small class="text-muted">Hide entire site from search results</small>
                  </div>
                </label>
              </div>
            </div>

            <!-- Follow / NoFollow -->
            <div class="col-md-6">
              <label class="form-label fw-semibold">Link Traversal <span class="text-danger">*</span></label>
              @php $curFollow = $settings->get('seo_robots_follow', 'follow'); @endphp
              <div class="d-flex gap-3">
                <label class="form-check p-3 border rounded-3 flex-fill cursor-pointer d-flex align-items-center gap-2">
                  <input class="form-check-input mt-0" type="radio" name="seo_robots_follow" value="follow" {{ $curFollow === 'follow' ? 'checked' : '' }}>
                  <div>
                    <div class="fw-bold text-success">follow</div>
                    <small class="text-muted">Allow crawlers to follow article links</small>
                  </div>
                </label>
                <label class="form-check p-3 border rounded-3 flex-fill cursor-pointer d-flex align-items-center gap-2">
                  <input class="form-check-input mt-0" type="radio" name="seo_robots_follow" value="nofollow" {{ $curFollow === 'nofollow' ? 'checked' : '' }}>
                  <div>
                    <div class="fw-bold text-danger">nofollow</div>
                    <small class="text-muted">Instruct crawlers not to follow outbound links</small>
                  </div>
                </label>
              </div>
            </div>

            <!-- Advanced Directives -->
            <div class="col-12">
              <label class="form-label fw-semibold">Advanced Search Directives</label>
              @php 
                $advStr = $settings->get('seo_robots_advanced', 'max-snippet:-1, max-image-preview:large, max-video-preview:-1');
              @endphp
              <div class="row g-2">
                <div class="col-md-4">
                  <label class="form-check border p-2.5 rounded-3 d-flex align-items-center gap-2 mb-0 cursor-pointer">
                    <input class="form-check-input mt-0" type="checkbox" name="seo_robots_advanced[]" value="max-snippet:-1" {{ str_contains($advStr, 'max-snippet:-1') ? 'checked' : '' }}>
                    <span class="small fw-semibold">max-snippet:-1 (Unlimited snippet)</span>
                  </label>
                </div>
                <div class="col-md-4">
                  <label class="form-check border p-2.5 rounded-3 d-flex align-items-center gap-2 mb-0 cursor-pointer">
                    <input class="form-check-input mt-0" type="checkbox" name="seo_robots_advanced[]" value="max-image-preview:large" {{ str_contains($advStr, 'max-image-preview:large') ? 'checked' : '' }}>
                    <span class="small fw-semibold">max-image-preview:large (High-res discover)</span>
                  </label>
                </div>
                <div class="col-md-4">
                  <label class="form-check border p-2.5 rounded-3 d-flex align-items-center gap-2 mb-0 cursor-pointer">
                    <input class="form-check-input mt-0" type="checkbox" name="seo_robots_advanced[]" value="max-video-preview:-1" {{ str_contains($advStr, 'max-video-preview:-1') ? 'checked' : '' }}>
                    <span class="small fw-semibold">max-video-preview:-1 (Video previews)</span>
                  </label>
                </div>
                <div class="col-md-6">
                  <label class="form-check border p-2.5 rounded-3 d-flex align-items-center gap-2 mb-0 cursor-pointer">
                    <input class="form-check-input mt-0" type="checkbox" name="seo_robots_advanced[]" value="noarchive" {{ str_contains($advStr, 'noarchive') ? 'checked' : '' }}>
                    <span class="small fw-semibold">noarchive (Prevent search engine caching)</span>
                  </label>
                </div>
                <div class="col-md-6">
                  <label class="form-check border p-2.5 rounded-3 d-flex align-items-center gap-2 mb-0 cursor-pointer">
                    <input class="form-check-input mt-0" type="checkbox" name="seo_robots_advanced[]" value="nosnippet" {{ str_contains($advStr, 'nosnippet') ? 'checked' : '' }}>
                    <span class="small fw-semibold">nosnippet (Disable all search previews)</span>
                  </label>
                </div>
              </div>
            </div>
          </div>

          <!-- Interactive robots.txt Editor -->
          <div class="border-top pt-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-2 gap-2">
              <div>
                <label for="seo_robots_txt" class="form-label fw-bold mb-0">Custom robots.txt Ruleset</label>
                <p class="text-muted small mb-0">Served directly at <code>/robots.txt</code>.</p>
              </div>
              <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" onclick="loadRobotsPreset('allow_all')">
                  Allow All
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" onclick="loadRobotsPreset('hardened')">
                  Production Hardened
                </button>
                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill" onclick="loadRobotsPreset('block_all')">
                  Block All Crawlers
                </button>
              </div>
            </div>

            <textarea name="seo_robots_txt" id="seo_robots_txt" class="form-control font-monospace rounded-3 bg-dark text-light p-3" rows="7" style="font-size: 0.85rem;">{{ old('seo_robots_txt', $settings->get('seo_robots_txt', "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /login\nDisallow: /register\nDisallow: /storage/app/backups/\n\nSitemap: " . url('/sitemap.xml'))) }}</textarea>
          </div>
        </div>
      </div>

      <!-- ================= TAB 4: STRUCTURED DATA (JSON-LD) ================= -->
      <div class="tab-pane fade" id="tab-schema" role="tabpanel">
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
          <div class="d-flex align-items-center gap-2 mb-3">
            <div class="bg-success-subtle text-success p-2 rounded-3">
              <i class="bi bi-code-slash fs-5"></i>
            </div>
            <div>
              <h5 class="fw-bold mb-0">Schema.org Structured Data (JSON-LD)</h5>
              <p class="text-muted small mb-0">Provides Google Knowledge Graph and Rich Snippets with structured entity signals.</p>
            </div>
          </div>

          <div class="row g-4">
            <div class="col-md-4">
              <label for="seo_schema_type" class="form-label fw-semibold">Organization / Entity Type <span class="text-danger">*</span></label>
              <select name="seo_schema_type" id="seo_schema_type" class="form-select rounded-3">
                @php $curSchema = $settings->get('seo_schema_type', 'NewsMediaOrganization'); @endphp
                <option value="NewsMediaOrganization" {{ $curSchema === 'NewsMediaOrganization' ? 'selected' : '' }}>NewsMediaOrganization (Editorial Journal)</option>
                <option value="Organization" {{ $curSchema === 'Organization' ? 'selected' : '' }}>Organization (Corporate / Brand)</option>
                <option value="Blog" {{ $curSchema === 'Blog' ? 'selected' : '' }}>Blog (Personal / Tech Blog)</option>
                <option value="LocalBusiness" {{ $curSchema === 'LocalBusiness' ? 'selected' : '' }}>LocalBusiness</option>
                <option value="Person" {{ $curSchema === 'Person' ? 'selected' : '' }}>Person (Author Portfolio)</option>
              </select>
            </div>

            <div class="col-md-4">
              <label for="seo_schema_name" class="form-label fw-semibold">Entity Legal Name <span class="text-danger">*</span></label>
              <input type="text" name="seo_schema_name" id="seo_schema_name" class="form-control rounded-3" 
                     value="{{ old('seo_schema_name', $settings->get('seo_schema_name', 'XIVIG Maison Gazette')) }}" required>
            </div>

            <div class="col-md-4">
              <label for="seo_schema_logo" class="form-label fw-semibold">Organization Logo URL</label>
              <input type="text" name="seo_schema_logo" id="seo_schema_logo" class="form-control rounded-3" 
                     value="{{ old('seo_schema_logo', $settings->get('seo_schema_logo', url('/xivig-admin/img/favicon-DgHNGhkV.ico'))) }}">
            </div>

            <div class="col-md-6">
              <label for="seo_schema_same_as" class="form-label fw-semibold">Verified Social Profiles (sameAs)</label>
              <textarea name="seo_schema_same_as" id="seo_schema_same_as" class="form-control rounded-3 font-monospace small" rows="5" 
                        placeholder="https://twitter.com/brand&#10;https://instagram.com/brand&#10;https://linkedin.com/company/brand">{{ old('seo_schema_same_as', $settings->get('seo_schema_same_as', "https://twitter.com/xivigmaison\nhttps://instagram.com/xivigmaison\nhttps://linkedin.com/company/xivigmaison\nhttps://github.com/xivig")) }}</textarea>
              <small class="text-muted">Enter one profile URL per line (Twitter, Instagram, LinkedIn, GitHub, YouTube, Facebook).</small>
            </div>

            <div class="col-md-6">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="form-label fw-semibold mb-0">Generated JSON-LD Payload Preview</label>
                <button type="button" class="btn btn-sm btn-outline-secondary py-0.5 px-2 rounded-pill" onclick="copyJsonLd()">
                  <i class="bi bi-clipboard me-1"></i> Copy JSON
                </button>
              </div>
              <pre class="bg-dark text-light p-3 rounded-3 font-monospace small mb-0" style="max-height: 155px; overflow-y: auto;"><code id="jsonLdCode">{{ $jsonLdFormatted }}</code></pre>
            </div>
          </div>
        </div>
      </div>

      <!-- ================= TAB 5: WEBMASTER VERIFICATIONS ================= -->
      <div class="tab-pane fade" id="tab-verify" role="tabpanel">
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
          <div class="d-flex align-items-center gap-2 mb-3">
            <div class="bg-primary-subtle text-primary p-2 rounded-3">
              <i class="bi bi-shield-check fs-5"></i>
            </div>
            <div>
              <h5 class="fw-bold mb-0">Search Console &amp; Webmaster Verifications</h5>
              <p class="text-muted small mb-0">Prove domain ownership to major search engines without uploading root HTML verification files.</p>
            </div>
          </div>

          <div class="row g-4">
            <div class="col-md-6">
              <label for="seo_google_verification" class="form-label fw-semibold">
                <i class="bi bi-google text-danger me-1"></i> Google Search Console Token
              </label>
              <input type="text" name="seo_google_verification" id="seo_google_verification" class="form-control rounded-3 font-monospace" 
                     value="{{ old('seo_google_verification', $settings->get('seo_google_verification', 'google-site-verification=XIVIG_PRO_GSC_SAMPLE_TOKEN_2026')) }}" 
                     placeholder="google-site-verification=XXXXXXXXXXXXXXXXXXXX">
              <small class="text-muted">Copy the HTML tag or meta token from Google Search Console.</small>
            </div>

            <div class="col-md-6">
              <label for="seo_bing_verification" class="form-label fw-semibold">
                <i class="bi bi-microsoft text-primary me-1"></i> Bing Webmaster Tools Token
              </label>
              <input type="text" name="seo_bing_verification" id="seo_bing_verification" class="form-control rounded-3 font-monospace" 
                     value="{{ old('seo_bing_verification', $settings->get('seo_bing_verification', 'msvalidate.01=XIVIG_BING_SAMPLE_TOKEN_2026')) }}" 
                     placeholder="msvalidate.01=XXXXXXXXXXXXXXXXXXXX">
              <small class="text-muted">Token for Bing and Yahoo search indexing verification.</small>
            </div>

            <div class="col-md-6">
              <label for="seo_yandex_verification" class="form-label fw-semibold">Yandex Webmaster Token</label>
              <input type="text" name="seo_yandex_verification" id="seo_yandex_verification" class="form-control rounded-3 font-monospace" 
                     value="{{ old('seo_yandex_verification', $settings->get('seo_yandex_verification', '')) }}" placeholder="yandex-verification=...">
            </div>

            <div class="col-md-6">
              <label for="seo_pinterest_verification" class="form-label fw-semibold">Pinterest Domain Claim Token</label>
              <input type="text" name="seo_pinterest_verification" id="seo_pinterest_verification" class="form-control rounded-3 font-monospace" 
                     value="{{ old('seo_pinterest_verification', $settings->get('seo_pinterest_verification', '')) }}" placeholder="p:domain_verify=...">
            </div>
          </div>
        </div>
      </div>

      <!-- ================= TAB 6: LIVE SIMULATOR ================= -->
      <div class="tab-pane fade" id="tab-preview" role="tabpanel">
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
          <div class="d-flex align-items-center gap-2 mb-3">
            <div class="bg-primary-subtle text-primary p-2 rounded-3">
              <i class="bi bi-eye fs-5"></i>
            </div>
            <div>
              <h5 class="fw-bold mb-0">Search Engine &amp; Social Simulator</h5>
              <p class="text-muted small mb-0">Live interactive preview of how your site appears to real searchers on Google and social media.</p>
            </div>
          </div>

          <div class="row g-4">
            <!-- Google SERP Preview Card -->
            <div class="col-lg-6">
              <div class="border rounded-4 p-3 bg-white text-dark shadow-2xs h-100">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                  <span class="small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Google Search SERP</span>
                  <span class="badge bg-light text-muted border small">Desktop &amp; Mobile</span>
                </div>

                <div class="google-serp-preview p-2" style="font-family: Arial, sans-serif;">
                  <div class="d-flex align-items-center gap-2 mb-1">
                    <img src="{{ asset('xivig-admin/img/favicon-DgHNGhkV.ico') }}" alt="Favicon" style="width: 18px; height: 18px; border-radius: 4px;">
                    <div class="d-flex flex-column" style="line-height: 1.2;">
                      <span class="fw-semibold text-dark" style="font-size: 13px;" id="simSiteName">XIVIG Maison</span>
                      <span class="text-muted" style="font-size: 11px;" id="simUrl">{{ url('/') }}</span>
                    </div>
                  </div>
                  <h6 class="mb-1 fw-normal" style="color: #1a0dab; font-size: 19px; cursor: pointer;" id="simGoogleTitle">
                    {{ $settings->get('seo_meta_title', 'XIVIG Maison — Luxury Horology & Haute Couture Journal') }}
                  </h6>
                  <p class="text-muted mb-0" style="font-size: 13px; line-height: 1.45;" id="simGoogleDesc">
                    {{ $settings->get('seo_meta_description', 'Chronicles of bespoke craftsmanship, luxury timepieces, and contemporary aesthetics.') }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Facebook / OpenGraph Preview Card -->
            <div class="col-lg-6">
              <div class="border rounded-4 p-3 bg-white text-dark shadow-2xs h-100">
                <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                  <span class="small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">Facebook / OpenGraph Card</span>
                  <span class="badge bg-light text-muted border small">1200 x 630</span>
                </div>

                <div class="border rounded-3 overflow-hidden shadow-xs">
                  <div style="height: 180px; background: #e2e8f0; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                    <img id="simOgImg" src="{{ $settings->get('seo_og_image', url('/xivig-public/images/blog/laravel-hyperscale-architecture.jpg')) }}" alt="OG Preview" style="width: 100%; height: 100%; object-fit: cover;">
                  </div>
                  <div class="p-3 bg-light">
                    <span class="text-uppercase text-muted d-block small" style="font-size: 11px;" id="simOgDomain">{{ parse_url(url('/'), PHP_URL_HOST) }}</span>
                    <h6 class="fw-bold mb-1 text-dark text-truncate" id="simOgTitle">{{ $settings->get('seo_og_title', 'XIVIG Maison Gazette') }}</h6>
                    <p class="text-muted small mb-0 text-truncate" id="simOgDesc">{{ $settings->get('seo_og_description', 'Chronicles of bespoke craftsmanship, luxury timepieces, and contemporary aesthetics.') }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Bottom Action Bar -->
    <div class="card border-0 shadow-sm rounded-4 p-3 d-flex flex-row justify-content-between align-items-center">
      <div class="text-muted small">
        <i class="bi bi-info-circle me-1 text-primary"></i> Changes take effect across public blog articles, Open Graph syndication, and search crawlers immediately.
      </div>
      <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-semibold shadow-sm" style="background-color: #5551ff; border-color: #5551ff;">
        <i class="bi bi-check-lg me-1"></i> Save All Changes
      </button>
    </div>
  </form>

</div>

@include('admin.partials.media-picker-modal')

@push('scripts')
<script>
  // Real-time character counter for Meta Description
  const descInput = document.getElementById('seo_meta_description');
  const countEl = document.getElementById('charCount');
  const barEl = document.getElementById('descProgressBar');
  const simDesc = document.getElementById('simGoogleDesc');
  const simOgDesc = document.getElementById('simOgDesc');

  function updateDescProgress() {
    if (!descInput) return;
    const len = descInput.value.length;
    if (countEl) countEl.innerText = len;

    const pct = Math.min(100, Math.round((len / 160) * 100));
    if (barEl) {
      barEl.style.width = pct + '%';
      if (len >= 120 && len <= 160) {
        barEl.className = 'progress-bar bg-success';
      } else if (len > 160) {
        barEl.className = 'progress-bar bg-danger';
      } else {
        barEl.className = 'progress-bar bg-warning';
      }
    }

    if (simDesc) simDesc.innerText = descInput.value;
    if (simOgDesc) simOgDesc.innerText = descInput.value;
  }

  if (descInput) {
    descInput.addEventListener('input', updateDescProgress);
    updateDescProgress();
  }

  // Real-time Title preview update
  const titleInput = document.getElementById('seo_meta_title');
  const simTitle = document.getElementById('simGoogleTitle');
  const ogTitleInput = document.getElementById('seo_og_title');
  const simOgTitle = document.getElementById('simOgTitle');

  if (titleInput) {
    titleInput.addEventListener('input', () => {
      if (simTitle) simTitle.innerText = titleInput.value;
      if (simOgTitle && (!ogTitleInput || !ogTitleInput.value)) {
        simOgTitle.innerText = titleInput.value;
      }
    });
  }

  if (ogTitleInput) {
    ogTitleInput.addEventListener('input', () => {
      if (simOgTitle) simOgTitle.innerText = ogTitleInput.value || titleInput.value;
    });
  }

  // Image preview live binding
  const ogImageInput = document.getElementById('seo_og_image');
  const ogImgPreview = document.getElementById('ogImagePreview');
  const simOgImg = document.getElementById('simOgImg');

  if (ogImageInput) {
    ogImageInput.addEventListener('input', () => {
      if (ogImgPreview) ogImgPreview.src = ogImageInput.value;
      if (simOgImg) simOgImg.src = ogImageInput.value;
    });
  }

  // Copy JSON-LD payload
  function copyJsonLd() {
    const code = document.getElementById('jsonLdCode').innerText;
    navigator.clipboard.writeText(code).then(() => {
      if (window.showToast) {
        window.showToast('Schema.org JSON-LD copied to clipboard!', 'success');
      } else {
        alert('Copied to clipboard!');
      }
    });
  }

  // robots.txt presets
  function loadRobotsPreset(type) {
    const sitemapUrl = '{{ url("/sitemap.xml") }}';
    const txtArea = document.getElementById('seo_robots_txt');
    if (!txtArea) return;

    if (type === 'allow_all') {
      txtArea.value = 'User-agent: *\nAllow: /\n\nSitemap: ' + sitemapUrl;
    } else if (type === 'hardened') {
      txtArea.value = 'User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /login\nDisallow: /register\nDisallow: /storage/app/backups/\n\nSitemap: ' + sitemapUrl;
    } else if (type === 'block_all') {
      txtArea.value = 'User-agent: *\nDisallow: /';
    }

    if (window.showToast) {
      window.showToast('robots.txt preset loaded!', 'info');
    }
  }

  // Media Picker Callback override for SEO page
  window.selectPickerAsset = function(id, url, name) {
    if (window.targetInputId) {
      const targetInput = document.getElementById(window.targetInputId);
      if (targetInput) {
        targetInput.value = url;
        targetInput.dispatchEvent(new Event('input'));
      }
    }
    const modalEl = document.getElementById('mediaPickerModal');
    const modalInstance = bootstrap.Modal.getInstance(modalEl);
    if (modalInstance) modalInstance.hide();
  };

  window.openMediaPickerModal = function(targetInputId, previewImgId) {
    window.targetInputId = targetInputId;
    window.targetPreviewId = previewImgId;
    const modal = new bootstrap.Modal(document.getElementById('mediaPickerModal'));
    modal.show();
  };
</script>
@endpush

@endsection
