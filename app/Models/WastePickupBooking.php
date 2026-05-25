<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WastePickupBooking extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = ['id'];

    protected $casts = [
        'scheduled_date' => 'date',
        'next_pickup_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'estimated_waste_kg' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_uuid', 'uuid');
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_uuid', 'uuid');
    }

    public function company()
    {
        return $this->belongsTo(WasteCompany::class, 'waste_company_uuid', 'uuid');
    }

    public function plan()
    {
        return $this->belongsTo(WasteServicePlan::class, 'waste_service_plan_uuid', 'uuid');
    }

    public function pickups()
    {
        return $this->hasMany(WastePickup::class, 'waste_pickup_booking_uuid', 'uuid');
    }

    public function providerChanges()
    {
        return $this->hasMany(WasteProviderChange::class, 'waste_pickup_booking_uuid', 'uuid');
    }
}
