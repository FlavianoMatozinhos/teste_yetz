<?php

namespace App\Http\Controllers;

use App\Services\LogoutService;
use Illuminate\Http\Request;


class LogoutController extends Controller
{
    protected $logoutService;

    public function __construct(LogoutService $logoutService)
    {
        $this->logoutService = $logoutService;
    }

    /**
     * @OA\Post(
     *     path="/logout",
     *     summary="Realiza o logout do usuário.",
     *     tags={"Logout"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout realizado com sucesso.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Logout realizado com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao realizar logout.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Erro interno do servidor ao realizar logout.")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $result = $this->logoutService->logoutUser($request->user());

        if ($result['status'] === 'error') {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message']
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => $result['message']]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => $result['message']
            ], 200);
        }

        return redirect()->route('login')->with('success', $result['message']);
    }
}
