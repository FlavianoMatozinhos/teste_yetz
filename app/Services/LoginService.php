<?php

namespace App\Services;

use App\Repositories\PlayerRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Exception;

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
    public function login(array $credentials)
    {
        try {
            $validator = validator($credentials, [
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
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
        } catch (ValidationException $e) {
            return [
                'status' => 'error',
                'message' => 'Dados de login inválidos.',
                'errors' => $e->errors()
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erro ao realizar login.',
                'error' => $e->getMessage()
            ];
        }
    }
}
