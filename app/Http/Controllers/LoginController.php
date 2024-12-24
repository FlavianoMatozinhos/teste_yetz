<?php

namespace App\Http\Controllers;

use App\Services\LoginService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    protected $loginService;

    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    /**
     * Exibe a página de login.
     */
    public function index()
    {
        return view('auth.login');
    }

    /**
     * Processa o login do usuário.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $result = $this->loginService->login($request->all());

        if ($result['status'] === 'error') {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message']
                ], 401);
            }

            return redirect()->back()->withErrors(['error' => $result['message']]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'token' => $result['token']
            ], 200);
        }

        return redirect('/')->with('success', 'Login realizado com sucesso.');
    }
}
