<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SubscribedPlanController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\SupportDepartmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\WalletTransactionController;
use App\Models\SubscriptionPlan;
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

Route::get('/', [SiteController::class, 'index']);
Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('/login/authenticate', [AuthController::class, 'authenticate'])->name('authenticate');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/registration', [AuthController::class, 'registration'])->name('add.registration');




Route::group(['middleware' => 'prevent-back-history'], function () {

    Route::group(['middleware' => ['auth', 'active']], function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/logout', [UserController::class, 'logout'])->name('logout');

        Route::get('user/create', [UserController::class, 'create']);
        Route::get('user/{role_id?}', [UserController::class, 'index']);
        Route::post('user/add', [UserController::class, 'add'])->name('user.add');
        Route::get('/user/get-list/{id?}', [UserController::class, 'getUserList']);
        Route::get('/users/get-wallet-transaction/{id?}', [UserController::class, 'getWalletTransaction']);
        Route::get('/users/get-wallet/{id?}', [UserController::class, 'getWallet']);
        Route::get('/user/edit/{id}', [UserController::class, 'edit']);
        Route::get('/user/view/{id}', [UserController::class, 'view']);
        Route::post('user/update/{id}', [UserController::class, 'update'])->name('user.update');
        Route::post('state-change', [UserController::class, 'stateChange']);
        Route::post('/admin-serach', [UserController::class, 'search'])->name('admin.serach');
        Route::get('/serach-user/{id}', [UserController::class, 'searchUser'])->name('serach.user');
        Route::get('/user-login/{id}', [UserController::class, 'userLogin']);





        Route::get('wallet/create', [WalletController::class, 'create']);
        Route::get('wallet/', [WalletController::class, 'index']);
        Route::post('wallet/add', [WalletController::class, 'add'])->name('wallet.add');
        Route::get('/wallet/get-list/{role_id?}', [WalletController::class, 'getWalletList']);
        Route::get('/wallet/edit/{id}', [WalletController::class, 'edit']);
        Route::get('/wallet/view/{id}', [WalletController::class, 'view']);
        Route::post('wallet/update/{id}', [WalletController::class, 'update'])->name('wallet.update');
        Route::get('relation/get-list', [UserController::class, 'getrelationData']);


        Route::get('wallet/wallet-transaction', [WalletTransactionController::class, 'index']);
        Route::get('/wallet/wallet-transaction/get-list/{id?}', [WalletTransactionController::class, 'getWalletTransactionList']);
        Route::get('/wallet/wallet-transaction/view/{id}', [WalletTransactionController::class, 'view']);


        Route::get('subscription/plan/create', [SubscriptionPlanController::class, 'create']);
        Route::get('subscription/plan/', [SubscriptionPlanController::class, 'index']);
        Route::post('subscription/plan/add', [SubscriptionPlanController::class, 'add'])->name('subscriptionPlan.add');
        Route::get('/subscription/plan/get-list/{role_id?}', [SubscriptionPlanController::class, 'getSubscriptionPlanList']);
        Route::get('/subscription/plan/edit/{id}', [SubscriptionPlanController::class, 'edit']);
        Route::get('/subscription/plan/view/{id}', [SubscriptionPlanController::class, 'view']);
        Route::post('subscription/plan/update/{id}', [SubscriptionPlanController::class, 'update'])->name('subscriptionPlan.update');

        Route::get('subscription/subscribed-plan/', [SubscribedPlanController::class, 'index']);
        Route::get('/subscription/subscribed-plan/get-list/{id?}', [SubscribedPlanController::class, 'getSubscribedPlanList']);
        Route::get('/subscription/subscribed-plan/view/{id}', [SubscribedPlanController::class, 'view']);
        Route::get('subscription/subscribed-plan/{id}', [SubscribedPlanController::class, 'add']);
        Route::get('subscription/testing/', [SubscribedPlanController::class, 'testing']);

        Route::get('/subscription/totat-sale', [SubscribedPlanController::class, 'getSalesData'])->name('subscribed.totatSale');
        Route::get('/wallet/fetch-transaction', [WalletTransactionController::class, 'fetchTransaction'])->name('wallet.fetchTransaction');
        Route::get('/transactions', [WalletTransactionController::class, 'getTransactions'])->name('transactions.get');




        Route::get('support', [SupportController::class, 'index']);
        Route::get('support/create', [SupportController::class, 'create']);
        Route::post('support/add', [SupportController::class, 'add'])->name('support.add');
        Route::get('/support/get-list', [SupportController::class, 'getSupportList']);
        Route::get('/support/edit/{id}', [SupportController::class, 'edit']);
        Route::get('/support/view/{id}', [SupportController::class, 'view']);
        Route::post('support/update', [SupportController::class, 'update'])->name('support.update');


        Route::get('support/department', [SupportDepartmentController::class, 'index']);
        Route::post('support/department/add', [SupportDepartmentController::class, 'store'])->name('supportDepartment.add');
        Route::get('/support/department/get-list', [SupportDepartmentController::class, 'getDepartmenttList']);
        Route::get('/support/department/edit/{id}', [SupportDepartmentController::class, 'edit']);
        Route::get('/support/department/view/{id}', [SupportDepartmentController::class, 'view']);
        Route::post('support/department/update/{id}', [SupportDepartmentController::class, 'update'])->name('supportDepartment.update');
        Route::get('/support/department/stateChange/{id}/{state_id}', [SupportDepartmentController::class, 'stateChange']);
        Route::get('/support/department/delete/{id}', [SupportDepartmentController::class, 'finalDelete']);
    });
});
