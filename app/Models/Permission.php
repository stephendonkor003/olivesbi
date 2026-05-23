<?php

namespace App\Models;

class Permission extends \Spatie\Permission\Models\Permission
{
    public function guardName(): string
    {
        return 'web';
    }
}
