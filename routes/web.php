<?php


use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\RegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Front\BlogController;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
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

//Route::get('/blog-list', [PostController::class, 'index']);
Route::get('/blog-list', function () {

    return view('blog.blog-list');
});
Route::get('/about', static fn() => view('home.about'));
Route::get('/contact', static fn() => view('home.contact'));
Route::get('/services',static fn() => view('home.services'));
Route::get('/pricing',static fn() => view('home.pricing'));
Route::get(/**
 * @return Factory|View|Application
 */ '/project',static fn() => view('home.project'));



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

Route::prefix('admin')->middleware('guest:admin')->group(function () {

    Route::get('register', [RegisterController::class, 'create'])->name('admin.register');
    Route::post('register', [RegisterController::class, 'store']);

    Route::get('login', [LoginController::class, 'create'])->name('admin.login');
    Route::post('login', [LoginController::class, 'store']);

});

Route::prefix('admin')->middleware('auth:admin')->group(function () {

    Route::get('/dashboard', function () {
//        return view('admin.dashboard');
        return view('admin');
    })->name('admin.dashboard');

    Route::post('logout', [LoginController::class, 'destroy'])->name('admin.logout');

});

//Route::middleware([
//    'auth:sanctum',
//    config('jetstream.auth_session'),
//    'admin',
//    'verified',
//])->group(function () {
//    Route::get('admin/dashboard', static function () {
//        return view('admin.dashboard');
//    })->name('admin.dashboard');
//});
//
//Route::group(['prefix' =>'admin', 'middleware'=>['admin:admin']], static function (){
//    Route::get('/login',[AdminController::class, 'loginForm']);
//    Route::post('/login',[AdminController::class, 'store'])->name('admin.login');
//});




Route::get('logout', [AuthController::class, 'logout'])->name('logout');


//route group
Route::prefix('dashboard')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile'])->middleware('auth')->name('user-profile');
    Route::get('/inbox', [AuthController::class, 'inbox'])->middleware('auth')->name('user-inbox');
    Route::get('/setting', [AuthController::class, 'setting'])->middleware('auth')->name('user-setting');
});
