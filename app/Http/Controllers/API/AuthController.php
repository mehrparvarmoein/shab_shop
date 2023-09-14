<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(private UserService $userService)
    {
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->userService->addUser($request);

        return response()->json([
            'message' => 'User created successfully',
            'authorization' => [
                'token' => $user->createToken('ApiToken')->plainTextToken,
                'type'  => 'bearer',
            ],
            'user'    => $user,
        ]);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('username', 'password');
        if (Auth::attempt($credentials)) {
            $user = auth::user();

            return response()->json([
                'authorization' => [
                    'token' => $user->createToken('ApiToken')->plainTextToken,
                    'type'  => 'bearer',
                ],
                'user' => $user,
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }
}
