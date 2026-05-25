<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Bill extends Model
{
    use HasFactory, HasUuid;

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

    protected static function booted(): void
    {
        static::creating(function (Bill $bill) {
            if (empty($bill->uuid)) {
                $bill->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'uuid');
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id', 'uuid');
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
            ->whereRaw('LOWER(transaction_status) = ?', ['success']);
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
            ->whereRaw('LOWER(transaction_status) = ?', ['success'])
            ->sum('amount');
    }

    public function getLastPaymentAttribute()
    {
        return $this->payments()
            ->whereRaw('LOWER(transaction_status) = ?', ['success'])
            ->orderBy('created_at', 'desc')
            ->value('amount');
    }

    public function getTotalArrearsAttribute()
    {
        return $this->total_bills_sum - $this->current_amount;
    }
}
