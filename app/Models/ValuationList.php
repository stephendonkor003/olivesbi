<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValuationList extends Model
{
    use HasFactory;

    protected $table = 'valuation_lists';

    protected $guarded = [
        'id',
    ];
}
