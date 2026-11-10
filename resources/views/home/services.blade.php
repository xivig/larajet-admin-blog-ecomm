@extends('home')
@section('title', 'Services & Engineering Capabilities - Xivig')
@section('page-title', 'Our Services')
@section('page-heading', 'What We Deliver')
@section('content')

@php
    $services = [
        [
            'code' => '01',
            'title' => 'Web Platforms & Core Engines',
            'practice' => 'platforms',
            'practice_label' => 'Platforms & APIs',
            'icon' => 'bi-hdd-network-fill',
            'sla' => '99.99% Uptime',
            'timeline' => '4–8 Weeks',
            'team' => '2 Staff Engineers + 1 Architect',
            'summary' => 'High-concurrency multi-tenant SaaS platforms, distributed queues, and sub-15ms cached backends built on modern Laravel 13, Livewire, and Octane.',
            'overview' => 'We design horizontally scalable web platforms engineered for mission-critical traffic bursts, transactional integrity, and clean modular service boundaries.',
            'highlights' => [
                'Multi-tenant database partitioning & Octane worker tuning',
                'Idempotent Redis/Kafka job pipelines with dead-letter recovery',
                'Full PHPUnit / Feature test coverage & Canary CI/CD gates',
            ],
            'deliverables' => [
                'Complete domain architecture specification & ERD',
                'Production-hardened codebase with automated feature tests',
                'Zero-downtime deployment pipelines & telemetry dashboards',
                'Load test benchmarks verifying peak RPS SLAs',
            ],
            'stack' => ['Laravel 13', 'Livewire 3', 'Octane', 'Redis Cluster', 'Vite'],
        ],
        [
            'code' => '02',
            'title' => 'Interface & Product Design Systems',
            'practice' => 'design',
            'practice_label' => 'UI & Design',
            'icon' => 'bi-palette2',
            'sla' => 'WCAG AA Ready',
            'timeline' => '3–6 Weeks',
            'team' => 'Lead Product Designer + UI Engineer',
            'summary' => 'Conversion-engineered web interfaces featuring dual dark/light token Parity, fluid micro-interactions, and responsive cross-viewport ergonomics.',
            'overview' => 'Every interface component is crafted with mathematical spacing, fluid responsive breakpoints, instant keyboard navigation, and accessible contrast ratios across dark and light modes.',
            'highlights' => [
                'Dual Dark/Light glassmorphic design token architecture',
                'Modular Blade & interactive frontend component kits',
                '98+ Core Web Vitals & sub-second First Contentful Paint',
            ],
            'deliverables' => [
                'Tokenized stylesheet system & interactive UI primitive library',
                'Responsive multi-column templates & accessibility audit',
                'Cross-browser interactive prototyping & motion specs',
                'Clean CSS/JS bundle optimization guidelines',
            ],
            'stack' => ['Bootstrap 5.3', 'Design Tokens', 'CSS Grid / Flex', 'WCAG AA'],
        ],
        [
            'code' => '03',
            'title' => 'Enterprise Consulting & Audits',
            'practice' => 'consulting',
            'practice_label' => 'Consulting',
            'icon' => 'bi-diagram-3-fill',
            'sla' => 'CTO Advisory',
            'timeline' => '1–3 Weeks',
            'team' => 'Principal Systems Architect',
            'summary' => 'Deep-dive architectural auditing, query bottleneck elimination, legacy codebase modernization, and cloud TCO reduction roadmaps.',
            'overview' => 'Engage directly with principal engineers to diagnose database N+1 bottlenecks, memory leaks, monolith coupling, or security exposure before scaling up.',
            'highlights' => [
                'SQL query execution plan profiling & index optimization',
                'Monolith-to-modular domain decomposition roadmaps',
                'OWASP ASVS security review & dependency hygiene checks',
            ],
            'deliverables' => [
                'Comprehensive Technical Audit & Risk Scorecard PDF',
                'Prioritized P0/P1 remediation pull requests & benchmarks',
                'Infrastructure right-sizing & cost reduction projection',
                'Executive engineering briefing with Q&A handoff',
            ],
            'stack' => ['System Audits', 'SQL Profiling', 'OWASP', 'TCO Analysis'],
        ],
        [
            'code' => '04',
            'title' => 'Brand & Component Engineering',
            'practice' => 'design',
            'practice_label' => 'UI & Design',
            'icon' => 'bi-vector-pen',
            'sla' => 'Design Ops',
            'timeline' => '3–5 Weeks',
            'team' => 'Brand Architect + Frontend Specialist',
            'summary' => 'Cohesive digital visual languages, bespoke iconography systems, typography scales, and reusable multi-surface editorial kits.',
            'overview' => 'We bridge brand storytelling with production code so marketing sites, admin dashboards, and customer portals share one unmistakable visual identity.',
            'highlights' => [
                'Unified typography hierarchy (Outfit + Plus Jakarta Sans)',
                'Custom visual asset templates & aurora color palettes',
                'Reusable Blade UI sections with zero visual drift',
            ],
            'deliverables' => [
                'Comprehensive Brand & UI Engineering Styleguide',
                'Reusable layout wrappers, hero banners & CTA components',
                'Custom optimized WebP visual media kit',
                'Editorial & e-commerce template consistency suite',
            ],
            'stack' => ['Typography', 'UI Kits', 'WebP Pipeline', 'Blade Components'],
        ],
        [
            'code' => '05',
            'title' => 'Mobile Backends & Headless APIs',
            'practice' => 'platforms',
            'practice_label' => 'Platforms & APIs',
            'icon' => 'bi-phone-flip',
            'sla' => '< 15ms API P99',
            'timeline' => '4–7 Weeks',
            'team' => 'API Architect + Backend Engineers',
            'summary' => 'Resilient versioned REST & GraphQL gateways with Sanctum token scoping, WebSocket pub/sub channels, and signed payment webhooks.',
            'overview' => 'Built to serve mobile apps, SPAs, and external SaaS partners with strict request validation, adaptive rate limiters, and predictable JSON response contracts.',
            'highlights' => [
                'Sanctum & OAuth2 granular ability-scoped API tokens',
                'Real-time WebSocket event broadcasting via Echo / Reverb',
                'Signed HMAC-SHA256 webhooks with exponential retry backoff',
            ],
            'deliverables' => [
                'Versioned /api/v1 endpoints & typed JSON Resource classes',
                'Interactive OpenAPI / Postman API contract collection',
                'Sliding-window rate-limiting & abuse defense rules',
                'Payment gateway & third-party webhook orchestrators',
            ],
            'stack' => ['REST API', 'Sanctum', 'GraphQL', 'WebSockets', 'Webhooks'],
        ],
        [
            'code' => '06',
            'title' => 'Technical Content & DX Systems',
            'practice' => 'consulting',
            'practice_label' => 'Consulting',
            'icon' => 'bi-journal-code',
            'sla' => 'DX Acceleration',
            'timeline' => '2–4 Weeks',
            'team' => 'Developer Advocate + Staff Writer',
            'summary' => 'Interactive API reference portals, executable integration playbooks, SEO-indexed knowledge bases, and schema-verified tutorials.',
            'overview' => 'Accelerate partner and developer adoption with structured engineering portals, copy-ready SDK guides, and full JSON-LD Schema.org technical search visibility.',
            'highlights' => [
                'Complete Schema.org Article, FAQ & Breadcrumb metadata',
                'Interactive multi-format documentation & tutorial portals',
                'Automated sitemap generation & canonical SEO governance',
            ],
            'deliverables' => [
                'Developer onboarding playbooks & integration recipes',
                'Technical editorial calendar & SEO taxonomy framework',
                'Automated TOC, code walkthroughs & reading-time indexing',
                'Internal engineering runbooks & disaster recovery SOPs',
            ],
            'stack' => ['OpenAPI Docs', 'SEO Schema', 'Runbooks', 'Knowledge Hub'],
        ],
    ];

    $practiceTabs = [
        ['key' => 'all', 'label' => 'All Capabilities', 'icon' => 'bi-grid-1x2-fill', 'count' => count($services)],
        ['key' => 'platforms', 'label' => 'Platforms & APIs', 'icon' => 'bi-hdd-network', 'count' => 2],
        ['key' => 'design', 'label' => 'UI & Brand Systems', 'icon' => 'bi-palette', 'count' => 2],
        ['key' => 'consulting', 'label' => 'Consulting & DX', 'icon' => 'bi-diagram-3', 'count' => 2],
    ];

    $engagementModels = [
        [
            'tier' => 'Rapid Diagnostics',
            'name' => 'Architecture & Security Audit',
            'badge' => 'Fixed 2 Weeks',
            'recommended' => false,
            'desc' => 'Focused deep-dive into bottlenecks, query latency, deployment risks, and OWASP security hardening.',
            'points' => [
                'Full codebase & database index profiling',
                'Security headers, Auth & RBAC vulnerability review',
                'Actionable remediation roadmap + live briefing',
            ],
            'cta' => 'Request Audit Scope',
        ],
        [
            'tier' => 'Flagship Delivery',
            'name' => 'Dedicated Engineering Pod',
            'badge' => 'Most Popular',
            'recommended' => true,
            'desc' => 'Embedded senior Laravel architects, UI engineers, and DevOps specialists shipping weekly production releases.',
            'points' => [
                'Principal Architect + Full-Stack & UI Engineering Pod',
                'Weekly production increments with full test coverage',
                '24/7 telemetry monitoring & zero-downtime CI/CD',
            ],
            'cta' => 'Launch Dedicated Pod',
        ],
        [
            'tier' => 'Predictable Milestone',
            'name' => 'Turnkey System Build',
            'badge' => 'Fixed Scope',
            'recommended' => false,
            'desc' => 'End-to-end design, engineering, gateway integration, and launch of complete custom platforms or modules.',
            'points' => [
                'Locked milestone deliverables & deterministic timeline',
                'Custom SaaS, E-Commerce & Gateway orchestrations',
                'Complete source ownership + post-launch warranty',
            ],
            'cta' => 'Discuss Fixed Scope',
        ],
    ];
