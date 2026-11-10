<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Category;
use App\Models\Picture;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * 50 realistic editorial & engineering articles (5 per category across 10 categories).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function articles(): array
    {
        return [
            // 1. Technology (5)
            [
                'category' => 'Technology',
                'tag' => 'Laravel 13',
                'title' => 'Mastering Reactive Cloud Blueprints with Laravel 13',
                'excerpt' => 'How modern PHP teams combine Octane workers, lightweight queues, and typed DTO pipelines to deliver sub-25ms API responses at production scale.',
                'takeaways' => [
                    'Keep request lifecycles stateless by isolating caches and session stores from long-lived worker memory.',
                    'Validate edge payloads through explicit Form Requests before dispatching domain action pipelines.',
                    'Pair Horizon supervisor pools with exponential backoff strategies for webhook and payment bursts.',
                ],
                'featured' => true,
                'sticky' => true,
                'views' => 4820,
                'video_url' => 'https://www.youtube.com/watch?v=tgbNymZ7vqY',
                'external_url' => 'https://laravel.com/docs',
            ],
            [
                'category' => 'Technology',
                'tag' => 'WebAssembly',
                'title' => 'WebAssembly Components Inside Edge API Workers',
                'excerpt' => 'Portable WASI modules let engineering platforms sandbox third-party extensions without provisioning heavyweight microVMs per tenant.',
                'takeaways' => [
                    'Compile user-supplied image filters and validation predicates once into deterministic component graphs.',
                    'Enforce fuel metering limits on each invocation so runaway loops terminate within single-digit milliseconds.',
                    'Cache instantiated engine modules per worker thread to keep cold starts under 2ms.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 1940,
            ],
            [
                'category' => 'Technology',
                'tag' => 'TypeScript',
                'title' => 'End-to-End Type Contracts Without Heavy Codegen',
                'excerpt' => 'Schema-first inference patterns synchronize backend resource payloads and frontend UI state without brittle build steps.',
                'takeaways' => [
                    'Treat validation rules as the single canonical contract shared across transport boundaries.',
                    'Detect silent enum drift during pull-request CI checks before shipping backward-incompatible payloads.',
                    'Eliminate defensive null chaining inside core view models via strict narrowed response shapes.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 1620,
            ],
            [
                'category' => 'Technology',
                'tag' => 'Performance',
                'title' => 'Why N+1 Query Audits Still Outperform Expensive Redis Upgrades',
                'excerpt' => 'A practical profiling study showing how eager-loading scopes and composite indexes slashed median database CPU utilization by 68%.',
                'takeaways' => [
                    'Assert query count budgets directly inside feature tests for every high-traffic listing endpoint.',
                    'Prefer covering b-tree indexes on published timestamp and status columns over brute-force cache warming.',
                    'Use read-only query explain gates to catch full table scans during peer review.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 2380,
            ],
            [
                'category' => 'Technology',
                'tag' => 'Edge Computing',
                'title' => 'RISC-V Server Silicon Enters Heterogeneous Edge Clusters',
                'excerpt' => 'Telemetry ingestion pipelines are adopting open-ISA co-processors for predictable power envelopes at regional points of presence.',
                'takeaways' => [
                    'Match compression and TLS termination workloads to low-wattage edge compute blades.',
                    'Maintain multi-arch OCI container manifests across amd64, arm64, and riscv64 targets automatically.',
                    'Benchmark tail latency rather than peak synthetic throughput when sizing distributed gateways.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 1215,
            ],

            // 2. Cloud & Infrastructure (5)
            [
                'category' => 'Cloud & Infrastructure',
                'tag' => 'Kubernetes',
                'title' => 'Zero-Downtime Node Pool Rotation on Production Kubernetes',
                'excerpt' => 'Pod disruption budgets, preStop drain hooks, and graceful connection shedding prevent 502 spikes during weekly kernel patching.',
                'takeaways' => [
                    'Set terminationGracePeriodSeconds higher than ingress keep-alive timeouts so in-flight HTTP requests finish cleanly.',
                    'Configure PodDisruptionBudgets with minAvailable guarantees on every stateful and ingress controller.',
                    'Automate surge node provisioning before cordoning aging spot instances.',
                ],
                'featured' => true,
                'sticky' => false,
                'views' => 3610,
                'video_url' => 'https://player.vimeo.com/video/76979871',
                'external_url' => 'https://kubernetes.io/docs',
            ],
            [
                'category' => 'Cloud & Infrastructure',
                'tag' => 'FinOps',
                'title' => 'Cutting Cloud NAT Gateway Egress Costs 62% With VPC Endpoints',
                'excerpt' => 'Re-routing object storage and container registry pulls across private backbone links erased tens of thousands in monthly transfer fees.',
                'takeaways' => [
                    'Audit top NAT flow-log destinations weekly—artifact registries and logging agents typically dominate traffic.',
                    'Attach gateway endpoints in every private subnet route table before tuning instance sizes.',
                    'Place intra-AZ affinity rules on chatty stream brokers to avoid cross-zone billing.',
                ],
                'featured' => true,
                'sticky' => false,
                'views' => 4190,
            ],
            [
                'category' => 'Cloud & Infrastructure',
                'tag' => 'Docker',
                'title' => 'Slim Distroless Containers Without Losing Emergency Diagnostics',
                'excerpt' => 'Ephemeral debug containers and reproducible multi-stage builds shrink attack surface while keeping live triage fast.',
                'takeaways' => [
                    'Strip package managers, shells, and compilers from final runtime layers using scratch or distroless bases.',
                    'Attach temporary kubectl debug sidecars during incidents without restarting impacted application pods.',
                    'Pin exact digest SHAs in base images to guarantee repeatable security scans.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 1850,
            ],
            [
                'category' => 'Cloud & Infrastructure',
                'tag' => 'Edge Computing',
                'title' => 'Geo-Distributed Cache Invalidations in Under 180 Milliseconds',
                'excerpt' => 'Replacing rigid TTL expirations with surrogate-key pub/sub fan-out keeps storefront pricing and headlines globally consistent.',
                'takeaways' => [
                    'Tag HTML fragments and JSON responses with fine-grained entity keys for selective purge bursts.',
                    'Combine stale-while-revalidate directives with regional origin shields during flash traffic.',
                    'Simulate regional PoP disconnects to verify fallback stale serving stays graceful.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 2090,
            ],
            [
                'category' => 'Cloud & Infrastructure',
                'tag' => 'PostgreSQL',
                'title' => 'Multi-Region Active-Active Failover Without Split-Brain Writes',
                'excerpt' => 'Leased consensus coordinators and fencing tokens protect ledger integrity across continental cloud zones.',
                'takeaways' => [
                    'Scope single-writer leases per tenant shard when full synchronous Raft across continents adds too much RTT.',
                    'Store monotonic fencing epoch IDs on storage layers to reject stale zombie writers cleanly.',
                    'Drill region failover quarter-over-quarter under synthetic load.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 2740,
            ],

            // 3. AI & Machine Learning (5)
            [
                'category' => 'AI & Machine Learning',
                'tag' => 'LLM Inference',
                'title' => 'Quantizing 70B Models for Sub-50ms Time-to-First-Token',
                'excerpt' => 'Combining FP8 KV-cache compression with continuous batching doubles GPU throughput without degrading reasoning fidelity.',
                'takeaways' => [
                    'Separate prefill-heavy nodes from decode-bound replicas to avoid head-of-line stalls on long contexts.',
                    'Run task-specific regression evals after every quantization step rather than trusting aggregate perplexity alone.',
                    'Warm speculative draft heads alongside main models to accelerate structured JSON output.',
                ],
                'featured' => true,
                'sticky' => false,
                'views' => 4480,
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            ],
            [
                'category' => 'AI & Machine Learning',
                'tag' => 'RAG Pipelines',
                'title' => 'Hybrid BM25 and Dense Vector Retrieval for Technical Docs',
                'excerpt' => 'Reciprocal rank fusion captures both exact error-code identifiers and semantic concepts where pure embeddings miss.',
                'takeaways' => [
                    'Chunk documentation along markdown heading hierarchies so parent section titles travel with every vector.',
                    'Pair fast lexical sparse matches with lightweight cross-encoder reranking on the top 25 candidates.',
                    'Track citation grounding accuracy continuously against curated golden Q&A benchmarks.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 3150,
            ],
            [
                'category' => 'AI & Machine Learning',
                'tag' => 'LLM Inference',
                'title' => 'Structured Tool Calling Verification in Enterprise Agents',
                'excerpt' => 'Strict JSON schema decoding and dry-run authorization gates stop autonomous workflows from executing malformed mutations.',
                'takeaways' => [
                    'Restrict destructive agent actions behind idempotent policy checks outside model prompt weights.',
                    'Log full tool arguments, latency histograms, and model revisions for deterministic replay audits.',
                    'Require explicit human approval steps whenever spend or permission thresholds trigger.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 2640,
            ],
            [
                'category' => 'AI & Machine Learning',
                'tag' => 'RAG Pipelines',
                'title' => 'Evaluating Summarization Drift Across Prompt Releases',
                'excerpt' => 'Building lightweight automated pairwise judges catches subtle hallucinated metrics before customer deployments.',
                'takeaways' => [
                    'Version system instructions alongside code commits so regressions pinpoint exact prompt diffs.',
                    'Include adversarial contradiction documents in staging datasets to stress-test abstention behavior.',
                    'Monitor output token bloat alongside refusal rate spikes per model checkpoint.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 1760,
            ],
            [
                'category' => 'AI & Machine Learning',
                'tag' => 'ClickHouse',
                'title' => 'Real-Time Feature Stores Built on Columnar Streaming Tables',
                'excerpt' => 'Materializing rolling user-session aggregates continuously removes training-serving skew between offline notebooks and APIs.',
                'takeaways' => [
                    'Compute point-in-time historical windows with identical SQL definitions shared across training and inference.',
                    'Use Redis point lookups in front of ClickHouse materialized rollups for sub-5ms feature hydration.',
                    'Monitor null-feature drift every five minutes when upstream schema events change.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 1520,
            ],

            // 4. Cybersecurity (5)
            [
                'category' => 'Cybersecurity',
                'tag' => 'Zero Trust',
                'title' => 'Implementing Passkey WebAuthn Flows Across Enterprise SSO',
                'excerpt' => 'Phishing-resistant hardware credentials slash credential-stuffing tickets while cutting engineering sign-in friction in half.',
                'takeaways' => [
                    'Bind enterprise recovery tokens to verified manager approvals or backed-up secondary hardware keys.',
                    'Pair short-lived mTLS workload identities with user WebAuthn assertions on admin consoles.',
                    'Eliminate SMS one-time fallbacks cleanly from high-privilege production pathways.',
                ],
                'featured' => true,
                'sticky' => false,
                'views' => 3890,
                'external_url' => 'https://owasp.org/www-project-top-ten/',
            ],
            [
                'category' => 'Cybersecurity',
                'tag' => 'DevSecOps',
                'title' => 'Signed Container Provenance With Sigstore and Admission Gates',
                'excerpt' => 'Keyless OIDC artifact attestation blocks unsigned third-party binaries from ever scheduling on production clusters.',
                'takeaways' => [
                    'Attach SBOM and build-runner provenance directly inside CI release workflows.',
                    'Enforce cluster admission webhook validation for critical production namespaces first in audit mode.',
                    'Automate dependency base-image rebuild triggers upon critical CVE disclosures.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 2210,
            ],
            [
                'category' => 'Cybersecurity',
                'tag' => 'Zero Trust',
                'title' => 'Hardening Multi-Tenant APIs Against IDOR and Mass Assignment',
                'excerpt' => 'Global tenant scoping policies and strict allow-listed DTO hydration close the highest-impact SaaS privilege bugs.',
                'takeaways' => [
                    'Never pass `$request->all()` directly into Eloquent `update()` or `create()` calls.',
                    'Authorize ownership through Laravel Policies checking tenant scope on every route model binding.',
                    'Add automated cross-tenant negative assertions inside feature CI test suites.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 2970,
            ],
            [
                'category' => 'Cybersecurity',
                'tag' => 'DevSecOps',
                'title' => 'Runtime eBPF Anomaly Detection Without Kernel Module Risk',
                'excerpt' => 'Verifiable ring-buffer probes inspect suspicious unexpected child shells and outbound sockets with under 1% CPU overhead.',
                'takeaways' => [
                    'Alert immediately when stateless web containers spawn interactive shells or unexpected curl transfers.',
                    'Baseline legitimate syscall profiles inside staging load runs to prevent noisy false-positive pages.',
                    'Correlate runtime alerts directly with pod labels, git SHAs, and recent deployment traces.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 1680,
            ],
            [
                'category' => 'Cybersecurity',
                'tag' => 'API Gateway',
                'title' => 'Adaptive Rate Limiting & Webhook HMAC Replay Protection',
                'excerpt' => 'Dual-window sliding counters paired with timestamped HMAC signatures harden public APIs and billing callbacks.',
                'takeaways' => [
                    'Reject webhook callbacks whose timestamp skew exceeds 300 seconds to neutralize capture replays.',
                    'Verify signatures using constant-time `hash_equals` against raw unmodified HTTP request payloads.',
                    'Apply stricter per-IP burst limits cleanly on login, checkout, and search endpoints.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 2040,
            ],

            // 5. Software Architecture (5)
            [
                'category' => 'Software Architecture',
                'tag' => 'Clean Architecture',
                'title' => 'Modular Monoliths Over Premature Microservices',
                'excerpt' => 'Enforcing strict in-process module interfaces gives teams fast refactoring agility without distributed network complexity.',
                'takeaways' => [
                    'Prohibit direct cross-module table joins in favor of well-defined service facades and events.',
                    'Split a network service only when scaling profile, compliance isolation, or deploy cadence truly diverges.',
                    'Enforce architectural import boundaries cleanly using static analysis rules in CI.',
                ],
                'featured' => true,
                'sticky' => true,
                'views' => 4920,
            ],
            [
                'category' => 'Software Architecture',
                'tag' => 'Event Sourcing',
                'title' => 'The Transactional Outbox Pattern for Reliable Payments',
                'excerpt' => 'Storing domain state mutations and outbound webhook events in one atomic database transaction eliminates dual-write drift.',
                'takeaways' => [
                    'Publish integration events asynchronously from a durable outbox table rather than inside DB locks.',
                    'Design downstream consumers cleanly as idempotent handlers keyed on unique event UUIDs.',
                    'Archive processed outbox records onto partitioned tables to keep operational indexes tiny.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 2860,
            ],
            [
                'category' => 'Software Architecture',
                'tag' => 'Microservices',
                'title' => 'Backpressure, Circuit Breakers, and Bulkheads That Actually Work',
                'excerpt' => 'Preventing one slow third-party payment API from exhausting PHP-FPM or queue worker pools across the platform.',
                'takeaways' => [
                    'Set explicit tight connect and total timeouts on every outbound HTTP client dependency.',
                    'Isolate slow partner queues onto dedicated worker pools so fast transactional email never stalls.',
                    'Fail fast cleanly with cached degraded state when downstream error rates breach thresholds.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 2410,
            ],
            [
                'category' => 'Software Architecture',
                'tag' => 'GraphQL',
                'title' => 'Federation vs Explicit REST Resource Boundaries at Scale',
                'excerpt' => 'Choosing predictable HTTP caching and concise versioned REST payloads over unbounded client query trees.',
                'takeaways' => [
                    'Apply persisted-query allowlists and strict query depth limits whenever exposing flexible graphs externally.',
                    'Leverage standard CDN `ETag` and `Cache-Control` semantics cleanly on public REST read collections.',
                    'Keep resource transformers thin so domain calculations never hide inside serialization classes.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 1790,
            ],
            [
                'category' => 'Software Architecture',
                'tag' => 'Clean Architecture',
                'title' => 'Zero-Downtime Database Migrations on Hundred-Million-Row Tables',
                'excerpt' => 'Expand-and-contract column rollouts let application clusters ship continuously without acquiring table-wide write locks.',
                'takeaways' => [
                    'Add new nullable columns first, backfill in throttled batches, and flip readers only after verification.',
                    'Drop deprecated columns in an independent follow-up release once old container replicas drain.',
                    'Test migration timing against anonymized production volume snapshots before merging.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 2550,
            ],

            // 6. Business & Strategy (5)
            [
                'category' => 'Business & Strategy',
                'tag' => 'SaaS Growth',
                'title' => 'Usage-Based vs Hybrid Seat Pricing in B2B Developer Platforms',
                'excerpt' => 'Why predictable platform tiers paired with elastic metered capacity outperform pure per-seat billing during AI automation shifts.',
                'takeaways' => [
                    'Expose clear real-time usage telemetry inside admin consoles before customers hit billing overages.',
                    'Align value metrics cleanly to outcomes customers celebrate rather than punitive team collaborator counts.',
                    'Offer enterprise committed annual volume discounts paired with automated invoice reconciliation.',
                ],
                'featured' => true,
                'sticky' => false,
                'views' => 3470,
            ],
            [
                'category' => 'Business & Strategy',
                'tag' => 'SaaS Growth',
                'title' => 'Designing Self-Serve Checkout Flows That Convert Technical Buyers',
                'excerpt' => 'Transparent license terms, instant sandbox verification, and clean multi-gateway support accelerate developer procurement.',
                'takeaways' => [
                    'Deliver crisp itemized PDF/HTML receipts immediately for instant corporate expense reimbursements.',
                    'Remove mandatory sales calls for starter and mid-market license tiers under annual threshold limits.',
                    'Support Stripe, PayPal, and invoice purchase orders cleanly from one unified checkout controller.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 2320,
            ],
            [
                'category' => 'Business & Strategy',
                'tag' => 'FinOps',
                'title' => 'Gross Margin Engineering as a Core Product Requirement',
                'excerpt' => 'Instrumenting per-tenant compute, storage, and LLM token cost reveals hidden unprofitable tiers early.',
                'takeaways' => [
                    'Attribute infrastructure bills dynamically to customer workspaces during nightly warehouse rollups.',
                    'Apply smart caching and model routing cleanly on heavy free-tier endpoints before scaling sales.',
                    'Review unit margin shifts alongside latency metrics in weekly engineering leadership reviews.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 1910,
            ],
            [
                'category' => 'Business & Strategy',
                'tag' => 'Microservices',
                'title' => 'Platform Teams as Product Organizations for Internal Engineers',
                'excerpt' => 'Treating paved golden paths as opt-in products drives adoption faster than top-down architecture mandates.',
                'takeaways' => [
                    'Measure time-to-first-hello-world and onboarding NPS every quarter across new engineering hires.',
                    'Deliver sensible defaults that handle logging, auth, and deployments without locking out escape hatches.',
                    'Maintain transparent public roadmaps for platform tooling migrations.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 1490,
            ],
            [
                'category' => 'Business & Strategy',
                'tag' => 'API Gateway',
                'title' => 'Building Defensible Ecosystem Marketplaces Around Open Cores',
                'excerpt' => 'Standardized plugin manifests and clean webhook SDKs transform customer integrations into compounding retention moats.',
                'takeaways' => [
                    'Provide curated starter templates and automated contract validators for marketplace partners.',
                    'Version public extension hooks explicitly with generous 12-month deprecation runways.',
                    'Feature high-craft community extensions prominently directly inside workspace discovery panels.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 1640,
            ],

            // 7. Design & UX Systems (5)
            [
                'category' => 'Design & UX Systems',
                'tag' => 'Dark Mode',
                'title' => 'Dual-Theme Token Systems: Fixing Contrast Over Dark Photo Scrims',
                'excerpt' => 'How scoped semantic surface tokens prevent light-mode heading overrides from breaking legible typography on hero banners.',
                'takeaways' => [
                    'Separate inverse photographic overlay surfaces from theme-reactive page card tokens explicitly.',
                    'Validate WCAG 4.5:1 body contrast and 3:1 heading contrast across both `[data-bs-theme="light"]` and dark themes.',
                    'Pair fluid editorial scrim gradients behind crisp headline overlays cleanly.',
                ],
                'featured' => true,
                'sticky' => false,
                'views' => 3720,
            ],
            [
                'category' => 'Design & UX Systems',
                'tag' => 'Design Systems',
                'title' => 'Vertical Rhythm Audits: Eliminating Dead Padding Across Page Bands',
                'excerpt' => 'Harmonizing legacy framework margins with compact breadcrumb strips creates tighter, intentional visual pacing.',
                'takeaways' => [
                    'Audit cumulative section padding where banner headers meet archive content wrappers.',
                    'Keep breadcrumb bars compact under standard-flow navbars without phantom fixed-header offsets.',
                    'Collapse single-item grid columns gracefully to full width so no half-page void remains.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 2890,
            ],
            [
                'category' => 'Design & UX Systems',
                'tag' => 'Design Systems',
                'title' => 'Editorial Typography Scales for Longform Technical Reading',
                'excerpt' => 'Optimal 65ch line measure, drop-cap lead rhythms, and anchored tables of contents elevate complex technical prose.',
                'takeaways' => [
                    'Use `clamp()` fluid sizing for hero display titles so desktop drama adapts gracefully on phones.',
                    'Anchor article H2/H3 subsections automatically to power persistent one-click tables of contents.',
                    'Offer distraction-free Zen Reading toggles cleanly for deep-dive visual essays.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 2140,
            ],
            [
                'category' => 'Design & UX Systems',
                'tag' => 'React',
                'title' => 'Accessible Lightbox & Keyboard Navigation Patterns Without Bloat',
                'excerpt' => 'Progressive vanilla JS filmstrips with Escape/Arrow bindings deliver tactile studio inspection with zero bundle weight.',
                'takeaways' => [
                    'Trap focus politely and support `Escape`, `ArrowLeft`, and `ArrowRight` cleanly in full-screen overlays.',
                    'Expose real resolution and format telemetry without fabricating fake EXIF camera numbers.',
                    'Defer non-blocking toast notifications instead of intrusive browser `alert()` dialogs.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 1810,
            ],
            [
                'category' => 'Design & UX Systems',
                'tag' => 'Dark Mode',
                'title' => 'Tactile Card Elevations Without Cluttered Badge Overlays',
                'excerpt' => 'Decoupling top-right status pills from top-left category anchors prevents overlapping visual noise on small viewports.',
                'takeaways' => [
                    'Keep primary category labels truncated gracefully with `text-overflow: ellipsis` cleanly on narrow sidebars.',
                    'Use hairline subtle borders in dark mode rather than bright high-contrast outer strokes.',
                    'Maintain consistent aspect ratios across card imagery wrappers with `object-fit: cover`.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 1570,
            ],

            // 8. Data & Analytics (5)
            [
                'category' => 'Data & Analytics',
                'tag' => 'ClickHouse',
                'title' => 'Sub-Second Audience Analytics Over 500M Page View Events',
                'excerpt' => 'Replacing sluggish ORM group-by scans with ClickHouse SummingMergeTrees brings live dashboard filtering down to 40ms.',
                'takeaways' => [
                    'Order primary sorting keys cleanly along `(tenant_id, event_date, path)` for instant range pruning.',
                    'Use HyperLogLog sketches (`uniqCombined`) for accurate distinct-visitor counts without memory spikes.',
                    'Pre-aggregate hourly device, browser, and country rollups asynchronously.',
                ],
                'featured' => true,
                'sticky' => false,
                'views' => 3340,
            ],
            [
                'category' => 'Data & Analytics',
                'tag' => 'PostgreSQL',
                'title' => 'Tuning PostgreSQL 17 Partial Indexes for Multi-Status Content Queues',
                'excerpt' => 'Indexing strictly active published rows or pending retry jobs cuts index size 10x and eliminates vacuum bloat.',
                'takeaways' => [
                    'Define `WHERE post_status = \'published\'` partial indexes matching exact frontend scope predicates.',
                    'Schedule regular `pg_stat_statements` audits cleanly to capture unexpected sequential scans.',
                    'Configure connection pooling cleanly so bursting queue workers never exhaust max_connections.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 2480,
            ],
            [
                'category' => 'Data & Analytics',
                'tag' => 'Laravel 13',
                'title' => 'Full-Text Search Autocomplete & Search Log Analytics in SQL',
                'excerpt' => 'Capturing zero-result search queries guides editorial content gaps while prefix indexes power instant dropdown suggestions.',
                'takeaways' => [
                    'Debounce client autocomplete requests at 220ms cleanly to cut unnecessary database load.',
                    'Track `results_count = 0` queries in Search Analytics to surface missing documentation topics.',
                    'Weight headline and tag matches above long body text for crisp, relevant dropdown ordering.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 1990,
            ],
            [
                'category' => 'Data & Analytics',
                'tag' => 'Event Sourcing',
                'title' => 'Warehouse-Native Attribution Without Fragile Third-Party Pixels',
                'excerpt' => 'First-party server-side referrer capture respects reader privacy while preserving clean campaign conversion telemetry.',
                'takeaways' => [
                    'Normalize UTM campaign parameters server-side into structured dimensions before persisting views.',
                    'Exclude internal prefetchers, bots, and preview requests from editorial view counters cleanly.',
                    'Retain aggregated country/device trends cleanly while rotating raw IP hashes on schedule.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 1710,
            ],
            [
                'category' => 'Data & Analytics',
                'tag' => 'Performance',
                'title' => 'Eliminating Pagination Drift With Cursor Keyset Seeking',
                'excerpt' => 'Offset pagination slows down dramatically at page 400 and skips items when live dispatches arrive mid-scroll.',
                'takeaways' => [
                    'Adopt keyset cursor tokens `(published_at, id)` cleanly for infinite feed streams and high-volume APIs.',
                    'Return opaque base64-encoded cursors to keep client URL state tidy and resilient.',
                    'Reserve classic page numbers purely for bounded search archives cleanly.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 1430,
            ],

            // 9. DevOps & Reliability (5)
            [
                'category' => 'DevOps & Reliability',
                'tag' => 'OpenTelemetry',
                'title' => 'Distributed Tracing With OpenTelemetry Across Async Queues',
                'excerpt' => 'Propagating W3C `traceparent` headers into Redis job payloads links slow checkout webhooks back to exact HTTP requests.',
                'takeaways' => [
                    'Sample 100% of errored or high-latency traces via tail-based OpenTelemetry collectors cleanly.',
                    'Attach business keys like `order_id` cleanly as span attributes without leaking raw card/PII fields.',
                    'Correlate structured JSON logs and metrics natively via shared trace IDs.',
                ],
                'featured' => true,
                'sticky' => false,
                'views' => 3520,
                'video_url' => 'https://vimeo.com/76979871',
                'external_url' => 'https://opentelemetry.io/docs',
            ],
            [
                'category' => 'DevOps & Reliability',
                'tag' => 'OpenTelemetry',
                'title' => 'Actionable SLO Error Budgets That Prevent Alert Fatigue',
                'excerpt' => 'Multi-window burn-rate alerts wake on-call engineers solely when availability budgets face genuine exhaustion.',
                'takeaways' => [
                    'Define user-facing SLIs at edge load balancers rather than synthetic internal health pings.',
                    'Freeze risky feature deployments automatically when 30-day error budgets drop below zero.',
                    'Pair every alert cleanly with a living runbook link and rollback command.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 2280,
            ],
            [
                'category' => 'DevOps & Reliability',
                'tag' => 'Kubernetes',
                'title' => 'Progressive Canary Rollouts With Automated Metric Rollbacks',
                'excerpt' => 'Shifting 5% traffic increments through Argo Rollouts catches memory leaks long before touching 95% of users.',
                'takeaways' => [
                    'Compare p95 latency and 5xx ratios between canary and baseline pods cleanly over 10-minute windows.',
                    'Verify database backward compatibility cleanly before shrinking or aborting failing revisions.',
                    'Mirror read traffic safely to shadow clusters when validating risky ORM query rewrites.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 1950,
            ],
            [
                'category' => 'DevOps & Reliability',
                'tag' => 'CI/CD',
                'title' => 'Automated SQLite & MySQL Backup Drills With Checksum Audits',
                'excerpt' => 'Untested backups are merely optimistic files—scheduled weekly restore pipelines verify recovery RTO empirically.',
                'takeaways' => [
                    'Compute SHA-256 checksums on compressed nightly database snapshots cleanly before off-site sync.',
                    'Run automated restore verification onto ephemeral CI containers to catch broken archive flags.',
                    'Encrypt credential exports separately cleanly from static upload backups.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 1690,
            ],
            [
                'category' => 'DevOps & Reliability',
                'tag' => 'Docker',
                'title' => 'Chaos Injection Drills for Queue Backlogs and DNS Timeouts',
                'excerpt' => 'Controlled staging fault injection reveals silent retry storms before real cloud provider brownouts hit.',
                'takeaways' => [
                    'Add randomized jitter cleanly to all queue retries to break synchronized thundering herds.',
                    'Verify supervisor auto-scaling catches sustained backlog bursts within target SLO thresholds.',
                    'Conduct blameless postmortems turning every uncovered timeout gap into regression tests.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 1360,
            ],

            // 10. Developer Productivity (5)
            [
                'category' => 'Developer Productivity',
                'tag' => 'CI/CD',
                'title' => 'Ephemeral Pull-Request Previews With Seeded Databases in 45s',
                'excerpt' => 'Copy-on-write branching lets designers, reviewers, and QA click live PR previews rather than guessing from screenshots.',
                'takeaways' => [
                    'Seed deterministic realistic content sets automatically so review previews match production visuals.',
                    'Tear down idle preview namespaces automatically on PR merge cleanly to cap cloud spend.',
                    'Post clickable preview + PHPUnit assertion links straight into PR summaries.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 2790,
            ],
            [
                'category' => 'Developer Productivity',
                'tag' => 'TypeScript',
                'title' => 'Remote Build Caching for Monorepos Without Custom Daemon Ops',
                'excerpt' => 'Content-addressed artifact hashing skips unchanged workspace test and Vite bundle steps across developer laptops and CI.',
                'takeaways' => [
                    'Isolate environment variable leaks cleanly so cache keys remain deterministic across CI runners.',
                    'Split long monolithic feature test suites cleanly across parallel shards for sub-2-minute feedback.',
                    'Surface cache hit-rate metrics weekly alongside median PR queue wait time.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 1880,
            ],
            [
                'category' => 'Developer Productivity',
                'tag' => 'Laravel 13',
                'title' => 'Writing Fast, Flake-Free Feature Tests With DatabaseTransactions',
                'excerpt' => 'Keeping SQLite WAL locks tidy and isolating per-test factories drops 100-test feedback loops below two seconds.',
                'takeaways' => [
                    'Seed crisp domain fixtures once and wrap state-mutating assertions cleanly inside transactions.',
                    'Assert structural DOM markers, route contracts, and authorization boundaries instead of fragile copy.',
                    'Mock external gateway HTTP transfers cleanly via sandbox test drivers.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 2460,
            ],
            [
                'category' => 'Developer Productivity',
                'tag' => 'React',
                'title' => 'Automated Visual Regression Guards for Light & Dark Theme Releases',
                'excerpt' => 'Catching overlapping badges, broken flexbox wraps, and unreadable dark-scrim titles automatically on every UI PR.',
                'takeaways' => [
                    'Capture desktop and mobile snapshots across both `[data-bs-theme="light"]` and dark mode toggles.',
                    'Fail builds whenever horizontal `scrollWidth` exceeds `clientWidth` cleanly across key routes.',
                    'Keep CSS component primitives consolidated cleanly to avoid conflicting overrides.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 1610,
            ],
            [
                'category' => 'Developer Productivity',
                'tag' => 'Edge Computing',
                'title' => 'Internal Developer Portals That Respect Engineering Flow State',
                'excerpt' => 'Unified service catalogs, deploy logs, and instant API key rotation turn cognitive overload into self-serve velocity.',
                'takeaways' => [
                    'Embed live health telemetry, runbooks, and dependency owners cleanly onto every service overview.',
                    'Standardize repository scaffold templates with pre-wired linters, CI, and OpenTelemetry SDKs.',
                    'Track deployment frequency alongside engineering survey feedback cleanly every quarter.',
                ],
                'featured' => false,
                'sticky' => false,
                'views' => 1420,
            ],
        ];
    }

    /**
     * Build rich multi-section HTML body for realistic longform reading, TOC anchors, and clean layout.
     */
    private function buildRichBody(array $article): string
    {
        $bullets = '';
        foreach ($article['takeaways'] as $point) {
            $bullets .= '<li>' . e($point) . '</li>';
        }

        $category = e($article['category']);
        $tag = e($article['tag']);
        $title = e($article['title']);
        $excerpt = e($article['excerpt']);

        return <<<HTML
<p>{$excerpt} Across fast-moving engineering teams working in <strong>{$category}</strong>, reliability and clarity hinge on making sound architectural trade-offs before traffic or organizational complexity forces an emergency rewrite.</p>

<h2>Architectural Context</h2>
<p>Traditional deployments in <strong>{$category}</strong> often accumulate accidental complexity around state boundaries, implicit coupling, and unmonitored tail latency. By anchoring implementation decisions around <strong>{$tag}</strong> fundamentals, teams gain predictable throughput without inflating operational surface area.</p>

<blockquote>
<p>"Systems stay resilient not because every dependency is infallible, but because failure domains, timeouts, and observability telemetry are explicitly engineered from day one."</p>
</blockquote>

<h2>Production Patterns &amp; Trade-Offs</h2>
<p>When rolling out improvements discussed in <em>{$title}</em>, prioritize measurable observability signals over theoretical benchmarks. Staging traffic mirrors, query budget assertions, and incremental rollout gates surface edge cases safely ahead of customer impact.</p>

<h3>Implementation Checklist</h3>
<ul>
{$bullets}
</ul>

<p>Pairing strong automated verification suites with clean operational telemetry ensures changes shipped under <strong>{$category}</strong> remain auditable, fast, and maintainable across successive release cycles.</p>
HTML;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Category::count() === 0) {
            $this->call(CategorySeeder::class);
        }
        if (Tag::count() === 0) {
            $this->call(TagSeeder::class);
        }
        if (Picture::count() === 0) {
            $this->call(PictureSeeder::class);
        }

        $categoriesByName = Category::pluck('id', 'name');
        $tagsByName = Tag::pluck('id', 'name');
        $pictureIds = Picture::selectRaw('MIN(id) as id, link')
            ->groupBy('link')
            ->orderBy('id')
            ->pluck('id')
            ->values()
            ->all();

        $authorIds = Author::orderBy('id')->pluck('id')->values()->all();
        if (empty($authorIds)) {
            $user = User::first() ?? User::factory()->create(['name' => 'Elena Vance', 'email' => 'elena@xivig.com']);
            $author = Author::create(['user_id' => $user->id, 'nickname' => 'Elena Vance']);
            $authorIds = [$author->id];
        }

        $userIds = User::orderBy('id')->pluck('id')->values()->all();
        if (empty($userIds)) {
            $userIds = [User::factory()->create()->id];
        }

        foreach (self::articles() as $index => $item) {
            $categoryId = $categoriesByName->get($item['category']) ?? $categoriesByName->first();
            $tagId = $tagsByName->get($item['tag']) ?? $tagsByName->first();
            $pictureId = !empty($pictureIds) ? $pictureIds[$index % count($pictureIds)] : null;
            $authorId = $authorIds[$index % count($authorIds)];
            $userId = $userIds[$index % count($userIds)];

            $slug = Str::slug($item['title']);
            $content = $this->buildRichBody($item);
            $readingTime = max(3, (int) ceil(str_word_count(strip_tags($content)) / 120) + 2);
            $publishedAt = now()->subHours(($index * 13) + 4);

            $post = Post::updateOrCreate(
                ['slug' => $slug],
                [
                    'user_id' => $userId,
                    'category_id' => $categoryId,
                    'tag_id' => $tagId,
                    'image_id' => $pictureId,
                    'author_id' => $authorId,
                    'title' => $item['title'],
                    'content' => $content,
                    'excerpt' => $item['excerpt'],
                    'status' => true,
                    'post_status' => 'published',
                    'published_at' => $publishedAt,
                    'is_featured' => (bool) ($item['featured'] ?? false),
                    'is_sticky' => (bool) ($item['sticky'] ?? false),
                    'visibility' => 'public',
                    'reading_time' => $readingTime,
                    'views_count' => (int) ($item['views'] ?? (420 + ($index * 37))),
                    'video_url' => $item['video_url'] ?? null,
                    'external_url' => $item['external_url'] ?? null,
                    'meta_title' => $item['title'],
                    'meta_description' => $item['excerpt'],
                    'meta_keywords' => $item['category'] . ', ' . $item['tag'] . ', Xivig Engineering',
                    'schema_type' => 'Article',
                ]
            );
        }
    }
}
