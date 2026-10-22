<?php

namespace App\Support;

use App\Models\OTP;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PasswordResetCodes
{
    public const LIFETIME_MINUTES = 10;

    public static function reset(string $code, string $password): void
    {
        DB::transaction(function () use ($code, $password) {
            $matches = OTP::query()
                ->where('code', $code)
                ->where('type', 'password')
                ->whereNotNull('user_id')
                ->where('created_at', '>=', now()->subMinutes(self::LIFETIME_MINUTES))
                ->lockForUpdate()
                ->get();

            // Existing clients send code/password only. A collision must fail
            // closed rather than selecting a different account's first row.
            if ($matches->count() !== 1) {
                self::invalid();
            }

            $otp = $matches->first();
            $user = User::query()->whereKey($otp->user_id)->lockForUpdate()->first();
            if (! $user || $user->status !== 'Active') {
                self::invalid();
            }

            $user->forceFill(['password' => Hash::make($password), 'remember_token' => Str::random(60)])->save();
            OTP::query()->where('user_id', $user->id)->where('type', 'password')->delete();
            $user->tokens()->delete();

            if (config('session.driver') === 'database') {
                DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
                    ->where('user_id', $user->id)->delete();
            }
            PasswordResetSessions::revokeFiles($user->id);
        });
    }

    private static function invalid(): never
    {
        throw ValidationException::withMessages(['code' => 'The password reset code is invalid or expired.']);
    }
}
