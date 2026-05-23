<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'min_amt_2',
        'bill_serial',
        'bill_no',
        'bill_date',
        'due_date',
        'arrears',
        'current_amount',
        'amount_due',
        'account_no',
    ];
}
