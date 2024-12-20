<?php

namespace App\Repositories;

use App\Models\Guild;

class GuildRepository
{
    public function getAllGuilds()
    {
        return Guild::all();
    }

    public function createGuild(array $data)
    {
        return Guild::create($data);
    }

    public function findGuildById($id)
    {
        return Guild::findOrFail($id);
    }

    public function deleteGuild($id)
    {
        return Guild::findOrFail($id)->delete();
    }
}
