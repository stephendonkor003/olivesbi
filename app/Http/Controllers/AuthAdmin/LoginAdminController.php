<?php

namespace App\Http\Controllers\AuthAdmin;

use App\Actions\SMS\SendSensitiveSMS;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginAdminRequest;
use App\Http\Requests\ChangePassword\ChangePasswordRequest as ChangePasswordChangePasswordRequest;
use App\Http\Requests\ChangePassword\SendOTPRequest;
use App\Jobs\OTP\SendPasswordChangeOTPSMS;
use App\Models\CustomerType;
use App\Models\User;
use App\Support\OlivesCardNumber;
use App\Support\PasswordResetCodes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginAdminController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function LandingPage()
    {
        return view('welcome');
    }

    public function register()
    {
        $customerTypes = CustomerType::get();

        return view('auth.register', compact('customerTypes'));
    }

    public function login(LoginAdminRequest $request)
    {
        $data = $request->validated();
        $identifier = trim($data['username']);
        $normalizedCardNumber = OlivesCardNumber::normalize($identifier);

        $user = User::query()
            ->where('status', 'Active')
            ->where(function ($query) use ($identifier, $normalizedCardNumber) {
                $query->where('phone', $identifier)
                    ->orWhere('email', $identifier)
                    ->orWhere('erms_id', $identifier)
                    ->orWhere('olives_card_number', $normalizedCardNumber);
            })
            ->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'username' => 'Your provided credentials could not be verified.',
            ]);
        }

        Auth::login($user);
        session()->put('password_hash_web', $user->password);

        session()->regenerate();
        if (Hash::check(config('erms.default_password'), $user->password)) {
            return redirect()->route('auth.change');
        }

        return redirect()->route('dashboard.entry');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        return redirect()->route('auth.index');
    }

    public function change()
    {
        return view('auth.change-password');
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = $request->user();

        $user->update($data);

        return redirect()->route('auth.index')->with('status', 'Password changed successfully');
    }

    public function sendOTP()
    {
        return view('auth.change-password-otp');
    }

    public function sendUserOTP(SendOTPRequest $request)
    {
        $user = User::where('phone', $request->phone)->first();

        if (empty($user)) {
            throw ValidationException::withMessages([
                'phone' => 'Phone number does not exist for any of the account!',
            ]);
        }

        SendSensitiveSMS::ensureAvailable();
        dispatch(new SendPasswordChangeOTPSMS($user));

        return redirect()->route('auth.changePasswordFront')->with('status', 'OTP for password reset sent successfully');
    }

    public function changePasswordFront()
    {
        return view('auth.change-password-front');
    }

    public function changePasswordFrontWa(ChangePasswordChangePasswordRequest $request)
    {
        PasswordResetCodes::reset($request->validated('code'), $request->validated('password'));

        return redirect()->route('auth.index')->with('status', 'Password changed successfully');
    }
}
