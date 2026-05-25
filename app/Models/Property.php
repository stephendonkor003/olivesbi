<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Property extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = [
        'id',
    ];

    protected static function booted(): void
    {
        static::creating(function (Property $property) {
            if (empty($property->uuid)) {
                $property->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function customer()
    {
        return $this->belongsTo(Owner::class, 'owner_id', 'erms_id');
    }

    public function polygon()
    {
        return $this->belongsTo(Polygon::class, 'polygon_id');
    }

    public function revenueStream()
    {
        return $this->belongsTo(RevenueStream::class, 'prop_rev_id');
    }

    public function entityType()
    {
        return $this->belongsTo(BusinessClassType::class, 'entity_type');
    }

    public function assembly()
    {
        return $this->belongsTo(Assembly::class, 'assembly_code', 'assembly_code');
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }

    public function propertyUse()
    {
        return $this->belongsTo(PropertyUser::class, 'property_use_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function block()
    {
        return $this->belongsTo(Block::class, 'block_id');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class, 'property_id', 'uuid');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
