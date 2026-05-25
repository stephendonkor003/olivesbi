<?php

namespace App\Support;

use App\Models\User;

class OlivesCardNumber
{
    private const ISSUER_PREFIX = '4761';

    public static function generate(?string $tier = null): string
    {
        do {
            $number = self::ISSUER_PREFIX.self::tierCode($tier).self::randomDigits(10);
        } while (User::where('olives_card_number', $number)->exists());

        return $number;
    }

    public static function normalize(?string $value): ?string
    {
        $normalized = strtoupper(preg_replace('/[^A-Z0-9]/', '', (string) $value));

        return $normalized !== '' ? $normalized : null;
    }

    public static function format(?string $value): string
    {
        $normalized = self::normalize($value);

        if ($normalized === null) {
            return 'Pending';
        }

        return trim(chunk_split($normalized, 4, ' '));
    }

    private static function tierCode(?string $tier): string
    {
        return match ($tier) {
            OlivesAccountTier::SUBSCRIBER => '01',
            OlivesAccountTier::PRO => '03',
            OlivesAccountTier::SKY => '04',
            OlivesAccountTier::CLOUD => '05',
            OlivesAccountTier::SHELL => '06',
            default => '02',
        };
    }

    private static function randomDigits(int $length): string
    {
        $digits = '';

        for ($i = 0; $i < $length; $i++) {
            $digits .= random_int(0, 9);
        }

        return $digits;
    }
}
