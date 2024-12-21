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


// Rotas para players
Route::get('/players', [PlayerController::class, 'index']);
Route::post('/players', [PlayerController::class, 'store']);
Route::get('/players/{id}', [PlayerController::class, 'show']);
Route::put('/players/{id}', [PlayerController::class, 'update']);
Route::delete('/players/{id}', [PlayerController::class, 'destroy']);

// Rotas para classes
Route::get('/classes', [ClassController::class, 'index']);
Route::post('/classes', [ClassController::class, 'store']);
Route::get('/classes/{id}', [ClassController::class, 'show']);
Route::put('/classes/{id}', [ClassController::class, 'update']);
Route::delete('/classes/{id}', [ClassController::class, 'destroy']);


Route::get('/guilds', [GuildController::class, 'index']);
Route::middleware('auth:api')->post('/guilds', [GuildController::class, 'store']);

Route::get('/guilds/{id}', [GuildController::class, 'show']);
Route::put('/guilds/{id}', [GuildController::class, 'update']);
Route::delete('/guilds/{id}', [GuildController::class, 'destroy']);

// Rota para balancear guildas
Route::get('/balance-guilds', [GuildController::class, 'balance']);
Route::post('/guilds/balance', [GuildController::class, 'balance']);

Route::post('/register', [RegisterController::class, 'store']);
Route::post('/login', [LoginController::class, 'store']);
Route::middleware('auth:api')->post('/logout', [LogoutController::class, 'logout']);
