<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'blocks';

    protected $fillable = [
        'code',
        'name',
        'assembly_code',
        'division_id',
        'erms_id',
        'created_by',
        'created_on',
        'updated_on',
        'geofence',
    ];

    protected $casts = [
        'geofence' => 'array',
    ];

    // Define the relationship with the Assembly model
    public function assembly()
    {
        return $this->belongsTo(Assembly::class, 'assembly_code', 'assembly_code');
    }

    // Define the relationship with the Division model
    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }
}
