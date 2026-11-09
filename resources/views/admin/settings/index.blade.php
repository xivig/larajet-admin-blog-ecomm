@extends('layouts.admin')

@section('title', 'Site Settings & System Engine - XivigAdmin PRO')

@section('content')
<div class="page-site-settings">

  <!-- Header -->
  <div class="page-header mb-3">
    <h1 class="page-title mb-1">System Engine &amp; Global Properties</h1>
    <nav class="breadcrumb mb-0">
      <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item">Dashboard</a>
      <span class="breadcrumb-item">Administration</span>
      <span class="breadcrumb-item active">Site Settings</span>
    </nav>
  </div>

  <!-- Settings Navigation Tabs -->
  <div class="d-flex align-items-center justify-content-between mb-4">
    <ul class="nav nav-pills gap-2 bg-body-secondary p-1.5 rounded-4 shadow-2xs">
      <li class="nav-item">
        <a class="nav-link active rounded-pill px-3 py-1.5 fw-semibold" href="{{ route('admin.settings.index') }}">
          <i class="bi bi-gear-fill me-1"></i> General Settings &amp; Engine
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link rounded-pill px-3 py-1.5 fw-semibold text-body" href="{{ route('admin.settings.seo') }}">
          <i class="bi bi-globe-americas me-1"></i> SEO &amp; Meta Suite <span class="badge bg-primary-subtle text-primary rounded-pill ms-1" style="font-size: 0.65rem;">PRO</span>
        </a>
      </li>
    </ul>
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
            <div class="border rounded-4 p-3.5 mb-4 bg-light-subtle shadow-sm">
              <div class="d-flex flex-wrap justify-content-between align-items-center mb-2 gap-2">
                <div class="d-flex align-items-center gap-2 fw-semibold text-body">
                  <div class="rounded-2 p-1.5 bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center">
                    <i class="bi bi-clock-history"></i>
                  </div>
                  <span>Default System Timezone</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                  <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1" id="btnDetectTz" style="font-size: 11px;" title="Automatically detect your device timezone">
                    <i class="bi bi-geo-alt"></i>
                    <span>Detect Mine</span>
                  </button>
                  <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1.5 fw-medium" id="tzActiveBadge" style="font-size: 11px;">
                    Active: {{ $currentTimeInTz }}
                  </span>
                </div>
              </div>

              <div class="text-muted small mb-3">
                Global baseline timezone used across the entire application for post scheduling, article publish dates, activity logs, dispatches, and notifications.
              </div>

              <!-- Filter / Search Box -->
              <div class="input-group input-group-sm mb-2">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="tzSearchInput" class="form-control border-start-0 ps-0" placeholder="Quick search timezone (e.g. New York, London, Tokyo, Kolkata, Berlin, UTC)..." autocomplete="off">
                <button type="button" class="btn btn-outline-secondary border-start-0" id="tzClearFilter" style="display: none;" title="Clear filter">
                  <i class="bi bi-x-lg"></i>
                </button>
              </div>

              <!-- Timezone Select with ALL Timezones -->
              <select name="system_timezone" id="systemTimezoneSelect" class="form-select rounded-3 py-2 mb-3" style="max-height: 280px;">
                @foreach ($allTimezones as $region => $tzList)
                  <optgroup label="{{ $region }} ({{ count($tzList) }})">
                    @foreach ($tzList as $tz)
                      <option value="{{ $tz['id'] }}" data-offset="{{ $tz['formatted_offset'] }}" {{ $activeTimezone === $tz['id'] ? 'selected' : '' }}>
                        {{ $tz['label'] }}
                      </option>
                    @endforeach
                  </optgroup>
                @endforeach
              </select>

              <!-- Live Information and Instant Apply Bar -->
              <div class="p-2.5 rounded-3 border bg-white d-flex flex-wrap align-items-center justify-content-between gap-2" id="tzInfoBar">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                  <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1" style="font-size: 11px;" id="tzRegionBadge">
                    Region: {{ explode('/', $activeTimezone)[0] ?? 'UTC' }}
                  </span>
                  <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2.5 py-1" style="font-size: 11px;" id="tzOffsetBadge">
                    UTC Offset
                  </span>
                  <span class="small text-muted d-inline-flex align-items-center gap-1">
                    <i class="bi bi-clock"></i> Local Time: <strong class="text-body font-monospace" id="tzLiveClock">{{ $currentTimeInTz }}</strong>
                  </span>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1" id="btnApplyTimezoneAjax" style="font-size: 12px;">
                  <i class="bi bi-check2-circle"></i>
                  <span>Apply Now</span>
                </button>
              </div>
            </div>

            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold" style="background-color: #5551ff; border-color: #5551ff;">
              Save Editorial Configuration
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Right Column: Production CMS Engine Telemetry & Architecture -->
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

      <!-- 2. Engine & Local Storage Widget -->
      <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
          <h6 class="fw-bold text-uppercase text-muted mb-0" style="font-size: 11px; letter-spacing: 0.8px;">Engine &amp; Storage Architecture</h6>
          <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 10px;">PHP 8.3/8.4</span>
        </div>
        <div class="card-body p-4 pt-2">
          <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
            <span class="text-muted small">PHP Runtime</span>
            <span class="fw-semibold text-body small font-monospace">{{ $phpVersion }}</span>
          </div>

          <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
            <span class="text-muted small">Database Connection</span>
            <span class="fw-semibold text-body small">{{ $dbDriverName }}</span>
          </div>

          <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
            <span class="text-muted small">Local Storage Disk</span>
            <span class="fw-semibold text-body small">{{ $storageDriver }}</span>
          </div>

          <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
            <span class="text-muted small">Storage Symlink</span>
            <span class="badge {{ $storageLinkConnected ? 'bg-success-subtle text-success border-success-subtle' : 'bg-primary-subtle text-primary border-primary-subtle' }} border rounded-pill px-2 py-0.5" style="font-size: 11px;">
              {{ $storageLinkConnected ? 'Connected' : 'Standalone (/uploads)' }}
            </span>
          </div>

          <div class="d-flex align-items-center justify-content-between pt-2">
            <span class="text-muted small">Uploads Directory</span>
            <span class="badge {{ $uploadsWritable ? 'bg-success-subtle text-success border-success-subtle' : 'bg-danger-subtle text-danger border-danger-subtle' }} border rounded-pill px-2 py-0.5" style="font-size: 11px;">
              {{ $uploadsWritable ? 'Writable (0755)' : 'Read-Only' }}
            </span>
          </div>
        </div>
      </div>

      <!-- 3. Database Cache Widget -->
      <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center gap-1.5">
            <i class="bi bi-hdd-stack text-primary"></i>
            <h6 class="fw-bold text-uppercase text-muted mb-0" style="font-size: 11px; letter-spacing: 0.8px;">Database Cache</h6>
          </div>
          <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5" style="font-size: 10px;">
            Driver: {{ ucfirst($cacheDriver) }}
          </span>
        </div>
        <div class="card-body p-4 pt-2">
          <p class="text-muted small mb-3" style="font-size: 12px;">
            Accelerates site settings, navigation trees, and syndication feeds via the dedicated <code>cache</code> database table.
          </p>

          <div class="d-flex align-items-center justify-content-between py-2 border-bottom mb-3">
            <span class="text-muted small">Cached Records in DB</span>
            <span class="fw-bold text-primary small font-monospace">{{ $cacheCount }} item(s)</span>
          </div>

          <div class="d-flex gap-2">
            <form action="{{ route('admin.settings.cache.warm') }}" method="POST" class="flex-grow-1">
              @csrf
              <button type="submit" class="btn btn-sm btn-outline-primary w-100 rounded-pill py-1.5" style="font-size: 12px;">
                <i class="bi bi-fire me-1"></i> Pre-Warm Cache
              </button>
            </form>
            <form action="{{ route('admin.settings.cache.purge') }}" method="POST" class="flex-grow-1">
              @csrf
              <button type="submit" class="btn btn-sm btn-outline-danger w-100 rounded-pill py-1.5" style="font-size: 12px;" onclick="return confirm('Flush all database cache entries?')">
                <i class="bi bi-trash3 me-1"></i> Flush Cache
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- 4. Database Queue & Background Workers Widget -->
      <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center gap-1.5">
            <i class="bi bi-cpu text-primary"></i>
            <h6 class="fw-bold text-uppercase text-muted mb-0" style="font-size: 11px; letter-spacing: 0.8px;">Database Queue</h6>
          </div>
          <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5" style="font-size: 10px;">
            Driver: {{ ucfirst($queueDriver) }}
          </span>
        </div>
        <div class="card-body p-4 pt-2">
          <p class="text-muted small mb-3" style="font-size: 12px;">
            Asynchronously executes emails, image optimization, and background CMS processing in the <code>jobs</code> table.
          </p>

          <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
            <span class="text-muted small">Pending Jobs</span>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-0.5 font-monospace">
              {{ $pendingJobsCount }}
            </span>
          </div>

          <div class="d-flex align-items-center justify-content-between py-2 border-bottom mb-3">
            <span class="text-muted small">Failed Jobs</span>
            <span class="badge {{ $failedJobsCount > 0 ? 'bg-danger text-white' : 'bg-light text-muted border' }} rounded-pill px-2.5 py-0.5 font-monospace">
              {{ $failedJobsCount }}
            </span>
          </div>

          <div class="d-flex flex-column gap-2">
            <form action="{{ route('admin.settings.queue.work') }}" method="POST">
              @csrf
              <button type="submit" class="btn btn-sm btn-outline-primary w-100 rounded-pill py-1.5" style="font-size: 12px;">
                <i class="bi bi-play-circle me-1"></i> Process Queue Batch Now
              </button>
            </form>

            @if ($failedJobsCount > 0)
              <div class="d-flex gap-2">
                <form action="{{ route('admin.settings.queue.retry') }}" method="POST" class="flex-grow-1">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-outline-warning w-100 rounded-pill py-1" style="font-size: 11px;">
                    <i class="bi bi-arrow-clockwise me-1"></i> Retry Failed
                  </button>
                </form>
                <form action="{{ route('admin.settings.queue.flush') }}" method="POST" class="flex-grow-1">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-outline-secondary w-100 rounded-pill py-1" style="font-size: 11px;" onclick="return confirm('Clear all failed jobs?')">
                    <i class="bi bi-x-circle me-1"></i> Clear Failed
                  </button>
                </form>
              </div>
            @endif
          </div>
        </div>
      </div>

      <!-- 5. Automated Scheduler & Cron Jobs Widget -->
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center gap-1.5">
            <i class="bi bi-calendar-check text-primary"></i>
            <h6 class="fw-bold text-uppercase text-muted mb-0" style="font-size: 11px; letter-spacing: 0.8px;">Scheduler &amp; Cron Jobs</h6>
          </div>
          <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 10px;">
            Armed
          </span>
        </div>
        <div class="card-body p-4 pt-2">
          <p class="text-muted small mb-2" style="font-size: 12px;">
            Executes scheduled post releases, queue maintenance, cache pre-warming, and session pruning automatically.
          </p>

          <!-- Registered Tasks List -->
          <div class="mb-3 border rounded-3 p-2 bg-light-subtle" style="max-height: 180px; overflow-y: auto;">
            <div class="fw-semibold text-muted text-uppercase mb-1.5" style="font-size: 10px; letter-spacing: 0.5px;">Registered Tasks ({{ count($schedulerEvents) }})</div>
            @forelse ($schedulerEvents as $ev)
              <div class="py-1.5 border-bottom small d-flex align-items-center justify-content-between gap-2" style="font-size: 11px;">
                <span class="text-truncate text-body" title="{{ $ev['command'] }}">
                  <i class="bi bi-terminal me-1 text-primary"></i>{{ $ev['command'] }}
                </span>
                <span class="badge bg-secondary-subtle text-secondary font-monospace" style="font-size: 10px;">{{ $ev['expression'] }}</span>
              </div>
            @empty
              <div class="text-muted small py-2 text-center" style="font-size: 11px;">No scheduled tasks registered.</div>
            @endforelse
          </div>

          <!-- Crontab Command Helper -->
          <div class="mb-3">
            <label class="form-label text-muted fw-semibold mb-1" style="font-size: 11px;">Production Linux Crontab Entry:</label>
            <div class="input-group input-group-sm">
              <input type="text" class="form-control font-monospace bg-light" id="crontabSnippet" style="font-size: 10px;" value="* * * * * cd {{ base_path() }} && php artisan schedule:run >> /dev/null 2>&1" readonly>
              <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('crontabSnippet').value); alert('Crontab entry copied to clipboard!');" title="Copy Crontab">
                <i class="bi bi-copy"></i>
              </button>
            </div>
          </div>

          <form action="{{ route('admin.settings.scheduler.run') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-sm btn-primary w-100 rounded-pill py-1.5 fw-semibold" style="font-size: 12px; background-color: #5551ff; border-color: #5551ff;">
              <i class="bi bi-arrow-repeat me-1"></i> Run Scheduler Now (Manual Test)
            </button>
          </form>
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

  // Timezone Manager Interactive Functionality
  (function() {
    const tzSelect = document.getElementById('systemTimezoneSelect');
    const tzSearchInput = document.getElementById('tzSearchInput');
    const tzClearBtn = document.getElementById('tzClearFilter');
    const tzActiveBadge = document.getElementById('tzActiveBadge');
    const tzLiveClock = document.getElementById('tzLiveClock');
    const tzRegionBadge = document.getElementById('tzRegionBadge');
    const tzOffsetBadge = document.getElementById('tzOffsetBadge');
    const btnDetectTz = document.getElementById('btnDetectTz');
    const btnApplyAjax = document.getElementById('btnApplyTimezoneAjax');

    if (!tzSelect) return;

    // 1. Live Time Formatter for any IANA timezone
    function formatTimeInZone(timeZone) {
      try {
        const d = new Date();
        const formatter = new Intl.DateTimeFormat('en-US', {
          timeZone: timeZone,
          month: 'short',
          day: '2-digit',
          year: 'numeric',
          hour: '2-digit',
          minute: '2-digit',
          second: '2-digit',
          hour12: true
        });
        return formatter.format(d);
      } catch (e) {
        return new Date().toLocaleTimeString();
      }
    }

    // 2. Update UI badges and live clocks
    function updateTzDisplay() {
      const selectedOption = tzSelect.options[tzSelect.selectedIndex];
      if (!selectedOption) return;
      const zoneId = selectedOption.value;
      const offset = selectedOption.getAttribute('data-offset') || '';
      const region = zoneId.split('/')[0] || 'UTC';

      const formatted = formatTimeInZone(zoneId);
      if (tzLiveClock) tzLiveClock.textContent = formatted;
      if (tzRegionBadge) tzRegionBadge.textContent = 'Region: ' + region;
      if (tzOffsetBadge) tzOffsetBadge.textContent = offset || 'UTC Offset';
    }

    tzSelect.addEventListener('change', updateTzDisplay);

    // Initial update
    updateTzDisplay();

    // 1-second live ticking clock
    setInterval(function() {
      const selectedOption = tzSelect.options[tzSelect.selectedIndex];
      if (selectedOption) {
        const formatted = formatTimeInZone(selectedOption.value);
        if (tzLiveClock) tzLiveClock.textContent = formatted;
      }
    }, 1000);

    // 3. Quick Filter Search
    if (tzSearchInput) {
      tzSearchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        if (tzClearBtn) tzClearBtn.style.display = query ? '' : 'none';

        const optgroups = tzSelect.querySelectorAll('optgroup');
        optgroups.forEach(function(group) {
          let visibleCount = 0;
          const options = group.querySelectorAll('option');
          options.forEach(function(opt) {
            const text = opt.textContent.toLowerCase();
            const val = opt.value.toLowerCase();
            const matches = !query || text.includes(query) || val.includes(query);
            opt.hidden = !matches;
            if (matches) visibleCount++;
          });
          group.hidden = (visibleCount === 0);
        });
      });

      if (tzClearBtn) {
        tzClearBtn.addEventListener('click', function() {
          tzSearchInput.value = '';
          tzSearchInput.dispatchEvent(new Event('input'));
          tzSearchInput.focus();
        });
      }
    }

    // 4. Detect Device Browser Timezone
    if (btnDetectTz) {
      btnDetectTz.addEventListener('click', function() {
        try {
          const detected = Intl.DateTimeFormat().resolvedOptions().timeZone;
          let matched = false;
          for (let i = 0; i < tzSelect.options.length; i++) {
            if (tzSelect.options[i].value === detected) {
              tzSelect.selectedIndex = i;
              matched = true;
              break;
            }
          }
          if (matched) {
            updateTzDisplay();
            if (typeof Swal !== 'undefined') {
              Swal.fire({
                icon: 'success',
                title: 'Timezone Detected!',
                text: 'Selected "' + detected + '" from your device clock.',
                timer: 2500,
                showConfirmButton: false
              });
            }
          } else {
            alert('Detected timezone "' + detected + '" is not in standard list.');
          }
        } catch (e) {
          alert('Could not detect device timezone.');
        }
      });
    }

    // 5. Instant Apply Button (AJAX)
    if (btnApplyAjax) {
      btnApplyAjax.addEventListener('click', function() {
        const selectedTz = tzSelect.value;
        const btn = this;
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Applying...';

        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                      document.querySelector('input[name="_token"]')?.value;

        fetch('{{ route("admin.settings.timezone") }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
          },
          body: JSON.stringify({ system_timezone: selectedTz })
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
          btn.disabled = false;
          btn.innerHTML = originalHtml;
          if (data.success) {
            if (tzActiveBadge) {
              tzActiveBadge.textContent = 'Active: ' + data.current_time;
              tzActiveBadge.classList.add('bg-success-subtle', 'text-success', 'border-success-subtle');
              tzActiveBadge.classList.remove('bg-primary-subtle', 'text-primary', 'border-primary-subtle');
              setTimeout(function() {
                tzActiveBadge.classList.remove('bg-success-subtle', 'text-success', 'border-success-subtle');
                tzActiveBadge.classList.add('bg-primary-subtle', 'text-primary', 'border-primary-subtle');
              }, 3000);
            }
            if (typeof Swal !== 'undefined') {
              Swal.fire({
                icon: 'success',
                title: 'Timezone Applied!',
                text: data.message + ' Current server time: ' + data.current_time,
                timer: 2500,
                showConfirmButton: false
              });
            }
          } else {
            alert(data.message || 'Error updating timezone.');
          }
        })
        .catch(function(err) {
          btn.disabled = false;
          btn.innerHTML = originalHtml;
          alert('Failed to update timezone. Please check server logs.');
        });
      });
    }
  })();
</script>
@endpush

@endsection
