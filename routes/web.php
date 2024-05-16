<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [AuthController::class, 'login'])->name('login');
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login/authenticate', [AuthController::class, 'authenticate'])->name('authenticate');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/registration', [AuthController::class, 'registration'])->name('add.registration');




Route::group(['middleware' => 'prevent-back-history'], function () {

    Route::group(['middleware' => 'auth'], function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/logout', [UserController::class, 'logout'])->name('logout');

        Route::get('user/create', [UserController::class, 'create']);
        Route::get('user/{role_id?}', [UserController::class, 'index']);
        Route::post('user/add', [UserController::class, 'add'])->name('user.add');
        Route::get('/user/get-list/{role_id?}', [UserController::class, 'getUserList']);
        Route::get('/user/edit/{id}', [UserController::class, 'edit']);
        Route::get('/user/view/{id}', [UserController::class, 'view']);
        Route::post('user/update/{id}', [UserController::class, 'update'])->name('user.update');
        Route::post('state-change', [UserController::class, 'stateChange']);
        Route::post('/admin-serach', [UserController::class, 'search'])->name('admin.serach');
        Route::get('/serach-user/{id}', [UserController::class, 'searchUser'])->name('serach.user');
    });
});
