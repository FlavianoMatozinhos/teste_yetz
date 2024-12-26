<?php

namespace App\Http\Controllers;

use App\Services\RegisterService;
use App\Services\ClassService;
use App\Services\GuildBalancerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Exception;

/**
 * @OA\Tag(
 *     name="Register",
 *     description="Gerenciamento de registro de jogadores"
 * )
*/

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
    *     summary="Exibe os dados de registro de jogadores",
    *     tags={"Register"},
    *     @OA\Response(
    *         response=200,
    *         description="Retorna dados de jogadores e classes",
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Erro ao carregar dados de registro"
    *     )
    * )
    */
    public function index(Request $request)
    {
        try {
            $players = $this->registerService->getAllPlayers();
            $classesResult = $this->registerService->getAllClasses();
            $classes = $classesResult['data'];

            if ($request->expectsJson()) {
                return response()->json($players, 200);
            }

            return view('auth.register', compact('classes', 'players'));
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao carregar dados de registro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
    * @OA\Post(
    *     path="/register",
    *     summary="Registra um novo jogador",
    *     tags={"Register"},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             required={"name", "email", "class_id"},
    *             @OA\Property(property="name", type="string", example="JogadorExemplo"),
    *             @OA\Property(property="email", type="string", example="jogador@example.com"),
    *             @OA\Property(property="class_id", type="integer", example=1)
    *         )
    *     ),
    *     @OA\Response(
    *         response=201,
    *         description="Registro realizado com sucesso"
    *     ),
    *     @OA\Response(
    *         response=400,
    *         description="Erro de validação"
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Erro ao registrar jogador"
    *     )
    * )
    */
    public function store(Request $request)
    {
        try {
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
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Erro ao realizar o registro: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withErrors(['general' => 'Erro ao realizar o registro.']);
        }
    }

    /** 
    * @OA\Get(
    *     path="/players/{id}",
    *     summary="Exibe detalhes do jogador",
    *     tags={"Register"},
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         required=true,
    *         description="ID do jogador",
    *         @OA\Schema(type="integer", example=1)
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Retorna detalhes do jogador",
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Jogador não encontrado"
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Erro ao carregar detalhes do jogador"
    *     )
    * )
    */
    public function show($id)
    {
        try {
            $result = $this->registerService->getPlayerById($id);

            if ($result['status'] === 'error') {
                return response()->json([
                    'message' => $result['message'],
                    'status_code' => $result['status_code'],
                    'error' => $result['error'] ?? null,
                ], $result['status_code']);
            }

            $player = $result['data'];
            return view('player.show', compact('player'));
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao carregar os dados do jogador: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/players/edit/{id}",
     *     summary="Editar jogador",
     *     description="Carrega a página de edição de um jogador, incluindo as classes disponíveis",
     *     tags={"Player"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do jogador a ser editado",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Página de edição carregada com sucesso",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao carregar a página de edição",
     *     )
     * )
     */
    public function edit($id)
    {
        try {
            $playerResult = $this->registerService->getPlayerById($id);
            $player = $playerResult['data'];
    
            $classesResult = $this->registerService->getAllClasses();
            $classes = $classesResult['data'];
    
            return view('player.update', [
                'player' => $player,
                'classes' => $classes,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao carregar a página de edição: ' . $e->getMessage()
            ], 500);
        }
    }

    /** 
    * @OA\Get(
    *     path="/players/confirm/{id}",
    *     summary="Confirma a participação do jogador em batalha",
    *     tags={"Register"},
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         required=true,
    *         description="ID do jogador",
    *         @OA\Schema(type="integer", example=1)
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Jogador confirmado para batalhar"
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Erro ao confirmar jogador"
    *     )
    * )
    */
    public function confirm($id)
    {
        try {
            $this->registerService->getPlayerByIdAndConfirm($id);
            return redirect()->back()->with('success', 'Confirmado para batalhar!');
        } catch (Exception $e) {
            session()->flash('error', 'Erro ao confirmar: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Houve um erro ao confirmar batalha. Por favor, tente novamente.');
        }
    }

    /** 
    * @OA\Get(
    *     path="/players/noconfirm/{id}",
    *     summary="Remove a confirmação do jogador para batalha",
    *     tags={"Register"},
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         required=true,
    *         description="ID do jogador",
    *         @OA\Schema(type="integer", example=1)
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Jogador retirado da batalha"
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Erro ao retirar confirmação"
    *     )
    * )
    */
    public function noconfirm($id)
    {
        try {
            $this->registerService->getPlayerByIdAndNoConfirm($id);
            return redirect()->back()->with('success', 'Se retirou da batalha!');
        } catch (Exception $e) {
            session()->flash('error', 'Erro ao retirar confirmação: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Houve um erro ao retirar confirmação da batalha. Por favor, tente novamente.');
        }
    }

    /**
    * @OA\Put(
    *     path="/players/{id}",
    *     summary="Atualiza os dados do jogador",
    *     tags={"Register"},
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         required=true,
    *         description="ID do jogador",
    *         @OA\Schema(type="integer", example=1)
    *     ),
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             required={"name", "email", "class_id"},
    *             @OA\Property(property="name", type="string", example="JogadorAtualizado"),
    *             @OA\Property(property="email", type="string", example="jogadoratualizado@example.com"),
    *             @OA\Property(property="class_id", type="integer", example=2)
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Jogador atualizado com sucesso"
    *     ),
    *     @OA\Response(
    *         response=400,
    *         description="Erro de validação"
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Erro ao atualizar jogador"
    *     )
    * )
    * 
    */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->except(['_token', '_method']);
            $result = $this->registerService->updateUser($id, $data);

            if ($result['status'] === 'error') {
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $result['data']['message']
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
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao atualizar o usuário: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
    * @OA\Delete(
    *     path="/players/{id}",
    *     summary="Remove um jogador",
    *     tags={"Register"},
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         required=true,
    *         description="ID do jogador",
    *         @OA\Schema(type="integer", example=1)
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Jogador removido com sucesso"
    *     ),
    *     @OA\Response(
    *         response=404,
    *         description="Jogador não encontrado"
    *     ),
    *     @OA\Response(
    *         response=500,
    *         description="Erro ao remover jogador"
    *     )
    * )
    */
    public function destroy($id)
    {
        try {
            $result = $this->registerService->deleteUser($id);

            if ($result['status'] === 'error') {
                return response()->json([
                    'message' => $result['message'],
                    'error' => $result['error'] ?? null,
                    'status_code' => $result['status_code'],
                ], $result['status_code']);
            }

            return redirect()->back()->with('success', $result['data']['message']);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao remover o usuário: ' . $e->getMessage()
            ], 500);
        }
    }
}
