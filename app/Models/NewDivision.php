<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewDivision extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'new_divisions';

    protected $guarded = [
        'id',
    ];

    public function assembly()
    {
        return $this->belongsTo(Assembly::class, 'assembly_code', 'assembly_code');
    }

    public function blocks()
    {
        return $this->hasMany(Block::class, 'division_code', 'division_code');
    }
}
