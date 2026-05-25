<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'bills_id',
        'payment_mode',
        'payment_source',
        'network',
        'phone',
        'transaction_status',
        'transaction_id',
        'req_transaction_id',
        'amount',
        'prompt',
        'assembly_code',
        'receipt',
        'created_by',
        'authorization_url',
    ];

    protected static function booted(): void
    {
        static::creating(function (Payment $payment) {
            if (empty($payment->uuid)) {
                $payment->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bills_id', 'bills_id');
    }

    public function assembly()
    {
        return $this->belongsTo(Assembly::class, 'assembly_code', 'assembly_code')
            ->withDefault(['name' => 'N/A']);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault(['name' => 'N/A']);
    }

    public static function generateImageFileName()
    {
        $fileName = Str::random();
        while (self::where('receipt', $fileName)->first()) {
            self::generateImageFileName();
        }

        return $fileName;
    }
}
