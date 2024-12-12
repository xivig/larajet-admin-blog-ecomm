<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
//    return view('welcome');
//});
Route::get('/', function () {
    $title = 'Home page';
    return view('home.content', ['title' => $title]);
});

//Route::get('/blog', function () {
//    return view('blog.blog-content');
//});

Route::get('/blog', [BlogController::class, 'index'])->name('single-post');
Route::get('/blog/{slug}', [BlogController::class, 'singlePost'])->name('single-post');
//Route::get('/blog/{slug}', [PostController::class, 'singlePost'])->name('single-post');

Route::get('/blog-list', [PostController::class, 'index']);
//Route::get('/blog-list', function () {
//
//    return view('blog.blog-list');
//});
Route::get('/about', function () {
    return view('home.about');
});
Route::get('/contact', function () {
    return view('home.contact');
});
Route::get('/services', function () {
    return view('home.services');
});
Route::get('/pricing', function () {
    return view('home.pricing');
});
Route::get('/project', function () {
    return view('home.project');
});



//Route::get('/', function () {
//    return view('home');
//});
Route::get('/user', function () {
    return view('user');
});
Route::get('/admin', function () {
    return view('admin');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('logout', [AuthController::class, 'logout'])->name('logout');


//route group
Route::prefix('dashboard')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile'])->middleware('auth')->name('user-profile');
    Route::get('/inbox', [AuthController::class, 'inbox'])->middleware('auth')->name('user-inbox');
    Route::get('/setting', [AuthController::class, 'setting'])->middleware('auth')->name('user-setting');
});
