<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessType extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'name',
        'parent_category',
        'sub_categories',
        'rate',
        'created_by',
    ];

    protected $casts = [
        'sub_categories' => 'array',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
