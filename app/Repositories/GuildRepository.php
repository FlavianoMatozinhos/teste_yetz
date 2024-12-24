<?php

namespace App\Repositories;

use App\Models\Guild;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class GuildRepository
{
    /**
     * Retorna todas as guildas.
     *
     * @return Collection
     */
    public function getAllGuilds(): Collection
    {
        return Guild::all();
    }

    /**
     * Retorna todas as guildas com os jogadores associados.
     *
     * @return Collection
     */
    public function getAllWithPlayers(): Collection
    {
        return Guild::with('players')->get();
    }

    /**
     * Retorna todos os jogadores.
     *
     * @return Collection
     */
    public function getAllPlayers(): Collection
    {
        return User::all();
    }

    /**
     * Retorna todas as guildas com os jogadores e seu status de confirmação.
     *
     * @return Collection
     */
    public function getAllWithConfirmationStatus(): Collection
    {
        return Guild::with('players')
            ->get()
            ->map(function ($guild) {
                $guild->confirmation_status = $this->getGuildConfirmationStatus($guild);
                return $guild;
            });
    }

    /**
     * Obtém o status de confirmação de uma guilda.
     *
     * @param Guild $guild
     * @return string
     */
    public function getGuildConfirmationStatus(Guild $guild): string
    {
        if ($guild->players->isEmpty()) {
            return 'Sem Players';
        }

        $allConfirmed = $guild->players->every(function ($player) {
            return $player->confirmed == 1;
        });

        return $allConfirmed ? 'Todos confirmados' : 'Jogadores pendentes';
    }

    /**
     * Conta o número de guildas.
     *
     * @return int
     */
    public function countGuilds(): int
    {
        return Guild::count();
    }

    /**
     * Cria uma nova guilda.
     *
     * @param array $data
     * @return Guild
     */
    public function createGuild(array $data): Guild
    {
        return Guild::create($data);
    }

    /**
     * Exclui uma guilda.
     *
     * @param Guild $guild
     * @return void
     */
    public function delete($id): void
    {
        $guild = Guild::findOrFail($id);
        $guild->delete();
    }

    /**
     * Encontra uma guilda pelo seu ID.
     *
     * @param int $id
     * @return Guild
     */
    public function findGuildById(int $id): ?Guild
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
}
