<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\PaymentGateway;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class EcommerceSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'title' => 'Enterprise SaaS Core & Multi-Tenant Engine',
                'slug' => 'enterprise-saas-core-multi-tenant',
                'sku' => 'XIV-SAAS-001',
                'category' => 'Starters',
                'badge_label' => 'Laravel 13 Starter',
                'badge_color' => '#4f46e5',
                'short_description' => 'Complete Fortify + Jetstream architecture with multi-guard admin authentication, RBAC policies, and tenant isolation.',
                'description' => 'Production-ready Laravel 13 SaaS foundation engineered with separate User & Admin guards, team seats, Sanctum REST API tokens, automated audit trails, and repository services.',
                'features' => [
                    'Dual-guard Fortify + Jetstream authentication with TOTP 2FA',
                    'Multi-tenant team invitations & granular role-permission matrix',
                    'Full RESTful v1 API & Sanctum bearer token scoping',
                    'Automated PHPUnit / Feature verification suite included',
                ],
                'price' => 199.00,
                'sale_price' => 149.00,
                'currency' => 'USD',
                'stock_qty' => -1,
                'is_digital' => true,
                'download_url' => 'https://github.com/xivig/laravel-blog-admin-template/releases',
                'image_url' => 'xivig-blog/images/portfolio/project-nexus-core.webp',
                'rating' => 4.95,
                'reviews_count' => 86,
                'sales_count' => 312,
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Elite Executive Admin Panel & Dashboard Suite',
                'slug' => 'elite-executive-admin-suite',
                'sku' => 'XIV-UI-002',
                'category' => 'UI Suites',
                'badge_label' => 'Analytics & UI',
                'badge_color' => '#06b6d4',
                'short_description' => 'High-performance executive admin command center with live telemetry charts, DataTables, dark mode, and modular widgets.',
                'description' => 'Built atop Bootstrap 5.3 with zero jQuery bloat, dual dark/light glassmorphic themes, real-time analytics dashboards, media cropper studio, and mail/chat workspace tools.',
                'features' => [
                    'Responsive dark & light mode design token architecture',
                    'Interactive ApexCharts / visitor telemetry & SEO scorecards',
                    'Drag-and-drop Menu Builder & Widget Area Composer',
                    'Media Library with native GD WebP compression & cropping',
                ],
                'price' => 129.00,
                'sale_price' => 99.00,
                'currency' => 'USD',
                'stock_qty' => -1,
                'is_digital' => true,
                'download_url' => 'https://github.com/xivig/laravel-blog-admin-template/releases',
                'image_url' => 'xivig-blog/images/portfolio/project-velocity-studio.webp',
                'rating' => 4.88,
                'reviews_count' => 64,
                'sales_count' => 245,
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Fortify Dual-Guard 2FA Authentication Shield',
                'slug' => 'fortify-dual-guard-2fa-shield',
                'sku' => 'XIV-SEC-003',
                'category' => 'Starters',
                'badge_label' => 'Security & Auth',
                'badge_color' => '#ec4899',
                'short_description' => 'Hardware-compatible FIDO2 passkeys, TOTP authenticator enforcement, login anomaly logs, and session device revocation.',
                'description' => 'Harden your mission-critical Laravel applications with zero-trust authentication policies, CSP security headers, turnstile/reCAPTCHA bridges, and full session device forensics.',
                'features' => [
                    'Enforced 2FA challenge workflows across admin & user portals',
                    'Real-time login history geo-IP & user-agent tracking',
                    'One-click remote device session revocation matrix',
                    'OWASP security headers & CSRF/XSS defense middleware',
                ],
                'price' => 129.00,
                'sale_price' => null,
                'currency' => 'USD',
                'stock_qty' => -1,
                'is_digital' => true,
                'download_url' => 'https://github.com/xivig/laravel-blog-admin-template/releases',
                'image_url' => 'xivig-blog/images/portfolio/project-cybermatrix.webp',
                'rating' => 5.00,
                'reviews_count' => 51,
                'sales_count' => 178,
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'title' => 'Kubernetes & Docker Enterprise Deployment Blueprints',
                'slug' => 'kubernetes-docker-enterprise-blueprints',
                'sku' => 'XIV-INFRA-004',
                'category' => 'Blueprints',
                'badge_label' => 'Cloud Infrastructure',
                'badge_color' => '#10b981',
                'short_description' => 'Zero-downtime rolling upgrades, Redis Sentinel clustering, Laravel Octane Swoole tuning, and auto-scaling Horizon workers.',
                'description' => 'Complete production infrastructure-as-code suite containing hardened multi-stage Dockerfiles, Helm charts, GitHub Actions Canary workflows, and automated daily backup pipelines.',
                'features' => [
                    'Production Octane + FrankenPHP / Swoole container images',
                    'Zero-downtime Canary rollout & automated rollback gates',
                    'Redis Queue & Horizon autoscaling HPA definitions',
                    'Encrypted database snapshots with S3 offsite sync',
                ],
                'price' => 249.00,
                'sale_price' => 199.00,
                'currency' => 'USD',
                'stock_qty' => -1,
                'is_digital' => true,
                'download_url' => 'https://github.com/xivig/laravel-blog-admin-template/releases',
                'image_url' => 'xivig-blog/images/portfolio/project-sovereign-cloud.webp',
                'rating' => 4.94,
                'reviews_count' => 43,
                'sales_count' => 129,
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'title' => 'Headless REST & Telemetry Stream Starter',
                'slug' => 'headless-rest-telemetry-starter',
                'sku' => 'XIV-API-005',
                'category' => 'Blueprints',
                'badge_label' => 'API & SDK',
                'badge_color' => '#8b5cf6',
                'short_description' => 'Sanctum bearer tokens, adaptive rate limiting, structured OpenAPI resource serializers, and real-time event streaming.',
                'description' => 'Designed for high-throughput headless backends and telemetry ingestion APIs with uniform JSON response envelopes, search analytics, and sub-15ms cached endpoints.',
                'features' => [
                    'Versioned /api/v1 resource transformers & pagination envelopes',
                    'Per-endpoint token rate-limiting & sliding quota guards',
                    'Webhook dispatcher with signed HMAC SHA-256 headers',
                    'Live query telemetry & fulltext search indexing service',
                ],
                'price' => 89.00,
                'sale_price' => null,
                'currency' => 'USD',
                'stock_qty' => -1,
                'is_digital' => true,
                'download_url' => 'https://github.com/xivig/laravel-blog-admin-template/releases',
                'image_url' => 'xivig-blog/images/portfolio/project-horizon-iot.webp',
                'rating' => 4.78,
                'reviews_count' => 39,
                'sales_count' => 196,
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'title' => 'Global Payment Gateway & Billing Orchestrator',
                'slug' => 'global-payment-gateway-orchestrator',
                'sku' => 'XIV-PAY-006',
                'category' => 'UI Suites',
                'badge_label' => 'E-Commerce',
                'badge_color' => '#f59e0b',
                'short_description' => 'Unified Stripe, Razorpay, PayPal, and Wire Invoice checkout orchestrator with signed webhooks, coupons, and license delivery.',
                'description' => 'Turnkey enterprise commerce module connecting multiple payment processors behind one clean checkout UI, automatic tax/promo calculation, instant license key issuance, and full admin order management.',
                'features' => [
                    'Native Stripe Checkout, Razorpay Orders & PayPal REST v2 adapters',
                    'Cryptographically verified webhook signature handling',
                    'Coupon code rules, tax percentages & PDF-ready receipts',
                    'Complete Admin Storefront, Gateways & Orders Command Center',
                ],
                'price' => 219.00,
                'sale_price' => 179.00,
                'currency' => 'USD',
                'stock_qty' => -1,
                'is_digital' => true,
                'download_url' => 'https://github.com/xivig/laravel-blog-admin-template/releases',
                'image_url' => 'xivig-blog/images/portfolio/project-apex-financial.webp',
                'rating' => 5.00,
                'reviews_count' => 72,
                'sales_count' => 264,
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($products as $item) {
            Product::updateOrCreate(['slug' => $item['slug']], $item);
        }

        $gateways = [
            [
                'driver' => 'stripe',
                'name' => 'Stripe Checkout',
                'subtitle' => 'Credit / Debit Cards, Apple Pay, Google Pay & Link',
                'icon' => 'bi-credit-card-2-front-fill',
                'is_enabled' => true,
                'mode' => 'sandbox',
                'config' => [
                    'publishable_key' => '',
                    'secret_key' => '',
                    'webhook_secret' => '',
                ],
                'sort_order' => 1,
            ],
            [
                'driver' => 'razorpay',
                'name' => 'Razorpay Global',
                'subtitle' => 'UPI, RuPay, International Cards & NetBanking',
                'icon' => 'bi-lightning-charge-fill',
                'is_enabled' => true,
                'mode' => 'sandbox',
                'config' => [
                    'key_id' => '',
                    'key_secret' => '',
                    'webhook_secret' => '',
                ],
                'sort_order' => 2,
            ],
            [
                'driver' => 'paypal',
                'name' => 'PayPal Express',
                'subtitle' => 'Pay with PayPal Balance, Venmo & Global Cards',
                'icon' => 'bi-paypal',
                'is_enabled' => true,
                'mode' => 'sandbox',
                'config' => [
                    'client_id' => '',
                    'client_secret' => '',
                ],
                'sort_order' => 3,
            ],
            [
                'driver' => 'sandbox_test',
                'name' => 'Sandbox Instant Express Pay',
                'subtitle' => 'One-Click Test Terminal for Instant Order & License Simulation',
                'icon' => 'bi-terminal-fill',
                'is_enabled' => true,
                'mode' => 'sandbox',
                'config' => [
                    'auto_approve' => false,
                ],
                'sort_order' => 4,
            ],
            [
                'driver' => 'cod_bank',
                'name' => 'Enterprise Invoice / Wire Transfer',
                'subtitle' => 'Direct IBAN / SWIFT Bank Settlement (Admin Manual Approval)',
                'icon' => 'bi-bank2',
                'is_enabled' => true,
                'mode' => 'live',
                'config' => [
                    'bank_instructions' => "Bank: Xivig Treasury Bank N.A.\nAccount Name: Xivig Systems LLC\nIBAN / Routing: US89-XIVG-0029-4821\nReference: Include your Order Number on transfer memo.",
                ],
                'sort_order' => 5,
            ],
        ];

        foreach ($gateways as $gw) {
            PaymentGateway::updateOrCreate(['driver' => $gw['driver']], $gw);
        }

        Coupon::updateOrCreate(
            ['code' => 'ELITE20'],
            [
                'type' => 'percent',
                'value' => 20.00,
                'min_order_amount' => 50.00,
                'max_uses' => 500,
                'is_active' => true,
            ]
        );

        Coupon::updateOrCreate(
            ['code' => 'XIVIG50'],
            [
                'type' => 'fixed',
                'value' => 50.00,
                'min_order_amount' => 150.00,
                'max_uses' => 250,
                'is_active' => true,
            ]
        );

        Setting::set('store_enabled', '1', 'store');
        Setting::set('store_currency', 'USD', 'store');
        Setting::set('store_tax_rate', '0', 'store');
        Setting::set('store_announcement', 'Save 20% on any Enterprise Starter Kit — Use Code ELITE20 at Checkout', 'store');
    }
}
