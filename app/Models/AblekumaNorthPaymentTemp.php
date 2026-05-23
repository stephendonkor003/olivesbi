<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AblekumaNorthPaymentTemp extends Model
{
    use HasFactory;

    // The table associated with the model.
    protected $table = 'ablekuma_north_payment_temp';

    // The attributes that are mass assignable.
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
    ];
}
