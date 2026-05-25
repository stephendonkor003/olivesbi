<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Assembly extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'assemblies';

    protected $fillable = [
        'name',
        'assembly_code',
        'regional_code',
        'supervisor',
        'geo_coordinate',
        'logo',
        'invoice_layout',
        'status',
        'address',
        'phone',
        'assembly_category',
        'currency_code',
        'currency_symbol',
        'currency_name',
        'bank_name',
        'bank_number',
        'bank_branch',
    ];

    public function region()
    {
        return $this->belongsTo(GhanaRegion::class, 'regional_code', 'regional_code');
    }

    public function assemblySupervisor()
    {
        return $this->belongsTo(User::class, 'supervisor');
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'assembly_code', 'assembly_code');
    }

    public function businesses()
    {
        return $this->hasMany(Business::class, 'assembly_code', 'assembly_code');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class, 'assembly_code', 'assembly_code');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'assembly_code', 'assembly_code');
    }

    public static function generateImageFileName()
    {
        $fileName = Str::random();
        while (self::where('logo', $fileName)->first()) {
            self::generateImageFileName();
        }

        return $fileName;
    }
}
