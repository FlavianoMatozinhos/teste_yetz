<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\PlayerRepository;

class ClassBalanceStrategy
{
    protected $playerRepository;

    public function __construct(PlayerRepository $playerRepository)
    {
        $this->playerRepository = $playerRepository;
    }

    public function balanceClasses($guilds, $players)
    {
        $classifications = $this->classifyPlayersByClass($players);
        $feedback = [];

        foreach ($guilds as $guild) {
            $this->assignClassToGuild($guild, $classifications, $feedback);
        }

        return $feedback;
    }

    protected function classifyPlayersByClass($players)
    {
        $classified = [
            'Clérigo' => [],
            'Guerreiro' => [],
            'Mago' => [],
            'Arqueiro' => [],
        ];

        // Classificando os jogadores pelas suas classes
        foreach ($players as $player) {
            $className = $player->class->name;
            if (isset($classified[$className])) {
                $classified[$className][] = $player;
            }
        }

        return $classified;
    }

    protected function assignClassToGuild($guild, &$classifiedPlayers, &$feedback)
    {
        // Tentamos garantir pelo menos um de cada classe necessária.
        $assignedPlayers = [
            'Clérigo' => null,
            'Guerreiro' => null,
            'Mago' => null,
            'Arqueiro' => null,
        ];

        // Garantindo pelo menos 1 Clérigo, 1 Guerreiro, e 1 Mago/Arqueiro
        foreach (['Clérigo', 'Guerreiro'] as $class) {
            if (empty($classifiedPlayers[$class])) {
                $feedback[] = "A guilda {$guild->name} não possui um {$class}!";
                return; // Se não houver, encerra a atribuição
            }

            $player = array_shift($classifiedPlayers[$class]);
            $assignedPlayers[$class] = $player;
            $guild->players()->save($player);
        }

        // Verificando se tem pelo menos um Mago ou Arqueiro
        $mageOrArcher = $this->getMageOrArcher($classifiedPlayers);

        if ($mageOrArcher === null) {
            $feedback[] = "A guilda {$guild->name} não possui um Mago ou Arqueiro!";
            return; // Se não houver Mago ou Arqueiro, encerra a atribuição
        }

        // Atribui um Mago ou Arqueiro
        $assignedPlayers['Mago/Arqueiro'] = $mageOrArcher;
        $guild->players()->save($mageOrArcher);

        // Atualizando a lista de jogadores restantes para que outras guildas possam ser preenchidas
        $classifiedPlayers['Mago'][] = $mageOrArcher;  // Apenas exemplo, a remoção pode ser mais complexa

        // Atribuindo o feedback, incluindo o sucesso de cada distribuição
        $feedback[] = "A guilda {$guild->name} foi preenchida com sucesso com as classes necessárias.";
    }

    protected function getMageOrArcher(&$classifiedPlayers)
    {
        // Tenta pegar um Mago ou Arqueiro
        if (!empty($classifiedPlayers['Mago'])) {
            return array_shift($classifiedPlayers['Mago']);
        } elseif (!empty($classifiedPlayers['Arqueiro'])) {
            return array_shift($classifiedPlayers['Arqueiro']);
        }
        return null;
    }
}
