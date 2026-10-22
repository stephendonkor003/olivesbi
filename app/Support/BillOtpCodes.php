<?php

namespace App\Support;

use App\Actions\SMS\SendSensitiveSMS;
use App\Models\Bill;
use App\Models\OTP;
use App\Models\Owner;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class BillOtpCodes
{
    public const TYPE = 'bill';

    public const LIFETIME_MINUTES = 10;

    public static function create(User $actor, string $billId): OTP
    {
        return DB::transaction(function () use ($actor, $billId) {
            $bill = self::bill($actor, $billId);
            if ($bill->issue_bill === 'Yes') {
                self::invalid();
            }
            $owner = self::owner($bill);
            SendSensitiveSMS::ensureAvailable();
            do {
                $code = (string) random_int(100000, 999999);
            } while (OTP::query()->where('type', self::TYPE)->where('code', $code)
                ->where('created_at', '>=', now()->subMinutes(self::LIFETIME_MINUTES))->exists());

            OTP::query()->where('bills_id', $bill->bills_id)->where('type', self::TYPE)->delete();

            return OTP::create(['citizen_id' => $owner->erms_id, 'bills_id' => $bill->bills_id,
                'type' => self::TYPE, 'code' => $code]);
        });
    }

    public static function issue(User $actor, string $billId, string $code): void
    {
        DB::transaction(function () use ($actor, $billId, $code) {
            $bill = self::bill($actor, $billId);
            $key = 'bill-verification:'.$actor->id.':'.hash('sha256', $billId);
            if (RateLimiter::tooManyAttempts($key, 5)) {
                self::invalid();
            }
            RateLimiter::hit($key, 60);
            if ($bill->issue_bill === 'Yes') {
                self::invalid();
            }
            $owner = self::owner($bill);
            $matches = OTP::query()->where('code', $code)->where('type', self::TYPE)->whereNull('user_id')
                ->where('created_at', '>=', now()->subMinutes(self::LIFETIME_MINUTES))
                ->where('created_at', '<=', now())->lockForUpdate()->get();
            if ($matches->count() !== 1) {
                self::invalid();
            }
            $otp = $matches->first();
            if ((string) $otp->bills_id !== (string) $bill->bills_id
                || (string) $otp->citizen_id !== (string) $owner->erms_id) {
                self::invalid();
            }
            $otp->delete();
            $bill->update(['issue_bill' => 'Yes', 'issued_by' => $actor->id]);
            RateLimiter::clear($key);
        });
    }

    public static function discardFailed(OTP $otp): void
    {
        if ($otp->type !== self::TYPE) {
            return;
        }
        OTP::query()->whereKey($otp->getKey())->where('type', self::TYPE)
            ->where('code', $otp->getRawOriginal('code'))->where('bills_id', $otp->getRawOriginal('bills_id'))->delete();
    }

    public static function recipient(OTP $otp): string
    {
        return DB::transaction(function () use ($otp) {
            $bill = Bill::query()->where('bills_id', $otp->bills_id)->lockForUpdate()->first();
            if (! $bill || $bill->issue_bill === 'Yes') {
                self::invalid();
            }
            $owner = self::owner($bill);
            $current = OTP::query()->whereKey($otp->getKey())->where('type', self::TYPE)
                ->where('code', $otp->getRawOriginal('code'))->whereNull('user_id')
                ->where('created_at', '>=', now()->subMinutes(self::LIFETIME_MINUTES))
                ->where('created_at', '<=', now())->lockForUpdate()->first();
            if (! $current || (string) $current->bills_id !== (string) $bill->bills_id
                || (string) $current->citizen_id !== (string) $owner->erms_id) {
                self::invalid();
            }

            return $owner->phone_no_primary;
        });
    }

    private static function bill(User $actor, string $id): Bill
    {
        abort_unless(UserAccountAccess::hasManagementPermission($actor, 'bills.update'), 403);
        $query = Bill::query()->where('bills_id', $id);
        if ($actor->access_level !== 'Super_User') {
            if (filled($actor->assembly_code)) {
                $query->where('assembly_code', $actor->assembly_code);
            }
            if (filled($actor->regional_code)) {
                $query->whereHas('assembly', fn ($assembly) => $assembly->where('regional_code', $actor->regional_code));
            }
        }

        return $query->lockForUpdate()->firstOrFail();
    }

    private static function owner(Bill $bill): Owner
    {
        if (filled($bill->property_id) === filled($bill->business_id)) {
            self::invalid();
        }
        $asset = filled($bill->property_id) ? $bill->property()->lockForUpdate()->first() : $bill->business()->lockForUpdate()->first();
        if (! $asset || (string) $asset->assembly_code !== (string) $bill->assembly_code) {
            self::invalid();
        }
        $owner = $asset->customer()->lockForUpdate()->first();
        if (! $owner || ! filled($owner->erms_id) || ! filled($owner->phone_no_primary)) {
            self::invalid();
        }

        return $owner;
    }

    private static function invalid(): never
    {
        throw ValidationException::withMessages(['code' => 'The bill verification code is invalid, expired or unavailable.']);
    }
}
