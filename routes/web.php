<?php

use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\DashboardController;
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
Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

Route::group(['prefix' => 'pesanan', 'as' => 'pesanan.'], function () {
    Route::get('/dinein', [OrderController::class, 'dinein'])->name('dinein');
    Route::get('/takeaway', [OrderController::class, 'takeaway'])->name('takeaway');
});

Route::group(['prefix' => 'menu', 'as' => 'menu.'], function () {
    Route::get('/', [MenuController::class, 'index'])->name('index');
    Route::post('/store', [MenuController::class, 'store'])->name('store');
});

Route::group(['prefix' => 'reward', 'as' => 'reward.'], function () {
    Route::get('/', [RewardController::class, 'index'])->name('index');
});