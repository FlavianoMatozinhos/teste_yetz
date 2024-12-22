<?php

namespace App\Repositories;

use App\Models\User;

class PlayerRepository
{
    public function getConfirmedPlayers()
    {
        return User::where('confirmed', true)->get();
    }

    public function getPlayersByClass($classId)
    {
        return User::where('class_id', $classId)->get();
    }

    public function getAllPlayers()
    {
        return User::all();
    }
    
    public function getAllWithConfirmationStatus()
    {
        return User::all()->map(function ($player) {
            $player->confirmed = $player->confirmed ? 'Sim' : 'Não';
            return $player;
        });
    }

    public function getPlayersByXP()
    {
        return User::orderBy('xp', 'desc')->get();
    }

    public function createPlayer(array $data)
    {
        return User::create($data);
    }
}
