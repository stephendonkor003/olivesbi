<?php

namespace App\Support;

use App\Models\User;

class OlivesAccountTier
{
    public const SUBSCRIBER = 'subscriber';
    public const ACCOUNT = 'account';
    public const PRO = 'pro';
    public const SKY = 'sky';
    public const CLOUD = 'cloud';
    public const SHELL = 'shell';

    public static function fromUser(User $user): string
    {
        $roles = $user->roles->pluck('name')->map(fn ($role) => strtolower((string) $role))->all();

        return self::fromAccessLevel($user->access_level, $roles);
    }

    public static function fromAccessLevel(?string $accessLevel, array $roles = []): string
    {
        $level = strtolower((string) $accessLevel);
        $haystack = trim($level.' '.implode(' ', $roles));

        return match (true) {
            str_contains($haystack, 'shell') ||
                str_contains($haystack, 'continent') ||
                str_contains($haystack, 'african union') ||
                str_contains($haystack, 'eu') => self::SHELL,

            str_contains($haystack, 'cloud') ||
                str_contains($haystack, 'regional') ||
                str_contains($haystack, 'ecowas') => self::CLOUD,

            str_contains($haystack, 'sky') ||
                str_contains($haystack, 'national') ||
                str_contains($haystack, 'country') ||
                str_contains($haystack, 'gog') ||
                str_contains($haystack, 'super_user') ||
                str_contains($haystack, 'super user') => self::SKY,

            str_contains($haystack, 'pro') ||
                str_contains($haystack, 'assembly') ||
                str_contains($haystack, 'municip') ||
                str_contains($haystack, 'district') => self::PRO,

            str_contains($haystack, 'subscriber') ||
                str_contains($haystack, 'newsletter') => self::SUBSCRIBER,

            default => self::ACCOUNT,
        };
    }

    public static function routeNameFor(User $user): string
    {
        return match (self::fromUser($user)) {
            self::SUBSCRIBER => 'dashboard.subscriber',
            self::PRO => 'dashboard.olives-pro',
            self::SKY => 'dashboard.olives-sky',
            self::CLOUD => 'dashboard.olives-cloud',
            self::SHELL => 'dashboard.olives-shell',
            default => 'dashboard.olives-account',
        };
    }
}
