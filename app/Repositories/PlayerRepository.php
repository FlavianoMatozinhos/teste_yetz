<?php

namespace App\Repositories;

use App\Models\Player;

class PlayerRepository
{
    public function getConfirmedPlayers()
    {
        return Player::where('confirmed', true)->get();
    }

    public function getPlayersByClass($classId)
    {
        return Player::where('class_id', $classId)->get();
    }

    public function getPlayersByXP()
    {
        return Player::orderBy('xp', 'desc')->get();
    }

    public function createPlayer(array $data)
    {
        return Player::create($data);
    }
}
