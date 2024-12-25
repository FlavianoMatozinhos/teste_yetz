<?php

namespace App\Services;

use App\Repositories\TokenRepository;
use Exception;

class LogoutService
{
    protected $tokenRepository;

    public function __construct(TokenRepository $tokenRepository)
    {
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * Realiza o logout do usuário.
     */
    public function logoutUser($user)
    {
        try {
            $this->tokenRepository->revokeTokens($user);

            auth()->logout();

            return [
                'status' => 'success',
                'message' => 'Logout realizado com sucesso.'
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erro ao realizar logout: ' . $e->getMessage()
            ];
        }
    }
}
