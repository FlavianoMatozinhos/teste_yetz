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
        $result = $this->loginService->login($request->all());

        if ($result['status'] === 'error') {
            return redirect()->back()->withErrors(['error' => $result['message']]);
        }

        return redirect('/')->with('success', 'Login realizado com sucesso.');
    }
}
