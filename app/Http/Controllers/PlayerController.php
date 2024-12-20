<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;

class PlayerController extends Controller
{
    // Lista todos os jogadores
    public function index()
    {
        try {
            $players = Player::all();
            return response()->json($players, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao listar jogadores.',
                'error' => $e->getMessage()
            ], 500); // Internal Server Error
        }
    }

    // Cria um novo jogador
    public function store(Request $request)
    {
        try {
            // Validação dos dados antes de salvar
            $request->validate([
                'name' => 'required|string|max:255',
                'class_id' => 'required|exists:classes,id',
                'guild_id' => 'required|exists:guilds,id',
                'xp' => 'required|integer',
                'confirmed' => 'required|boolean',
            ]);

            $player = Player::create($request->all());
            return response()->json($player, 201); // Created
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Dados inválidos.',
                'errors' => $e->errors()
            ], 422); // Unprocessable Entity
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar jogador.',
                'error' => $e->getMessage()
            ], 500); // Internal Server Error
        }
    }

    // Exibe um jogador específico
    public function show($id)
    {
        try {
            $player = Player::findOrFail($id);
            return response()->json($player, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Jogador não encontrado.',
                'error' => $e->getMessage()
            ], 404); // Not Found
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao buscar jogador.',
                'error' => $e->getMessage()
            ], 500); // Internal Server Error
        }
    }

    // Atualiza um jogador específico
    public function update(Request $request, $id)
    {
        try {
            $player = Player::findOrFail($id);

            // Validação dos dados antes de atualizar
            $request->validate([
                'name' => 'nullable|string|max:255',
                'class_id' => 'nullable|exists:classes,id',
                'guild_id' => 'nullable|exists:guilds,id',
                'xp' => 'nullable|integer',
                'confirmed' => 'nullable|boolean',
            ]);

            $player->update($request->all());
            return response()->json($player, 200); // OK
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Jogador não encontrado.',
                'error' => $e->getMessage()
            ], 404); // Not Found
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Dados inválidos.',
                'errors' => $e->errors()
            ], 422); // Unprocessable Entity
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar jogador.',
                'error' => $e->getMessage()
            ], 500); // Internal Server Error
        }
    }

    // Deleta um jogador específico
    public function destroy($id)
    {
        try {
            $player = Player::findOrFail($id);
            $player->delete();
            return response()->json(null, 204); // No Content
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Jogador não encontrado.',
                'error' => $e->getMessage()
            ], 404); // Not Found
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao excluir jogador.',
                'error' => $e->getMessage()
            ], 500); // Internal Server Error
        }
    }
}
