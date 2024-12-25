<?php

namespace App\Http\Controllers;

use App\Services\RegisterService;
use App\Services\ClassService;
use App\Services\GuildBalancerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegisterController extends Controller
{
    protected $registerService;
    protected $classService;
    protected $guildService;

    public function __construct(RegisterService $registerService, ClassService $classService, GuildBalancerService $guildService)
    {
        $this->registerService = $registerService;
        $this->classService = $classService;
        $this->guildService = $guildService;
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
    public function index(Request $request): View
    {
        $players = $this->registerService->getAllPlayers();
        $classesResult = $this->registerService->getAllClasses();

        $classes = $classesResult['data'];
    
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
    public function store(Request $request): View|RedirectResponse
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

    public function show($id): View
    {
        $result = $this->registerService->getPlayerById($id);

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

        $player = $result['data'];

        return view('player.show', compact('player'));
    }

    public function edit($id): View
    {
        $playerResult = $this->registerService->getPlayerById($id);
        $player = $playerResult['data'];
    
        $classesResult = $this->registerService->getAllClasses();
        $classes = $classesResult['data'];
    
        return view('player.update', [
            'player' => $player,
            'classes' => $classes,
        ]);
    }

    public function confirm($id): RedirectResponse
    {
        try {
            $this->registerService->getPlayerByIdAndConfirm($id);

            return redirect()->back()->with('success', 'Confirmado para batalhar!');
        } catch (Exception $e) {
            session()->flash('error', 'Erro ao confirmar: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Houve um erro ao confirmar batalha. Por favor, tente novamente.');
        }
    }

    public function noconfirm($id): RedirectResponse
    {
        try {
            $this->registerService->getPlayerByIdAndNoConfirm($id);

            return redirect()->back()->with('success', 'Se retirou da batalha!');
        } catch (Exception $e) {
            session()->flash('error', 'Erro ao retirar confirmacao: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Houve um erro ao retirar confirmacao da batalha. Por favor, tente novamente.');
        }
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
    public function update(Request $request, $id): RedirectResponse
    {
        $data = $request->except(['_token', '_method']);

        $result = $this->registerService->updateUser($id, $data);

        if ($result['status'] === 'error') {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $result['errors'] ?? $result['data']
                ], $result['status_code']);
            }

            return redirect()->back()->with('error', $result['message']);
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
    public function destroy($id): mixed
    {
        $result = $this->registerService->deleteUser($id);

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

        return redirect()->back()->with('success', $result['data']['message']);
    }

}
