<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillAdjustment extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'erms_id' => 'array',
    ];

    public function revenueStreamType()
    {
        return $this->belongsTo(RevenueStreamType::class, 'revenue_stream_type_id');
    }

    public function assembly()
    {
        return $this->belongsTo(Assembly::class, 'assembly_code', 'assembly_code');
    }

    public function block()
    {
        return $this->belongsTo(Block::class, 'block_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
