<?php

namespace App\Http\Controllers;

use App\Repositories\GuildRepository;
use App\Services\GuildBalancerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            return redirect()->route('home');
        }
    }

    public function index()
    {
        return view('guild.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:guilds,name',
                'min_players' => 'required|integer|min:1',
                'max_players' => 'required|integer|min:1|gte:min_players',
            ]);
    
            $guildData = $request->only(['name', 'min_players', 'max_players']);
            $guildData['creator_id'] = Auth::id();

            $this->guildRepository->createGuild($guildData);

            return redirect()->route('home')->with('success', 'Guilda criada com sucesso!');
        } catch (Exception $e) {
            return redirect()->route('guild.create')->with('error', 'Erro ao criar guilda: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $guild = $this->guildRepository->findGuildById($id);
            return response()->json($guild, 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Guilda não encontrada.', 'error' => $e->getMessage()], 404);
        }
    }

    public function destroy($id)
    {
        try {
            $guild = $this->guildRepository->findGuildById($id);
            $this->guildRepository->deleteGuild($guild);
            return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao excluir guilda.', 'error' => $e->getMessage()], 500);
        }
    }
}
