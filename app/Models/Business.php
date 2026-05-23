<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

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
        return $this->belongsTo(RevenueStream::class, 'buss_rev_id');
    }

    public function businessClass()
    {
        return $this->belongsTo(BusinessClass::class, 'business_class');
    }

    public function businessType()
    {
        return $this->belongsTo(BusinessType::class, 'business_type');
    }

    public function businessOwner()
    {
        return $this->hasMany(BusinessOwner::class, 'business_owner_id', 'business_owner_id');
    }

    public function assembly()
    {
        return $this->belongsTo(Assembly::class, 'assembly_code', 'assembly_code');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function block()
    {
        return $this->belongsTo(Block::class, 'block_id');
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }

    public function propertyUse()
    {
        return $this->belongsTo(PropertyUser::class, 'property_use_id');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class, 'business_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
