<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        $data = $this->authService->register($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'User registered successfully',
            'data' => [
                'user' => $data,
            ]
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $data = $this->authService->login($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => $data,
        ]);
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request->user());

        return response()->json([
            'status' => true,
            'message' => 'Logout successful'
        ]);
    }
}
