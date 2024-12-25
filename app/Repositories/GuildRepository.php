<?php

namespace App\Repositories;

use App\Models\Guild;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class GuildRepository
{
    /**
     * Retorna todas as guildas.
     *
     * @return Collection
     */
    public function getAllGuilds()
    {
        return Guild::all();
    }

    /**
     * Retorna todas as guildas com os jogadores associados.
     *
     * @return Collection
     */
    public function getAllWithPlayers()
    {
        return Guild::with('players')->get();
    }

    /**
     * Retorna todos os jogadores.
     *
     * @return Collection
     */
    public function getAllPlayers()
    {
        return User::all();
    }

    /**
     * Conta o número de guildas.
     *
     * @return int
     */
    public function countGuilds()
    {
        return Guild::count();
    }

    /**
     * Cria uma nova guilda.
     *
     * @param array $data
     * @return Guild
     */
    public function createGuild(array $data)
    {
        DB::beginTransaction();

        try {

            $guild = Guild::create($data);

            DB::commit();

            return ['status' => 'success', 'guild' => $guild];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'errors' => ['general' => $e->getMessage()]];
        }
    }

    /**
     * Exclui uma guilda.
     *
     * @param Guild $guild
     * @return void
     */
    public function delete($id)
    {
        DB::table('users')->where('guild_id', $id)->update(['guild_id' => null]);
        $guild = Guild::findOrFail($id);
        $guild->delete();
    }

    /**
     * Encontra uma guilda pelo seu ID.
     *
     * @param int $id
     * @return Guild
     */
    public function findGuildById(int $id)
    {
        
        return Guild::findOrFail($id);
    }

    protected function validateGuildId($guildId)
    {
        return Guild::where('id', $guildId)->exists();
    }

    public function update($id, array $data)
    {
        $class = Guild::findOrFail($id);
        $class->update($data);
        return $class;
    }

    public function existsByName($name)
    {
        $query = Guild::where('name', $name);

        return $query->exists();
    }

    public function getPlayersByGuildId($guildId)
    {
        return User::where('guild_id', $guildId)->with('class')->get();
    }

    public function findByName($name)
    {
        return Guild::where('name', $name)->first();
    }
}
