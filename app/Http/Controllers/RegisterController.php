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
     * Exibe a página de registro com as classes disponíveis.
     */
    public function index(Request $request)
    {
        $players = $this->registerService->getAllPlayers();
        $classes = $this->registerService->getAllClasses();
    
        if ($request->expectsJson()) {
            return response()->json($players, 200);
        }
    
        // Passe a variável $players para a view
        return view('auth.register', compact('classes', 'players'));
    }    

    /**
     * Armazena um novo usuário no banco de dados.
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
