<?php

namespace App\Http\Controllers;

use App\Repositories\GuildRepository;
use App\Services\GuildBalancerService;
use Illuminate\Http\Request;
use Exception;

class GuildController extends Controller
{
    protected $guildRepository;
    protected $guildBalancerService;

    public function __construct(GuildRepository $guildRepository, GuildBalancerService $guildBalancerService)
    {
        $this->guildRepository = $guildRepository;
        $this->guildBalancerService = $guildBalancerService;
    }

    public function balance()
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

    public function index(Request $request)
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

    public function store(Request $request)
    {
        $result = $this->guildBalancerService->createGuild($request->all());

        $responseData = $result->getData();

        $status = $responseData->status;
    
        if ($status === 'error') {
            // Faça algo em caso de erro
            return response()->json([
                'status' => 'error',
                'message' => $responseData->message,
                'status_code' => $responseData->status_code,
            ], $responseData->status_code);
        }
    
        // Retorna sucesso com os dados da guilda criada
        return response()->json(
            [
                'status' => 'success', // Adiciona status para indicar sucesso
                'message' => 'Guilda criada com sucesso.',
                'data' => $result['data'],
            ],
            201 // Código de status HTTP para sucesso
        );
    }

    public function show($id)
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

        return response()->json(
            [
                'data' => $result['data'],
                'message' => 'Guilda encontrada com sucesso.',
            ],
            200
        );
    }


    public function update(Request $request, $id)
    {
        $result = $this->guildBalancerService->updateGuild($id, $request->all());

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
                'message' => 'Guilda atualizada com sucesso.',
            ],
            200
        );
    }

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

        return response()->json(
            [
                'message' => 'Guilda deletada com sucesso.',
            ],
            204
        );
    }
}
