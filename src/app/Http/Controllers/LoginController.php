<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function Login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'username' => 'required',
                'password' => 'required',
            ],
            [
                'username.required' => 'Username tidak boleh kosong',
                'password.required' => 'Password tidak boleh kosong',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt([
            'username' => $request->username,
            'password' => $request->password
        ])) {
            return response()->json(['message' => 'Username atau password salah'], 401);
        }

        $user = User::where('username', $request->username)->first();
        $token = $user->createToken($user->username)->plainTextToken;

        return response()->json(['acces_token' => $token, 'token_type' => 'Bearer'], 200);
    }

    public function Logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Berhasil Logout'], 200);
    }
}
