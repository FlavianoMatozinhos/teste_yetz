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

    public function getAllPlayers()
    {
        return User::all();
    }

    public function getPlayersInGuild($guildId)
    {
        return User::where('guild_id', $guildId)->get();
    }

    public function findGuildById($id)
    {
        return Guild::findOrFail($id);
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

