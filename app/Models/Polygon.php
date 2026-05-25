<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Polygon extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'building' => 'array',
    ];

    public function block()
    {
        return $this->belongsTo(Block::class, 'block_id');
    }

    public function assembly()
    {
        return $this->belongsTo(Assembly::class, 'assembly_code', 'assembly_code');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
