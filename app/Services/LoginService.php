<?php

namespace App\Services;

use App\Repositories\PlayerRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginService
{
    protected $userRepository;

    public function __construct(PlayerRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Realiza o login do usuário.
     */
    public function login(array $credentials): array
    {
        $validator = validator($credentials, [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return [
                'status' => 'error',
                'message' => 'Dados de login inválidos.',
                'errors' => $validator->errors()
            ];
        }

        $user = $this->userRepository->findByEmail($credentials['email']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return [
                'status' => 'error',
                'message' => 'Credenciais inválidas.'
            ];
        }

        $tokenResult = $user->createToken('MyApp');
        $token = $tokenResult->accessToken;

        Auth::guard('web')->login($user);

        session(['api_token' => $token]);

        return [
            'status' => 'success',
            'token' => $token
        ];
    }
}
