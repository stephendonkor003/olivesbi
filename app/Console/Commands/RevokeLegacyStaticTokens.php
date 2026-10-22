<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class RevokeLegacyStaticTokens extends Command
{
    protected $signature = 'security:revoke-legacy-static-tokens';

    protected $description = 'Revoke only tokens issued by the removed unauthenticated legacy USSD endpoint';

    public function handle(): int
    {
        $user = User::query()->find(1);
        // The morph relationship constrains tokenable_type and tokenable_id.
        $count = $user?->tokens()->where('name', 'Static Token')->delete() ?? 0;
        $this->info("Revoked {$count} legacy static API token(s).");

        return self::SUCCESS;
    }
}
