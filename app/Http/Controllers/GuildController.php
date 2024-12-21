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
    
        $players = User::all()->sortByDesc('xp');  // Pega todos os jogadores e ordena por XP de forma decrescente
        $playersPerGuild = ceil(count($players) / $numGuildas);  // Calcula quantos jogadores por guilda
    
        // Inicializa um array para controlar o total de XP em cada guilda
        $guildXpTotals = array_fill(0, $numGuildas, 0);
        // Inicializa um array para contar o número de jogadores por guilda
        $guildPlayerCounts = array_fill(0, $numGuildas, 0);
    
        // Inicializa um array para armazenar os jogadores alocados
        $playerAllocations = [];
    
        // Distribuindo os jogadores nas guildas de acordo com o XP
        foreach ($players as $player) {
            // Tenta alocar o jogador para a guilda com o menor total de XP, respeitando min e max
            $guildIndex = $this->findSuitableGuild($guildXpTotals, $guildPlayerCounts, $guilds, $numGuildas);
    
            if ($guildIndex === null) {
                return redirect()->back()->with('error', 'Não é possível balancear os jogadores respeitando as restrições de tamanho de guilda.');
            }
    
            $guild = $guilds[$guildIndex];  // Pega a guilda com o menor total de XP e que ainda tem espaço
            $player->guild_id = $guild->id;  // Atualiza a guilda do jogador no banco de dados
            $player->save();  // Salva as alterações no banco
    
            // Atualiza o total de XP da guilda e o número de jogadores
            $guildXpTotals[$guildIndex] += $player->xp;
            $guildPlayerCounts[$guildIndex]++;
    
            // Armazena a alocação do jogador
            $playerAllocations[] = ['player_id' => $player->id, 'guild_id' => $guild->id];
        }
    
        // Agora verifica se as guildas respeitam as restrições min/max de jogadores
        $this->adjustGuilds($guilds, $guildPlayerCounts);
    
        return redirect()->back()->with('status', 'Guildas balanceadas com sucesso!');
    }
    
    private function findSuitableGuild($guildXpTotals, $guildPlayerCounts, $guilds, $numGuildas)
    {
        // Vamos primeiro verificar as guildas que têm espaço para mais jogadores
        $validGuilds = [];
    
        foreach ($guilds as $index => $guild) {
            $minPlayers = $guild->min_players;
            $maxPlayers = $guild->max_players;
            $currentPlayerCount = $guildPlayerCounts[$index];
    
            // A guilda deve ter espaço suficiente para mais jogadores e respeitar o limite
            if ($currentPlayerCount < $maxPlayers) {
                $validGuilds[] = $index;  // Adiciona a guilda aos candidatos
            }
        }
    
        if (empty($validGuilds)) {
            return null;  // Se não houver guildas com espaço disponível, retorna null
        }
    
        // Agora entre as guildas válidas, escolhemos a que tem o menor total de XP
        $lowestXpGuildIndex = null;
        $lowestXpTotal = PHP_INT_MAX;
    
        foreach ($validGuilds as $index) {
            if ($guildXpTotals[$index] < $lowestXpTotal) {
                $lowestXpTotal = $guildXpTotals[$index];
                $lowestXpGuildIndex = $index;
            }
        }
    
        return $lowestXpGuildIndex;  // Retorna a guilda com o menor total de XP
    }    
    
    private function adjustGuilds($guilds, $guildPlayerCounts)
    {
        foreach ($guilds as $index => $guild) {
            $minPlayers = $guild->min_players;
            $maxPlayers = $guild->max_players;
            $currentPlayerCount = $guildPlayerCounts[$index];

            // Verifica se a guilda tem jogadores abaixo do mínimo ou acima do máximo
            if ($currentPlayerCount < $minPlayers) {
                \Log::error("A guilda {$guild->name} tem {$currentPlayerCount} jogadores, que é menor que o mínimo de {$minPlayers}.");
                throw new \Exception("A guilda {$guild->name} tem menos jogadores do que o mínimo necessário.");
            }
    
            if ($currentPlayerCount > $maxPlayers) {
                \Log::error("A guilda {$guild->name} tem {$currentPlayerCount} jogadores, que é maior que o máximo de {$maxPlayers}.");
                throw new \Exception("A guilda {$guild->name} tem mais jogadores do que o máximo permitido.");
            }
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
