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
            $guilds = Guild::all();  // Pega todas as guildas disponíveis
            $players = User::all();  // Pega todos os jogadores
            
            if ($numGuildas > count($guilds)) {
                return redirect()->back()->with('error', 'O número de guildas selecionadas é maior do que o número de guildas cadastradas.');
            }

            if ($numGuildas < count($guilds)) {
                return redirect()->back()->with('error', "Não é possível balancear com menos de {$guilds->count()} guildas.");
            }

            $playersPerGuild = ceil(count($players) / $numGuildas);  // Calcula quantos jogadores por guilda

            // Inicializa um array para controlar o número de jogadores em cada guilda
            $guildPlayerCounts = array_fill(0, count($guilds), 0);

            // Ordena os jogadores por XP (do maior para o menor)
            $players = $players->sortByDesc('xp');

            // Inicializa um array para armazenar as alocações dos jogadores
            $playerAllocations = [];

            // Inicializa variáveis de alerta para as classes
            $missingClassesWarning = false;

            // Distribui os jogadores entre as guildas balanceando o XP
            foreach ($players as $player) {
                // Encontrar a guilda com o menor total de XP
                $guildIndex = $this->findGuildWithLeastXP($guildPlayerCounts, $guilds, $player);

                if ($guildIndex === null) {
                    session()->flash('error', 'Não é possível balancear os jogadores respeitando as restrições de tamanho de guilda.');
                    // throw new Exception('Não é possível balancear os jogadores respeitando as restrições de tamanho de guilda.');
                }

                $guild = $guilds[$guildIndex];  // Pega a guilda com menor XP
                $player->guild_id = $guild->id;  // Atualiza a guilda do jogador
                $player->save();  // Salva a alteração no banco de dados

                // Atualiza o número de jogadores na guilda
                $guildPlayerCounts[$guildIndex]++;

                // Armazena a alocação do jogador
                $playerAllocations[] = ['player_id' => $player->id, 'guild_id' => $guild->id];

                // Verifica se a guilda tem pelo menos um Clérigo, Guerreiro e Mago ou Arqueiro
                $this->checkGuildClasses($guild, $playerAllocations, $missingClassesWarning);
            }

            // Verifica se todas as guildas respeitam as restrições de jogadores mínimos e máximos
            $this->checkGuildSizes($guilds, $guildPlayerCounts);

            return $missingClassesWarning;
        } catch (Exception $e) {
            // Loga o erro
            Log::error('Erro no balanceamento de guildas: ' . $e->getMessage());
            // Retorna o erro para o usuário
            session()->flash('error', 'Erro ao balancear as guildas: ' . $e->getMessage());
            return redirect()->route('home');
        }
    }

    private function findGuildWithLeastXP($guildPlayerCounts, $guilds, $player)
    {
        // Inicializa o array de total de XP por guilda
        $guildXP = [];

        // Calcula o total de XP de cada guilda
        foreach ($guilds as $index => $guild) {
            $guildXP[$index] = $this->calculateTotalXPForGuild($guild);
        }

        // Encontra a guilda com o menor total de XP
        asort($guildXP);
        foreach ($guildXP as $index => $xp) {
            $guild = $guilds[$index];  // Pega a guilda com menor total de XP

            // Verifica se a guilda tem espaço
            if ($guildPlayerCounts[$index] < $guild->max_players) {
                return $index;  // Retorna o índice da guilda com menor XP e espaço disponível
            }
        }

        return null;  // Retorna null se não houver guildas com espaço disponível
    }

    private function calculateTotalXPForGuild($guild)
    {
        // Calcula o total de XP de todos os jogadores na guilda
        $players = User::where('guild_id', $guild->id)->get();
        return $players->sum('xp');
    }

    private function checkGuildSizes($guilds, $guildPlayerCounts)
    {
        // Verifica se todas as guildas têm o número mínimo de jogadores
        foreach ($guilds as $index => $guild) {
            $count = $guildPlayerCounts[$index];

            if ($count < $guild->min_players) {
                // Se uma guilda não atingir o mínimo de jogadores, exibe um erro
                session()->flash('error', "A guilda {$guild->name} não tem jogadores suficientes.");
            }
        }
    }

    private function checkGuildClasses($guild, $playerAllocations, &$missingClassesWarning)
    {
        // Inicializa contadores para as classes
        $clericCount = 0;
        $warriorCount = 0;
        $mageCount = 0;
        $archerCount = 0;

        // Conta os jogadores de cada classe alocados à guilda
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

        // Verifica se todas as classes estão representadas
        if ($clericCount < 1 || $warriorCount < 1 || ($mageCount < 1 && $archerCount < 1)) {
            $missingClassesWarning = true;
        }
    }
}
