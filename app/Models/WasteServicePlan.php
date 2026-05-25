<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteServicePlan extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = ['id'];

    protected $casts = [
        'active' => 'boolean',
        'service_charge' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(WasteCompany::class, 'waste_company_uuid', 'uuid');
    }

    public function bookings()
    {
        return $this->hasMany(WastePickupBooking::class, 'waste_service_plan_uuid', 'uuid');
    }
}
