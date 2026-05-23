<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function index(LoginRequest $request)
    {
        try {
            $result = filter_var($request->validated('username'), FILTER_VALIDATE_EMAIL);

            if (empty($result)) {
                if (! auth()->attempt([
                    'phone' => $request->validated('username'),
                    'password' => $request->validated('password'),
                    'status' => 'Active',
                ])) {
                    throw ValidationException::withMessages([
                        'username' => 'Your provided credentials could not be verified.',
                    ]);
                }
            }

            if (! empty($result)) {
                if (! auth()->attempt([
                    'email' => $request->validated('username'),
                    'password' => $request->validated('password'),
                    'status' => 'Active',
                ])) {
                    throw ValidationException::withMessages([
                        'username' => 'Your provided credentials could not be verified.',
                    ]);
                }
            }

            $user = User::where('phone', $request->validated('username'))
                ->orWhere('email', $request->validated('username'))
                ->first();

            session()->regenerate();

            return response()->json([
                'message' => 'User logged in successfully',
                'data' => $user,
                'meta' => [
                    'token' => explode('|', $user->createToken('auth_token')->plainTextToken, 2)[1],
                ],
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'message' => 'Unable to login '.$ex->getMessage(),
            ], 422);
        }
    }
}
