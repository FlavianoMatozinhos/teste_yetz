<?php

namespace App\Http\Controllers;

use App\Models\Guild;
use Illuminate\Http\Request;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {

        $players = User::all()->map(function ($player) {
            $player->confirmed = $player->confirmed ? 'Sim' : 'Não';
            return $player;
        });

        $guilds = Guild::all()->map(function ($guild) {
            if ($guild->players->isEmpty()) {
                $guild->confirmation_status = 'Sem Players';
            } else {
                $allConfirmed = $guild->players->every(function ($player) {
                    return $player->confirmed == 1;
                });
                $guild->confirmation_status = $allConfirmed ? 'Todos confirmados' : 'Jogadores pendentes';
            }
            return $guild;
        });

        $data = [
            'message' => 'Bem-vindo à Home!',
            'guids' => $guilds,
            'players' => $players,
            'numGuildas' => Guild::count(),
        ];
    
        return view('home', $data);
    }
}
