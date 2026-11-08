<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EmailController as AdminEmailController;
use App\Http\Controllers\Admin\FileManagerController as AdminFileManagerController;
use App\Http\Controllers\Admin\ForgotPasswordController;
use App\Http\Controllers\Admin\ImageCropperController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\MediaController as AdminMediaController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\RegisterController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\TagController as AdminTagController;
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
Route::get('/blog/{slug}', [BlogController::class, 'singlePost'])->name('single-post');
Route::get('/single-post/{slug}', [BlogController::class, 'singlePost']);

// Comments & replies
Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
Route::post('/comment-replies', [CommentReplyController::class, 'store'])->name('comment-replies.store');

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

// Admin Authenticated Routes
Route::prefix('admin')->middleware('auth:admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::post('logout', [LoginController::class, 'destroy'])->name('admin.logout');

    // Posts Management
    Route::post('posts/quick-draft', [AdminPostController::class, 'quickDraft'])->name('admin.posts.quick-draft');
    Route::post('posts/{post}/duplicate', [AdminPostController::class, 'duplicate'])->name('admin.posts.duplicate');
    Route::post('posts/{id}/restore', [AdminPostController::class, 'restore'])->name('admin.posts.restore');
    Route::delete('posts/{id}/force-delete', [AdminPostController::class, 'forceDelete'])->name('admin.posts.force-delete');
    Route::resource('posts', AdminPostController::class)->names('admin.posts');

    // Categories Management
    Route::post('categories/{category}/duplicate', [AdminCategoryController::class, 'duplicate'])->name('admin.categories.duplicate');
    Route::post('categories/{id}/restore', [AdminCategoryController::class, 'restore'])->name('admin.categories.restore');
    Route::delete('categories/{id}/force-delete', [AdminCategoryController::class, 'forceDelete'])->name('admin.categories.force-delete');
    Route::resource('categories', AdminCategoryController::class)->names('admin.categories');

    // Tags Management
    Route::post('tags/{id}/restore', [AdminTagController::class, 'restore'])->name('admin.tags.restore');
    Route::delete('tags/{id}/force-delete', [AdminTagController::class, 'forceDelete'])->name('admin.tags.force-delete');
    Route::resource('tags', AdminTagController::class)->names('admin.tags');

    // Comments Moderation & Actions
    Route::get('comments', [AdminCommentController::class, 'index'])->name('admin.comments.index');
    Route::post('comments/{comment}/status', [AdminCommentController::class, 'updateStatus'])->name('admin.comments.status');
    Route::post('comments/{comment}/reply', [AdminCommentController::class, 'reply'])->name('admin.comments.reply');
    Route::put('comments/{comment}', [AdminCommentController::class, 'update'])->name('admin.comments.update');
    Route::post('comments/bulk', [AdminCommentController::class, 'bulkAction'])->name('admin.comments.bulk');
    Route::post('comments/{id}/restore', [AdminCommentController::class, 'restore'])->name('admin.comments.restore');
    Route::delete('comments/{id}/force-delete', [AdminCommentController::class, 'forceDelete'])->name('admin.comments.force-delete');
    Route::delete('comments/{comment}', [AdminCommentController::class, 'destroy'])->name('admin.comments.destroy');

    // Notifications Center
    Route::get('notifications', [AdminNotificationController::class, 'index'])->name('admin.notifications.index');
    Route::post('notifications/{notification}/read', [AdminNotificationController::class, 'markAsRead'])->name('admin.notifications.read');
    Route::post('notifications/mark-all-read', [AdminNotificationController::class, 'markAllAsRead'])->name('admin.notifications.mark-all-read');
    Route::post('notifications/clear-read', [AdminNotificationController::class, 'clearRead'])->name('admin.notifications.clear-read');
    Route::delete('notifications/{notification}', [AdminNotificationController::class, 'destroy'])->name('admin.notifications.destroy');

    // Chat App
    Route::get('chat', [AdminChatController::class, 'index'])->name('admin.chat.index');
    Route::post('chat', [AdminChatController::class, 'store'])->name('admin.chat.store');
    Route::get('chat/api/messages', [AdminChatController::class, 'fetchMessages'])->name('admin.chat.messages');

    // File Manager App
    Route::get('file-manager', [AdminFileManagerController::class, 'index'])->name('admin.file-manager.index');
    Route::post('file-manager/upload', [AdminFileManagerController::class, 'upload'])->name('admin.file-manager.upload');
    Route::post('file-manager/duplicate', [AdminFileManagerController::class, 'duplicate'])->name('admin.file-manager.duplicate');
    Route::post('file-manager/restore', [AdminFileManagerController::class, 'restore'])->name('admin.file-manager.restore');
    Route::delete('file-manager/force-delete', [AdminFileManagerController::class, 'forceDelete'])->name('admin.file-manager.force-delete');
    Route::post('file-manager/empty-trash', [AdminFileManagerController::class, 'emptyTrash'])->name('admin.file-manager.empty-trash');
    Route::delete('file-manager', [AdminFileManagerController::class, 'destroy'])->name('admin.file-manager.destroy');

    // Email / Communications App
    Route::get('email', [AdminEmailController::class, 'index'])->name('admin.email.index');
    Route::post('email', [AdminEmailController::class, 'store'])->name('admin.email.store');
    Route::get('email/{email}', [AdminEmailController::class, 'show'])->name('admin.email.show');
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
    Route::put('media/{picture}', [AdminMediaController::class, 'update'])->name('admin.media.update');
    Route::post('media/bulk', [AdminMediaController::class, 'bulkAction'])->name('admin.media.bulk');
    Route::delete('media/{picture}', [AdminMediaController::class, 'destroy'])->name('admin.media.destroy');

    // Image Cropper Studio
    Route::get('image-cropper', [ImageCropperController::class, 'index'])->name('admin.image-cropper.index');
    Route::post('image-cropper/save', [ImageCropperController::class, 'save'])->name('admin.image-cropper.save');

    // Users Management & Authority Reassignment
    Route::post('users/{user}/reassign-authority', [AdminUserController::class, 'reassignAuthority'])->name('admin.users.reassign-authority');
    Route::resource('users', AdminUserController::class)->names('admin.users');

    // Admin Profile
    Route::get('profile', [AdminProfileController::class, 'edit'])->name('admin.profile');
    Route::put('profile', [AdminProfileController::class, 'update'])->name('admin.profile.update');
    Route::put('profile/password', [AdminProfileController::class, 'updatePassword'])->name('admin.profile.password');
});

// Auth logout & user dashboard subpages
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('dashboard')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile'])->middleware('auth')->name('user-profile');
    Route::get('/inbox', [AuthController::class, 'inbox'])->middleware('auth')->name('user-inbox');
    Route::get('/setting', [AuthController::class, 'setting'])->middleware('auth')->name('user-setting');
});
