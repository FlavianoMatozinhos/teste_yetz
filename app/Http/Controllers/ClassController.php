<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;

class ClassController extends Controller
{
    // Lista todas as classes
    public function index()
    {
        try {
            $classes = ClassModel::all();
            return response()->json($classes, 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao listar classes.',
                'error' => $e->getMessage()
            ], 500); // Internal Server Error
        }
    }

    // Cria uma nova classe
    public function store(Request $request)
    {
        try {
            // Validação dos dados antes de salvar
            $request->validate([
                'name' => 'required|string|max:255',
                // Adicione outras validações aqui, se necessário
            ]);

            $class = ClassModel::create($request->all());
            return response()->json($class, 201); // Created
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Dados inválidos.',
                'errors' => $e->errors()
            ], 422); // Unprocessable Entity
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar classe.',
                'error' => $e->getMessage()
            ], 500); // Internal Server Error
        }
    }

    // Exibe uma classe específica
    public function show($id)
    {
        try {
            $class = ClassModel::findOrFail($id);
            return response()->json($class, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Classe não encontrada.',
                'error' => $e->getMessage()
            ], 404); // Not Found
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao buscar classe.',
                'error' => $e->getMessage()
            ], 500); // Internal Server Error
        }
    }

    // Atualiza uma classe específica
    public function update(Request $request, $id)
    {
        try {
            $class = ClassModel::findOrFail($id);

            // Validação dos dados antes de atualizar
            $request->validate([
                'name' => 'nullable|string|max:255',
                // Adicione outras validações aqui, se necessário
            ]);

            $class->update($request->all());
            return response()->json($class, 200); // OK
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Classe não encontrada.',
                'error' => $e->getMessage()
            ], 404); // Not Found
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Dados inválidos.',
                'errors' => $e->errors()
            ], 422); // Unprocessable Entity
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar classe.',
                'error' => $e->getMessage()
            ], 500); // Internal Server Error
        }
    }

    // Deleta uma classe específica
    public function destroy($id)
    {
        try {
            $class = ClassModel::findOrFail($id);
            $class->delete();
            return response()->json(null, 204); // No Content
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Classe não encontrada.',
                'error' => $e->getMessage()
            ], 404); // Not Found
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao excluir classe.',
                'error' => $e->getMessage()
            ], 500); // Internal Server Error
        }
    }
}
