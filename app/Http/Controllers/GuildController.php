<?php

namespace App\Http\Controllers;

use App\Models\Guild;
use App\Models\Player;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Http\Request;
use App\Services\GuildBalancerService;
use App\Repositories\GuildRepository;
use App\Services\ClassBalanceStrategy;
use App\Services\XPBalanceStrategy;

class GuildController extends Controller
{
    protected $guildBalancerService;

    public function __construct(GuildBalancerService $guildBalancerService)
    {
        $this->guildBalancerService = $guildBalancerService;
    }

    public function balance(Request $request)
    {
        $numGuilds = $request->input('num_guilds');
        
        $result = $this->guildBalancerService->balanceGuilds($numGuilds);

        return response()->json($result);
    }

    // Lista todas as guildas
    public function index()
    {
        try {
            $guilds = Guild::all();
            return response()->json($guilds, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao listar guildas.',
                'error' => $e->getMessage()
            ], 500); // Internal Server Error
        }
    }

    // Cria uma nova guilda
    public function store(Request $request)
    {
        try {
            // Validação dos dados antes de salvar
            $request->validate([
                'name' => 'required|string|max:255',
                // Adicione outras validações aqui, se necessário
            ]);

            $guild = Guild::create($request->all());
            return response()->json($guild, 201); // Created
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Dados inválidos.',
                'errors' => $e->errors()
            ], 422); // Unprocessable Entity
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar guilda.',
                'error' => $e->getMessage()
            ], 500); // Internal Server Error
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
