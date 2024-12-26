<?php

namespace App\Http\Controllers;

use App\Services\LoginService;
use Illuminate\Http\Request;
use Exception;

class LoginController extends Controller
{
    protected $loginService;

    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    /**
     * @OA\Get(
     *     path="/login",
     *     summary="Exibe a página de login.",
     *     tags={"Login"},
     *     @OA\Response(
     *         response=200,
     *         description="Retorna a página de login."
     *     )
     * )
     */
    public function index(): mixed
    {
        return view('auth.login');
    }

    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Processa o login do usuário.",
     *     tags={"Login"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string", format="email", example="usuario@exemplo.com"),
     *             @OA\Property(property="password", type="string", format="password", example="senha123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login realizado com sucesso.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Erro de autenticação.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Credenciais inválidas.")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
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
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Erro ao processar login: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Erro ao processar login: ' . $e->getMessage()]);
        }
    }
}
