<?php

namespace App\Models;

class Role extends \Spatie\Permission\Models\Role
{
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
