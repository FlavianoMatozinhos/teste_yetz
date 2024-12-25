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

Route::get('/classes', [ClassController::class, 'index']);
Route::post('/classes', [ClassController::class, 'store']);
Route::get('/classes/{id}', [ClassController::class, 'show']);
Route::put('/classes/{id}', [ClassController::class, 'update']);
Route::delete('/classes/{id}', [ClassController::class, 'destroy']);

Route::middleware(['auth'])->group(function () {
    Route::get('/players/{id}', [RegisterController::class, 'show'])->name('player.show');
    Route::get('/players/edit/{id}', [RegisterController::class, 'edit'])->name('player.edit');
    Route::put('/players/{id}', [RegisterController::class, 'update'])->name('player.update');
    Route::delete('/players/{id}', [RegisterController::class, 'destroy'])->name('player.destroy');
    Route::get('/players/confirm/{id}', [RegisterController::class, 'confirm'])->name('player.confirm');
    Route::get('/players/noconfirm/{id}', [RegisterController::class, 'noconfirm'])->name('player.noconfirm');

    Route::get('/guild/create', [GuildController::class, 'index'])->name('guild.create');
    Route::post('/guild', [GuildController::class, 'store'])->name('guild.store');
    Route::get('/guild/{id}', [GuildController::class, 'show'])->name('guild.show');
    Route::get('/guild/edit/{id}', [GuildController::class, 'edit'])->name('guild.edit');
    Route::put('/guild/{id}', [GuildController::class, 'update'])->name('guild.update');
    Route::delete('/guild/{id}', [GuildController::class, 'destroy'])->name('guild.destroy');

    Route::post('/guilds/balance', [GuildController::class, 'balance'])->name('balance');
});
