<?php

use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\LaporanController;
use App\Http\Controllers\Web\MenuController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\RewardController;
use App\Http\Controllers\WebController;
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

// Route::get('/', function () {
//     return view('index');
// });
Route::get('/login', [UserController::class, 'index'])->name('login');

Route::post('/login', [UserController::class, 'login'])->name('login.post');

Route::get('/logout', [UserController::class, 'logout'])->name('logout');

Route::group(['middleware' => 'isLogin'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::group(['prefix' => 'pesanan', 'as' => 'pesanan.'], function () {
        Route::get('/dinein', [OrderController::class, 'dinein'])->name('dinein');
        Route::get('/takeaway', [OrderController::class, 'takeaway'])->name('takeaway');
        Route::get('/data/dinein', [OrderController::class, 'data_order_dinein'])->name('data.dinein');
        Route::get('/data/takeaway', [OrderController::class, 'data_order_takeaway'])->name('data.takeaway');
        Route::get('/{id}', [OrderController::class, 'show'])->name('show');
        Route::post('/update/{id}', [OrderController::class, 'update'])->name('update');
    });
    
    Route::group(['prefix' => 'menu', 'as' => 'menu.'], function () {
        Route::get('/', [MenuController::class, 'index'])->name('index');
        Route::post('/store', [MenuController::class, 'store'])->name('store');
        Route::get('/data', [MenuController::class, 'data_menu'])->name('data');
        Route::get('/{id}', [MenuController::class, 'show'])->name('show');
        Route::post('/update/{id}', [MenuController::class, 'update'])->name('update');
        Route::post('/delete/{id}', [MenuController::class, 'destroy'])->name('delete');
    });
    
    Route::group(['prefix' => 'reward', 'as' => 'reward.'], function () {
        Route::get('/', [RewardController::class, 'index'])->name('index');
        Route::post('/store', [RewardController::class, 'store'])->name('store');
        Route::get('/data', [RewardController::class, 'data_reward'])->name('data');
        Route::get('/{id}', [RewardController::class, 'show'])->name('show');
        Route::post('/update/{id}', [RewardController::class, 'update'])->name('update');
        Route::post('/delete/{id}', [RewardController::class, 'destroy'])->name('delete');
    });

    Route::group(['prefix' => 'laporan', 'as' => 'laporan.'], function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::post('/generate', [LaporanController::class, 'generate'])->name('generate');
        Route::get('/data', [LaporanController::class, 'data'])->name('data');
        Route::get('/print', [LaporanController::class, 'laporan_print'])->name('print');
    });
});