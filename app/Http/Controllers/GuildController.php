<?php

namespace App\Http\Controllers;

use App\Repositories\GuildRepository;
use App\Services\GuildBalancerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Exception;
use Illuminate\View\View;

/**
 * @OA\Schema(
 *     schema="Guild",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Guild Name"),
 *     @OA\Property(property="level", type="integer", example=10),
 *     @OA\Property(property="members", type="array", @OA\Items(type="string"), example={"Player1", "Player2"})
 * )
 */
class GuildController extends Controller
{
    protected $guildRepository;
    protected $guildBalancerService;

    public function __construct(GuildRepository $guildRepository, GuildBalancerService $guildBalancerService)
    {
        $this->guildRepository = $guildRepository;
        $this->guildBalancerService = $guildBalancerService;
    }

    /**
     * @OA\Post(
     *     path="/guilds/balance",
     *     summary="Balancear guildas",
     *     tags={"Guilds"},
     *     @OA\Response(
     *         response=200,
     *         description="Guildas balanceadas com sucesso"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao balancear guildas"
     *     )
     * )
     */
    public function balance(): RedirectResponse
    {
        try {
            $guilds = $this->guildRepository->getAllGuilds();
            $players = $this->guildRepository->getAllPlayers();

            $missingClassesWarning = $this->guildBalancerService->balanceGuilds($guilds, $players);

            if ($missingClassesWarning) {
                return redirect()->back()->with('warning', 'Guildas balanceadas com sucesso! Algumas guildas não possuem uma formação ideal de classes.');
            }

            return redirect()->back()->with('success', 'Guildas balanceadas com sucesso!');
        } catch (Exception $e) {
            session()->flash('error', 'Erro ao balancear as guildas: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Houve um erro ao tentar balancear as guildas. Por favor, tente novamente.');
        }
    }

    /**
     * @OA\Get(
     *     path="/guilds",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de guildas",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Guild")
     *         )
     *     )
     * )
     */
    public function index(Request $request): View
    {
        try {
            $guilds = $this->guildRepository->getAllGuilds();

            if ($request->expectsJson()) {
                return response()->json($guilds, 200);
            }

            return view('guild.create', compact('guilds'));
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao listar guildas: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/guilds",
     *     summary="Criar uma nova guilda",
     *     tags={"Guilds"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Guild")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Guilda criada com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Guild")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro ao criar guilda"
     *     )
     * )
     */
    public function store(Request $request): mixed
    {
        $result = $this->guildBalancerService->createGuild($request->all());

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
                'message' => 'Guilda criada com sucesso.'
            ], 201);
        }

        return redirect('/')->with('success', 'Guilda criada com sucesso.');

    }

    /**
     * @OA\Get(
     *     path="/guilds/{id}",
     *     summary="Exibir uma guilda específica",
     *     tags={"Guilds"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da guilda",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Guilda encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/Guild")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Guilda não encontrada"
     *     )
     * )
     */
    public function show($id): JsonResponse|View
    {
        $result = $this->guildBalancerService->getGuildById($id);

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

        $guild = $result['data'];

        $players = $this->guildBalancerService->getPlayersByGuildId($id);

        return view('guild.show', compact('guild', 'players'));
    }

    /**
     * @OA\Get(
     *     path="/guilds/{id}/edit",
     *     summary="Exibir formulário de edição de uma guilda específica",
     *     tags={"Guilds"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da guilda",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Formulário de edição exibido com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/Guild")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Guilda não encontrada"
     *     )
     * )
     */
    public function edit($id): View|RedirectResponse
    {
        try {
            $result = $this->guildBalancerService->getGuildById($id);
            
            if (!$result['status'] === 'error') {
                return redirect()->route('home')->with('error', 'Guilda não encontrada.');
            }

            $guild = $result['data'];

            return view('guild.update', compact('guild'));
        } catch (Exception $e) {
            return redirect()->route('home')->with('error', 'Erro ao carregar a guilda: ' . $e->getMessage());
        }
    }


    /**
     * @OA\Put(
     *     path="/guilds/{id}",
     *     summary="Atualizar uma guilda específica",
     *     tags={"Guilds"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da guilda",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Guild")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Guilda atualizada",
     *         @OA\JsonContent(ref="#/components/schemas/Guild")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro ao atualizar guilda"
     *     )
     * )
     */
    public function update(Request $request, $id): mixed
    {
        $result = $this->guildBalancerService->updateGuild($id, $request->all());

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
     *     path="/guilds/{id}",
     *     summary="Deletar uma guilda específica",
     *     tags={"Guilds"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da guilda",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Guilda deletada com sucesso"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro ao deletar guilda"
     *     )
     * )
     */
    public function destroy($id)
    {
        $result = $this->guildBalancerService->deleteGuild($id);

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
