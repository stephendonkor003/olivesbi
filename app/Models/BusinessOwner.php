<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessOwner extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_owner_id',
        'firstname',
        'lastname',
        'middlename',
        'gender',
        'tin_number',
        'organization_name',
        'email',
        'primary_phone',
        'secondary_phone',
        'house_number',
        'digital_address',
        'residential_address',
        'postal_address',
        'created_by',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function business()
    {
        return $this->hasMany(Business::class, 'business_owner_id', 'business_owner_id');
    }

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function (self $routine) {
    //         $routine->business_owner_id =  $routine->generateBusinessOwnerId();
    //     });
    // }

    // public function generateBusinessOwnerId()
    // {
    //     $business_owner_id = rand(10000000, 99999999);

    //     while (self::where('business_owner_id', $business_owner_id)->exists()) {
    //         $this->generateBusinessOwnerId();
    //     }

    //     return $business_owner_id;
    // }
}
