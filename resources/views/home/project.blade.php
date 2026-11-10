@extends('home')
@section('title', 'Featured Portfolio - Xivig Platform')
@section('page-title', 'Selected Works')
@section('page-heading', 'Enterprise Portfolio')
@section('content')

@php
    $projects = [
        [
            'slug' => 'project-nexus-core',
            'title' => 'Project Nexus Core',
            'domain' => 'Cloud Microservices',
            'filter' => 'cloud',
            'status' => '99.99% SLA',
            'sector' => 'Enterprise FinOps',
            'metric' => '< 12ms P99',
            'version' => 'v4.2 Cluster',
            'image' => 'xivig-blog/images/portfolio/project-nexus-core.webp',
            'fallback' => 'xivig-blog/images/portfolio/project-nexus-core.jpg',
            'summary' => 'Distributed high-throughput transaction platform handling millions of asynchronous events daily with sub-second finality and automated dead-letter recovery.',
            'overview' => 'Architected for high-volume payment clearing and cross-region order orchestration. Decouples ingestion gateways from settlement workers via partitioned Kafka brokers and Redis atomic locks.',
            'stack' => ['Laravel 13', 'Apache Kafka', 'Redis Cluster', 'Octane Swoole', 'gRPC'],
            'stats' => [
                ['label' => 'Daily Throughput', 'value' => '48M+'],
                ['label' => 'P99 Latency', 'value' => '11.4ms'],
                ['label' => 'Availability', 'value' => '99.99%'],
            ],
            'deliverables' => [
                'Idempotent outbox pattern with deterministic retry queues',
                'Zero-downtime rolling schema migrations across sharded nodes',
                'OpenTelemetry end-to-end distributed trace correlation',
            ],
        ],
        [
            'slug' => 'apex-financial-engine',
            'title' => 'Apex Financial Engine',
            'domain' => 'Fintech Hub',
            'filter' => 'fintech',
            'status' => 'PCI-DSS Ready',
            'sector' => 'Capital Markets',
            'metric' => '$4.1B+ Settled',
            'version' => 'v3.8 Core',
            'image' => 'xivig-blog/images/portfolio/project-apex-financial.webp',
            'fallback' => 'xivig-blog/images/portfolio/project-apex-financial.jpg',
            'summary' => 'Real-time double-entry asset accounting, multi-currency FX liquidity routing, and automated regulatory audit compliance reporting.',
            'overview' => 'Engineered to power institutional treasury desks requiring immutable audit trails, sub-cent rounding safety, and instant balance reconciliation across 42 fiat and synthetic settlement corridors.',
            'stack' => ['Livewire 3', 'PostgreSQL', 'Laravel Sanctum', 'Immutable Ledger', 'Tailwind / BS5'],
            'stats' => [
                ['label' => 'Settlement Vol.', 'value' => '$4.1B'],
                ['label' => 'Active Currencies', 'value' => '42 FX'],
                ['label' => 'Audit Integrity', 'value' => '100%'],
            ],
            'deliverables' => [
                'Append-only cryptographic ledger with tamper-evident hashes',
                'Automated SWIFT / ISO-20022 reconciliation pipelines',
                'Role-segregated dual-approval treasury workflow engine',
            ],
        ],
        [
            'slug' => 'cybermatrix-identity-hub',
            'title' => 'CyberMatrix Identity Hub',
            'domain' => 'Security & Identity',
            'filter' => 'security',
            'status' => 'Zero-Trust',
            'sector' => 'IAM & Compliance',
            'metric' => 'FIDO2 / WebAuthn',
            'version' => 'v5.0 Shield',
            'image' => 'xivig-blog/images/portfolio/project-cybermatrix.webp',
            'fallback' => 'xivig-blog/images/portfolio/project-cybermatrix.jpg',
            'summary' => 'Zero-trust enterprise authentication gateway supporting hardware FIDO2 passkeys, adaptive TOTP 2FA, SAML 2.0 federation, and strict RBAC enforcement.',
            'overview' => 'Centralizes workforce and customer identity verification behind an isolated policy engine. Enforces risk-scored step-up challenges, hardware key binding, and continuous session attestation.',
            'stack' => ['Laravel Fortify', 'Jetstream', 'WebAuthn / FIDO2', 'OAuth2 / OIDC', 'RBAC Policy'],
            'stats' => [
                ['label' => 'Auth Checks / Day', 'value' => '19M+'],
                ['label' => 'Phishing Block', 'value' => '100%'],
                ['label' => 'Token Verify', 'value' => '4.2ms'],
            ],
            'deliverables' => [
                'Passwordless WebAuthn passkey & hardware security key enrollment',
                'Multi-tenant SAML 2.0 / OIDC SSO identity broker',
                'Real-time geo-velocity anomaly & session revocation engine',
            ],
        ],
        [
            'slug' => 'velocity-reactive-studio',
            'title' => 'Velocity Reactive Studio',
            'domain' => 'Reactive UI',
            'filter' => 'ui',
            'status' => 'Live Production',
            'sector' => 'Design Engineering',
            'metric' => '60 FPS Edge UI',
            'version' => 'v2.9 Studio',
            'image' => 'xivig-blog/images/portfolio/project-velocity-studio.webp',
            'fallback' => 'xivig-blog/images/portfolio/project-velocity-studio.jpg',
            'summary' => 'Instantaneous server-driven workspace canvas featuring optimistic state synchronization, multiplayer cursors, and WebSocket broadcast pipelines.',
            'overview' => 'Bridges server-rendered resilience with desktop-class reactivity. Built for enterprise product operations teams collaborating simultaneously across design tokens, workflows, and live previews.',
            'stack' => ['Laravel Echo', 'Reverb / Pusher', 'Vite 6', 'Livewire 3', 'Alpine.js'],
            'stats' => [
                ['label' => 'Concurrent Users', 'value' => '120k'],
                ['label' => 'Sync Latency', 'value' => '18ms'],
                ['label' => 'Lighthouse Perf', 'value' => '99/100'],
            ],
            'deliverables' => [
                'CRDT-inspired optimistic state conflict resolution layer',
                'Zero-bloat server-driven component hydration via Vite 6',
                'WCAG AA accessible dual-theme design token architecture',
            ],
        ],
        [
            'slug' => 'horizon-telemetry-stream',
            'title' => 'Horizon Telemetry Stream',
            'domain' => 'Telemetry Streaming',
            'filter' => 'telemetry',
            'status' => '50k Pulses/s',
            'sector' => 'Industrial IoT',
            'metric' => 'Timescale Edge',
            'version' => 'v3.4 Grid',
            'image' => 'xivig-blog/images/portfolio/project-horizon-iot.webp',
            'fallback' => 'xivig-blog/images/portfolio/project-horizon-iot.jpg',
            'summary' => 'Time-series ingestion platform processing 50,000 live edge sensor pulses per second with continuous rolling aggregations and automated anomaly alerting.',
            'overview' => 'Connects globally distributed industrial hardware arrays into a real-time observability fabric. Performs window-based Z-score anomaly detection before persisting compressed hypertables.',
            'stack' => ['TimescaleDB', 'MQTT Broker', 'Laravel Horizon', 'WebSockets', 'Prometheus'],
            'stats' => [
                ['label' => 'Peak Ingest', 'value' => '50k/s'],
                ['label' => 'Connected Nodes', 'value' => '280k'],
                ['label' => 'Storage Saved', 'value' => '84%'],
            ],
            'deliverables' => [
                'Backpressure-aware MQTT ingestion workers on Laravel Horizon',
                'Sub-second continuous rollup materialized views',
                'Automated multi-channel pager & webhook alert routing',
            ],
        ],
        [
            'slug' => 'sovereign-cloud-orchestration',
            'title' => 'Sovereign Cloud Orchestration',
            'domain' => 'Cloud Native',
            'filter' => 'cloud',
            'status' => 'Multi-Region',
            'sector' => 'Platform Infra',
            'metric' => '0s Downtime',
            'version' => 'v6.1 Mesh',
            'image' => 'xivig-blog/images/portfolio/project-sovereign-cloud.webp',
            'fallback' => 'xivig-blog/images/portfolio/project-sovereign-cloud.jpg',
            'summary' => 'Automated multi-region Kubernetes failover mesh featuring geopolitical data residency pinning, encrypted service-to-service mTLS, and Canary releases.',
            'overview' => 'Empowers regulated enterprises to deploy sovereign cloud footprints across US, EU, and APAC availability zones using declarative Infrastructure-as-Code and automated progressive delivery.',
            'stack' => ['Kubernetes', 'Docker', 'Terraform', 'Envoy mTLS', 'GitHub Actions'],
            'stats' => [
                ['label' => 'Global Regions', 'value' => '14 Zones'],
                ['label' => 'Failover RTO', 'value' => '< 2.5s'],
                ['label' => 'Canary Rollback', 'value' => 'Auto'],
            ],
            'deliverables' => [
                'Declarative Terraform multi-region cluster provisioning modules',
                'Automated Canary health gating tied to real-time error budgets',
                'Strict EU/APAC data sovereignty routing & encrypted backups',
            ],
        ],
    ];

    $filterTabs = [
        ['key' => 'all', 'label' => 'All Deployments', 'icon' => 'bi-grid-1x2-fill', 'count' => count($projects)],
        ['key' => 'cloud', 'label' => 'Cloud & Infra', 'icon' => 'bi-cloud-check-fill', 'count' => 2],
        ['key' => 'fintech', 'label' => 'Fintech', 'icon' => 'bi-bank2', 'count' => 1],
        ['key' => 'security', 'label' => 'Zero-Trust Security', 'icon' => 'bi-shield-lock-fill', 'count' => 1],
        ['key' => 'ui', 'label' => 'Reactive UI', 'icon' => 'bi-window-stack', 'count' => 1],
        ['key' => 'telemetry', 'label' => 'IoT & Telemetry', 'icon' => 'bi-activity', 'count' => 1],
    ];
