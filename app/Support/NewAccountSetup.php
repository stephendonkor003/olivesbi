<?php

namespace App\Support;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class NewAccountSetup
{
    public static function passwordHash(): string
    {
        return Hash::make(Str::random(64));
    }

    public static function response(): array
    {
        return [
            'setup_required' => true,
            'setup_url' => route('auth.sendOTP'),
            'setup_instructions' => 'Open the password setup page to request a one-time code using your registered phone number.',
        ];
    }
}
