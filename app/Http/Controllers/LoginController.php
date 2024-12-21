<?php

namespace App\Http\Controllers;

use App\Models\Guild;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $tokenResult = $user->createToken('MyApp');
        $token = $tokenResult->accessToken;

        Auth::guard('web')->login($user);

        session(['api_token' => $token]);

        return redirect('/')->with('success', 'Login realizado com sucesso.');
    }
}
