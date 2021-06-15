<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Jobs\SendForgotPasswordEmailJob;
use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ForgotPasswordController extends Controller
{

    public function forgotPassword(ForgotPasswordRequest $request) {
        $data = $request->validated();

        if(!User::whereEmail($data['email'])->exists()) {
            return true;
        }

        $code = rand(111111, 999999);

        SendForgotPasswordEmailJob::dispatch($data['email'], $code);

        $code = \Hash::make($code);

        PasswordReset::create([
            'email' => $data['email'],
            'token' => $code,
            'created_at' => Carbon::now()
        ]);


        return true;
    }

    public function resetPassword(ResetPasswordRequest $request) {
        $data = $request->validated();

        $user = User::whereEmail($data['email'])->first();

        $passwordReset = PasswordReset::where('email', $data['email'])->latest()->first();

        if(!\Hash::check($data['code'], $passwordReset->token)) {
            return response()->json(['message' => 'The given data is invalid', 'errors' => ['code' => 'Código inválido']])->setStatusCode(422);
        }

        if($passwordReset->created_at->addMinutes(30)->isPast()) {
            return response()->json(['message' => 'The given data is invalid', 'errors' => ['code' => 'Código expirado. Por favor, requisite um novo código.']])->setStatusCode(422);

        }

        $user->update([
            'password' => $data['password']
        ]);

        $token = $user->createToken('API Grant')->accessToken;

        return response()->json(['token' => $token, 'user' => new \App\Http\Resources\User($user)]);
    }


}
