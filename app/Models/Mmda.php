<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mmda extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'mmdas';

    protected $fillable = ['region_id', 'region_code', 'assembly_code', 'assembly_name', 'assembly_id', 'assembly_category', 'created_by'];

    public function region()
    {
        return $this->belongsTo(GhanaRegion::class, 'region_id');
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
