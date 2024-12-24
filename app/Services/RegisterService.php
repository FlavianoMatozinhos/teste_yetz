<?php

namespace App\Services;

use App\Models\Role;
use App\Repositories\ClassRepository;
use App\Repositories\GuildRepository;
use App\Repositories\PlayerRepository;
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
    public function getAllClasses()
    {
        return $this->classRepository->getAll(); 
    }

    public function getAllPlayers()
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
                'password' => 'nullable|string|min:8|confirmed',
                'role_id' => 'sometimes|exists:roles,id',
                'xp' => 'nullable|numeric',
                'confirmed' => 'sometimes|boolean',
                'class_id' => 'sometimes|exists:classes,id',
                'guild_id' => 'nullable|string|exists:guilds,id',
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
}
