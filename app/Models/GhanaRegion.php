<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GhanaRegion extends Model
{
    use HasFactory;

    protected $table = 'ghana_regions';

    protected $fillable = [
        'regionalcode',
        'name',
    ];

    public function assemblies()
    {
        return $this->hasMany(Assembly::class, 'regional_code', 'regional_code');
    }
}
