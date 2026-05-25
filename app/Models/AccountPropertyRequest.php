<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AccountPropertyRequest extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid',
        'tracking_code',
        'user_id',
        'citizen_id',
        'owner_id',
        'assembly_code',
        'status',
        'source',
        'parcel_name',
        'display_name',
        'address_line',
        'continent',
        'country',
        'country_code',
        'administrative_area',
        'city',
        'district',
        'postal_code',
        'latitude',
        'longitude',
        'osm_type',
        'osm_id',
        'osm_place_id',
        'osm_class',
        'osm_category',
        'parcel_geojson',
        'notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'parcel_geojson' => 'array',
        'reviewed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (AccountPropertyRequest $request) {
            if (empty($request->uuid)) {
                $request->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function citizen()
    {
        return $this->belongsTo(Citizen::class);
    }

    public function assembly()
    {
        return $this->belongsTo(Assembly::class, 'assembly_code', 'assembly_code');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