@endphp

<!-- Section Services Start -->
<section class="section py-5 position-relative">
    <div class="container">

        <!-- Practice Header -->
        <div class="row justify-content-between align-items-end mb-4">
            <div class="col-lg-8">
                <span class="badge-elite mb-2">Full-Lifecycle Engineering</span>
                <h2 class="font-weight-bold text-dark mb-2" style="font-size: clamp(1.75rem, 3vw, 2.5rem); letter-spacing: -0.025em;">
                    Tailored Technology Solutions
                </h2>
                <p class="text-muted mb-0" style="font-size: 1.05rem; max-width: 650px;">
                    From greenfield systems architecture and UI engineering to multi-gateway commerce and 24/7 production reliability.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end text-lg-right mt-3 mt-lg-0">
                <a href="{{ url('/project') }}" class="btn-elite btn-elite-outline btn-sm me-2 mr-2">
                    <i class="bi bi-briefcase me-1 mr-1"></i> Case Studies
                </a>
                <a href="{{ url('/contact') }}?subject={{ urlencode('Architecture Review Request') }}" class="btn-elite btn-elite-primary btn-sm">
                    <i class="bi bi-calendar-check me-1 mr-1"></i> Book Review
                </a>
            </div>
        </div>

        <!-- Engineering SLA Proof KPI Strip -->
        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-sm-6">
                <div class="portfolio-kpi-card">
                    <div class="portfolio-kpi-icon"><i class="bi bi-activity"></i></div>
                    <div>
                        <div class="portfolio-kpi-value">99.99%</div>
                        <div class="portfolio-kpi-label">Reliability SLA Target</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="portfolio-kpi-card">
                    <div class="portfolio-kpi-icon"><i class="bi bi-rocket-takeoff-fill"></i></div>
                    <div>
                        <div class="portfolio-kpi-value">&lt; 2 Wks</div>
                        <div class="portfolio-kpi-label">First Production Increment</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="portfolio-kpi-card">
                    <div class="portfolio-kpi-icon"><i class="bi bi-shield-fill-check"></i></div>
                    <div>
                        <div class="portfolio-kpi-value">100%</div>
                        <div class="portfolio-kpi-label">OWASP &amp; PSR-12 Tested</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="portfolio-kpi-card">
                    <div class="portfolio-kpi-icon"><i class="bi bi-clock-history"></i></div>
                    <div>
                        <div class="portfolio-kpi-value">24/7</div>
                        <div class="portfolio-kpi-label">SRE On-Call Telemetry</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Interactive Practice Filter & Search Bar -->
        <div class="portfolio-filter-bar mb-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap: 14px;">
                <div class="d-flex flex-wrap align-items-center" style="gap: 8px;" id="serviceFilterTabs">
                    @foreach($practiceTabs as $tab)
                        <button type="button"
                                class="portfolio-filter-btn {{ $tab['key'] === 'all' ? 'active' : '' }}"
                                data-practice="{{ $tab['key'] }}">
                            <i class="bi {{ $tab['icon'] }}"></i>
                            <span>{{ $tab['label'] }}</span>
                            <span class="portfolio-filter-count">{{ $tab['count'] }}</span>
                        </button>
                    @endforeach
                </div>

                <div class="position-relative" style="min-width: 230px; flex: 0 1 260px;">
                    <input type="text"
                           id="serviceSearchInput"
                           class="form-control form-control-elite"
                           style="border-radius: 999px; padding-left: 38px; height: 40px; font-size: 13.5px;"
                           placeholder="Filter service or tech..."
                           aria-label="Filter services">
                    <i class="bi bi-search text-muted position-absolute" style="left: 14px; top: 50%; transform: translateY(-50%); font-size: 13px; pointer-events: none;"></i>
                </div>
            </div>
        </div>

        <!-- 6 Elite Service Capability Cards -->
        <div class="row g-4" id="servicesGrid">
            @foreach($services as $idx => $svc)
                <div class="col-lg-4 col-md-6 service-grid-item"
                     data-practice="{{ $svc['practice'] }}"
                     data-search="{{ strtolower($svc['title'] . ' ' . $svc['practice_label'] . ' ' . $svc['summary'] . ' ' . implode(' ', $svc['stack'])) }}">
                    <article class="service-card-elite">
                        <div class="service-card-accent-line"></div>

                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="icon-box-elite mb-0">
                                <i class="bi {{ $svc['icon'] }}"></i>
                            </div>
                            <div class="d-flex flex-column align-items-end" style="gap: 6px;">
                                <span class="service-num-pill">PRACTICE {{ $svc['code'] }}</span>
                                <span class="portfolio-metric-chip">
                                    <i class="bi bi-lightning-charge-fill"></i> {{ $svc['sla'] }}
                                </span>
                            </div>
                        </div>

                        <h4 class="font-weight-bold text-dark mb-2" style="font-size: 1.2rem; letter-spacing: -0.015em;">
                            {{ $svc['title'] }}
                        </h4>

                        <p class="text-muted small mb-3" style="line-height: 1.6; min-height: 4.6em;">
                            {{ $svc['summary'] }}
                        </p>

                        <ul class="service-deliverables-list">
                            @foreach($svc['highlights'] as $hl)
                                <li>
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>{{ $hl }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <div class="portfolio-tech-pills mb-3">
                            @foreach(array_slice($svc['stack'], 0, 3) as $tech)
                                <span class="portfolio-tech-chip">{{ $tech }}</span>
                            @endforeach
                            @if(count($svc['stack']) > 3)
                                <span class="portfolio-tech-chip">+{{ count($svc['stack']) - 3 }}</span>
                            @endif
                        </div>

                        <div class="portfolio-footer-elite">
                            <button type="button"
                                    class="btn-read-elite inspect-service-btn"
                                    data-service-index="{{ $idx }}">
                                Scope Details <i class="bi bi-arrow-right"></i>
                            </button>
                            <span class="portfolio-version-badge">
                                <i class="bi bi-clock"></i> {{ $svc['timeline'] }}
                            </span>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>

        <!-- Empty Search Fallback -->
        <div id="serviceEmptyState" class="text-center py-5 d-none">
            <div class="portfolio-kpi-card d-inline-flex flex-column text-center px-5 py-4">
                <i class="bi bi-search text-primary mb-2" style="font-size: 2rem;"></i>
                <h5 class="font-weight-bold text-dark mb-1">No matching engineering capabilities</h5>
                <p class="text-muted small mb-3">Reset the filter or contact us directly for bespoke requirements.</p>
                <button type="button" id="resetServiceFilterBtn" class="btn-elite btn-elite-primary btn-sm mx-auto">Show All Services</button>
            </div>
        </div>

        <!-- 4-Phase Delivery Lifecycle Strip -->
        <div class="row g-4 mt-4 pt-3">
            <div class="col-12 mb-1">
                <span class="badge-elite mb-2">Delivery Lifecycle</span>
                <h3 class="font-weight-bold text-dark mb-0" style="letter-spacing: -0.02em;">From Blueprint to Global Scale in Four Disciplined Phases</h3>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="portfolio-pillar-card">
                    <span class="portfolio-pillar-step">Phase 01 &middot; Discovery</span>
                    <h6 class="font-weight-bold text-dark mb-2">Architecture Blueprint</h6>
                    <p class="text-muted small mb-0">Domain modeling, schema contracts, security posture analysis, and deterministic SLA budgets.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="portfolio-pillar-card">
                    <span class="portfolio-pillar-step">Phase 02 &middot; Execution</span>
                    <h6 class="font-weight-bold text-dark mb-2">Agile Core Sprints</h6>
                    <p class="text-muted small mb-0">PSR-12 modular PHP &amp; reactive frontend increments shipped behind feature flags every week.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="portfolio-pillar-card">
                    <span class="portfolio-pillar-step">Phase 03 &middot; Validation</span>
                    <h6 class="font-weight-bold text-dark mb-2">Hardening &amp; Benchmarks</h6>
                    <p class="text-muted small mb-0">Automated PHPUnit suites, SQL query tuning, penetration reviews, and synthetic load stress tests.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="portfolio-pillar-card">
                    <span class="portfolio-pillar-step">Phase 04 &middot; Operations</span>
                    <h6 class="font-weight-bold text-dark mb-2">Zero-Downtime SRE</h6>
                    <p class="text-muted small mb-0">Blue-green Canary deploys, live telemetry dashboards, encrypted backups, and priority support.</p>
                </div>
            </div>
        </div>

        <!-- Engagement Models Comparison -->
        <div class="row g-4 mt-4 pt-3">
            <div class="col-12 mb-1">
                <span class="badge-elite mb-2">Engagement Options</span>
                <h3 class="font-weight-bold text-dark mb-0" style="letter-spacing: -0.02em;">Flexible Collaboration Models</h3>
            </div>

            @foreach($engagementModels as $model)
                <div class="col-lg-4 col-md-6">
                    <div class="service-engagement-card {{ $model['recommended'] ? 'recommended' : '' }}">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="small font-weight-bold text-uppercase text-primary" style="letter-spacing: 0.06em;">{{ $model['tier'] }}</span>
                            <span class="badge {{ $model['recommended'] ? 'bg-primary text-white' : 'bg-primary-subtle text-primary' }} rounded-pill px-3 py-1" style="font-size: 11px;">
                                {{ $model['badge'] }}
                            </span>
                        </div>
                        <h4 class="font-weight-bold text-dark mb-2">{{ $model['name'] }}</h4>
                        <p class="text-muted small mb-3" style="line-height: 1.6;">{{ $model['desc'] }}</p>
                        <ul class="service-deliverables-list mb-4 flex-grow-1">
                            @foreach($model['points'] as $pt)
                                <li><i class="bi bi-check2-circle"></i><span>{{ $pt }}</span></li>
                            @endforeach
                        </ul>
                        <a href="{{ url('/contact') }}?subject={{ urlencode('Engagement Inquiry: ' . $model['name']) }}"
                           class="btn-elite {{ $model['recommended'] ? 'btn-elite-primary' : 'btn-elite-outline' }} w-100 justify-content-center text-center">
                            {{ $model['cta'] }} <i class="bi bi-arrow-right ms-1 ml-1"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Bottom CTA Box (Aurora Dark/Light Parity) -->
        <div class="portfolio-cta-elite mt-5 text-center">
            <span class="badge-elite mb-3">Custom Solutions</span>
            <h3 class="font-weight-bold text-dark mb-2" style="font-size: clamp(1.5rem, 2.5vw, 2rem); letter-spacing: -0.02em;">
                Have a Custom Architecture Requirement?
            </h3>
            <p class="text-muted lead mb-4 mx-auto" style="max-width: 640px; font-size: 1rem;">
                We architect custom microservices, automated queue workers, payment orchestrators, and multi-tenant enterprise platforms.
            </p>
            <div class="d-flex flex-wrap justify-content-center" style="gap: 14px;">
                <a href="{{ url('/contact') }}?subject={{ urlencode('Architecture Review & Scoping') }}" class="btn-elite btn-elite-primary">
                    <i class="bi bi-send-fill me-2 mr-2"></i> Schedule an Architecture Review
                </a>
                <a href="{{ url('/pricing') }}" class="btn-elite btn-elite-outline">
                    <i class="bi bi-tag-fill me-2 mr-2"></i> View Transparent Pricing
                </a>
            </div>
        </div>

    </div>
</section>
<!-- Section Services End -->

<!-- Interactive Service Scope Inspector Modal -->
<div class="modal fade portfolio-modal-elite" id="serviceScopeModal" tabindex="-1" aria-labelledby="serviceScopeModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-bottom px-4 py-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center" style="gap: 10px;">
                    <span class="service-num-pill" id="svcModalCode"></span>
                    <span class="portfolio-metric-chip" id="svcModalSla"></span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 p-md-5">
                <h3 class="font-weight-bold text-dark mb-2" id="serviceScopeModalTitle" style="letter-spacing: -0.02em;"></h3>
                <p class="text-muted mb-4" id="svcModalOverview" style="line-height: 1.65; font-size: 15px;"></p>

                <div class="row g-3 mb-4">
                    <div class="col-sm-4">
                        <div class="portfolio-modal-stat">
                            <div class="small text-muted mb-1">Practice Track</div>
                            <div class="font-weight-bold text-primary" id="svcModalPractice" style="font-size: 14px;"></div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="portfolio-modal-stat">
                            <div class="small text-muted mb-1">Typical Delivery</div>
                            <div class="font-weight-bold text-primary" id="svcModalTimeline" style="font-size: 14px;"></div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="portfolio-modal-stat">
                            <div class="small text-muted mb-1">Team Pod</div>
                            <div class="font-weight-bold text-primary" id="svcModalTeam" style="font-size: 14px;"></div>
                        </div>
                    </div>
                </div>

                <h6 class="font-weight-bold text-dark mb-3">
                    <i class="bi bi-check2-circle text-success me-2 mr-2"></i> Standard Engagement Deliverables
                </h6>
                <ul class="service-deliverables-list mb-4" id="svcModalDeliverables"></ul>

                <h6 class="font-weight-bold text-dark mb-2">
                    <i class="bi bi-stack text-primary me-2 mr-2"></i> Core Capabilities &amp; Stack
                </h6>
                <div class="portfolio-tech-pills mb-4" id="svcModalStack"></div>

                <div class="d-flex flex-wrap align-items-center justify-content-between pt-3 border-top" style="gap: 12px;">
                    <a id="svcModalQuoteLink" href="{{ url('/contact') }}" class="btn-elite btn-elite-primary btn-sm">
                        <i class="bi bi-calendar-check me-2 mr-2"></i> Request Scope Proposal
                    </a>
                    <button type="button" class="btn-elite btn-elite-outline btn-sm" data-bs-dismiss="modal" data-dismiss="modal">
                        Close Scope
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var servicesData = @json($services);
    var filterTabs = document.querySelectorAll('#serviceFilterTabs .portfolio-filter-btn');
    var searchInput = document.getElementById('serviceSearchInput');
    var gridItems = document.querySelectorAll('.service-grid-item');
    var emptyState = document.getElementById('serviceEmptyState');
    var resetBtn = document.getElementById('resetServiceFilterBtn');
    var activePractice = 'all';

    function applyServiceFilters() {
        var q = (searchInput ? searchInput.value : '').trim().toLowerCase();
        var visible = 0;

        gridItems.forEach(function(item) {
            var p = item.getAttribute('data-practice');
            var hay = item.getAttribute('data-search') || '';
            var show = (activePractice === 'all' || p === activePractice) && (!q || hay.indexOf(q) !== -1);
            item.classList.toggle('d-none', !show);
            if (show) visible++;
        });

        if (emptyState) {
            emptyState.classList.toggle('d-none', visible > 0);
        }
    }

    filterTabs.forEach(function(btn) {
        btn.addEventListener('click', function() {
            filterTabs.forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
            activePractice = this.getAttribute('data-practice') || 'all';
            applyServiceFilters();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', applyServiceFilters);
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            activePractice = 'all';
            if (searchInput) searchInput.value = '';
            filterTabs.forEach(function(b) {
                b.classList.toggle('active', b.getAttribute('data-practice') === 'all');
            });
            applyServiceFilters();
        });
    }

    var modalEl = document.getElementById('serviceScopeModal');
    var svcModal = null;

    function openScopeModal(svc) {
        if (!svc || !modalEl) return;
        document.getElementById('svcModalCode').textContent = 'PRACTICE ' + svc.code;
        document.getElementById('svcModalSla').innerHTML = '<i class="bi bi-lightning-charge-fill"></i> ' + svc.sla;
        document.getElementById('serviceScopeModalTitle').textContent = svc.title;
        document.getElementById('svcModalOverview').textContent = svc.overview;
        document.getElementById('svcModalPractice').textContent = svc.practice_label;
        document.getElementById('svcModalTimeline').textContent = svc.timeline;
        document.getElementById('svcModalTeam').textContent = svc.team;

        var dHtml = '';
        (svc.deliverables || []).forEach(function(d) {
            dHtml += '<li><i class="bi bi-check-circle-fill"></i><span>' + d + '</span></li>';
        });
        document.getElementById('svcModalDeliverables').innerHTML = dHtml;

        var sHtml = '';
        (svc.stack || []).forEach(function(t) {
            sHtml += '<span class="portfolio-tech-chip">' + t + '</span>';
        });
        document.getElementById('svcModalStack').innerHTML = sHtml;

        document.getElementById('svcModalQuoteLink').href = "{{ url('/contact') }}?subject=" + encodeURIComponent('Service Inquiry: ' + svc.title);

        if (window.bootstrap && window.bootstrap.Modal) {
            svcModal = svcModal || window.bootstrap.Modal.getOrCreateInstance(modalEl);
            svcModal.show();
        } else if (window.jQuery && window.jQuery(modalEl).modal) {
            window.jQuery(modalEl).modal('show');
        }
    }

    document.querySelectorAll('.inspect-service-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var idx = parseInt(this.getAttribute('data-service-index'), 10);
            if (!isNaN(idx) && servicesData[idx]) {
                openScopeModal(servicesData[idx]);
            }
        });
    });
});
</script>
@endsection
