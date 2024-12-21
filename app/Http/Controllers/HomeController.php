<?php

namespace App\Http\Controllers;

use App\Models\Guild;
use Illuminate\Http\Request;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Exibe a página inicial.
     */
    public function index()
    {
        // Dados para a view
        $data = [
            'message' => 'Bem-vindo à Home!',
            'guids' => Guild::all(),  // Pega todas as guildas
            'players' => User::all(),  // Pega todos os jogadores
        ];
    
        return view('home', $data);
    }
}
