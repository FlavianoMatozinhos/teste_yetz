<?php

namespace App\Http\Controllers;

use App\Services\GuildBalancerService;
use App\Repositories\GuildRepository;
use App\Repositories\PlayerRepository;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $guildRepository;
    protected $playerRepository;
    protected $guildBalancerService;

    public function __construct(GuildRepository $guildRepository, PlayerRepository $playerRepository, GuildBalancerService $guildBalancerService)
    {
        $this->guildRepository = $guildRepository;
        $this->playerRepository = $playerRepository;
        $this->guildBalancerService = $guildBalancerService;
    }

    public function index()
    {
        $guilds = $this->guildRepository->getAllWithConfirmationStatus();
        $players = $this->playerRepository->getAllWithConfirmationStatus();

        $data = [
            'message' => 'Bem-vindo à Home!',
            'guilds' => $guilds,
            'players' => $players,
            'numGuildas' => $this->guildRepository->countGuilds(),
        ];

        return view('home', $data);
    }
}
