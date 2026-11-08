<?php

use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\RegisterController;
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
Route::get('/blog/{slug}', [BlogController::class, 'singlePost'])->name('single-post');

// Comments & replies
Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
Route::post('/comment-replies', [CommentReplyController::class, 'store'])->name('comment-replies.store');

// General previews
Route::get('/user', static fn() => view('user'));
Route::get('/admin', static fn() => view('admin'));

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
    Route::post('register', [RegisterController::class, 'store']);

    Route::get('login', [LoginController::class, 'create'])->name('admin.login');
    Route::post('login', [LoginController::class, 'store']);
});

// Admin Authenticated Routes
Route::prefix('admin')->middleware('auth:admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin');
    })->name('admin.dashboard');

    Route::post('logout', [LoginController::class, 'destroy'])->name('admin.logout');

    Route::resource('posts', AdminPostController::class)->names('admin.posts');
});

// Auth logout & user dashboard subpages
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('dashboard')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile'])->middleware('auth')->name('user-profile');
    Route::get('/inbox', [AuthController::class, 'inbox'])->middleware('auth')->name('user-inbox');
    Route::get('/setting', [AuthController::class, 'setting'])->middleware('auth')->name('user-setting');
});
