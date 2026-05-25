<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = [
        'id',
    ];

    protected $table = 'ablekuma_north_invoice_payments';

    public function invoice()
    {
        return $this->belongsTo(PaymentTemp::class, 'bills_id', 'bills_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
