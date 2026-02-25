<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Login: devuelve un token JWT.
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'El correo no tiene un formato válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'message' => 'Credenciales inválidas.',
            ], 401);
        }

        return response()->json([
            'message' => 'Login exitoso.',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth('api')->user(),
        ]);
    }

    /**
     * Cerrar sesión e invalidar el token.
     */
    public function logout(Request $request): JsonResponse
    {
        auth('api')->logout();

        return response()->json([
            'message' => 'Logout exitoso.',
        ]);
    }

    /**
     * Devuelve el usuario autenticado por JWT.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => auth('api')->user(),
        ]);
    }

    /**
     * Refrescar el token (opcional).
     */
    public function refresh(): JsonResponse
    {
        $token = auth('api')->refresh();

        return response()->json([
            'message' => 'Token renovado.',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }
}
