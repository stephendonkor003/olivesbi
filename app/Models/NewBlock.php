<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewBlock extends Model
{
    use HasFactory;

    protected $table = 'new_blocks';

    protected $guarded = [
        'id',
    ];

    public function assembly()
    {
        return $this->belongsTo(Assembly::class, 'assembly_code', 'assembly_code');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }
}
