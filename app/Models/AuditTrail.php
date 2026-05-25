<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'user_id',
        'action_performed',
        'action_date',
        'ip_address',
        'device_used',
        'remarks',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
