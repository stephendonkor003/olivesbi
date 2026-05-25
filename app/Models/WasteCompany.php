<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteCompany extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = ['id'];

    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registered_by_uuid', 'uuid');
    }

    public function assembly()
    {
        return $this->belongsTo(Assembly::class, 'assembly_code', 'assembly_code');
    }

    public function plans()
    {
        return $this->hasMany(WasteServicePlan::class, 'waste_company_uuid', 'uuid');
    }

    public function activePlans()
    {
        return $this->plans()->where('active', true);
    }

    public function bookings()
    {
        return $this->hasMany(WastePickupBooking::class, 'waste_company_uuid', 'uuid');
    }
}
