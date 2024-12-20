<?php

namespace App\Services;

use App\Repositories\GuildRepository;
use App\Repositories\PlayerRepository;

class GuildBalancerService
{
    protected $guildRepository;
    protected $playerRepository;
    protected $classBalanceStrategy;
    protected $xpBalanceStrategy;

    public function __construct(
        GuildRepository $guildRepository,
        PlayerRepository $playerRepository,
        ClassBalanceStrategy $classBalanceStrategy,
        XPBalanceStrategy $xpBalanceStrategy
    ) {
        $this->guildRepository = $guildRepository;
        $this->playerRepository = $playerRepository;
        $this->classBalanceStrategy = $classBalanceStrategy;
        $this->xpBalanceStrategy = $xpBalanceStrategy;
    }

    public function balanceGuilds($numGuilds)
    {
        // Obter todos os jogadores confirmados
        $players = $this->playerRepository->getConfirmedPlayers();
        
        // Obter as guildas
        $guilds = $this->guildRepository->getAllGuilds()->take($numGuilds);

        // Balanceamento de classes
        $classFeedback = $this->classBalanceStrategy->balanceClasses($guilds, $players);
        if (isset($classFeedback['status']) && $classFeedback['status'] === 'warning') {
            return $classFeedback;  // Retorna feedback sobre problemas de distribuição de classes
        }

        // Balanceamento de XP
        $this->xpBalanceStrategy->balanceXP($guilds, $players);

        // Retorna as guildas com a distribuição ajustada
        return [
            'status' => 'success',
            'guilds' => $guilds
        ];
    }
}
