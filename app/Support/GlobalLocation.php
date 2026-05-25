<?php

namespace App\Support;

class GlobalLocation
{
    public const FIELDS = [
        'continent',
        'country',
        'country_code',
        'administrative_area',
        'city',
        'district',
        'address_line',
        'postal_code',
    ];

    public static function rules(bool $includeAddressLine = true): array
    {
        $rules = [
            'continent' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:120'],
            'country_code' => ['nullable', 'string', 'max:3'],
            'administrative_area' => ['nullable', 'string', 'max:160'],
            'city' => ['nullable', 'string', 'max:160'],
            'district' => ['nullable', 'string', 'max:160'],
            'postal_code' => ['nullable', 'string', 'max:50'],
        ];

        if ($includeAddressLine) {
            $rules['address_line'] = ['nullable', 'string', 'max:255'];
        }

        return $rules;
    }

    public static function from(array $data, ?string $fallbackAddress = null): array
    {
        $location = [];

        foreach (self::FIELDS as $field) {
            if (array_key_exists($field, $data)) {
                $location[$field] = blank($data[$field]) ? null : trim((string) $data[$field]);
            }
        }

        if (array_key_exists('country_code', $location) && $location['country_code']) {
            $location['country_code'] = strtoupper($location['country_code']);
        }

        if (array_key_exists('address_line', $location) && blank($location['address_line']) && $fallbackAddress) {
            $location['address_line'] = $fallbackAddress;
        }

        return $location;
    }

    public static function addressFor($record): string
    {
        return collect([
            $record->address_line ?? null,
            $record->digitaladdress ?? null,
            $record->address ?? null,
            $record->location ?? null,
            $record->streetname ?? null,
            $record->street_name ?? null,
        ])->filter()->first() ?: 'No address recorded';
    }

    public static function regionFor($record): string
    {
        return collect([
            $record->city ?? null,
            $record->district ?? null,
            $record->administrative_area ?? null,
            $record->country ?? null,
            $record->continent ?? null,
        ])->filter()->implode(', ') ?: 'Global location not recorded';
    }
}
