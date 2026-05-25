<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;

class Permission extends \Spatie\Permission\Models\Permission
{
    use HasUuid;

    public function guardName(): string
    {
        return 'web';
    }
}
