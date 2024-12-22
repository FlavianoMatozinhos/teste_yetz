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
     * Realiza o logout do usuário.
     */
    public function logout(Request $request)
    {
        $result = $this->logoutService->logoutUser($request->user());

        if ($result['status'] === 'error') {
            return redirect()->back()->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('login')->with('success', 'Você saiu com sucesso.');
    }
}
