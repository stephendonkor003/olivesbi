<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTemp extends Model
{
    use HasFactory;

    protected $table = 'ablekuma_north_payment_temp';

    protected $fillable = [
        'SN',
        'Account',
        'Address',
        'OwnerName',
        'Suburb',
        'RateableV',
        'Zone',
        'Use_',
        'Rate',
        'CurrentRate',
        'BasicRate',
        'Arrears',
        'Balance',
        'amount_paid',
        'paid_by',
        'payment_method',
        'transaction_id',
        'reason',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $routine) {
            $routine->bills_id = $routine->generateBillsId();
        });
    }

    public function generateBillsId()
    {
        $bills_id = rand(10000000, 99999999);

        while (self::where('bills_id', $bills_id)->exists()) {
            $this->generateBillsId();
        }

        return $bills_id;
    }
}
