<?php

namespace App\Http\Controllers;

use App\Services\RegisterService;
use Illuminate\Http\Request;


class RegisterController extends Controller
{
    protected $registerService;

    public function __construct(RegisterService $registerService)
    {
        $this->registerService = $registerService;
    }

    /**
     * @OA\Get(
     *     path="/register",
     *     summary="Exibe a página de registro com classes e jogadores disponíveis.",
     *     tags={"Register"},
     *     @OA\Response(
     *         response=200,
     *         description="Dados de classes e jogadores disponíveis.",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="classes", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="players", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $players = $this->registerService->getAllPlayers();
        $classes = $this->registerService->getAllClasses();

        if ($request->expectsJson()) {
            return response()->json($players, 200);
        }

        return view('auth.register', compact('classes', 'players'));
    }

    /**
     * @OA\Post(
     *     path="/register",
     *     summary="Armazena um novo usuário no banco de dados.",
     *     tags={"Register"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="class", type="string", example="Warrior")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Registro realizado com sucesso.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Registro realizado com sucesso! Agora você pode fazer login.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro na validação dos dados.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $result = $this->registerService->registerUser($request->all());

        if ($result['status'] === 'error') {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $result['errors']
                ], 400);
            }

            return redirect()->back()->withErrors($result['errors']);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Registro realizado com sucesso! Agora você pode fazer login.'
            ], 201);
        }

        return redirect('/login')->with('success', 'Registro realizado com sucesso! Faça login para continuar.');
    }

    /**
     * @OA\Put(
     *     path="/register/{id}",
     *     summary="Atualiza os dados de um usuário existente.",
     *     tags={"Register"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do usuário.",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Jane Doe"),
     *             @OA\Property(property="email", type="string", example="jane.doe@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário atualizado com sucesso.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Usuário atualizado com sucesso.")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $result = $this->registerService->updateUser($id, $request->all());

        if ($result['status'] === 'error') {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $result['errors'] ?? $result['data']
                ], $result['status_code']);
            }

            return redirect()->back()->withErrors($result['errors'] ?? $result['data']['message']);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => $result['data']['message']
            ], $result['status_code']);
        }

        return redirect()->back()->with('success', $result['data']['message']);
    }

    /**
     * @OA\Delete(
     *     path="/register/{id}",
     *     summary="Remove um usuário.",
     *     tags={"Register"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do usuário.",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário removido com sucesso.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Usuário removido com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro ao remover usuário.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Erro ao remover usuário.")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $result = $this->registerService->deleteUser($id);

        if ($result['status'] === 'error') {
            return response()->json([
                'status' => 'error',
                'message' => $result['data']['message'],
                'error' => $result['data']['error'] ?? null,
            ], $result['status_code']);
        }

        return response()->json([
            'status' => 'success',
            'message' => $result['data']['message'],
        ], $result['status_code']);
    }
}
