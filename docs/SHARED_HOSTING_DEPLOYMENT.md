# Shared Hosting Deployment & Security Guide

Production checklist and hardening reference for deploying **Xivig CMS (`laravel-blog-admin-template`)** on shared hosting (cPanel, Plesk, DirectAdmin, Apache/LiteSpeed).

---

## 1. Pre-Upload Build & Packaging

Most shared hosting plans do not ship Node.js. Compile frontend assets locally before uploading:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

This builds `public/build/*`, `resources/css/frontend.css`, and `public/xivig-public/css/elite-design.css`. Ensure `vendor/` and `public/build/` are included in your deployment package (`.gitignore` excludes them from Git).

---

## 2. Directory Placement & Document Root

- Point the domain's **Document Root** strictly to `public/` (for example `/home/<user>/xivig-cms/public`), never to the application root.
- Keep `.env`, `app/`, `config/`, `database/`, `routes/`, `storage/`, and `vendor/` **above the webroot**.
- [public/.htaccess](../public/.htaccess) enforces:
  - `Options -MultiViews -Indexes`
  - Dotfile blocking (`^\.(?!well-known/)`), blocking accidental access to `.env` or `.htaccess`
  - Blocking `composer.json`, `composer.lock`, `package.json`, `phpunit.xml`, and `artisan` if exposed
  - `Authorization` header forwarding, trailing-slash 301 normalization, and Laravel front-controller routing
- [public/uploads/.htaccess](../public/uploads/.htaccess) disables Apache directory listing, CGI, and PHP engine execution inside `public/uploads/`.

---

## 3. Production `.env` Configuration

Use [`.env.example`](../.env.example) as the production baseline:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_cpanel_db
DB_USERNAME=your_cpanel_user
DB_PASSWORD=your_strong_db_password

SESSION_DRIVER=database
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

QUEUE_CONNECTION=database
CACHE_STORE=database

ALLOW_ADMIN_REGISTRATION=false
COMMENTS_AUTO_APPROVE=false
```

Then generate a fresh application key on the server (or once locally before upload):

```bash
php artisan key:generate --force
```

> **MySQL & MariaDB Compatibility**: Monthly trend and archive queries in [`AnalyticsService`](../app/Services/AnalyticsService.php) and [`widget-renderer.blade.php`](../resources/views/blog/partials/widget-renderer.blade.php) automatically switch between `strftime('%Y-%m', ...)` (SQLite) and `DATE_FORMAT(..., '%Y-%m')` (MySQL/MariaDB).

---

## 4. Database, Migrations & Permissions

Run migrations and verify storage write permissions:

```bash
php artisan migrate --force
php artisan cms:storage-check
```

- Ensure `storage/`, `storage/logs/`, `storage/framework/`, `bootstrap/cache/`, and `public/uploads/` are writable (`chmod 755` directories / `644` files on suPHP/FastCGI).
- Core media, post images, avatars, and store assets write directly to `public/uploads/`, so the application remains functional even on hosts where `php artisan storage:link` (`symlink()`) is disabled.

---

## 5. Security Controls Built Into the Codebase

- **Upload & Path Hardening ([`SecurityFileGuard`](../app/Services/SecurityFileGuard.php))**:
  - `FileManagerController`, `MediaController`, `EmailController`, `ChatController`, and `StoreAdminController` enforce MIME/extension allowlists, reject double-extension tricks (e.g. `payload.php.jpg`), validate raster image headers via `getimagesize()`, and randomize filenames.
  - `FileManagerController` confines delete/trash/restore/bulk operations strictly under `public/uploads/` using `realpath` checks.
  - `ImageCropperController` accepts only PNG/JPEG/WebP/GIF data URLs and validates image bytes (`getimagesizefromstring`).
  - `BackupService::restoreBackup()` checks archive entries with `SecurityFileGuard::isSafeZipEntry()` and streams file contents safely without `ZipArchive::extractTo()`.
- **Payment & E-Commerce Security ([`PaymentGatewayService`](../app/Services/PaymentGatewayService.php), [`StoreController`](../app/Http/Controllers/Front/StoreController.php))**:
  - Sandbox decision overrides are rejected in `APP_ENV=production` and whenever a gateway uses `'live'` mode or live credentials.
  - Stripe and Razorpay webhooks fail closed (`403`) if `webhook_secret` is missing or masked.
  - Order receipts (`/ecomm/order/{orderNumber}`) enforce admin auth, owner match, checkout-session ownership, or valid signed URLs (`403` otherwise).
  - Rotate/disable the `sandbox_test` payment gateway under **Admin → Store → Payment Gateways** before live launch.
- **Auth, CSRF, Headers & Rate Limiting**:
  - Public `/admin/register` is blocked (`403` on POST, redirect on GET) once an initial `Admin` exists unless `ALLOW_ADMIN_REGISTRATION=true`.
  - User and admin logouts operate strictly via `POST` (`@csrf`).
  - [`bootstrap/app.php`](../bootstrap/app.php) trusts proxy SSL headers (`$middleware->trustProxies(at: '*')`), [`AppServiceProvider`](../app/Providers/AppServiceProvider.php) enforces HTTPS URL generation when `APP_ENV=production` and `APP_URL` starts with `https://`, and [`SecurityHeadersMiddleware`](../app/Http/Middleware/SecurityHeadersMiddleware.php) emits `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`, and HSTS on HTTPS requests.
  - Public mutations (`/comments*`, `/comment-replies*`, `/newsletter/subscribe`, `/ecomm/checkout`, `/ecomm/cart/coupon`, `/blog/{slug}/unlock`, `/api/search/autocomplete`) are rate-limited via `throttle:` middleware, and guest comments default to `pending` unless `COMMENTS_AUTO_APPROVE=true`.

---

## 6. SEO Endpoints (`robots.txt` & `sitemap.xml`)

- Static `public/robots.txt` and `public/sitemap.xml` shadow files are omitted by default so Laravel serves live domain-aware responses cleanly:
  - `GET /robots.txt` → [`SeoSettingController::robotsTxt()`](../app/Http/Controllers/Admin/SeoSettingController.php) (blocks `/admin/`, `/login`, `/register`, `/storage/app/backups/`).
  - `GET /sitemap.xml` → [`SitemapController::index()`](../app/Http/Controllers/Frontend/SitemapController.php) (cached 1 hour under `cms_sitemap_xml` using `APP_URL`).
- Optionally run `php artisan sitemap:generate` (or trigger via **Admin → URL Management**) after deploying under your production domain.

---

## 7. Shared Hosting Cron & Cache Warmup

No background daemon (Supervisor) is required. [`routes/console.php`](../routes/console.php) schedules `cms:publish-scheduled`, `queue:work --stop-when-empty --tries=3`, `sitemap:generate`, `cms:warm-cache`, and cleanup tasks. Configure one cPanel cron job running every minute:

```cron
* * * * * cd /home/<user>/xivig-cms && /usr/local/bin/php artisan schedule:run >> /dev/null 2>&1
```

Finish deployment with framework caches:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Rotate all default seeded passwords immediately upon first login.
