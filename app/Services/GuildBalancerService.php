<?php

namespace App\Services;

use App\Models\Guild;
use App\Models\User;
use App\Repositories\GuildRepository;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class GuildBalancerService
{
    protected $classRepository;
    protected $userRepository;
    protected $guildRepository;

    public function __construct(GuildRepository $guildRepository)
    {
        $this->guildRepository = $guildRepository;
    }

    public function balanceGuilds($guilds, $players)
    {
        try {
            $players = $players->sortByDesc('xp');

            $playerAllocations = [];
            $guildPlayerCounts = array_fill(0, count($guilds), 0);

            foreach ($players as $player) {
                $guildIndex = $this->findGuildWithLeastXP($guildPlayerCounts, $guilds, $player);

                if ($guildIndex === null) {
                    continue;
                }

                $guild = $guilds[$guildIndex];
                $player->guild_id = $guild->id;
                $player->save();

                $guildPlayerCounts[$guildIndex]++;

                $playerAllocations[] = ['player_id' => $player->id, 'guild_id' => $guild->id];
            }

            $missingClassesWarning = false;
            $this->checkGuildClasses($guilds, $playerAllocations, $missingClassesWarning);

            return $missingClassesWarning;
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

    private function checkGuildClasses($guilds, $playerAllocations, &$missingClassesWarning)
    {
        foreach ($guilds as $guild) {
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
                Log::warning("Guilda {$guild->name} não tem uma formação ideal de classes.");
            }
        }
    }

    public function createGuild(array $data)
    {    
        try {
            $validator = validator($data, [
                'name' => 'required|string|max:255|unique:guilds,name',
                'min_players' => 'required|integer|min:1',
                'max_players' => 'required|integer|min:1|gte:min_players',
            ]);

            if ($this->guildRepository->existsByName($data['name'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Já existe uma classe com esse nome.',
                    'status_code' => 400,
                    'data' => null
                ], 400);
            }
            
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Dados inválidos.',
                    'errors' => $validator->errors(),
                    'status_code' => 422,
                    'data' => null
                ], 422);
            }
    
            $guildData = $data;
            $guildData['creator_id'] = Auth::id();
    
            $this->guildRepository->createGuild($guildData);
    
            return response()->json([
                'status' => 'success',
                'message' => 'Guilda criada com sucesso!',
                'status_code' => 200,
                'data' => $guildData
            ], 200);
    
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao criar a guilda.',
                'error' => $e->getMessage(),
                'status_code' => 500,
                'data' => null
            ], 500);
        }
    }
    

    public function getGuildById($id)
    {
        try {
            $class = $this->guildRepository->findGuildById($id);
            
            if (!$class) {
                return [
                    'status' => 'error',
                    'message' => 'Guilda não encontrada.',
                    'status_code' => 404
                ];
            }

            return [
                'status' => 'success',
                'data' => $class
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erro ao buscar Guilda.',
                'error' => $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    public function updateGuild($id, array $data)
    {
        try {
            $validator = validator($data, [
                'name' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            if (empty($data['name'])) {
                return [
                    'status' => 'error',
                    'message' => 'O nome da Guilda não pode estar vazio.',
                    'status_code' => 422
                ];
            }

            if ($this->guildRepository->existsByName($data['name'], $id)) {
                return [
                    'status' => 'error',
                    'message' => 'Já existe uma Guilda com esse nome.',
                    'status_code' => 400
                ];
            }

            $updatedClass = $this->guildRepository->update($id, $data);

            return [
                'status' => 'success',
                'data' => $updatedClass
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erro ao atualizar Guilda.',
                'error' => $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    public function deleteGuild($id)
    {
        try {
            $this->guildRepository->delete($id);
            return ['status' => 'success', 'data' => null];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Erro ao excluir Guilda.',
                'error' => $e->getMessage(),
                'status_code' => 500,
                'data' => null
            ];
        }
    }
}
