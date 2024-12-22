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

        $players = User::all()->map(function ($player) {
            $player->confirmed = $player->confirmed ? 'Sim' : 'Não';
            return $player;
        });

        $guilds = Guild::all()->map(function ($guild) {
            $allConfirmed = $guild->players->every(function ($player) {
                return $player->confirmed == 1;
            });
    
            $guild->confirmation_status = $allConfirmed ? 'Todos confirmados' : 'Jogadores pendentes';
            return $guild;
        });

        // Dados para a view
        $data = [
            'message' => 'Bem-vindo à Home!',
            'guids' => $guilds,  // Pega todas as guildas
            'players' => $players,
            'numGuildas' => Guild::count(), // Pega todos os jogadores
        ];
    
        return view('home', $data);
    }
}
