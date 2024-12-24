<?php

namespace App\Http\Controllers;

use App\Services\ClassService;
use Illuminate\Http\Request;

/**
 * @OA\Info(title="API de Classes", version="1.0")
 */
class ClassController extends Controller
{
    protected $classService;

    public function __construct(ClassService $classService)
    {
        $this->classService = $classService;
    }

    /**
     * @OA\Get(
     *     path="/api/classes",
     *     summary="Lista todas as classes",
     *     tags={"Classes"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de classes",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Classes listadas com sucesso."),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object", @OA\Property(property="id", type="integer"), @OA\Property(property="name", type="string")))
     *         )
     *     ),
     *     @OA\Response(response=400, description="Erro")
     * )
     */
    public function index()
    {
        $result = $this->classService->getAllClasses();

        if ($result['status'] === 'error') {
            return response()->json(
                [
                    'message' => $result['message'],
                    'status_code' => $result['status_code'],
                    'error' => $result['error'] ?? null,
                ],
                $result['status_code']
            );
        }

        return response()->json(
            [
                'data' => $result['data'],
                'message' => 'Classes listadas com sucesso.',
            ],
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/classes",
     *     summary="Cria uma nova classe",
     *     tags={"Classes"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="New Class")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Classe criada com sucesso",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Classe criada com sucesso."),
     *             @OA\Property(property="data", type="object", @OA\Property(property="name", type="string", example="New Class"))
     *         )
     *     ),
     *     @OA\Response(response=400, description="Erro")
     * )
     */
    public function store(Request $request)
    {
        $result = $this->classService->createClass($request->all());

        if ($result['status'] === 'error') {
            return response()->json(
                [
                    'message' => $result['message'],
                    'errors' => $result['errors'] ?? null,
                    'status_code' => $result['status_code'],
                ],
                $result['status_code']
            );
        }

        return response()->json(
            [
                'data' => $result['data'],
                'message' => 'Classe criada com sucesso.',
            ],
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/classes/{id}",
     *     summary="Exibe uma classe específica",
     *     tags={"Classes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Classe encontrada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Classe encontrada com sucesso."),
     *             @OA\Property(property="data", type="object", @OA\Property(property="id", type="integer", example=1), @OA\Property(property="name", type="string", example="Class 1"))
     *         )
     *     ),
     *     @OA\Response(response=404, description="Classe não encontrada")
     * )
     */
    public function show($id)
    {
        $result = $this->classService->getClassById($id);

        if ($result['status'] === 'error') {
            return response()->json(
                [
                    'message' => $result['message'],
                    'status_code' => $result['status_code'],
                    'error' => $result['error'] ?? null,
                ],
                $result['status_code']
            );
        }

        return response()->json(
            [
                'data' => $result['data'],
                'message' => 'Classe encontrada com sucesso.',
            ],
            200
        );
    }

    /**
     * @OA\Put(
     *     path="/api/classes/{id}",
     *     summary="Atualiza uma classe específica",
     *     tags={"Classes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Updated Class")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Classe atualizada",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Classe atualizada com sucesso."),
     *             @OA\Property(property="data", type="object", @OA\Property(property="name", type="string", example="Updated Class"))
     *         )
     *     ),
     *     @OA\Response(response=400, description="Erro")
     * )
     */
    public function update(Request $request, $id)
    {
        $result = $this->classService->updateClass($id, $request->all());

        if ($result['status'] === 'error') {
            return response()->json(
                [
                    'message' => $result['message'],
                    'errors' => $result['errors'] ?? null,
                    'status_code' => $result['status_code'],
                ],
                $result['status_code']
            );
        }

        return response()->json(
            [
                'data' => $result['data'],
                'message' => 'Classe atualizada com sucesso.',
            ],
            200
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/classes/{id}",
     *     summary="Deleta uma classe específica",
     *     tags={"Classes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Classe deletada"
     *     ),
     *     @OA\Response(response=404, description="Classe não encontrada")
     * )
     */
    public function destroy($id)
    {
        $result = $this->classService->deleteClass($id);

        if ($result['status'] === 'error') {
            return response()->json(
                [
                    'message' => $result['message'],
                    'error' => $result['error'] ?? null,
                    'status_code' => $result['status_code'],
                ],
                $result['status_code']
            );
        }

        return response()->json(
            [
                'message' => 'Classe deletada com sucesso.',
            ],
            204
        );
    }
}
