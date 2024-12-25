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


Route::get('/register', [RegisterController::class, 'index']);
Route::post('/register', [RegisterController::class, 'store']);

Route::get('/login', [LoginController::class, 'index']);
Route::post('/login', [LoginController::class, 'store']);

Route::middleware('auth:api')->post('/logout', [LogoutController::class, 'logout']);

Route::middleware(['auth:api'])->group(function () {
    Route::get('/players', [RegisterController::class, 'index']);
    Route::get('/player/{id}', [RegisterController::class, 'store']);
    Route::get('/players/edit/{id}', [RegisterController::class, 'edit']);
    Route::put('/players/{id}', [RegisterController::class, 'update']);
    Route::delete('/players/{id}', [RegisterController::class, 'destroy']);
    Route::get('/players/confirm/{id}', [RegisterController::class, 'confirm']);
    Route::get('/players/noconfirm/{id}', [RegisterController::class, 'noconfirm']);

    Route::get('/classes', [ClassController::class, 'index']);
    Route::post('/classes', [ClassController::class, 'store']);
    Route::get('/classes/{id}', [ClassController::class, 'show']);
    Route::put('/classes/{id}', [ClassController::class, 'update']);
    Route::delete('/classes/{id}', [ClassController::class, 'destroy']);

    Route::get('/guilds', [GuildController::class, 'index']);
    Route::post('/guilds', [GuildController::class, 'store']);
    Route::get('/guilds/{id}', [GuildController::class, 'show']);
    Route::get('/guilds/edit/{id}', [GuildController::class, 'edit']);
    Route::put('/guilds/{id}', [GuildController::class, 'update']);
    Route::delete('/guilds/{id}', [GuildController::class, 'destroy']);

    Route::post('/guilds/balance', [GuildController::class, 'balance']);
});
