<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if(!$user) {
            throw ValidationException::withMessages(
                [
                    'email' => ['The provided credentials are incorrect.']
                ]
            );
        }

        if(!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(
                [
                    'email' => ['The provided credentials are incorrect.']
                ]
            );
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
        ]);


//        $credentials = $request->only('email', 'password');
//
//        if (!auth()->attempt($credentials)) {
//            return response()->json(['message' => 'Invalid credentials'], 401);
//        }
//
//        $user = auth()->user();
//        $token = $user->createToken('api-token')->plainTextToken;
//
//        return response()->json([
//            'access_token' => $token,
//            'token_type' => 'Bearer',
//        ], 200);
    }

    //logout
    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
