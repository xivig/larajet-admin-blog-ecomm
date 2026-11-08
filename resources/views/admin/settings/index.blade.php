@extends('layouts.admin')

@section('title', 'Site Settings & System Engine - XivigAdmin PRO')

@section('content')
<div class="page-site-settings">

  <!-- Header -->
  <div class="page-header mb-4">
    <h1 class="page-title mb-1">System Engine &amp; Global Properties</h1>
    <nav class="breadcrumb mb-0">
      <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
      <span class="breadcrumb-item">Administration</span>
      <span class="breadcrumb-item active">Site Settings</span>
    </nav>
  </div>

  <!-- 1. Site Maintenance Card (Matching Screenshot) -->
  <div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
      <div class="d-flex align-items-center gap-2.5">
        <div class="d-flex align-items-center justify-content-center text-danger" style="width: 24px; height: 24px;">
          <i class="bi bi-dash-circle-fill fs-5"></i>
        </div>
        <div>
          <h5 class="fw-bold mb-0.5 text-danger">Site Maintenance</h5>
          <div class="text-muted small">Control public access during architectural refinement.</div>
        </div>
      </div>
      <form action="{{ route('admin.settings.maintenance') }}" method="POST" id="maintenanceForm">
        @csrf
        <div class="form-check form-switch fs-4 mb-0">
          <input class="form-check-input cursor-pointer" type="checkbox" name="site_maintenance" id="maintenanceToggle" value="1"
            {{ $settings->get('site_maintenance') === '1' ? 'checked' : '' }}
            onchange="document.getElementById('maintenanceForm').submit();">
        </div>
      </form>
    </div>
  </div>

  <!-- 2. External Integrations Card (Matching Screenshot) -->
  <div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
      <div class="d-flex align-items-center gap-2.5 mb-1">
        <div class="d-flex align-items-center justify-content-center text-secondary" style="width: 24px; height: 24px;">
          <i class="bi bi-dash-circle-fill fs-5"></i>
        </div>
        <h5 class="fw-bold mb-0">External Integrations</h5>
      </div>
      <div class="text-muted small mb-4 ms-4 ps-1">
        Configure third-party analytics, intelligence endpoints, and newsletter provider credentials.
      </div>

      <form action="{{ route('admin.settings.integrations') }}" method="POST">
        @csrf
        <div class="row g-4 mb-4">
          <!-- Google Analytics -->
          <div class="col-md-6">
            <label class="form-label text-uppercase fw-semibold text-muted" style="font-size: 11px; letter-spacing: 0.5px;">Google Analytics G-ID</label>
            <input type="text" name="google_analytics_id" class="form-control rounded-3 py-2 px-3" 
                   value="{{ $settings->get('google_analytics_id', 'G-XXXXXXXXXX') }}" placeholder="G-XXXXXXXXXX">
          </div>

          <!-- OpenAI API Key -->
          <div class="col-md-6">
            <label class="form-label text-uppercase fw-semibold text-muted" style="font-size: 11px; letter-spacing: 0.5px;">OpenAI API Key (AI Content)</label>
            <div class="input-group">
              <input type="password" name="openai_api_key" id="openaiKeyInput" class="form-control rounded-start-3 py-2 px-3 font-monospace" 
                     value="{{ $settings->get('openai_api_key', 'sk-proj-••••••••••••••••••••') }}">
              <button class="btn btn-outline-secondary rounded-end-3 px-3" type="button" onclick="togglePasswordVisibility('openaiKeyInput', this)">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>

          <!-- Mailchimp API Key -->
          <div class="col-md-6">
            <label class="form-label text-uppercase fw-semibold text-muted" style="font-size: 11px; letter-spacing: 0.5px;">Mailchimp API Key</label>
            <div class="input-group">
              <input type="password" name="mailchimp_api_key" id="mailchimpKeyInput" class="form-control rounded-start-3 py-2 px-3 font-monospace" 
                     value="{{ $settings->get('mailchimp_api_key', '••••••••••••••••••••-us1') }}">
              <button class="btn btn-outline-secondary rounded-end-3 px-3" type="button" onclick="togglePasswordVisibility('mailchimpKeyInput', this)">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>

          <!-- Mailchimp List ID -->
          <div class="col-md-6">
            <label class="form-label text-uppercase fw-semibold text-muted" style="font-size: 11px; letter-spacing: 0.5px;">Mailchimp List ID</label>
            <input type="text" name="mailchimp_list_id" class="form-control rounded-3 py-2 px-3" 
                   value="{{ $settings->get('mailchimp_list_id', 'XXXXXXXXXX') }}" placeholder="XXXXXXXXXX">
          </div>
        </div>

        <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold" style="background-color: #5551ff; border-color: #5551ff;">
          Sync Integrations
        </button>
      </form>
    </div>
  </div>

  <div class="row g-4">
    <!-- Left Column: Editorial Configuration & Branding -->
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-1">Editorial Configuration &amp; Branding</h5>
          <div class="text-muted small mb-4">
            System properties governing publication titles, SEO descriptions, and pagination.
          </div>

          <form action="{{ route('admin.settings.editorial') }}" method="POST">
            @csrf
            
            <!-- Gazette Title -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Gazette Title <span class="text-danger">*</span></label>
              <input type="text" name="gazette_title" class="form-control rounded-3 py-2 px-3" 
                     value="{{ $settings->get('gazette_title', 'XIVIG Maison Gazette') }}" required>
            </div>

            <!-- Gazette Tagline -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Gazette Tagline</label>
              <input type="text" name="gazette_tagline" class="form-control rounded-3 py-2 px-3" 
                     value="{{ $settings->get('gazette_tagline', 'The Haute Couture & Horology Journal') }}">
            </div>

            <!-- Editorial Synopsis & Search Description -->
            <div class="mb-4">
              <label class="form-label fw-semibold">Editorial Synopsis &amp; Search Description</label>
              <textarea name="editorial_synopsis" class="form-control rounded-3 p-3" rows="3">{{ $settings->get('editorial_synopsis', 'Chronicles of bespoke craftsmanship, luxury timepieces, and contemporary aesthetics.') }}</textarea>
            </div>

            <!-- 3 Column Row -->
            <div class="row g-3 mb-4">
              <div class="col-md-5">
                <label class="form-label fw-semibold">Editorial Contact Email <span class="text-danger">*</span></label>
                <input type="email" name="editorial_email" class="form-control rounded-3 py-2" 
                       value="{{ $settings->get('editorial_email', 'gazette@xivig.com') }}" required>
              </div>

              <div class="col-md-3">
                <label class="form-label fw-semibold">Stories Per Page <span class="text-danger">*</span></label>
                <input type="number" name="stories_per_page" class="form-control rounded-3 py-2" min="1" max="100" 
                       value="{{ $settings->get('stories_per_page', '9') }}" required>
              </div>

              <div class="col-md-4">
                <label class="form-label fw-semibold">Comments Moderation</label>
                <select name="comments_moderation" class="form-select rounded-3 py-2">
                  <option value="Auto-Approve" {{ $settings->get('comments_moderation') === 'Auto-Approve' ? 'selected' : '' }}>Auto-Approve</option>
                  <option value="Manual Review" {{ $settings->get('comments_moderation') === 'Manual Review' ? 'selected' : '' }}>Manual Review</option>
                  <option value="Strict Spam Filter" {{ $settings->get('comments_moderation') === 'Strict Spam Filter' ? 'selected' : '' }}>Strict Spam Filter</option>
                </select>
              </div>
            </div>

            <!-- Default System Timezone Box -->
            <div class="border rounded-4 p-3.5 mb-4 bg-light-subtle">
              <div class="d-flex flex-wrap justify-content-between align-items-center mb-2 gap-2">
                <div class="d-flex align-items-center gap-1.5 fw-semibold text-body">
                  <i class="bi bi-clock text-primary"></i>
                  <span>Default System Timezone</span>
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 fw-medium" style="font-size: 11px;">
                  Active: {{ $currentTimeInTz }}
                </span>
              </div>
              <div class="text-muted small mb-3">
                Global baseline timezone used for editorial scheduling, activity audit timestamps, and dispatches.
              </div>

              <select name="system_timezone" class="form-select rounded-3">
                <option value="Europe/Paris" {{ $activeTimezone === 'Europe/Paris' ? 'selected' : '' }}>(UTC+01:00/+02:00) Paris, Brussels, Geneva, Madrid</option>
                <option value="Europe/London" {{ $activeTimezone === 'Europe/London' ? 'selected' : '' }}>(UTC+00:00/+01:00) London, Dublin, Lisbon</option>
                <option value="America/New_York" {{ $activeTimezone === 'America/New_York' ? 'selected' : '' }}>(UTC-05:00/-04:00) Eastern Time (US &amp; Canada)</option>
                <option value="America/Los_Angeles" {{ $activeTimezone === 'America/Los_Angeles' ? 'selected' : '' }}>(UTC-08:00/-07:00) Pacific Time (US &amp; Canada)</option>
                <option value="Asia/Tokyo" {{ $activeTimezone === 'Asia/Tokyo' ? 'selected' : '' }}>(UTC+09:00) Tokyo, Osaka, Sapporo</option>
                <option value="Asia/Dubai" {{ $activeTimezone === 'Asia/Dubai' ? 'selected' : '' }}>(UTC+04:00) Dubai, Abu Dhabi</option>
                <option value="UTC" {{ $activeTimezone === 'UTC' ? 'selected' : '' }}>(UTC+00:00) Coordinated Universal Time</option>
              </select>
            </div>

            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold" style="background-color: #5551ff; border-color: #5551ff;">
              Save Editorial Configuration
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Right Column: Sidebar Widgets (Matching Screenshot) -->
    <div class="col-lg-4">
      <!-- 1. Syndication & Feeds Widget -->
      <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
          <h6 class="fw-bold text-uppercase text-muted mb-0" style="font-size: 11px; letter-spacing: 0.8px;">Syndication &amp; Feeds</h6>
        </div>
        <div class="card-body p-4 pt-2">
          <!-- RSS 2.0 Feed -->
          <div class="d-flex align-items-center justify-content-between py-2.5 border-bottom">
            <div>
              <div class="fw-bold text-body small">RSS 2.0 Feed</div>
              <div class="text-muted small" style="font-size: 11px;">
                <code>/feed.xml</code>
              </div>
            </div>
            <a href="{{ route('feed.xml') }}" target="_blank" class="btn btn-sm btn-link text-decoration-none text-primary fw-semibold p-0 d-inline-flex align-items-center gap-1" style="font-size: 12px;">
              <span>Open</span>
              <i class="bi bi-box-arrow-up-right" style="font-size: 11px;"></i>
            </a>
          </div>

          <!-- Dynamic XML Sitemap -->
          <div class="d-flex align-items-center justify-content-between pt-2.5">
            <div>
              <div class="fw-bold text-body small">Dynamic XML Sitemap</div>
              <div class="text-muted small" style="font-size: 11px;">
                <code>/sitemap.xml</code>
              </div>
            </div>
            <a href="{{ route('sitemap.xml') }}" target="_blank" class="btn btn-sm btn-link text-decoration-none text-primary fw-semibold p-0 d-inline-flex align-items-center gap-1" style="font-size: 12px;">
              <span>Open</span>
              <i class="bi bi-box-arrow-up-right" style="font-size: 11px;"></i>
            </a>
          </div>
        </div>
      </div>

      <!-- 2. Engine Information Widget -->
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
          <h6 class="fw-bold text-uppercase text-muted mb-0" style="font-size: 11px; letter-spacing: 0.8px;">Engine Information</h6>
        </div>
        <div class="card-body p-4 pt-2">
          <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
            <span class="text-muted small">Storage Driver</span>
            <span class="fw-semibold text-body small">{{ $storageDriver }}</span>
          </div>

          <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
            <span class="text-muted small">Database</span>
            <span class="fw-semibold text-body small">{{ $dbDriverName }}</span>
          </div>

          <div class="d-flex align-items-center justify-content-between pt-2">
            <span class="text-muted small">Rate Limiting</span>
            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-medium" style="font-size: 11px;">
              Armed
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

@push('scripts')
<script>
  function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.remove('bi-eye');
      icon.classList.add('bi-eye-slash');
    } else {
      input.type = 'password';
      icon.classList.remove('bi-eye-slash');
      icon.classList.add('bi-eye');
    }
  }
</script>
@endpush

@endsection