@endphp

<!-- Section Portfolio Start -->
<section class="section py-5 position-relative">
    <div class="container">

        <!-- Intro Header (Compact so Hero & Content flow smoothly without duplicated huge display titles) -->
        <div class="row justify-content-between align-items-end mb-4">
            <div class="col-lg-8">
                <span class="badge-elite mb-2">Architectural Showcases</span>
                <h2 class="font-weight-bold text-dark mb-2" style="font-size: clamp(1.75rem, 3vw, 2.5rem); letter-spacing: -0.025em;">
                    Pioneering Enterprise Deployments
                </h2>
                <p class="text-muted mb-0" style="font-size: 1.05rem; max-width: 640px;">
                    Selected mission-critical platforms architected for extreme throughput, zero-trust security, and uninterrupted global scale.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end text-lg-right mt-3 mt-lg-0">
                <a href="{{ url('/contact') }}" class="btn-elite btn-elite-primary btn-sm">
                    <i class="bi bi-calendar-check me-2 mr-2"></i> Book Architecture Review
                </a>
            </div>
        </div>

        <!-- Executive Proof KPI Bar -->
        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-sm-6 mb-3 mb-lg-0">
                <div class="portfolio-kpi-card">
                    <div class="portfolio-kpi-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div>
                        <div class="portfolio-kpi-value">99.99%</div>
                        <div class="portfolio-kpi-label">Verified Global SLA</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-3 mb-lg-0">
                <div class="portfolio-kpi-card">
                    <div class="portfolio-kpi-icon">
                        <i class="bi bi-cpu-fill"></i>
                    </div>
                    <div>
                        <div class="portfolio-kpi-value">140M+</div>
                        <div class="portfolio-kpi-label">Daily Events Routed</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-3 mb-sm-0">
                <div class="portfolio-kpi-card">
                    <div class="portfolio-kpi-icon">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </div>
                    <div>
                        <div class="portfolio-kpi-value">&lt;14ms</div>
                        <div class="portfolio-kpi-label">Median Edge Latency</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="portfolio-kpi-card">
                    <div class="portfolio-kpi-icon">
                        <i class="bi bi-globe-americas"></i>
                    </div>
                    <div>
                        <div class="portfolio-kpi-value">14 Zones</div>
                        <div class="portfolio-kpi-label">Sovereign Mesh Clusters</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Interactive Filter & Search Bar -->
        <div class="portfolio-filter-bar mb-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap: 14px;">
                <div class="d-flex flex-wrap align-items-center" style="gap: 8px;" id="portfolioFilterPills">
                    @foreach($filterTabs as $tab)
                        <button type="button"
                                class="portfolio-filter-btn {{ $tab['key'] === 'all' ? 'active' : '' }}"
                                data-filter="{{ $tab['key'] }}">
                            <i class="bi {{ $tab['icon'] }}"></i>
                            <span>{{ $tab['label'] }}</span>
                            <span class="portfolio-filter-count">{{ $tab['count'] }}</span>
                        </button>
                    @endforeach
                </div>

                <div class="position-relative" style="min-width: 230px; flex: 0 1 260px;">
                    <input type="text"
                           id="portfolioSearchInput"
                           class="form-control form-control-elite"
                           style="border-radius: 999px; padding-left: 38px; height: 40px; font-size: 13.5px;"
                           placeholder="Filter stack or title..."
                           aria-label="Filter projects">
                    <i class="bi bi-search text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%); font-size: 13px; pointer-events: none;"></i>
                </div>
            </div>
        </div>

        <!-- Portfolio Cards Grid (wrapped in .portfolio-gallery for Magnific Popup gallery mode) -->
        <div class="row portfolio-gallery" id="portfolioGrid">
            @foreach($projects as $index => $project)
                <div class="col-lg-4 col-md-6 mb-4 portfolio-grid-item"
                     data-category="{{ $project['filter'] }}"
                     data-search="{{ strtolower($project['title'] . ' ' . $project['domain'] . ' ' . $project['sector'] . ' ' . implode(' ', $project['stack'])) }}">
                    <article class="portfolio-card-elite">
                        <!-- Visual Cover + Lightbox Link + Decoupled Badges -->
                        <div class="portfolio-img-wrap">
                            <a href="{{ asset($project['image']) }}"
                               class="popup-gallery"
                               title="{{ $project['title'] }} — {{ $project['domain'] }}">
                                <img src="{{ asset($project['image']) }}"
                                     alt="{{ $project['title'] }}"
                                     loading="{{ $index < 3 ? 'eager' : 'lazy' }}"
                                     onerror="this.onerror=null; this.src='{{ asset($project['fallback']) }}';">
                            </a>

                            <div class="portfolio-img-scrim"></div>

                            <!-- Decoupled Header Badges: Live Status Left, Domain Category Right -->
                            <div class="portfolio-card-badges">
                                <span class="badge-portfolio-status">
                                    <span class="badge-dot pulse" style="background: #34d399;"></span>
                                    {{ $project['status'] }}
                                </span>

                                <span class="badge-elite-category" title="{{ $project['domain'] }}">
                                    {{ $project['domain'] }}
                                </span>
                            </div>

                            <!-- Hover Zoom Hint -->
                            <span class="portfolio-zoom-pill">
                                <i class="bi bi-arrows-fullscreen"></i> Zoom Preview
                            </span>
                        </div>

                        <!-- Card Content -->
                        <div class="portfolio-body-elite">
                            <div class="portfolio-meta-row">
                                <span><i class="bi bi-buildings me-1 mr-1 text-primary"></i> {{ $project['sector'] }}</span>
                                <span class="portfolio-metric-chip">
                                    <i class="bi bi-lightning-charge-fill"></i> {{ $project['metric'] }}
                                </span>
                            </div>

                            <h3 class="portfolio-title-elite">{{ $project['title'] }}</h3>

                            <p class="portfolio-desc-elite">{{ $project['summary'] }}</p>

                            <!-- Dark/Light Aware Tech Stack Pills -->
                            <div class="portfolio-tech-pills">
                                @foreach(array_slice($project['stack'], 0, 3) as $tech)
                                    <span class="portfolio-tech-chip">{{ $tech }}</span>
                                @endforeach
                                @if(count($project['stack']) > 3)
                                    <span class="portfolio-tech-chip" title="{{ implode(', ', array_slice($project['stack'], 3)) }}">
                                        +{{ count($project['stack']) - 3 }} more
                                    </span>
                                @endif
                            </div>

                            <!-- Card Footer -->
                            <div class="portfolio-footer-elite">
                                <button type="button"
                                        class="btn-read-elite open-case-study-btn"
                                        data-project-index="{{ $index }}">
                                    Case Study <i class="bi bi-arrow-right"></i>
                                </button>
                                <span class="portfolio-version-badge">
                                    <i class="bi bi-git"></i> {{ $project['version'] }}
                                </span>
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>

        <!-- Empty State (Shown only when search/filter finds zero projects) -->
        <div id="portfolioEmptyState" class="text-center py-5 d-none">
            <div class="portfolio-kpi-card d-inline-flex flex-column text-center px-5 py-4">
                <i class="bi bi-search text-primary mb-2" style="font-size: 2rem;"></i>
                <h5 class="font-weight-bold text-dark mb-1">No matching architectures found</h5>
                <p class="text-muted small mb-3">Try clearing your search term or switching back to all deployments.</p>
                <button type="button" id="resetPortfolioFilterBtn" class="btn-elite btn-elite-primary btn-sm mx-auto">
                    Show All Deployments
                </button>
            </div>
        </div>

        <!-- Engineering Delivery Standards -->
        <div class="row g-4 mt-4 pt-3">
            <div class="col-12 mb-1">
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <div>
                        <span class="badge-elite mb-2">Delivery Methodology</span>
                        <h3 class="font-weight-bold text-dark mb-0" style="letter-spacing: -0.02em;">Built on Production-First Engineering Standards</h3>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="portfolio-pillar-card">
                    <span class="portfolio-pillar-step">01 / Zero-Downtime Rollouts</span>
                    <h5 class="font-weight-bold text-dark mb-2">Blue-Green &amp; Canary Mesh</h5>
                    <p class="text-muted small mb-0">
                        Every deployment ships behind automated synthetic probes, progressive traffic shifting, and instant atomic rollback triggers.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="portfolio-pillar-card">
                    <span class="portfolio-pillar-step">02 / Full-Stack Telemetry</span>
                    <h5 class="font-weight-bold text-dark mb-2">Observable by Default</h5>
                    <p class="text-muted small mb-0">
                        Distributed OpenTelemetry traces, real-time queue depth budgets, and P99 latency dashboards ship integrated from day one.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-12">
                <div class="portfolio-pillar-card">
                    <span class="portfolio-pillar-step">03 / Hardened Security</span>
                    <h5 class="font-weight-bold text-dark mb-2">Zero-Trust Perimeters</h5>
                    <p class="text-muted small mb-0">
                        Strict RBAC isolation, hardware passkey enforcement, CSP headers, and immutable audit logs protect every operational plane.
                    </p>
                </div>
            </div>
        </div>

        <!-- Bottom Portfolio CTA (Native Dark & Light Glassmorphism) -->
        <div class="portfolio-cta-elite mt-5 text-center">
            <span class="badge-elite mb-3">Custom Architecture</span>
            <h3 class="font-weight-bold text-dark mb-2" style="font-size: clamp(1.5rem, 2.5vw, 2rem); letter-spacing: -0.02em;">
                Have an Ambitious Platform in Mind?
            </h3>
            <p class="text-muted lead mb-4 mx-auto" style="max-width: 620px; font-size: 1rem;">
                Partner directly with our principal systems architects to design, benchmark, and ship your next high-scale deployment.
            </p>
            <div class="d-flex flex-wrap justify-content-center" style="gap: 14px;">
                <a href="{{ url('/contact') }}" class="btn-elite btn-elite-primary">
                    <i class="bi bi-send-fill me-2 mr-2"></i> Request Consultation
                </a>
                <a href="{{ url('/pricing') }}" class="btn-elite btn-elite-outline">
                    <i class="bi bi-layers me-2 mr-2"></i> Explore Capability Tiers
                </a>
            </div>
        </div>

    </div>
