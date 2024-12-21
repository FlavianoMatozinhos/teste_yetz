<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        // Revogar o token de autenticação
        $request->user()->tokens->each(function ($token) {
            $token->delete();
        });
    
        // Encerrar a sessão
        auth()->logout();
    
        // Redirecionar para a página de login com uma mensagem de sucesso
        return redirect()->route('login')->with('success', 'Você saiu com sucesso.');
    }
}
