<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;

class Role extends \Spatie\Permission\Models\Role
{
    use HasUuid;

    protected $casts = [
        'active' => 'boolean',
    ];

    public function guardName(): string
    {
        return 'web';
    }

    /**
     * Scope to get only active roles
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
