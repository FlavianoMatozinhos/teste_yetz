<?php

namespace App\Services;

use App\Models\Guild;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class GuildBalancerService
{
    public function balanceGuilds($guilds, $players)
    {
        try {
            $players = $players->sortByDesc('xp'); // Ordena por XP em ordem decrescente

            $playerAllocations = [];
            $guildPlayerCounts = array_fill(0, count($guilds), 0);

            foreach ($players as $player) {
                $guildIndex = $this->findGuildWithLeastXP($guildPlayerCounts, $guilds, $player);

                if ($guildIndex === null) {
                    continue; // Se nenhuma guilda estiver disponível, o jogador não será alocado
                }

                $guild = $guilds[$guildIndex];
                $player->guild_id = $guild->id;
                $player->save();

                $guildPlayerCounts[$guildIndex]++;

                $playerAllocations[] = ['player_id' => $player->id, 'guild_id' => $guild->id];
            }

            return false; // Não haverá mais warning para classes ausentes
        } catch (Exception $e) {
            Log::error('Erro no balanceamento de guildas: ' . $e->getMessage());
            throw $e;
        }
    }


    private function findGuildWithLeastXP($guildPlayerCounts, $guilds, $player)
    {
        $guildXP = [];

        foreach ($guilds as $index => $guild) {
            $guildXP[$index] = $this->calculateTotalXPForGuild($guild);
        }
        asort($guildXP);
        foreach ($guildXP as $index => $xp) {
            $guild = $guilds[$index];

            if ($guildPlayerCounts[$index] < $guild->max_players) {
                return $index;
            }
        }

        return null;
    }

    private function calculateTotalXPForGuild($guild)
    {
        $players = User::where('guild_id', $guild->id)->get();
        return $players->sum('xp');
    }

    private function checkGuildSizes($guilds, $guildPlayerCounts)
    {
        foreach ($guilds as $index => $guild) {
            $count = $guildPlayerCounts[$index];

            if ($count < $guild->min_players) {
                session()->flash('error', "A guilda {$guild->name} não tem jogadores suficientes.");
            }
        }
    }

    private function checkGuildClasses($guild, $playerAllocations, &$missingClassesWarning)
    {
        $clericCount = 0;
        $warriorCount = 0;
        $mageCount = 0;
        $archerCount = 0;

        foreach ($playerAllocations as $allocation) {
            if ($allocation['guild_id'] == $guild->id) {
                $player = User::find($allocation['player_id']);
                if ($player->class == 'Clérigo') {
                    $clericCount++;
                } elseif ($player->class == 'Guerreiro') {
                    $warriorCount++;
                } elseif ($player->class == 'Mago') {
                    $mageCount++;
                } elseif ($player->class == 'Arqueiro') {
                    $archerCount++;
                }
            }
        }

        if ($clericCount < 1 || $warriorCount < 1 || ($mageCount < 1 && $archerCount < 1)) {
            $missingClassesWarning = true;
        }
    }
}
