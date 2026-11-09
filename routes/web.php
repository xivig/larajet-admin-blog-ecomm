<?php

use App\Http\Controllers\Admin\ActivityLogController as AdminActivityLogController;
use App\Http\Controllers\Admin\BackupController as AdminBackupController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EmailController as AdminEmailController;
use App\Http\Controllers\Admin\FileManagerController as AdminFileManagerController;
use App\Http\Controllers\Admin\ForgotPasswordController;
use App\Http\Controllers\Admin\GalleryController as AdminGalleryController;
use App\Http\Controllers\Admin\ImageCropperController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\MediaController as AdminMediaController;
use App\Http\Controllers\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\Admin\NewsletterController as AdminNewsletterController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\RegisterController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\SeoSettingController as AdminSeoSettingController;
use App\Http\Controllers\Admin\SessionController as AdminSessionController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\TagController as AdminTagController;
use App\Http\Controllers\Admin\TwoFactorChallengeController as AdminTwoFactorChallengeController;
use App\Http\Controllers\Admin\TwoFactorController as AdminTwoFactorController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentReplyController;
use App\Http\Controllers\Front\BlogController;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

// Public marketing & content pages
Route::get('/', function () {
    $title = 'Home page';
    return view('home.content', ['title' => $title]);
})->name('home');

Route::get('/about', static fn() => view('home.about'))->name('about');
Route::get('/contact', static fn() => view('home.contact'))->name('contact');
Route::get('/services', static fn() => view('home.services'))->name('services');
Route::get('/pricing', static fn() => view('home.pricing'))->name('pricing');
Route::get('/project', static fn() => view('home.project'))->name('project');
Route::get('/ecomm', static fn() => view('ecomm'))->name('ecomm');

// Blog routes
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog-list', [BlogController::class, 'index'])->name('blog.list');
Route::get('/blog-post', static fn() => redirect()->route('blog.list'));

// Dist-front extensions: Magazine, Feed, Gallery, Videos, Formats, Authors, Tags, Categories
Route::get('/blog/magazine', [BlogController::class, 'magazine'])->name('blog.magazine');
Route::get('/magazine', [BlogController::class, 'magazine']);

Route::get('/blog/magazine-post/{slug?}', [BlogController::class, 'singleMagazine'])->name('blog.single-magazine');
Route::get('/post-magazine/{slug?}', [BlogController::class, 'singleMagazine']);

Route::get('/blog/format/image/{slug?}', [BlogController::class, 'formatImage'])->name('blog.format.image');
Route::get('/blog/format/link/{slug?}', [BlogController::class, 'formatLink'])->name('blog.format.link');

Route::get('/blog/feed', [BlogController::class, 'feed'])->name('blog.feed');
Route::get('/feed', [BlogController::class, 'feed']);

Route::get('/blog/gallery', [BlogController::class, 'gallery'])->name('blog.gallery');
Route::get('/gallery', [BlogController::class, 'gallery']);

Route::get('/blog/videos', [BlogController::class, 'videos'])->name('blog.videos');
Route::get('/videos', [BlogController::class, 'videos']);
Route::get('/blog/video/{slug}', [BlogController::class, 'singleVideo'])->name('blog.video');

Route::get('/blog/authors', [BlogController::class, 'authors'])->name('blog.authors');
Route::get('/authors', [BlogController::class, 'authors']);
Route::get('/blog/author/{id}', [BlogController::class, 'author'])->name('blog.author');
Route::get('/author/{id}', [BlogController::class, 'author']);

Route::get('/blog/tags', [BlogController::class, 'tags'])->name('blog.tags');
Route::get('/blog/tag/{id}', [BlogController::class, 'tag'])->name('blog.tag');
Route::get('/tag/{id}', [BlogController::class, 'tag']);

Route::get('/blog/category/{id}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/category/{id}', [BlogController::class, 'category']);

Route::get('/blog/{slug}', [BlogController::class, 'singlePost'])->name('single-post');
Route::get('/post/{slug}', [BlogController::class, 'singlePost'])->name('blog.single');
Route::post('/blog/{slug}/unlock', [BlogController::class, 'unlock'])->name('blog.unlock');
Route::get('/single-post/{slug}', [BlogController::class, 'singlePost']);

// Comments & replies
Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
Route::post('/comment-replies', [CommentReplyController::class, 'store'])->name('comment-replies.store');

