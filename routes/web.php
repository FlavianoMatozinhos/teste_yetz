<?php

use App\Http\Controllers\GuildController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/register', [RegisterController::class, 'index'])->name('register.form');
Route::post('/register', [RegisterController::class, 'store'])->name('register.submit');

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.submit');

Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    // Tela inicial, exibe todas as guildas
    Route::get('/', [HomeController::class, 'index'])->name('home');
    
    // Somente MESTRE pode criar guilda
    Route::get('/guild/create', [GuildController::class, 'index'])->name('guild.create');
    Route::post('/guild', [GuildController::class, 'store'])->name('guild.store');
    
    // Rota para visualizar detalhes de uma guilda específica
    Route::get('/guild/{id}', [GuildController::class, 'show'])->name('guild.show');

    // Somente MESTRE pode editar ou excluir guilda
    Route::get('/guild/{id}/edit', [GuildController::class, 'edit'])->name('guild.edit');
    Route::put('/guild/{id}', [GuildController::class, 'update'])->name('guild.update');
    Route::delete('/guild/{id}', [GuildController::class, 'destroy'])->name('guild.destroy');

    Route::post('/guilds/balance', [GuildController::class, 'balance'])->name('balance');
});

