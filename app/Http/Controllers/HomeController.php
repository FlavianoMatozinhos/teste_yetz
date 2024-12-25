<?php

namespace App\Http\Controllers;

use App\Services\GuildBalancerService;
use App\Services\RegisterService;

/**
 * @OA\Info(title="API Home", version="1.0.0")
 * 
 * @OA\Get(
 *     path="/",
 *     summary="Exibe informações sobre guildas e jogadores",
 *     tags={"Home"},
 *     @OA\Response(
 *         response=200,
 *         description="Retorna informações da Home com guildas e jogadores",
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro interno do servidor"
 *     )
 * )
 */
class HomeController extends Controller
{
    protected $registerService;
    protected $guildBalancerService;

    public function __construct(RegisterService $registerService, GuildBalancerService $guildBalancerService)
    {
        $this->registerService = $registerService;
        $this->guildBalancerService = $guildBalancerService;
    }

    public function index(): mixed
    {
        $guilds = $this->guildBalancerService->getAllWithConfirmationStatus();
        $players = $this->registerService->getAllWithConfirmationStatus();

        $data = [
            'message' => 'Bem-vindo à Home!',
            'guilds' => $guilds,
            'players' => $players,
        ];

        return view('home', $data);
    }
}
