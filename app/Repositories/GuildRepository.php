<?php

namespace App\Repositories;

use App\Models\Guild;
use App\Models\User;

class GuildRepository
{
    public function getAllGuilds()
    {
        return Guild::all();
    }

    public function getAllWithPlayers()
    {
        return Guild::with('players')->get();
    }

    public function getAllPlayers()
    {
        return User::all(); // Ou outro modelo apropriado para representar os jogadores
    }

    public function getAllWithConfirmationStatus()
    {
        return Guild::with('players')
            ->get()
            ->map(function ($guild) {
                $guild->confirmation_status = $this->getGuildConfirmationStatus($guild);
                return $guild;
            });
    }

    public function getGuildConfirmationStatus($guild)
    {
        if ($guild->players->isEmpty()) {
            return 'Sem Players';
        }

        $allConfirmed = $guild->players->every(function ($player) {
            return $player->confirmed == 1;
        });

        return $allConfirmed ? 'Todos confirmados' : 'Jogadores pendentes';
    }

    public function countGuilds()
    {
        return Guild::count();
    }

    public function createGuild($data)
    {
        return Guild::create($data);
    }

    public function deleteGuild($guild)
    {
        $guild->delete();
    }
}
