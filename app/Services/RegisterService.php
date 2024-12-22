<?php

namespace App\Services;

use App\Repositories\ClasseRepository;
use App\Repositories\PlayerRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterService
{
    protected $classeRepository;
    protected $userRepository;

    public function __construct(ClasseRepository $classeRepository, PlayerRepository $userRepository)
    {
        $this->classeRepository = $classeRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Retorna todas as classes disponíveis.
     */
    public function getAllClasses()
    {
        return $this->classeRepository->getAll();
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
            return ['status' => 'error', 'errors' => $validator->errors()];
        }

        return $this->userRepository->createPlayer($data);
    }
}
