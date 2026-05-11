<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\UserTypeEnum;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class AuthController
{
    public function register(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'username' => ['required', 'string', 'max:255', 'unique:users'],
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
            ]);

            $user = User::query()->create([
                'profile_image' => 'https://api.dicebear.com/9.x/thumbs/svg?seed='.$data['username'],
                'type' => UserTypeEnum::USER,
                ...$data,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
            ], 201);
        } catch (ValidationException $e) {
            throw $e;
        } catch (QueryException) {
            abort(500, 'Error de base de datos al registrar el usuario.');
        } catch (Exception) {
            abort(500, 'Error: no se ha podido registrar el usuario.');
        }
    }

    public function login(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'username' => ['required', 'string'],
                'password' => ['required', 'string'],
            ]);

            $user = User::query()->where('username', $data['username'])->first();

            if (! $user || ! Hash::check($data['password'], $user->password)) {
                throw ValidationException::withMessages([
                    'credentials' => 'Error: las credenciales no coinciden.',
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (QueryException) {
            abort(500, 'Error de base de datos al iniciar sesión.');
        } catch (Exception) {
            abort(500, 'Error: no se ha podido iniciar sesión.');
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Sesión cerrada correctamente.',
            ]);
        } catch (QueryException) {
            abort(500, 'Error de base de datos al cerrar la sesión.');
        } catch (Exception) {
            abort(500, 'Error: no se ha podido cerrar la sesión.');
        }
    }
}
