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
