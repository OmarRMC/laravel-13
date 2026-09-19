<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * `POST /api/v1/login`
     *
     * No usa Auth::attempt(): ese metodo escribe en la sesion del guard 'web', y las rutas
     * de la API no tienen sesion iniciada (no llevan el middleware `web`). Se valida la
     * contraseña a mano y se emite un token Sanctum, sin tocar la sesion para nada.
     */
    public function login(Request $request): JsonResponse
    {
        $credenciales = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credenciales['email'])->first();

        if (! $user || ! Hash::check($credenciales['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        return response()->json([
            'token' => $user->createToken('api')->plainTextToken,
            'user'  => new UserResource($user),
        ]);
    }

    /** `POST /api/v1/logout` */
    public function logout(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}
