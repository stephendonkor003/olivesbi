<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerSupport extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'user_id',
        'description',
        'assembly_code',
        'status',
        'response',
        'response_by',
        'response_date',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function citizen()
    {
        return $this->belongsTo(Citizen::class, 'user_id', 'user_id');
    }

    public function assembly()
    {
        return $this->belongsTo(Assembly::class, 'assembly_code', 'assembly_code');
    }

    public function responseBy()
    {
        return $this->belongsTo(User::class, 'response_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
