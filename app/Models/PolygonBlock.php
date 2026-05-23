<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolygonBlock extends Model
{
    use HasFactory;

    protected $table = 'polygon_blocks';

    protected $fillable = ['name', 'block_number', 'boundary'];

    protected $casts = [
        'boundary' => 'string',
    ];
}
