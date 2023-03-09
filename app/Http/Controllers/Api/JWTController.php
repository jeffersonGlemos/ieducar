<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class JWTController extends Controller
{
    public function login()
    {
        $credentials = request(['login', 'password']);

        if (! $token = auth('jwt')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('jwt')->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60
        ]);
    }
}
