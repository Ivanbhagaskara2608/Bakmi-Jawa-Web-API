<?php

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
Route::get('/login', [WebController::class, 'login'])->name('login');
Route::get('/', [WebController::class, 'index'])->name('dashboard.index');

Route::group(['prefix' => 'pesanan', 'as' => 'pesanan.'], function () {
    Route::get('/dinein', [WebController::class, 'dinein'])->name('dinein');
    Route::get('/takeaway', [WebController::class, 'takeaway'])->name('takeaway');
});

Route::group(['prefix' => 'menu', 'as' => 'menu.'], function () {
    Route::get('/menu', [WebController::class, 'menu'])->name('index');
});

Route::group(['prefix' => 'reward', 'as' => 'reward.'], function () {
    Route::get('/reward', [WebController::class, 'reward'])->name('index');
});