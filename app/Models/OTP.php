<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OTP extends Model
{
    use HasFactory;

    protected $fillable = ['citizen_id', 'bills_id', 'code', 'type', 'user_id'];

    protected $table = 'otps';

    public function citizen()
    {
        return $this->belongsTo(Citizen::class, 'citizen_id');
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bills_id', 'bills_id');
    }
}
