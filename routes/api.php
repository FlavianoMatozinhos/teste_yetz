<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\GuildController;
use App\Http\Controllers\AuthController;


Route::get('/players', [RegisterController::class, 'index']);
Route::post('/players', [RegisterController::class, 'store']);
Route::get('/players/{id}', [RegisterController::class, 'show']);
Route::put('/players/{id}', [RegisterController::class, 'update']);
Route::delete('/players/{id}', [RegisterController::class, 'destroy']);

Route::get('/classes', [ClassController::class, 'index']);
Route::post('/classes', [ClassController::class, 'store']);
Route::get('/classes/{id}', [ClassController::class, 'show']);
Route::put('/classes/{id}', [ClassController::class, 'update']);
Route::delete('/classes/{id}', [ClassController::class, 'destroy']);

Route::middleware(['auth:api'])->group(function () {
    Route::get('/guilds', [GuildController::class, 'index']);
    Route::post('/guilds', [GuildController::class, 'store']);
    Route::get('/guilds/{id}', [GuildController::class, 'show']);
    Route::put('/guilds/{id}', [GuildController::class, 'update']);
    Route::delete('/guilds/{id}', [GuildController::class, 'destroy']);
    Route::post('/guilds/balance', [GuildController::class, 'balance']);
});

Route::post('/register', [RegisterController::class, 'store']);
Route::post('/login', [LoginController::class, 'store']);
Route::middleware('auth:api')->post('/logout', [LogoutController::class, 'logout']);
