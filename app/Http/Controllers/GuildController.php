<?php

namespace App\Http\Controllers;

use App\Models\Guild;
use App\Models\User;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Http\Request;
use App\Services\GuildBalancerService;
use Illuminate\Support\Facades\Auth;

class GuildController extends Controller
{
    protected $guildBalancerService;

    public function __construct(GuildBalancerService $guildBalancerService)
    {
        $this->guildBalancerService = $guildBalancerService;
    }

    public function balance(Request $request)
    {
        $numGuildas = $request->input('num_guildas');  // Número de guildas selecionado
        $guilds = Guild::all();  // Pega todas as guildas disponíveis
    
        // Verifica se o número de guildas selecionado é maior que o número de guildas cadastradas
        if ($numGuildas > count($guilds)) {
            return redirect()->back()->with('error', 'O número de guildas selecionadas é maior do que o número de guildas cadastradas.');
        }
    
        // Verifica se o número de guildas é menor do que 2 (não pode balancear se for 1)
        if ($numGuildas < 2) {
            return redirect()->back()->with('error', 'Não é possível balancear com menos de 2 guildas.');
        }
    
        $players = User::all();  // Pega todos os jogadores
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
                return redirect()->back()->with('error', 'Não é possível balancear os jogadores respeitando as restrições de tamanho de guilda.');
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
        try {
            $this->checkGuildSizes($guilds, $guildPlayerCounts);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    
        // Exibe um warning se faltar alguma classe
        if ($missingClassesWarning) {
            return redirect()->back()->with('warning', 'Guildas balanceadas com sucesso! Algumas guildas não possuem uma formação ideal de classes (Clérigo, Guerreiro, Mago ou Arqueiro).');
        }
    
        // Sucesso ao balancear
        return redirect()->back()->with('success', 'Guildas balanceadas com sucesso!');
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
        $totalXP = $players->sum('xp');
        
        return $totalXP;
    }
    
    private function checkGuildSizes($guilds, $guildPlayerCounts)
    {
        // Verifica se todas as guildas têm o número mínimo de jogadores
        foreach ($guilds as $index => $guild) {
            $count = $guildPlayerCounts[$index];
    
            if ($count < $guild->min_players) {
                // Se uma guilda não atingir o mínimo de jogadores, exibe um erro
                throw new \Exception("A guilda {$guild->name} não tem jogadores suficientes.");
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
    
    // Lista todas as guildas
    public function index()
    {
        return view('guild.create');
    }

    // Cria uma nova guilda
    public function store(Request $request)
    {
        try {
            // Verificar se o usuário tem permissão
            if (Auth::user()->role_id !== 1) {
                return redirect()->route('guild.create')->with('error', 'Você não tem permissão para criar guildas.');
            }
    
            // Validar os dados
            $request->validate([
                'name' => 'required|string|max:255',
                'min_players' => 'required|integer|min:1',
                'max_players' => 'required|integer|min:1',
            ]);
    
            // Criar a guilda
            Guild::create([
                'name' => $request->input('name'),
                'min_players' => $request->input('min_players'),
                'max_players' => $request->input('max_players'),
                'creator_id' => Auth::id(), // Associa o mestre à guilda
            ]);
    
            return redirect()->route('home')->with('success', 'Guilda criada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('guild.create')->with('error', 'Erro ao criar guilda: ' . $e->getMessage());
        }
    }

    // Exibe uma guilda específica
    public function show($id)
    {
        try {
            $guild = Guild::findOrFail($id);
            return response()->json($guild, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Guilda não encontrada.',
                'error' => $e->getMessage()
            ], 404); // Not Found
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao buscar guilda.',
                'error' => $e->getMessage()
            ], 500); // Internal Server Error
        }
    }

    // Atualiza uma guilda específica
    public function update(Request $request, $id)
    {
        try {
            $guild = Guild::findOrFail($id);

            // Validação dos dados antes de atualizar
            $request->validate([
                'name' => 'nullable|string|max:255',
                // Adicione outras validações aqui, se necessário
            ]);

            $guild->update($request->all());
            return response()->json($guild, 200); // OK
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Guilda não encontrada.',
                'error' => $e->getMessage()
            ], 404); // Not Found
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Dados inválidos.',
                'errors' => $e->errors()
            ], 422); // Unprocessable Entity
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar guilda.',
                'error' => $e->getMessage()
            ], 500); // Internal Server Error
        }
    }

    // Deleta uma guilda específica
    public function destroy($id)
    {
        try {
            $guild = Guild::findOrFail($id);
            $guild->delete();
            return response()->json(null, 204); // No Content
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Guilda não encontrada.',
                'error' => $e->getMessage()
            ], 404); // Not Found
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao excluir guilda.',
                'error' => $e->getMessage()
            ], 500); // Internal Server Error
        }
    }
}
