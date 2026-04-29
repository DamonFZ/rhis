<?php

use App\Http\Controllers\Mobile\AuthController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('mobile')->middleware(['web', 'set.mobile.locale'])->group(function () {
    Route::get('bind', [\App\Http\Controllers\Mobile\AuthController::class, 'bindConfirm'])->name('mobile.bind');
    Route::post('bind', [\App\Http\Controllers\Mobile\AuthController::class, 'bindStore'])->name('mobile.bind.store');
});

// 动态分配中间件
$mobileMiddlewares = ['web', 'set.mobile.locale'];
if (!app()->isLocal()) {
    $mobileMiddlewares[] = 'wechat.oauth:default,snsapi_base';
}

Route::prefix('mobile')->middleware($mobileMiddlewares)->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Mobile\DashboardController::class, 'index'])->name('mobile.dashboard');
    Route::get('packages', [\App\Http\Controllers\Mobile\PackageController::class, 'index'])->name('mobile.packages');
});
