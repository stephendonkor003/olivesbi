<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'olives_card_number')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('olives_card_number')->nullable()->unique()->after('erms_id');
            });
        }

        DB::table('users')
            ->whereNull('olives_card_number')
            ->orderBy('id')
            ->select(['id', 'access_level'])
            ->chunkById(200, function ($users) {
                foreach ($users as $user) {
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update([
                            'olives_card_number' => $this->cardNumberForExistingUser($user->id, $user->access_level),
                        ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'olives_card_number')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['olives_card_number']);
                $table->dropColumn('olives_card_number');
            });
        }
    }

    private function cardNumberForExistingUser(int|string $id, ?string $accessLevel): string
    {
        $tierCode = match (true) {
            str_contains(strtolower((string) $accessLevel), 'subscriber') => '01',
            str_contains(strtolower((string) $accessLevel), 'pro') ||
                str_contains(strtolower((string) $accessLevel), 'assembly') ||
                str_contains(strtolower((string) $accessLevel), 'municip') ||
                str_contains(strtolower((string) $accessLevel), 'district') => '03',
            str_contains(strtolower((string) $accessLevel), 'sky') ||
                str_contains(strtolower((string) $accessLevel), 'national') ||
                str_contains(strtolower((string) $accessLevel), 'super') => '04',
            str_contains(strtolower((string) $accessLevel), 'cloud') ||
                str_contains(strtolower((string) $accessLevel), 'regional') => '05',
            str_contains(strtolower((string) $accessLevel), 'shell') ||
                str_contains(strtolower((string) $accessLevel), 'continent') => '06',
            default => '02',
        };

        return '4761'.$tierCode.str_pad((string) $id, 10, '0', STR_PAD_LEFT);
    }
};
