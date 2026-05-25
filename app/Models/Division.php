<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'divisions';

    protected $fillable = [
        'code',
        'name',
        'assembly_code',
        'erms_id',
        'created_by',
        'created_on',
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

    // Define the relationship with the Block model
    public function blocks()
    {
        return $this->hasMany(Block::class, 'division_id', 'id');
    }
}
