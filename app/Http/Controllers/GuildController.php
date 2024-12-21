<?php

namespace App\Http\Controllers;

use App\Repositories\GuildRepository;
use App\Services\GuildBalancerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuildController extends Controller
{
    protected $guildRepository;
    protected $guildBalancerService;

    public function __construct(GuildRepository $guildRepository, GuildBalancerService $guildBalancerService)
    {
        $this->guildRepository = $guildRepository;
        $this->guildBalancerService = $guildBalancerService;
    }

    public function balance(Request $request)
    {
        try {
            $numGuildas = $request->input('num_guildas');
            $guilds = $this->guildRepository->getAllGuilds();
            $players = $this->guildRepository->getAllPlayers();

            $missingClassesWarning = $this->guildBalancerService->balanceGuilds($numGuildas, $guilds, $players);

            if ($missingClassesWarning) {
                return redirect()->back()->with('warning', 'Guildas balanceadas com sucesso! Algumas guildas não possuem uma formação ideal de classes.');
            }

            return redirect()->back()->with('success', 'Guildas balanceadas com sucesso!');
        } catch (Exception $e) {
            // Exibe a mensagem de erro para o usuário
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
            // Validando os dados de entrada
            $request->validate([
                'name' => 'required|string|max:255',
                'min_players' => 'required|integer|min:1',
                'max_players' => 'required|integer|min:1',
            ]);
    
            // Criando a guilda
            $this->guildRepository->createGuild([
                'name' => $request->input('name'),
                'min_players' => $request->input('min_players'),
                'max_players' => $request->input('max_players'),
                'creator_id' => Auth::id(),
            ]);
    
            // Redirecionando com mensagem de sucesso
            return redirect()->route('home')->with('success', 'Guilda criada com sucesso!');
        } catch (Exception $e) {
            // Em caso de erro, redireciona com a mensagem de erro
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
