<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteProviderChange extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = ['id'];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(WastePickupBooking::class, 'waste_pickup_booking_uuid', 'uuid');
    }

    public function fromCompany()
    {
        return $this->belongsTo(WasteCompany::class, 'from_company_uuid', 'uuid');
    }

    public function toCompany()
    {
        return $this->belongsTo(WasteCompany::class, 'to_company_uuid', 'uuid');
    }

    public function fromPlan()
    {
        return $this->belongsTo(WasteServicePlan::class, 'from_plan_uuid', 'uuid');
    }

    public function toPlan()
    {
        return $this->belongsTo(WasteServicePlan::class, 'to_plan_uuid', 'uuid');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_uuid', 'uuid');
    }
}
