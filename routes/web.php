<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RootController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/tracking_event', [UserController::class, 'trackingEvent'])->middleware('auth');

Route::get('/auth', [UserController::class, 'auth']);
Route::post('/login', [UserController::class, 'login']);

Route::post('/admin/login', [AdminController::class, 'login'])->middleware('adminIpRestrict');
Route::get('/admin/login-fail', [AdminController::class, 'loginFail']);

Route::group(['prefix' => '/me', 'middleware' => 'auth'], function() {
    Route::get('/', [UserController::class, 'show'])->middleware('auth');
    Route::put('/', [UserController::class, 'update'])->middleware('auth');
});

Route::group(['prefix' => '/admin', 'middleware' => ['adminIpRestrict', 'adminAuth']], function() {
    Route::get('/me', [AdminController::class, 'me']);
    Route::post('/logout', [AdminController::class, 'logout']);
    Route::post('/change_password', [AdminController::class, 'changePassword']);
});

Route::get('/{path?}', [RootController::class, 'show'])->where('path', '.*');
