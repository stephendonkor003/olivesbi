<?php

namespace App\Support;

use Closure;
use InvalidArgumentException;

class PaymentAmounts
{
    /** Convert the existing GHS amount to pesewas without floating point arithmetic. */
    public static function minor(mixed $amount): int
    {
        if (! is_string($amount) && ! is_int($amount) && ! is_float($amount)) {
            throw new InvalidArgumentException('Invalid payment amount');
        }
        $value = (string) $amount;
        if (! preg_match('/\A(0|[1-9][0-9]*)(?:\.([0-9]+))?\z/D', $value, $parts)) {
            throw new InvalidArgumentException('Invalid payment amount');
        }
        $fraction = $parts[2] ?? '';
        // Existing DECIMAL columns may return padded zeros beyond currency scale.
        if (strlen($fraction) > 2 && trim(substr($fraction, 2), '0') !== '') {
            throw new InvalidArgumentException('Invalid payment amount');
        }
        $digits = ltrim($parts[1].str_pad(substr($fraction, 0, 2), 2, '0'), '0');
        $limit = (string) PHP_INT_MAX;
        if ($digits === '' || strlen($digits) > strlen($limit)
            || (strlen($digits) === strlen($limit) && strcmp($digits, $limit) > 0)) {
            throw new InvalidArgumentException('Invalid payment amount');
        }

        return (int) $digits;
    }

    public static function decimal(mixed $amount): string
    {
        $minor = self::minor($amount);

        return intdiv($minor, 100).'.'.str_pad((string) ($minor % 100), 2, '0', STR_PAD_LEFT);
    }

    public static function rule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            try {
                if (! is_scalar($value) || ! preg_match('/\A(0|[1-9][0-9]*)(?:\.[0-9]{1,2})?\z/D', (string) $value)) {
                    throw new InvalidArgumentException('Invalid payment amount');
                }
                self::minor($value);
            } catch (InvalidArgumentException) {
                $fail('The amount must be positive with at most two decimal places.');
            }
        };
    }
}
