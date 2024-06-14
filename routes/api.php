<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\RewardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::get('/user', 'App\Http\Controllers\Api\UserController@index');
// Route::get('/menu/image/{image}', 'App\Http\Controllers\Api\MenuController@get_image');
Route::prefix('user')->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::get('/index', 'index');
        Route::post('/login', 'login');
        Route::post('/register', 'register');
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/profile', 'profile');
            Route::post('/logout', 'logout');
            Route::post('/change-password', 'changePassword');
            Route::post('/update-profile', 'updateProfile');
        });
    });
});

Route::prefix('menu')->group(function () {
    Route::controller(MenuController::class)->group(function () {
        Route::get('/index', 'index');
        Route::get('/show/{id}', 'show');
        Route::get('/image/{image}', 'get_image');
    });
});

Route::prefix('reward')->group(function () {
    Route::controller(RewardController::class)->group(function () {
        Route::get('/index', 'index');
        Route::get('/show/{id}', 'show');
    });
});