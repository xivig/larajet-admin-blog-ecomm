# XivigAdmin & Larajet Blog/E-Commerce Command Center

[![Laravel 12 / 13 Compatible](https://img.shields.io/badge/Laravel-12%20%7C%2013%20Compatible-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat&logo=php)](https://www.php.net)
[![Bootstrap 5](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat&logo=bootstrap)](https://getbootstrap.com)
[![License](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

An enterprise-grade, high-performance Blog & Digital Media Command Center built with **Laravel 12/13 Compatibility**, **Laravel Fortify**, **Laravel Jetstream**, and **Bootstrap 5**. Designed with a dual-guard architecture (`admin` and `web`), granular Role-Based Access Control (RBAC), an interactive Cropper.js Studio, modern File Manager, Webmail, Real-Time Chat, and an automated SEO/Backup suite.

---

## Table of Contents

1. [Architectural Overview](#architectural-overview)
2. [Key Features & Capabilities](#key-features--capabilities)
3. [System Requirements](#system-requirements)
4. [Step-by-Step Installation Guide](#step-by-step-installation-guide)
5. [Default Credentials & Access](#default-credentials--access)
6. [Core Modules Documentation](#core-modules-documentation)
   - [Authentication & RBAC Security](#authentication--rbac-security)
   - [User Management & Editorial Authors](#user-management--editorial-authors)
   - [Blog & Publication Management](#blog--publication-management)
   - [Categories & Tags](#categories--tags)
   - [Media Library & Cropper.js Studio](#media-library--cropperjs-studio)
   - [Apps: File Manager](#apps-file-manager)
   - [Apps: Webmail & Communications](#apps-webmail--communications)
   - [Apps: Live Workspace Chat](#apps-live-workspace-chat)
   - [Comments & Moderation](#comments--moderation)
   - [SEO Suite & Sitemap Generator](#seo-suite--sitemap-generator)
   - [Activity Logs & System Backups](#activity-logs--system-backups)
7. [Frontend Magazine Blog Walkthrough](#frontend-magazine-blog-walkthrough)
8. [Directory Structure](#directory-structure)
9. [CLI Commands & Maintenance](#cli-commands--maintenance)
10. [Troubleshooting & FAQs](#troubleshooting--faqs)

---

## Architectural Overview

- **Framework**: Laravel 12 / 13 ready with Strict Type Checking and Modern PHP 8.2+ Features.
- **Dual-Guard Authentication**:
  - `web`: Standard community readers, subscribers, and profile management (Fortify/Jetstream).
  - `admin`: Dedicated command center session guard (`Admin` model, customized login session handling, and activity logging).
- **Database Engine**: Out-of-the-box **SQLite** for rapid portable development and zero-configuration setups, with 100% **MySQL / PostgreSQL** production compatibility.
- **UI Engine**: Pure **Bootstrap 5.3**, compiled modern SCSS, dynamic CSS Variables, and responsive Light/Dark mode toggling.

---

## Key Features & Capabilities

- **Role-Based Access Control (RBAC)**: Fine-grained security roles (`Super Administrator`, `Managing Editor`, `Technical Author`, `Community Moderator`, `Subscriber`) with dynamic role-permission inheritance and authority auditing.
- **Soft Delete & Bulk Workflows**: Full soft-deletion lifecycle across Users, Posts, Categories, Tags, Comments, Galleries, and File Manager with multi-selection checkboxes and batch operations.
- **Interactive Cropper.js Studio**: Real-time canvas cropping, responsive live previews (16:9 Banner, 1:1 Square, Avatar Circle), boundary telemetry coordinates (X, Y, W, H, Angle), aspect-ratio presets, and 1-click media library persistence.
- **Modern File Manager**: Split-view workspace featuring dedicated directory trees (`/uploads/posts`, `/uploads/avatars`, `/uploads/chat`, `/uploads/trash`), live storage quota telemetry, instantaneous client-side filtering, grid/list view toggles, and bulk purge/restore.
- **Internal Communications Suite**:
  - **Webmail**: Folder categorization (`Inbox`, `Starred`, `Sent`, `Drafts`, `Trash`), compose modal, unread counts, and search.
  - **Live Chat**: Public team channels (`#general`, `#engineering`, `#editorial`) and private 1-on-1 direct messaging with attachment support.
- **SEO & Search Engine Intelligence**: Meta health scoring, dynamic `sitemap.xml`, and editable `robots.txt` generator.
- **Backup & Audit Engine**: Single-click database snapshot generator with downloadable ZIP archives, instant restore point rollbacks, and CSV export for activity audit logs.

---

## System Requirements

- **PHP**: `^8.2` or `^8.3`
- **Required PHP Extensions**: `pdo_sqlite` (or `pdo_mysql`), `mbstring`, `openssl`, `curl`, `fileinfo`, `gd` or `imagick`, `zip`
- **Composer**: `v2.x`
- **Node.js**: `v18.x` or `v20.x` & NPM

---

## Step-by-Step Installation Guide

### Step 1: Clone the Repository

```bash
git clone https://github.com/xivig/larajet-admin-blog-ecomm.git
cd larajet-admin-blog-ecomm
```

### Step 2: Environment Configuration

Copy the example environment file and create your local `.env`:

```bash
cp .env.example .env
```

By default, the `.env` is pre-configured for portable **SQLite**:

```env
APP_NAME=XivigAdmin
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

> **Note for MySQL users**: If you prefer MySQL, create an empty database and update your `.env`:
> ```env
> DB_CONNECTION=mysql
> DB_HOST=127.0.0.1
> DB_PORT=3306
> DB_DATABASE=xivig_admin
> DB_USERNAME=root
> DB_PASSWORD=
> ```

### Step 3: Install Dependencies

Install all Composer PHP packages:

```bash
composer install
```

Ensure SQLite database file exists (if using SQLite):

```bash
# On Linux/macOS
touch database/database.sqlite

# On Windows PowerShell
New-Item -ItemType File -Path database\database.sqlite -Force
```

### Step 4: Generate Application Encryption Key

```bash
php artisan key:generate
```

### Step 5: Run Database Migrations & Seeds

Execute the complete migration chain to create all system tables and seed initial roles, authors, settings, and sample data:

```bash
php artisan migrate --seed
```

### Step 6: Create Storage Symlink & Upload Directories

Create the public storage symlink and ensure upload folders are writable:

```bash
php artisan storage:link
```

Ensure the following upload directories exist:
- `public/uploads/posts`
- `public/uploads/avatars`
- `public/uploads/chat`
- `public/uploads/media`
- `public/uploads/galleries`

### Step 7: Launch Development Server

Start the local PHP server:

```bash
php artisan serve
```

The application is now accessible at:
- **Public Magazine Blog**: `http://localhost:8000`
- **Admin Command Center**: `http://localhost:8000/admin`

---

## Default Credentials & Access

| Portal | URL Route | Default Email | Default Password | Role |
| :--- | :--- | :--- | :--- | :--- |
| **Admin Command Center** | `/admin/login` | `admin@xivig.com` | `password` | Super Administrator |
| **Community Web Portal** | `/login` | `may32@example.com` | `password` | Subscriber / Author |

---

## Core Modules Documentation

### Authentication & RBAC Security
- **Dual Guards**: Handled via `config/auth.php`. Web requests authenticate against standard users, while `/admin/*` routes enforce the `auth:admin` middleware.
- **Roles & Permissions**: Access `/admin/roles` to inspect the permission matrix. Five default security roles govern access:
  - `Super Administrator`: Unrestricted access across all system entities, settings, and audits.
  - `Managing Editor`: Full editorial control over publications, categories, tags, and media.
  - `Technical Author`: Permission to draft, compose, and manage assigned publications.
  - `Community Moderator`: Moderates comments and discussion queues.
  - `Subscriber`: Standard community profile with read and comment access.

### User Management & Editorial Authors
- **Path**: `/admin/users`
- **Default Role Assignment**: Newly created users without an explicit role selection are automatically assigned the `Subscriber` role.
- **Editorial Author Switch**: Toggle the "Editorial Author Contributor" switch in user create/edit to instantly register or revoke publication authority.
- **Historical Post Integrity**: When an author's contributor status is revoked, historical articles preserve attribution via the `former_user_id` safety link without database constraint violations.
- **Soft Deletes & Multi-Selection**: Filter between `Active`, `Trash`, and `All`. Batch-select users using `#selectAllUsers` to activate, deactivate, or soft-delete in bulk.

### Blog & Publication Management
- **Path**: `/admin/posts`
- **Features**:
  - Full WYSIWYG article composer with Quill.js editor.
  - Category, tag, and editorial author association.
  - Direct integration with Media Library asset picker.
  - Publication lifecycle: Draft, Published, Soft Delete (Trash), Restore, and Permanent Purge.
  - Batch actions for multi-selection post publishing, unpublishing, or deletion.

### Categories & Tags
- **Paths**: `/admin/categories` and `/admin/tags`
- **Features**:
  - Modal-driven, instantaneous category creation and updates.
  - Unique slug generation and article usage counters.
  - Multi-selection checkboxes for batch status toggles and soft deletion.

### Media Library & Cropper.js Studio
- **Media Library Path**: `/admin/media`
- **Image Cropper Studio Path**: `/admin/image-cropper`
- **Cropper Studio Capabilities**:
  - **Pan vs. Crop Modes**: Click `Crop` to draw or adjust selection boxes, or click `Move` to pan the background canvas.
  - **Quick Controls**: Instant `Show / Restore Selection` (`crop`) and `Clear Selection` (`clear`).
  - **Aspect Ratio Locks**: `16:9 Banner`, `4:3 Card`, `1:1 Square`, `2:3 Portrait`, or `Free Form` arbitrary selection.
  - **Boundary Telemetry**: Real-time pixel readout of `X`, `Y`, `Width`, `Height`, and `Rotation Angle`.
  - **Direct Export**: Render cropped canvas and persist directly into the Media Library under `/uploads/media/YYYY/MM`.

### Apps: File Manager
- **Path**: `/admin/file-manager`
- **Architecture**:
  - Built with a modern two-column layout (`.fm-sidebar` and `.fm-main`).
  - Directory Tree: `/uploads/posts`, `/uploads/avatars`, `/uploads/chat`, `/admin-dist/img`, and `/uploads/trash`.
  - Live Storage Quota widget calculating real disk consumption against configurable capacity.
  - Grid vs. List layout toggle with persistent state.
  - Instant client-side search filtering by filename.
  - Batch selection toolbar for bulk file downloads, trash transfers, and permanent purge.

### Apps: Webmail & Communications
- **Path**: `/admin/email`
- **Capabilities**:
  - Built-in mail viewer with folder tabs: `Inbox`, `Starred`, `Sent`, `Drafts`, `Trash`.
  - Real-time unread badges.
  - Compose modal for sending outbound notifications or saving drafts.
  - Single-click starring, marking as read, and moving to trash.

### Apps: Live Workspace Chat
- **Path**: `/admin/chat`
- **Capabilities**:
  - Public collaborative channels: `#general`, `#engineering`, `#editorial`.
  - Private 1-on-1 direct messaging between team members with dedicated recipient routing.
  - File and image attachment uploads under `uploads/chat`.
  - REST polling API (`/admin/chat/api/messages`) for live updates.

### Comments & Moderation
- **Path**: `/admin/comments`
- **Capabilities**:
  - Moderation queue filtered by `All`, `Approved`, `Pending`, `Spam`, and `Trash`.
  - Single-click approve (`admin.comments.approve`), mark spam, or delete.
  - Administrator inline reply drawer to reply to community discussions directly.

### SEO Suite & Sitemap Generator
- **Path**: `/admin/settings/seo`
- **Capabilities**:
  - Real-time **SEO Health Score** calculated from titles, meta descriptions, OpenGraph tags, sitemap availability, and canonical URLs.
  - Live preview cards for Google Search SERP snippets and Facebook/Twitter Social Cards.
  - Built-in `robots.txt` editor with one-click factory reset.
  - Dynamic XML Sitemap available publicly at `/sitemap.xml`.

### Activity Logs & System Backups
- **Activity Logs Path**: `/admin/activity-logs`
  - Real-time audit log tracking user logins, article publications, role modifications, and system edits.
  - Filter by event type, date range, or actor.
  - One-click CSV audit log export.
- **System Backups Path**: `/admin/backups`
  - Generate full database snapshots on demand.
  - Secure storage in `storage/app/backups/`.
  - Instant one-click rollback/restore or ZIP download.

---

## Frontend Magazine Blog Walkthrough

- **Home (`/`)**: Hero carousel, featured editorials, trending topics, and newsletter signup.
- **Blog Archive (`/blog` and `/blog-list`)**: Grid and list article views with category and tag filter sidebars.
- **Single Article (`/single-post/{slug}`)**: Full article reader with author biography box, related posts carousel, and nested comment/reply discussion threads.
- **Theme Switcher**: Floating bottom-right widget to toggle between Light and Dark mode with `localStorage` persistence.

---

## Directory Structure

```text
larajet-admin-blog-ecomm/
├── app/
│   ├── Actions/Fortify/          # Fortify user creation, reset, and password rules
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/            # Admin controllers (Posts, Users, Media, Cropper, Chat, etc.)
│   │   │   └── Front/            # Frontend blog controllers
│   │   └── Middleware/           # Guard & route authentication middleware
│   └── Models/                   # Eloquent models (Post, User, Role, Category, Picture, etc.)
├── database/
│   ├── factories/                # Model seed factories
│   ├── migrations/               # Database schema definitions
│   └── seeders/                  # Role, permission, and demo data seeders
├── public/
│   ├── admin-dist/               # Compiled admin CSS, JS, icons, and Cropper assets
│   ├── uploads/                  # User uploads (posts, avatars, media, chat)
│   └── xivig-blog/               # Frontend blog styles, fonts, and scripts
├── resources/
│   ├── views/
│   │   ├── admin/                # Admin Blade views categorized by module
│   │   ├── blog/                 # Frontend blog Blade templates & layouts
│   │   ├── layouts/              # Master layouts (admin.blade.php, guest.blade.php)
│   │   └── partials/             # Admin header, sidebar, footer, head partials
└── routes/
    ├── web.php                   # All public and admin route definitions
    └── api.php                   # API endpoints
```

---

## CLI Commands & Maintenance

```bash
# Clear all application caches
php artisan optimize:clear

# Re-compile and cache Blade views
php artisan view:clear && php artisan view:cache

# Re-compile route cache
php artisan route:clear && php artisan route:cache

# Re-compile config cache
php artisan config:clear && php artisan config:cache

# Check migration status
php artisan migrate:status

# Create a fresh database snapshot via CLI
php artisan tinker --execute="app(App\Http\Controllers\Admin\BackupController::class)->store();"
```

---

## Troubleshooting & FAQs

### 1. `SQLSTATE[HY000]: General error: 1 no such table`
Run migrations to initialize the schema:
```bash
php artisan migrate --seed
```

### 2. Missing Avatar or Post Images
Ensure the storage link has been created and the upload folders exist:
```bash
php artisan storage:link
```

### 3. Permission denied on SQLite database
Ensure the `database/database.sqlite` file and the `database/` folder have read/write permissions for the web server user (`chmod 775` on Linux/macOS).

### 4. Image Cropper canvas export fails
If loading images from external CDNs, ensure CORS headers (`Access-Control-Allow-Origin: *`) are returned, or load images directly through the local Media Library.

---

## License

This software is open-sourced under the [MIT license](LICENSE).
Developed with excellence for the Xivig Enterprise Ecosystem.
