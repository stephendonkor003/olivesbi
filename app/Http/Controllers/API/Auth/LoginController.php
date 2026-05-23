<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Auth\ChangePasswordRequest as AuthChangePasswordRequest;
use App\Http\Requests\API\Auth\LoginRequest;
use App\Http\Requests\API\ChangePassword\ChangePasswordRequest;
use App\Http\Requests\API\ChangePassword\SendOTPRequest;
use App\Jobs\OTP\SendPasswordChangeOTPSMS;
use App\Models\OTP;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

            $user = User::with(['customer'])
                ->where('phone', $request->validated('username'))
                ->orWhere('email', $request->validated('username'))
                ->first();

            session()->regenerate();

            $user['must_change_password'] = $user->change_password;

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

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    public function sendOTP(SendOTPRequest $request)
    {
        $user = User::where('phone', $request->input('phone'))->first();

        if (empty($user)) {
            return response()->json([
                'message' => 'User with this phone not found',
            ], 422);
        }

        dispatch(new SendPasswordChangeOTPSMS($user));

        return response()->json([
            'message' => 'Password change OTP code sent successfully',
        ]);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $otp = OTP::where('code', $data['code'])->first();

        if ($otp) {
            $user = User::where('id', $otp->citizen_id)->first();
            $user->update($data);
        }

        return response()->json([
            'message' => 'Password changed successfully',
        ]);
    }

    public function changePasswordNoOTP(AuthChangePasswordRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = $request->user();

        $user->update($data);

        return response()->json([
            'message' => 'Password changed successfully',
        ]);
    }
}
