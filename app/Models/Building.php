<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'block_id', 'boundary'];

    protected $casts = [
        'boundary' => 'string',
    ];

    public function block()
    {
        return $this->belongsTo(PolygonBlock::class, 'block_id');
    }
}
