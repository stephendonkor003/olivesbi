<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyUser extends Model
{
    use HasFactory;

    protected $fillable = ['zone_id', 'assembly_code', 'name', 'created_by'];

    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
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
