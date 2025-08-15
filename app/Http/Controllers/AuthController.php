<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validatedData = $request->validated();

        try{
            $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully!',
            'data' => $user->only(['id', 'name', 'email']),
        ], 201);
        } catch (\Exception $e) {
            return response()->json([

                'error' => 'Registration failed'], 500);
        }
    }
    public function login(LoginRequest $request)
    {
         $credentials = $request->validated();

        try {
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = JWTAuth::fromUser($user);
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful',
                    'data' => [
                        'user' => $user,
                        'token' => $token
                    ]
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()], 401);
        }
    }

    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to log out'], 500);
        }
    }

    public function refresh()
    {
        try {
            $token = JWTAuth::refresh(JWTAuth::getToken());
            return response()->json([
                'success' => true,
                'token' => $token
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to refresh token'
            ], 500);
        }
    }
}
