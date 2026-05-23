<?php

namespace App\Http\Controllers\AuthAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginAdminRequest;
use App\Http\Requests\ChangePassword\ChangePasswordRequest as ChangePasswordChangePasswordRequest;
use App\Http\Requests\ChangePassword\SendOTPRequest;
use App\Jobs\OTP\SendPasswordChangeOTPSMS;
use App\Models\CustomerType;
use App\Models\OTP;
use App\Models\User;
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

        if (stripos($data['username'], 'ERMS') === 0 && strpos($data['username'], '@') === false) {
            $data['username'] .= '@erms.com';
        }

        $result = filter_var($data['username'], FILTER_VALIDATE_EMAIL);

        if (stripos($data['username'], 'ERMS') === 0 && strpos($data['username'], '@erms.com') !== false) {
            $data['username'] = str_replace('@erms.com', '', $data['username']);
        }

        if (empty($result)) {
            if (! auth()->attempt([
                'phone' => $data['username'],
                'password' => $data['password'],
                'status' => 'Active',
            ])) {
                throw ValidationException::withMessages([
                    'username' => 'Your provided credentials could not be verified.',
                ]);
            }
        }

        if (! empty($result)) {
            if (! auth()->attempt([
                'email' => $data['username'],
                'password' => $data['password'],
                'status' => 'Active',
            ])) {
                throw ValidationException::withMessages([
                    'username' => 'Your provided credentials could not be verified.',
                ]);
            }
        }

        $user = auth()->user();

        session()->regenerate();
        if (Hash::check(config('erms.default_password'), $user->password)) {
            return redirect()->route('auth.change');
        } else {
            return redirect()->route('dashboard.operational');
        }
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

        dispatch(new SendPasswordChangeOTPSMS($user));

        return redirect()->route('auth.changePasswordFront')->with('status', 'OTP for password reset sent successfully');
    }

    public function changePasswordFront()
    {
        return view('auth.change-password-front');
    }

    public function changePasswordFrontWa(ChangePasswordChangePasswordRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $otp = OTP::where('code', $data['code'])->first();

        if ($otp) {
            $user = User::where('id', $otp->citizen_id)->first();
            $user->update($data);
            OTP::where('citizen_id', $user->id)->delete();
        }

        return redirect()->route('auth.index')->with('status', 'Password changed successfully');
    }
}
