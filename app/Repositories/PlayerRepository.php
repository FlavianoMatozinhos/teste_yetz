<?php

namespace App\Repositories;

use App\Models\Guild;
use App\Models\User;
use App\Models\Role;
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
    public function getConfirmedPlayers()
    {
        return $this->model->where('confirmed', true)->get();
    }

    /**
     * Retorna os jogadores filtrados por uma classe específica.
     *
     * @param int $classId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPlayersByClass($classId)
    {
        return $this->model->where('class_id', $classId)->get();
    }

    /**
     * Retorna todos os jogadores.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllPlayers()
    {
        return $this->model->all();
    }

    /**
     * Retorna todos os jogadores com o status de confirmação formatado.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllWithConfirmationStatus()
    {
        return $this->model->all()->map(function ($player) {
            $player->confirmed = $player->confirmed ? 'Sim' : 'Não';
            return $player;
        });
    }

    /**
     * Retorna os jogadores ordenados por XP em ordem decrescente.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPlayersByXP()
    {
        return $this->model->orderBy('xp', 'desc')->get();
    }

    /**
     * Cria um novo jogador.
     *
     * @param array $data
     * @return array
     */
    public function createPlayer(array $data)
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
    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Busca um jogador pelo ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findById($id)
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
    public function updatePlayer($id, array $data)
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
    public function deletePlayer($id)
    {
        return $this->model->where('id', $id)->delete();
    }
}
