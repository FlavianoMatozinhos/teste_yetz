<?php

namespace App\Http\Controllers;

use App\Services\RegisterService;
use App\Services\ClassService;
use App\Services\GuildBalancerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Exception;

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

    public function update(Request $request, $id)
    {
        try {
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
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao atualizar o usuário: ' . $e->getMessage()
            ], 500);
        }
    }

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
