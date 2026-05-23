<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyRevenue extends Model
{
    use HasFactory;

    protected $table = 'property_class_revenues';

    protected $guarded = [
        'id',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
