<?php

namespace App\Support;

use App\Models\Bill;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymentRecordAccess
{
    public static function scopedStaff(?User $actor, Bill $bill): bool
    {
        if (! UserAccountAccess::hasManagementPermission($actor, 'payments.create')) {
            return false;
        }
        if ($actor->access_level === 'Super_User') {
            return true;
        }
        if (filled($actor->assembly_code) && (string) $actor->assembly_code !== (string) $bill->assembly_code) {
            return false;
        }

        return ! filled($actor->regional_code)
            || $bill->assembly()->where('regional_code', $actor->regional_code)->exists();
    }

    public static function ownBill(?User $actor, Bill $bill): bool
    {
        if (! $actor || $actor->status !== 'Active' || filled($bill->property_id) === filled($bill->business_id)) {
            return false;
        }
        $asset = filled($bill->property_id) ? $bill->property : $bill->business;

        return $asset && (string) $asset->assembly_code === (string) $bill->assembly_code
            && $asset->customer && OwnerRecordAccess::isSelf($actor, $asset->customer);
    }

    public static function creationBill(Request $request, bool $lock = false): Bill
    {
        $query = Bill::where('bills_id', $request->input('bills_id'));
        if ($lock) {
            $query->lockForUpdate();
        }
        $bills = $query->limit(2)->get();
        abort_unless($bills->count() === 1, 422, 'Bill details not found');
        $bill = $bills->first();
        abort_unless(filled($bill->assembly_code) && $bill->assembly()->exists(), 422, 'Bill assembly is unavailable');
        abort_unless(self::scopedStaff($request->user(), $bill)
            || ($request->input('payment_mode') === 'momo' && self::ownBill($request->user(), $bill)), 403);
        if ($request->exists('assembly_code') && (string) $request->input('assembly_code') !== (string) $bill->assembly_code) {
            throw ValidationException::withMessages(['assembly_code' => 'The assembly must match the bill.']);
        }

        return $bill;
    }

    public static function mayConfirm(?User $actor, Payment $payment): bool
    {
        $bill = $payment->bill;
        if (! $actor || $actor->status !== 'Active' || ! $bill
            || ! filled($bill->assembly_code) || (string) $payment->assembly_code !== (string) $bill->assembly_code) {
            return false;
        }

        return self::scopedStaff($actor, $bill)
            || ((string) $payment->created_by === (string) $actor->id && self::ownBill($actor, $bill));
    }
}
