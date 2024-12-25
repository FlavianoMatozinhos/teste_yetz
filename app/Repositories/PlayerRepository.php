<?php

namespace App\Repositories;

use App\Models\Classe;
use App\Models\Guild;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PlayerRepository
{
    protected $model;

    /**
     * Inicializa o repositório com o modelo User.
     *
     * @param User $player
     */
    public function __construct(User $player)
    {
        $this->model = $player;
    }

    /**
     * Retorna todos os jogadores confirmados.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getConfirmedPlayers(): Collection
    {
        return $this->model->where('confirmed', true)->get();
    }

    /**
     * Retorna os jogadores filtrados por uma classe específica.
     *
     * @param int $classId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPlayersByClass($classId): Collection
    {
        return $this->model->where('class_id', $classId)->get();
    }

    /**
     * Retorna todos os jogadores.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllPlayers(): Collection
    {
        return $this->model->all();
    }

    /**
     * Retorna os jogadores ordenados por XP em ordem decrescente.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPlayersByXP(): Collection
    {
        return $this->model->orderBy('xp', 'desc')->get();
    }

    /**
     * Cria um novo jogador.
     *
     * @param array $data
     * @return array
     */
    public function createPlayer(array $data): array
    {
        DB::beginTransaction();

        try {
            $role = Role::where('name', 'player')->firstOrFail();

            $user = $this->model->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => $role->id,
                'class_id' => $data['class_id'],
                'xp' => 0,
                'confirmed' => false,
            ]);

            DB::commit();

            return ['status' => 'success', 'user' => $user];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['status' => 'error', 'errors' => ['general' => $e->getMessage()]];
        }
    }

    /**
     * Busca um jogador pelo e-mail.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail($email): Model
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Busca um jogador pelo ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findById($id): Model
    {
        return $this->model->find($id);
    }

    /**
     * Atualiza os dados de um jogador.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updatePlayer($id, array $data): bool
    {
        unset($data['password_confirmation']);

        return $this->model->where('id', $id)->update($data);
    }

    /**
     * Exclui um jogador pelo ID.
     *
     * @param int $id
     * @return bool
     */
    public function deletePlayer($id): bool|null
    {
        DB::table('guilds')->where('user_id', $id)->update(['user_id' => null]);
        return $this->model->where('id', $id)->delete();
    }

    public function getGuildByPlayerId($id): array
    {
        $player = $this->model->with('guild')->find($id);
        
        if (!$player || !$player->guild) {
            return [
                'status' => 'error',
                'message' => 'Guilda não encontrada para este jogador.',
            ];
        }

        return [
            'status' => 'success',
            'data' => $player->guild,
        ];
    }

    public function findByIdAndConfirm($id): bool
    {
        return $this->model->where('id', $id)->update(['confirmed' => true]);
    }

    public function findByIdAndNoConfirm($id): bool
    {
        return $this->model->where('id', $id)->update(['confirmed' => false]);
    }
}
