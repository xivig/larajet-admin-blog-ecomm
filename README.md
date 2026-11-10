# Laravel Blog & CMS Platform (XivigAdmin PRO)

[![Laravel 12 / 13 Compatible](https://img.shields.io/badge/Laravel-12%20%7C%2013%20Compatible-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat&logo=php)](https://www.php.net)
[![Bootstrap 5](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat&logo=bootstrap)](https://getbootstrap.com)
[![License](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

An enterprise **Laravel Blog, CMS, Storefront & Admin Platform** built with **Laravel 12/13**, **Laravel Fortify**, **Laravel Jetstream**, **Bootstrap 5.3**, and **Vite 6**. Designed with dual authentication guards (`admin` and `web`), Role-Based Access Control (RBAC), visual Quill editing, synchronized Navigation Menu Builder & Modular Footer Widgets, a Digital E-Commerce Storefront, interactive Cropper.js Studio, File Manager, Webmail, Chat, SEO Suite, and ZIP Backup/Restore.

---

## Table of Contents

1. [Architectural Overview](#architectural-overview)
2. [Key Features & Capabilities](#key-features--capabilities)
3. [System Requirements](#system-requirements)
4. [Local Installation Guide](#local-installation-guide)
5. [Shared Hosting & Security Guide](#shared-hosting--security-guide)
6. [Default Credentials & Access](#default-credentials--access)
7. [Core Modules Documentation](#core-modules-documentation)
8. [Frontend Walkthrough](#frontend-walkthrough)
9. [Directory Structure](#directory-structure)
10. [CLI Commands & Maintenance](#cli-commands--maintenance)
11. [Troubleshooting & FAQs](#troubleshooting--faqs)

---

## Architectural Overview

- **Framework**: Laravel 12 / 13 with clean service layer (`app/Services/`), FormRequests, Observers, and Listeners.
- **Dual-Guard Authentication**:
  - `web`: Community users, readers, commenters, and storefront customers (`.fauth-*` themed Fortify/Jetstream pages).
  - `admin`: Dedicated admin session guard (`Admin` model with 2FA support, notification feed, and activity audit logs).
- **Database Engine**: Portable **SQLite** for local development with **MySQL / MariaDB** production support (including driver-aware analytics and archive SQL formatting).
- **Frontend Asset Pipeline**: Vite 6 (`resources/scss/frontend/`, `resources/js/frontend/`) compiling to `public/build/`, alongside consolidated static public assets in `public/xivig-public/` and admin assets in `public/xivig-admin/`.

---

## Key Features & Capabilities

- **Static Pages & Quill Visual Editor**: `/admin/pages` with shared Quill v2 Snow **Visual Editor / HTML Source** switcher, custom page templates (`default`, `about`, `landing`, `privacy`, `terms`, `custom`), and automatic synchronization with `/page/{slug}` routes and Header/Footer links.
- **Navigation Menu Builder**: `/admin/menu` supports hierarchical drag-free Up/Down ordering, inline modal editing, preset routes, pages, categories, SweetAlert2 delete confirmations, and live front-end sync across Header (`header`) and Footer (`footer`, `footer_ecosystem`, `footer_pages`) slots.
- **Modular Widgets & Footer Composer**: `/admin/widgets` manages `sidebar`, `header`, and all five front-end footer columns (`footer_1`–`footer_5`) across 12 widget types (`brand_info`, `navigation_menu`, `pages_list`, `newsletter`, `popular`, `recent_posts`, `trending`, `categories`, `tags`, `archive`, `ads`, `custom_html`) with live order, edit, and visibility controls.
- **E-Commerce Storefront & Gateways**: `/ecomm` and `/admin/store/*` with session cart, promotional coupons, digital license key issuance, order receipts, webhooks, and Stripe, Razorpay, PayPal, Bank Transfer, and Sandbox gateways.
- **Media Library & Cropper.js Studio**: `/admin/media` and `/admin/image-cropper` with aspect-ratio presets, boundary telemetry, WebP variant optimization (`ImageOptimizationService`), and uploads organized in `public/uploads/media/YYYY/MM`.
- **Apps Suite**: File Manager (`/admin/file-manager`), Webmail (`/admin/email`), Workspace Chat (`/admin/chat`), Comment Moderation (`/admin/comments`), Search Analytics (`/admin/search-analytics`), URL Redirects & 404 Monitor (`/admin/url-management`), Activity Logs (`/admin/activity-logs`), and ZIP System Backups (`/admin/backups`).
- **SEO & Hardening**: Meta/OpenGraph/Twitter/JSON-LD generator (`SeoService`), dynamic `/robots.txt`, dynamic `/sitemap.xml` + `/feed.xml`, `SecurityHeadersMiddleware`, `SecurityFileGuard` upload/path confinement, `public/uploads/.htaccess` script execution blocking, rate-limited public mutations, and `POST`-only logouts.

---

## System Requirements

- **PHP**: `^8.2` (tested on PHP 8.2 / 8.3 / 8.4)
- **Extensions**: `pdo_sqlite` or `pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo`, `gd` (or `imagick`), `zip`
- **Composer**: `v2.x`
- **Node.js & NPM**: `v18+` / `v20+` (for local Vite & SCSS compilation)

---

## Local Installation Guide

### Step 1: Clone & Enter Workspace

```bash
git clone https://github.com/xivig/laravel-blog-admin-template.git
cd laravel-blog-admin-template
```

### Step 2: Configure Environment

Copy `.env.example` to `.env`:

```bash
cp .env.example .env
```

For quick local SQLite development, update `.env`:

```env
APP_NAME="Xivig CMS"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=sqlite
```

For MySQL / MariaDB (recommended for production):

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=xivig_cms
DB_USERNAME=xivig_user
DB_PASSWORD=your_secure_password
```

### Step 3: Install PHP & Frontend Dependencies

```bash
composer install
npm install
npm run build
```

If using SQLite locally, ensure the database file exists:

```bash
# Linux/macOS
touch database/database.sqlite

# Windows PowerShell
New-Item -ItemType File -Path database\database.sqlite -Force
```

### Step 4: Generate Key, Migrate & Seed

```bash
php artisan key:generate
php artisan migrate --seed
```

### Step 5: Verify Storage & Serve

```bash
php artisan cms:storage-check
php artisan serve
```

Access locally:
- **Public Blog & Storefront**: `http://127.0.0.1:8000`
- **Admin Command Center**: `http://127.0.0.1:8000/admin/login`

---

## Shared Hosting & Security Guide

For production deployment on cPanel, Plesk, or Apache/LiteSpeed shared hosting, follow the dedicated checklist in [docs/SHARED_HOSTING_DEPLOYMENT.md](docs/SHARED_HOSTING_DEPLOYMENT.md):

- Build assets locally (`npm run build`) and install `--no-dev --optimize-autoloader`.
- Point domain Document Root to `public/` (never the project root).
- Switch `.env` to `APP_ENV=production`, `APP_DEBUG=false`, HTTPS `APP_URL`, `DB_CONNECTION=mysql`, `SESSION_SECURE_COOKIE=true`, `SESSION_ENCRYPT=true`, `ALLOW_ADMIN_REGISTRATION=false`, and `COMMENTS_AUTO_APPROVE=false`.
- Ensure [public/.htaccess](public/.htaccess) and [public/uploads/.htaccess](public/uploads/.htaccess) remain deployed to block dotfiles and PHP execution in upload folders.
- Add a single cron entry (`* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1`) for queued mail/image jobs, scheduled publishing, and daily sitemap generation.

---

## Default Credentials & Access

| Portal | Route | Default Email | Default Password |
| :--- | :--- | :--- | :--- |
| **Admin Portal** | `/admin/login` | `test@example.com` (or seeded `admin@xivig.com`) | `password` |
| **User Portal** | `/login` | Seeded user account | `password` |

> **Important**: Change or remove all default seeded credentials and disable `sandbox_test` payment gateway mode before exposing the application to the public internet.

---

## Core Modules Documentation

- **Authentication & RBAC (`/admin/roles`, `/admin/users`)**: Dual guards (`admin` / `web`), Jetstream 2FA support (`/admin/profile`), role-permission gates (`HasRolesAndPermissions`), and `ALLOW_ADMIN_REGISTRATION` protection on `/admin/register`.
- **Blog Posts, Categories & Tags (`/admin/posts`, `/admin/categories`, `/admin/tags`)**: Quill composer, scheduled publishing (`cms:publish-scheduled`), slug redirects (`SlugHistory`), featured/trending flags, and bulk trash/restore actions.
- **Static Pages (`/admin/pages`)**: Quill Snow visual editor + raw HTML source toggle via [\_content-editor.blade.php](resources/views/admin/pages/_content-editor.blade.php), SEO meta fields, and `/page/{slug}` live publishing.
- **Navigation Menu Builder (`/admin/menu`)**: Manages `header`, `footer`, `footer_ecosystem`, and `footer_pages` slots cleanly via [`MenuController`](app/Http/Controllers/Admin/MenuController.php) and [`Menu`](app/Models/Menu.php), with nested `parent_id` dropdowns, item editing, Up/Down ordering, and SweetAlert2 confirmation dialogs.
- **Modular Footer & Sidebar Widgets (`/admin/widgets`)**: Synchronizes `footer_1` (Brand & Contact), `footer_2` (Platform Menu), `footer_3` (Ecosystem Menu), `footer_4` (Pages List), `footer_5` (Newsletter CTA), plus `sidebar` and `header` slots cleanly via [`WidgetController`](app/Http/Controllers/Admin/WidgetController.php) and [`widget-renderer.blade.php`](resources/views/blog/partials/widget-renderer.blade.php).
- **E-Commerce Store (`/ecomm`, `/admin/store/*`)**: Product catalog, cart & coupon calculator, checkout authorization, live/sandbox payment gateway verification ([`PaymentGatewayService`](app/Services/PaymentGatewayService.php)), HMAC-verified Stripe/Razorpay webhooks, and session/owner-protected receipts.
- **Media, Cropper & File Manager (`/admin/media`, `/admin/image-cropper`, `/admin/file-manager`)**: Guarded by [`SecurityFileGuard`](app/Services/SecurityFileGuard.php) for safe extensions, image header verification, and strict `public/uploads/` confinement.
- **Communications & Comments (`/admin/email`, `/admin/chat`, `/admin/comments`, `/admin/newsletter`)**: Rate-limited public comments/replies with guest moderation queue (`pending`), subscriber broadcasts, team chat channels, and webmail inbox.
- **SEO, Redirects & Backups (`/admin/settings/seo`, `/admin/url-management`, `/admin/backups`, `/admin/activity-logs`)**: Meta/OG/JSON-LD suite, dynamic `/robots.txt` and `/sitemap.xml`, 301/302 redirect & 404 logs, CSV audit exports, and Zip-Slip-protected backup restoration.

---

## Frontend Walkthrough

- **Home (`/`) & Services / Pricing / Portfolio**: Dynamic topbar with rounded Sign In / Register pills, Dark/Light switcher, synchronized hover-intent navbar + Blog mega-menu + auto-synced Pages dropdown, and 5-column widget-driven Footer.
- **Magazine & Blog Views (`/blog`, `/blog-list`, `/blog/magazine`, `/blog/feed`, `/blog/gallery`, `/blog/videos`, `/blog/{slug}`)**: Category, tag, and author archives, live search autocomplete (`/api/search/autocomplete`), password-protected article unlock, and nested comments.
- **Static Pages (`/page/{slug}`) & Storefront (`/ecomm`)**: Database-backed custom pages (`/about` 301 redirects to `/page/about-us`) and digital product store checkout.

---

## Directory Structure

```text
laravel-blog-admin-template/
├── app/
│   ├── Console/Commands/         # Storage checks and maintenance Artisan commands
│   ├── Http/
│   │   ├── Controllers/          # Admin/, Front/, Frontend/, Api/, and Auth controllers
│   │   ├── Middleware/           # SecurityHeaders, UrlRedirect, CheckPermission, CheckSiteMaintenance
│   │   └── Requests/             # FormRequest validation & rate-limited AdminLoginRequest
│   ├── Models/                   # Post, Page, Menu, MenuItem, Widget, Product, Order, Admin, User, etc.
│   ├── Services/                 # SecurityFileGuard, SeoService, PaymentGatewayService, BackupService, AnalyticsService
│   └── Traits/                   # HasRolesAndPermissions
├── bootstrap/
│   └── app.php                   # Proxy trust, global middleware, exception 404 tracking
├── database/
│   ├── migrations/               # Complete schema + default Header/Footer menus and footer widgets
│   └── seeders/                  # Roles, demo posts, store catalog, and widgets seeders
├── docs/
│   └── SHARED_HOSTING_DEPLOYMENT.md
├── public/
│   ├── build/                    # Compiled Vite JS/CSS bundles
│   ├── uploads/                  # User uploads protected by public/uploads/.htaccess
│   ├── xivig-admin/              # Admin static theme CSS, JS, Quill, SweetAlert2, images
│   ├── xivig-public/             # Frontend static vendor plugins, elite-design.css, images
│   └── .htaccess                 # Apache rules (Indexes/MultiViews disabled, dotfile denial, front controller)
├── resources/
│   ├── js/frontend/              # Modular Vite frontend scripts & hover navigation
│   ├── scss/frontend/            # Bootstrap-integrated light/dark theme SCSS architecture
│   └── views/                    # Admin, Auth, Blog, Store, Layouts, and SEO Blade templates
├── routes/
│   ├── web.php                   # Public, storefront, webhook, SEO, and auth:admin routes
│   ├── api.php                   # Sanctum API routes
│   └── console.php               # Cron-friendly scheduled commands & queue runner
└── tests/                        # PHPUnit Feature & Unit tests
```

---

## CLI Commands & Maintenance

```bash
# Compile frontend CSS/JS for production
npm run build

# Clear & rebuild framework caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Database & storage health
php artisan migrate --force
php artisan cms:storage-check

# SEO, queues & scheduled posts
php artisan sitemap:generate
php artisan cms:publish-scheduled
php artisan cms:warm-cache
php artisan queue:work --stop-when-empty --tries=3
```

---

## Troubleshooting & FAQs

### 1. `SQLSTATE[HY000]: General error: 1 no such table`
Run `php artisan migrate --seed` (or `php artisan migrate --force` in production).

### 2. Symlinks Disabled on Shared Hosting (`storage:link` fails)
Media, post banners, avatars, and store assets are saved directly into `public/uploads/`. Verify `public/uploads/` is writable (`755`) via `php artisan cms:storage-check`.

### 3. `/robots.txt` or `/sitemap.xml` Not Reflecting Live Settings
Ensure static `public/robots.txt` does not exist so Laravel serves `SeoSettingController::robotsTxt`, and regenerate the sitemap after setting `APP_URL` with `php artisan sitemap:generate`.

### 4. HTTPS Redirect Loops Behind Cloudflare / Reverse Proxy
`bootstrap/app.php` configures `$middleware->trustProxies(at: '*')`. Ensure Cloudflare SSL mode is **Full (Strict)** (not Flexible) and `APP_URL` uses `https://`.

---

## License

This software is open-sourced under the [MIT license](LICENSE).
