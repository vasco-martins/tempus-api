<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Request;

class AuthController extends Controller
{

    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();

        if (!$user || !\Hash::check($data['password'], $user->password)) {
            return response()->json(['error' => __('Wrong email or password')], Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken('API Grant')->accessToken;

        return response()->json(['token' => $token, 'user' => new \App\Http\Resources\User($user)]);
    }

    public function validateRegister(RegisterRequest $request): bool
    {
        return true;
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create($data);
        $token = $user->createToken('API Grant')->accessToken;

        return response()->json(['token' => $token]);
    }

    public function logout(Request $request)
    {
        $token = auth()->user()->token();
        $token->revoke();

        return response('true', 200);

    }

    public function currentUser()
    {
        return new \App\Http\Resources\User(auth()->user());
    }
}
