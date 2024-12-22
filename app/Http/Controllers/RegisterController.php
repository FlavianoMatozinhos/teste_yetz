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
    public function index()
    {
        $classes = $this->registerService->getAllClasses();

        return view('auth.register', compact('classes'));
    }

    /**
     * Armazena um novo usuário no banco de dados.
     */
    public function store(Request $request)
    {
        $result = $this->registerService->registerUser($request->all());

        if ($result['status'] === 'error') {
            return redirect()->back()->withErrors($result['errors']);
        }

        return redirect('/login')->with('success', 'Registro realizado com sucesso! Faça login para continuar.');
    }
}
