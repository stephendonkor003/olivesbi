<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessClassType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'rate',
        'created_by',
        'identifier',
        'extra_class_identifier',
        'classcode',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
