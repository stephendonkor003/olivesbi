<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValuationList extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'valuation_lists';

    protected $guarded = [
        'id',
    ];
}
