<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProIntegration extends Model
{
    use HasFactory, HasUuid;

    public const PAYMENT_PROVIDERS = [
        'paystack' => 'Paystack',
        'flutterwave' => 'Flutterwave',
        'stripe' => 'Stripe',
        'hubtel' => 'Hubtel',
        'expresspay' => 'expressPay',
        'ghipss' => 'GhIPSS / GhQR',
    ];

    public const SMS_PROVIDERS = [
        'hubtel' => 'Hubtel SMS',
        'mnotify' => 'MNotify',
        'twilio' => 'Twilio',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'credentials' => 'encrypted:array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    public function proUser()
    {
        return $this->belongsTo(User::class, 'pro_user_uuid', 'uuid');
    }

    public function assembly()
    {
        return $this->belongsTo(Assembly::class, 'assembly_code', 'assembly_code');
    }

    public function paymentRequests()
    {
        return $this->hasMany(ProPaymentRequest::class, 'integration_uuid', 'uuid');
    }

    public function scopePayment($query)
    {
        return $query->where('category', 'payment');
    }

    public function scopeSms($query)
    {
        return $query->where('category', 'sms');
    }

    public function scopeDefault($query)
    {
        return $query->where('is_active', true)->where('is_default', true);
    }

    public function credential(string $key, mixed $default = null): mixed
    {
        return data_get($this->credentials ?: [], $key, $default);
    }

    public function providerLabel(): string
    {
        return self::PAYMENT_PROVIDERS[$this->provider]
            ?? self::SMS_PROVIDERS[$this->provider]
            ?? ucfirst((string) $this->provider);
    }
}