// Newsletter subscription
Route::post('/newsletter/subscribe', [AdminNewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// General previews
Route::get('/user', static fn() => view('user'));
Route::get('/admin', static fn() => redirect()->route('admin.dashboard'))->name('admin');

// Fortify & Jetstream Authenticated User Routes
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Admin Guest Routes
Route::prefix('admin')->middleware('guest:admin')->group(function () {
    Route::get('register', [RegisterController::class, 'create'])->name('admin.register');
    Route::post('register', [RegisterController::class, 'store'])->name('admin.register.store');

    Route::get('login', [LoginController::class, 'create'])->name('admin.login');
    Route::post('login', [LoginController::class, 'store'])->name('admin.login.store');

    // Admin Password Reset
    Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('admin.password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('admin.password.email');
    Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('admin.password.reset');
    Route::post('reset-password', [ForgotPasswordController::class, 'reset'])->name('admin.password.update');
});

// Admin 2FA Challenge (when 2FA is prompted upon sign-in)
Route::prefix('admin')->group(function () {
    Route::get('two-factor-challenge', [AdminTwoFactorChallengeController::class, 'create'])->name('admin.two-factor.challenge');
    Route::post('two-factor-challenge', [AdminTwoFactorChallengeController::class, 'store'])->name('admin.two-factor.challenge.store');
});

// Admin Authenticated Routes
Route::prefix('admin')->middleware('auth:admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::post('logout', [LoginController::class, 'destroy'])->name('admin.logout');

    // Posts Management
    Route::post('posts/bulk', [AdminPostController::class, 'bulkAction'])->name('admin.posts.bulk');
    Route::post('posts/quick-draft', [AdminPostController::class, 'quickDraft'])->name('admin.posts.quick-draft');
    Route::post('posts/{post}/clone', [AdminPostController::class, 'clone'])->name('admin.posts.clone');
    Route::get('posts/{post}/preview', [AdminPostController::class, 'preview'])->name('admin.posts.preview');
    Route::delete('posts/{post}/gallery/{image}', [AdminPostController::class, 'deleteGalleryImage'])->name('admin.posts.gallery.delete');
    Route::post('posts/{post}/duplicate', [AdminPostController::class, 'duplicate'])->name('admin.posts.duplicate');
    Route::post('posts/{id}/restore', [AdminPostController::class, 'restore'])->name('admin.posts.restore');
    Route::delete('posts/{id}/force-delete', [AdminPostController::class, 'forceDelete'])->name('admin.posts.force-delete');
    Route::resource('posts', AdminPostController::class)->names('admin.posts');

    // Static Pages Management
    Route::resource('pages', AdminPageController::class)->names('admin.pages');

    // Navigation Menu Builder
    Route::get('menu', [AdminMenuController::class, 'index'])->name('admin.menu.index');
    Route::post('menu', [AdminMenuController::class, 'store'])->name('admin.menu.store');
    Route::delete('menu/{menu}', [AdminMenuController::class, 'destroy'])->name('admin.menu.destroy');
    Route::post('menu/{menu}/items', [AdminMenuController::class, 'addItem'])->name('admin.menu.items.add');
    Route::delete('menu/items/{item}', [AdminMenuController::class, 'deleteItem'])->name('admin.menu.items.delete');

    // Newsletter Subscribers & Broadcasts
    Route::get('newsletter', [AdminNewsletterController::class, 'index'])->name('admin.newsletter.index');
    Route::post('newsletter/send', [AdminNewsletterController::class, 'send'])->name('admin.newsletter.send');
    Route::get('newsletter/export', [AdminNewsletterController::class, 'export'])->name('admin.newsletter.export');
    Route::delete('newsletter/{subscriber}', [AdminNewsletterController::class, 'destroy'])->name('admin.newsletter.destroy');

    // Categories Management
    Route::post('categories/bulk', [AdminCategoryController::class, 'bulkAction'])->name('admin.categories.bulk');
    Route::post('categories/{category}/duplicate', [AdminCategoryController::class, 'duplicate'])->name('admin.categories.duplicate');
    Route::post('categories/{id}/restore', [AdminCategoryController::class, 'restore'])->name('admin.categories.restore');
    Route::delete('categories/{id}/force-delete', [AdminCategoryController::class, 'forceDelete'])->name('admin.categories.force-delete');
    Route::resource('categories', AdminCategoryController::class)->names('admin.categories');

    // Tags Management
    Route::post('tags/bulk', [AdminTagController::class, 'bulkAction'])->name('admin.tags.bulk');
    Route::post('tags/{id}/restore', [AdminTagController::class, 'restore'])->name('admin.tags.restore');
    Route::delete('tags/{id}/force-delete', [AdminTagController::class, 'forceDelete'])->name('admin.tags.force-delete');
    Route::resource('tags', AdminTagController::class)->names('admin.tags');

    // Comments Moderation & Actions
    Route::get('comments', [AdminCommentController::class, 'index'])->name('admin.comments.index');
    Route::match(['get', 'post'], 'comments/{comment}/approve', [AdminCommentController::class, 'approve'])->name('admin.comments.approve');
    Route::post('comments/{comment}/status', [AdminCommentController::class, 'updateStatus'])->name('admin.comments.status');
    Route::post('comments/{comment}/reply', [AdminCommentController::class, 'reply'])->name('admin.comments.reply');
    Route::put('comments/{comment}', [AdminCommentController::class, 'update'])->name('admin.comments.update');
    Route::post('comments/bulk', [AdminCommentController::class, 'bulkAction'])->name('admin.comments.bulk');
    Route::post('comments/{id}/restore', [AdminCommentController::class, 'restore'])->name('admin.comments.restore');
    Route::delete('comments/{id}/force-delete', [AdminCommentController::class, 'forceDelete'])->name('admin.comments.force-delete');
    Route::delete('comments/{comment}', [AdminCommentController::class, 'destroy'])->name('admin.comments.destroy');

    // Notifications Center
    Route::get('notifications', [AdminNotificationController::class, 'index'])->name('admin.notifications.index');
    Route::match(['get', 'post'], 'notifications/{notification}/read', [AdminNotificationController::class, 'markAsRead'])->name('admin.notifications.read');
    Route::match(['get', 'post'], 'notifications/mark-all-read', [AdminNotificationController::class, 'markAllAsRead'])->name('admin.notifications.mark-all-read');
    Route::match(['get', 'post'], 'notifications/clear-read', [AdminNotificationController::class, 'clearRead'])->name('admin.notifications.clear-read');
    Route::delete('notifications/{notification}', [AdminNotificationController::class, 'destroy'])->name('admin.notifications.destroy');

    // Chat App
    Route::get('chat', [AdminChatController::class, 'index'])->name('admin.chat.index');
    Route::post('chat', [AdminChatController::class, 'store'])->name('admin.chat.store');
    Route::get('chat/api/messages', [AdminChatController::class, 'fetchMessages'])->name('admin.chat.messages');

    // File Manager App
    Route::get('file-manager', [AdminFileManagerController::class, 'index'])->name('admin.file-manager.index');
    Route::post('file-manager/create-folder', [AdminFileManagerController::class, 'createFolder'])->name('admin.file-manager.create-folder');
    Route::post('file-manager/upload', [AdminFileManagerController::class, 'upload'])->name('admin.file-manager.upload');
    Route::post('file-manager/duplicate', [AdminFileManagerController::class, 'duplicate'])->name('admin.file-manager.duplicate');
    Route::post('file-manager/restore', [AdminFileManagerController::class, 'restore'])->name('admin.file-manager.restore');
    Route::delete('file-manager/force-delete', [AdminFileManagerController::class, 'forceDelete'])->name('admin.file-manager.force-delete');
    Route::post('file-manager/empty-trash', [AdminFileManagerController::class, 'emptyTrash'])->name('admin.file-manager.empty-trash');
    Route::post('file-manager/bulk', [AdminFileManagerController::class, 'bulkAction'])->name('admin.file-manager.bulk');
    Route::delete('file-manager', [AdminFileManagerController::class, 'destroy'])->name('admin.file-manager.destroy');

    // Email / Communications App
    Route::get('email', [AdminEmailController::class, 'index'])->name('admin.email.index');
    Route::post('email', [AdminEmailController::class, 'store'])->name('admin.email.store');
    Route::post('email/bulk', [AdminEmailController::class, 'bulkAction'])->name('admin.email.bulk');
    Route::match(['get', 'post'], 'email/mark-all-read', [AdminEmailController::class, 'markAllRead'])->name('admin.email.mark-all-read');
    Route::post('email/empty-trash', [AdminEmailController::class, 'emptyTrash'])->name('admin.email.empty-trash');
    Route::get('email/{email}', [AdminEmailController::class, 'show'])->name('admin.email.show');
    Route::post('email/{email}/restore', [AdminEmailController::class, 'restore'])->name('admin.email.restore');
    Route::post('email/{email}/mark-read', [AdminEmailController::class, 'markRead'])->name('admin.email.mark-read');
    Route::post('email/{email}/mark-unread', [AdminEmailController::class, 'markUnread'])->name('admin.email.mark-unread');
    Route::post('email/{email}/spam', [AdminEmailController::class, 'reportSpam'])->name('admin.email.spam');
    Route::post('email/{email}/star', [AdminEmailController::class, 'toggleStar'])->name('admin.email.star');
    Route::post('email/{email}/move', [AdminEmailController::class, 'moveFolder'])->name('admin.email.move');
    Route::delete('email/{email}', [AdminEmailController::class, 'destroy'])->name('admin.email.destroy');

    // Roles & Permissions App
    Route::get('roles', [AdminRoleController::class, 'index'])->name('admin.roles.index');
    Route::post('roles', [AdminRoleController::class, 'store'])->name('admin.roles.store');
    Route::put('roles/{role}', [AdminRoleController::class, 'update'])->name('admin.roles.update');
    Route::delete('roles/{role}', [AdminRoleController::class, 'destroy'])->name('admin.roles.destroy');
    Route::post('roles/assign', [AdminRoleController::class, 'assignUser'])->name('admin.roles.assign');
    Route::post('roles/{role}/remove-user', [AdminRoleController::class, 'removeUser'])->name('admin.roles.remove-user');

    // Media Library
    Route::get('media', [AdminMediaController::class, 'index'])->name('admin.media.index');
    Route::post('media', [AdminMediaController::class, 'store'])->name('admin.media.store');
    Route::get('media/picker-api', [AdminMediaController::class, 'pickerApi'])->name('admin.media.picker');
    Route::post('media/{picture}/duplicate', [AdminMediaController::class, 'duplicate'])->name('admin.media.duplicate');
    Route::post('media/{id}/restore', [AdminMediaController::class, 'restore'])->name('admin.media.restore');
    Route::delete('media/{id}/force-delete', [AdminMediaController::class, 'forceDelete'])->name('admin.media.force-delete');
    Route::get('media/{picture}', [AdminMediaController::class, 'show'])->name('admin.media.show');
    Route::match(['put', 'post'], 'media/{picture}', [AdminMediaController::class, 'update'])->name('admin.media.update');
    Route::post('media/bulk', [AdminMediaController::class, 'bulkAction'])->name('admin.media.bulk');
    Route::delete('media/{picture}', [AdminMediaController::class, 'destroy'])->name('admin.media.destroy');

    // Image Cropper Studio
    Route::get('image-cropper', [ImageCropperController::class, 'index'])->name('admin.image-cropper.index');
    Route::post('image-cropper/save', [ImageCropperController::class, 'save'])->name('admin.image-cropper.save');

    // Users Management & Authority Reassignment
    Route::post('users/bulk', [AdminUserController::class, 'bulkAction'])->name('admin.users.bulk');
    Route::post('users/{id}/restore', [AdminUserController::class, 'restore'])->name('admin.users.restore');
    Route::delete('users/{id}/force-delete', [AdminUserController::class, 'forceDelete'])->name('admin.users.force-delete');
    Route::post('users/{user}/reassign-authority', [AdminUserController::class, 'reassignAuthority'])->name('admin.users.reassign-authority');
    Route::resource('users', AdminUserController::class)->names('admin.users');

    // Gallery Management
    Route::post('galleries/bulk', [AdminGalleryController::class, 'bulkAction'])->name('admin.galleries.bulk');
    Route::post('galleries/{id}/restore', [AdminGalleryController::class, 'restore'])->name('admin.galleries.restore');
    Route::delete('galleries/{id}/force-delete', [AdminGalleryController::class, 'forceDelete'])->name('admin.galleries.force-delete');
    Route::post('galleries/{gallery}/photos', [AdminGalleryController::class, 'uploadPhotos'])->name('admin.galleries.photos.upload');
    Route::post('galleries/{gallery}/attach-media', [AdminGalleryController::class, 'attachMedia'])->name('admin.galleries.media.attach');
    Route::delete('galleries/photos/{item}', [AdminGalleryController::class, 'deletePhoto'])->name('admin.galleries.photos.delete');
    Route::post('galleries/{gallery}/cover/{item}', [AdminGalleryController::class, 'setCover'])->name('admin.galleries.cover');
    Route::resource('galleries', AdminGalleryController::class)->names('admin.galleries');

    // Site Settings Management
    Route::get('settings', [AdminSettingController::class, 'index'])->name('admin.settings.index');
    Route::post('settings/maintenance', [AdminSettingController::class, 'updateMaintenance'])->name('admin.settings.maintenance');
    Route::post('settings/integrations', [AdminSettingController::class, 'updateIntegrations'])->name('admin.settings.integrations');
    Route::post('settings/editorial', [AdminSettingController::class, 'updateEditorial'])->name('admin.settings.editorial');
    Route::post('settings/timezone', [AdminSettingController::class, 'updateTimezone'])->name('admin.settings.timezone');
    Route::post('settings/cache/purge', [AdminSettingController::class, 'purgeCache'])->name('admin.settings.cache.purge');
    Route::post('settings/cache/warm', [AdminSettingController::class, 'warmCache'])->name('admin.settings.cache.warm');
    Route::post('settings/scheduler/run', [AdminSettingController::class, 'runScheduler'])->name('admin.settings.scheduler.run');
    Route::post('settings/queue/work', [AdminSettingController::class, 'runQueueWorker'])->name('admin.settings.queue.work');
    Route::post('settings/queue/retry', [AdminSettingController::class, 'retryFailedJobs'])->name('admin.settings.queue.retry');
    Route::post('settings/queue/flush', [AdminSettingController::class, 'flushFailedJobs'])->name('admin.settings.queue.flush');

    // SEO & Search Engine Intelligence Suite
    Route::get('settings/seo', [AdminSeoSettingController::class, 'index'])->name('admin.settings.seo');
    Route::post('settings/seo', [AdminSeoSettingController::class, 'update'])->name('admin.settings.seo.update');
    Route::post('settings/seo/reset-robots', [AdminSeoSettingController::class, 'resetRobots'])->name('admin.settings.seo.reset-robots');

    // Activity Logs
    Route::get('activity-logs', [AdminActivityLogController::class, 'index'])->name('admin.activity-logs.index');
    Route::post('activity-logs/clear', [AdminActivityLogController::class, 'clear'])->name('admin.activity-logs.clear');
    Route::get('activity-logs/export', [AdminActivityLogController::class, 'export'])->name('admin.activity-logs.export');

    // System Backups
    Route::get('backups', [AdminBackupController::class, 'index'])->name('admin.backups.index');
    Route::post('backups', [AdminBackupController::class, 'store'])->name('admin.backups.store');
    Route::get('backups/{backup}/download', [AdminBackupController::class, 'download'])->name('admin.backups.download');
    Route::post('backups/{backup}/restore', [AdminBackupController::class, 'restore'])->name('admin.backups.restore');
    Route::delete('backups/{backup}', [AdminBackupController::class, 'destroy'])->name('admin.backups.destroy');

    // Admin Profile
    Route::get('profile', [AdminProfileController::class, 'edit'])->name('admin.profile');
    Route::put('profile', [AdminProfileController::class, 'update'])->name('admin.profile.update');
    Route::put('profile/password', [AdminProfileController::class, 'updatePassword'])->name('admin.profile.password');
    Route::post('profile/email-verification/send', [AdminProfileController::class, 'sendVerification'])->name('admin.profile.verification.send');

    // Admin 2FA Management (Google Authenticator)
    Route::post('profile/two-factor/enable', [AdminTwoFactorController::class, 'enable'])->name('admin.profile.two-factor.enable');
    Route::post('profile/two-factor/confirm', [AdminTwoFactorController::class, 'confirm'])->name('admin.profile.two-factor.confirm');
    Route::post('profile/two-factor/recovery-codes', [AdminTwoFactorController::class, 'regenerateRecoveryCodes'])->name('admin.profile.two-factor.recovery-codes');
    Route::get('profile/two-factor/recovery-codes/download', [AdminTwoFactorController::class, 'downloadRecoveryCodes'])->name('admin.profile.two-factor.download-codes');
    Route::post('profile/two-factor/disable', [AdminTwoFactorController::class, 'disable'])->name('admin.profile.two-factor.disable');

    // Admin Active Sessions Management
    Route::delete('profile/sessions/{sessionId}', [AdminSessionController::class, 'destroy'])->name('admin.profile.sessions.destroy');
    Route::post('profile/sessions/logout-others', [AdminSessionController::class, 'logoutOthers'])->name('admin.profile.sessions.logout-others');
});

// Dynamic Public Feeds & Crawlers
Route::get('/feed.xml', [AdminSettingController::class, 'feedXml'])->name('feed.xml');
Route::get('/sitemap.xml', [AdminSettingController::class, 'sitemapXml'])->name('sitemap.xml');
Route::get('/robots.txt', [AdminSeoSettingController::class, 'robotsTxt'])->name('robots.txt');

// Auth logout & user dashboard subpages
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('dashboard')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile'])->middleware('auth')->name('user-profile');
    Route::get('/inbox', [AuthController::class, 'inbox'])->middleware('auth')->name('user-inbox');
    Route::get('/setting', [AuthController::class, 'setting'])->middleware('auth')->name('user-setting');
});
