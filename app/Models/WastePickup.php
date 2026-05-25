<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WastePickup extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = ['id'];

    protected $casts = [
        'scheduled_for' => 'datetime',
        'due_notified_at' => 'datetime',
        'missed_notified_at' => 'datetime',
        'completed_at' => 'datetime',
        'waste_amount_kg' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(WastePickupBooking::class, 'waste_pickup_booking_uuid', 'uuid');
    }

    public function company()
    {
        return $this->belongsTo(WasteCompany::class, 'waste_company_uuid', 'uuid');
    }
}
