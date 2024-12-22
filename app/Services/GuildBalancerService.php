<?php

namespace App\Services;

use App\Models\Guild;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class GuildBalancerService
{
    public function balanceGuilds(int $numGuildas)
    {
        try {
            $guilds = Guild::all();
            $players = User::where('confirmed', 1)->get();
            
            if ($numGuildas > count($guilds)) {
                return redirect()->back()->with('error', 'O número de guildas selecionadas é maior do que o número de guildas cadastradas.');
            }

            if ($numGuildas < count($guilds)) {
                return redirect()->back()->with('error', "Não é possível balancear com menos de {$guilds->count()} guildas.");
            }

            $playersPerGuild = ceil(count($players) / $numGuildas);

            $guildPlayerCounts = array_fill(0, count($guilds), 0);

            $players = $players->sortByDesc('xp');

            $playerAllocations = [];

            $missingClassesWarning = false;

            foreach ($players as $player) {
                $guildIndex = $this->findGuildWithLeastXP($guildPlayerCounts, $guilds, $player);

                if ($guildIndex === null) {
                    session()->flash('error', 'Não é possível balancear os jogadores respeitando as restrições de tamanho de guilda.');
                }

                $guild = $guilds[$guildIndex];
                $player->guild_id = $guild->id;
                $player->save();

                $guildPlayerCounts[$guildIndex]++;

                $playerAllocations[] = ['player_id' => $player->id, 'guild_id' => $guild->id];

                $this->checkGuildClasses($guild, $playerAllocations, $missingClassesWarning);
            }

            $this->checkGuildSizes($guilds, $guildPlayerCounts);

            return $missingClassesWarning;
        } catch (Exception $e) {
            Log::error('Erro no balanceamento de guildas: ' . $e->getMessage());
            session()->flash('error', 'Erro ao balancear as guildas: ' . $e->getMessage());
            return redirect()->route('home');
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
