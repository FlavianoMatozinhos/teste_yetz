<?php

namespace App\Services;

use App\Models\Role;
use App\Repositories\ClassRepository;
use App\Repositories\GuildRepository;
use App\Repositories\PlayerRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterService
{
    protected $classRepository;
    protected $userRepository;
    protected $guildRepository;

    public function __construct(ClassRepository $classRepository, PlayerRepository $userRepository, GuildRepository $guildRepository)
    {
        $this->classRepository = $classRepository;
        $this->userRepository = $userRepository;
        $this->guildRepository = $guildRepository;
    }

    /**
     * Retorna todas as classes disponíveis.
     */
    public function getAllClasses(): array
    {
        $class = $this->classRepository->getAll();

        if (!$class) {
            return [
                'status' => 'error',
                'message' => 'Classe não encontrada.',
                'status_code' => 404
            ];
        }

        return [
            'status' => 'success',
            'data' => $class
        ];
    }

    public function getAllPlayers(): Collection
    {
        return $this->userRepository->getAllPlayers();
    }

    /**
     * Registra um novo usuário.
     */
    public function registerUser(array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'class_id' => 'required|exists:classes,id',
        ]);

        if ($validator->fails()) {
            return [
                'status' => 'error',
                'errors' => $validator->errors(),
                'status_code' => 400,
            ];
        }

        $result = $this->userRepository->createPlayer($data);

        if ($result['status'] === 'success') {
            return [
                'status' => 'success',
                'data' => $result['user'],
                'status_code' => 201,
            ];
        }

        return [
            'status' => 'error',
            'data' => $result['errors'],
            'status_code' => 500,
        ];
    }

    protected function validateRoleId($roleId)
    {
        return Role::where('id', $roleId)->exists();
    }

    public function updateUser($id, array $data)
    {
        try {
            $validator = Validator::make($data, [
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
                'password' => 'nullable|string|min:8',
                'xp' => 'nullable|numeric',
                'class_id' => 'sometimes|exists:classes,id',
            ]);

            if ($validator->fails()) {
                return [
                    'status' => 'error',
                    'errors' => $validator->errors(),
                    'status_code' => 400,
                ];
            }

            $user = $this->userRepository->findById($id);

            if (!$user) {
                return [
                    'status' => 'error',
                    'data' => ['message' => 'Usuário não encontrado.'],
                    'status_code' => 404,
                ];
            }

            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $this->userRepository->updatePlayer($id, $data);

            return [
                'status' => 'success',
                'data' => ['message' => 'Usuário atualizado com sucesso.'],
                'status_code' => 200,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'data' => ['message' => 'Erro ao atualizar usuário.', 'error' => $e->getMessage()],
                'status_code' => 500,
            ];
        }
    }

    public function deleteUser($id)
    {
        try {
            $user = $this->userRepository->findById($id);

            if (!$user) {
                return [
                    'status' => 'error',
                    'data' => ['message' => 'Usuário não encontrado.'],
                    'status_code' => 404,
                ];
            }

            $this->userRepository->deletePlayer($id);

            return [
                'status' => 'success',
                'data' => ['message' => 'Usuário excluído com sucesso.'],
                'status_code' => 200,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'data' => ['message' => 'Erro ao excluir o usuário.', 'error' => $e->getMessage()],
                'status_code' => 500,
            ];
        }
    }

    public function getPlayerById($id)
    {
        try {
            $class = $this->userRepository->findById($id);
            
            if (!$class) {
                return [
                    'status' => 'error',
                    'message' => 'Player não encontrada.',
                    'status_code' => 404
                ];
            }

            return [
                'status' => 'success',
                'data' => $class
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erro ao buscar Player.',
                'error' => $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    public function getPlayerByIdAndConfirm($id)
    {
        try {
            $confirm = $this->userRepository->findByIdAndConfirm($id);
            
            if (!$confirm) {
                return [
                    'status' => 'error',
                    'message' => 'Voce ja esta pronto.',
                    'status_code' => 404
                ];
            }

            return [
                'status' => 'success',
                'data' => $confirm
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erro ao dar pronto.',
                'error' => $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    public function getPlayerByIdAndNoConfirm($id)
    {
        try {
            $confirm = $this->userRepository->findByIdAndNoConfirm($id);
            
            if (!$confirm) {
                return [
                    'status' => 'error',
                    'message' => 'Voce nao esta pronto!.',
                    'status_code' => 404
                ];
            }

            return [
                'status' => 'success',
                'data' => $confirm
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erro ao dar pronto.',
                'error' => $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    /**
     * Retorna todos os jogadores com o status de confirmação formatado.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllWithConfirmationStatus()
    {
        return $this->userRepository->getAllPlayers()->map(function ($player) {
            $player->confirmed = $player->confirmed ? 'Sim' : 'Não';
            return $player;
        });
    }
}
