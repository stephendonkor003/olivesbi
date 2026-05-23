<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'bills_id',
        'property_id',
        'business_id',
        'assembly_code',
        'bills_year',
        'arrears',
        'amount',
        'discount_amount',
        'billing_date',
        'issue_bill',
        'issued_by',
        'created_by',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function assembly()
    {
        return $this->belongsTo(Assembly::class, 'assembly_code', 'assembly_code');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'bills_id', 'bills_id');
    }

    public function successfulPayments()
    {
        return $this->hasMany(Payment::class, 'bills_id', 'bills_id')
            ->where('transaction_status', 'Success');
    }

    public function getCurrentAmountAttribute()
    {
        return $this->amount;
    }

    public function getTotalBillsSumAttribute()
    {
        return Bill::where('property_id', $this->property_id)->sum('amount');
    }

    public function getTotalBillsDiscountSumAttribute()
    {
        return Bill::where('property_id', $this->property_id)->sum('discount_amount');
    }

    public function getTotalPaymentsSumAttribute()
    {
        return $this->payments()
            ->where('transaction_status', 'Success')
            ->sum('amount');
    }

    public function getLastPaymentAttribute()
    {
        return $this->payments()
            ->where('transaction_status', 'Success')
            ->orderBy('created_at', 'desc')
            ->value('amount');
    }

    public function getTotalArrearsAttribute()
    {
        return $this->total_bills_sum - $this->current_amount;
    }
}
