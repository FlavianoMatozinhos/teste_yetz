<?php

namespace App\Services;

use App\Repositories\TokenRepository;

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

            return ['status' => 'success'];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erro ao realizar logout: ' . $e->getMessage()
            ];
        }
    }
}