</section>
<!-- Section Portfolio End -->

<!-- Interactive Case Study Modal -->
<div class="modal fade portfolio-modal-elite" id="portfolioCaseStudyModal" tabindex="-1" aria-labelledby="caseStudyModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="portfolio-modal-hero">
                <img id="caseStudyModalImg" src="" alt="Architecture Preview">
                <div class="portfolio-img-scrim"></div>
                <button type="button"
                        class="btn-close btn-close-white position-absolute"
                        data-bs-dismiss="modal"
                        data-dismiss="modal"
                        aria-label="Close"
                        style="top: 16px; right: 16px; z-index: 5; background-color: rgba(15,23,42,0.75); padding: 10px; border-radius: 50%; opacity: 1;"></button>
                <div class="position-absolute d-flex align-items-center flex-wrap" style="bottom: 16px; left: 20px; right: 20px; z-index: 4; gap: 10px;">
                    <span class="badge-portfolio-status" id="caseStudyModalStatus"></span>
                    <span class="badge-elite-category" id="caseStudyModalDomain" style="margin-left: 0 !important;"></span>
                </div>
            </div>

            <div class="modal-body p-4 p-md-5">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-2" style="gap: 8px;">
                    <span class="small font-weight-bold text-primary text-uppercase" id="caseStudyModalSector" style="letter-spacing: 0.06em;"></span>
                    <span class="portfolio-version-badge" id="caseStudyModalVersion"></span>
                </div>

                <h3 class="font-weight-bold text-dark mb-3" id="caseStudyModalTitle" style="letter-spacing: -0.02em;"></h3>
                <p class="text-muted mb-4" id="caseStudyModalOverview" style="line-height: 1.65; font-size: 15px;"></p>

                <!-- 3 Benchmark Metrics -->
                <div class="row g-3 mb-4" id="caseStudyModalStats"></div>

                <!-- Engineering Deliverables -->
                <div class="mb-4">
                    <h6 class="font-weight-bold text-dark mb-3">
                        <i class="bi bi-check2-circle text-primary me-2 mr-2"></i> Core Architectural Deliverables
                    </h6>
                    <ul class="list-unstyled mb-0" id="caseStudyModalDeliverables" style="display: grid; gap: 10px;"></ul>
                </div>

                <!-- Full Technology Stack -->
                <div class="mb-4">
                    <h6 class="font-weight-bold text-dark mb-2">
                        <i class="bi bi-stack text-primary me-2 mr-2"></i> Verified Technology Stack
                    </h6>
                    <div class="portfolio-tech-pills mb-0" id="caseStudyModalStack"></div>
                </div>

                <div class="d-flex flex-wrap align-items-center justify-content-between pt-3 border-top" style="gap: 12px;">
                    <a id="caseStudyModalQuoteLink" href="{{ url('/contact') }}" class="btn-elite btn-elite-primary btn-sm">
                        <i class="bi bi-chat-square-text-fill me-2 mr-2"></i> Architect a Similar System
                    </a>
                    <button type="button" class="btn-elite btn-elite-outline btn-sm" data-bs-dismiss="modal" data-dismiss="modal">
                        Close Preview
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var projectsData = @json($projects);
    var filterButtons = document.querySelectorAll('.portfolio-filter-btn');
    var searchInput = document.getElementById('portfolioSearchInput');
    var gridItems = document.querySelectorAll('.portfolio-grid-item');
    var emptyState = document.getElementById('portfolioEmptyState');
    var resetBtn = document.getElementById('resetPortfolioFilterBtn');
    var activeFilter = 'all';

    function applyPortfolioFilters() {
        var query = (searchInput ? searchInput.value : '').trim().toLowerCase();
        var visibleCount = 0;

        gridItems.forEach(function(item) {
            var cat = item.getAttribute('data-category');
            var haystack = item.getAttribute('data-search') || '';
            var matchesCategory = (activeFilter === 'all' || cat === activeFilter);
            var matchesSearch = (!query || haystack.indexOf(query) !== -1);

            if (matchesCategory && matchesSearch) {
                item.classList.remove('d-none');
                visibleCount++;
            } else {
                item.classList.add('d-none');
            }
        });

        if (emptyState) {
            if (visibleCount === 0) {
                emptyState.classList.remove('d-none');
            } else {
                emptyState.classList.add('d-none');
            }
        }
    }

    filterButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            filterButtons.forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
            activeFilter = this.getAttribute('data-filter') || 'all';
            applyPortfolioFilters();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', applyPortfolioFilters);
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            activeFilter = 'all';
            if (searchInput) searchInput.value = '';
            filterButtons.forEach(function(b) {
                b.classList.toggle('active', b.getAttribute('data-filter') === 'all');
            });
            applyPortfolioFilters();
        });
    }

    // Case Study Modal Handler (Bootstrap 5 native + jQuery fallback)
    var modalEl = document.getElementById('portfolioCaseStudyModal');
    var bsModal = null;

    function openCaseStudyModal(project) {
        if (!project || !modalEl) return;

        var baseAsset = "{{ asset('') }}";
        var modalImg = document.getElementById('caseStudyModalImg');
        modalImg.onerror = function() {
            this.onerror = null;
            this.src = baseAsset + (project.fallback || project.image);
        };
        modalImg.src = baseAsset + project.image;
        modalImg.alt = project.title;
        document.getElementById('caseStudyModalStatus').innerHTML = '<span class="badge-dot pulse" style="background: #34d399;"></span> ' + project.status;
        document.getElementById('caseStudyModalDomain').textContent = project.domain;
        document.getElementById('caseStudyModalSector').textContent = project.sector;
        document.getElementById('caseStudyModalVersion').innerHTML = '<i class="bi bi-git"></i> ' + project.version;
        document.getElementById('caseStudyModalTitle').textContent = project.title;
        document.getElementById('caseStudyModalOverview').textContent = project.overview;

        var statsHtml = '';
        (project.stats || []).forEach(function(stat) {
            statsHtml += '<div class="col-4">' +
                '<div class="portfolio-modal-stat">' +
                    '<div class="font-weight-bold text-primary" style="font-size: 1.25rem; font-family: var(--font-heading);">' + stat.value + '</div>' +
                    '<div class="small text-muted" style="font-size: 11.5px;">' + stat.label + '</div>' +
                '</div>' +
            '</div>';
        });
        document.getElementById('caseStudyModalStats').innerHTML = statsHtml;

        var delivHtml = '';
        (project.deliverables || []).forEach(function(item) {
            delivHtml += '<li class="d-flex align-items-start small text-muted">' +
                '<i class="bi bi-check-circle-fill text-success me-2 mr-2 mt-1 flex-shrink-0"></i>' +
                '<span>' + item + '</span>' +
            '</li>';
        });
        document.getElementById('caseStudyModalDeliverables').innerHTML = delivHtml;

        var stackHtml = '';
        (project.stack || []).forEach(function(tech) {
            stackHtml += '<span class="portfolio-tech-chip">' + tech + '</span>';
        });
        document.getElementById('caseStudyModalStack').innerHTML = stackHtml;

        document.getElementById('caseStudyModalQuoteLink').href = "{{ url('/contact') }}?subject=" + encodeURIComponent('Architecture Inquiry: ' + project.title);

        if (window.bootstrap && window.bootstrap.Modal) {
            bsModal = bsModal || window.bootstrap.Modal.getOrCreateInstance(modalEl);
            bsModal.show();
        } else if (window.jQuery && window.jQuery(modalEl).modal) {
            window.jQuery(modalEl).modal('show');
        }
    }

    document.querySelectorAll('.open-case-study-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var idx = parseInt(this.getAttribute('data-project-index'), 10);
            if (!isNaN(idx) && projectsData[idx]) {
                openCaseStudyModal(projectsData[idx]);
            }
        });
    });
});
</script>
@endsection
