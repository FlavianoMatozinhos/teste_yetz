<?php

namespace App\Services;

use App\Models\User;

class XPBalanceStrategy
{
    public function balanceXP($guilds, $players)
    {
        // Calcula o XP total dos jogadores
        $totalXP = $players->sum('xp');
        
        // Calcula a média de XP que cada guilda deve ter
        $averageXP = $totalXP / count($guilds);
    
        // Ordena os jogadores pelo XP de forma decrescente
        $sortedPlayers = $players->sortByDesc('xp');
    
        foreach ($guilds as $guild) {
            // Obtém o XP total atual da guilda
            $guildXP = $guild->players->sum('xp');
    
            // Enquanto o XP da guilda for menor que a média, adiciona jogadores
            while ($guildXP < $averageXP) {
                // Pega o primeiro jogador da lista ordenada (shift() em vez de array_shift)
                $player = $sortedPlayers->shift();
                
                // Se não houver mais jogadores disponíveis, saímos do loop
                if (!$player) {
                    break;
                }
                
                // Adiciona o jogador à guilda
                $guild->players()->save($player);
                
                // Atualiza o XP total da guilda
                $guildXP = $guild->players->sum('xp');
            }
        }
    }
}

