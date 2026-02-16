<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    private const TOKEN_NAME = 'auth_token';

    public function register(Request $request): JsonResponse
    {
        try {
            $validator = $this->validateRegistration($request);

            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', $validator->errors(), 422);
            }

            $user = $this->createUser($request);
            $token = $user->createToken(self::TOKEN_NAME)->plainTextToken;

            return $this->successResponse('Registration successful', [
                'user' => $user,
                'token' => $token
            ], 201);

        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage(), [
                'email' => $request->email,
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse('Registration failed', null, 500);
        }
    }

    public function login(Request $request): JsonResponse
    {
        try {
            $validator = $this->validateLogin($request);

            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', $validator->errors(), 422);
            }

            $user = User::where('email', $request->email)->first();

            if (!$this->validateCredentials($user, $request->password)) {
                return $this->errorResponse('Invalid credentials', [
                    'email' => ['The provided credentials are incorrect.']
                ], 422);
            }

            if (!$user->is_active) {
                return $this->errorResponse(
                    'Account is inactive. Please contact administrator.',
                    null,
                    403
                );
            }

            $token = $user->createToken(self::TOKEN_NAME)->plainTextToken;

            return $this->successResponse('Login successful', [
                'user' => $user,
                'token' => $token
            ]);

        } catch (\Exception $e) {
            Log::error('Login failed: ' . $e->getMessage(), [
                'email' => $request->email ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse('Login failed', null, 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return $this->successResponse('Logout successful');

        } catch (\Exception $e) {
            Log::error('Logout failed: ' . $e->getMessage(), [
                'user_id' => $request->user()->id ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse('Logout failed', null, 500);
        }
    }

    public function me(Request $request): JsonResponse
    {
        try {
            return $this->successResponse('User data retrieved successfully', $request->user());

        } catch (\Exception $e) {
            Log::error('Failed to get user data: ' . $e->getMessage(), [
                'user_id' => $request->user()->id ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse('Failed to get user data', null, 500);
        }
    }

    private function validateRegistration(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:owner,admin,kasir',
        ]);
    }

    private function validateLogin(Request $request)
    {
        return Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
    }

    private function createUser(Request $request): User
    {
        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => true,
        ]);
    }

    private function validateCredentials(?User $user, string $password): bool
    {
        return $user && Hash::check($password, $user->password);
    }

    private function successResponse(string $message, $data = null, int $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    private function errorResponse(string $message, $errors = null, int $code = 500): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }
}