<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'name',
        'phone',
        'audience_type',
        'topics',
        'source',
        'subscribed_at',
    ];

    protected $casts = [
        'topics' => 'array',
        'subscribed_at' => 'datetime',
    ];
}
