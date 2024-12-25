<?php

namespace App\Repositories;

class TokenRepository
{
    /**
     * Revoga todos os tokens do usuário.
     */
    public function revokeTokens($user): void
    {
        $user->tokens->each(function ($token) {
            $token->delete();
        });
    }
}
