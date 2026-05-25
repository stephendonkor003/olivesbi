<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    private array $tables = [
        'properties',
        'businesses',
        'bills',
        'payments',
        'account_property_requests',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (! Schema::hasTable($tableName) || Schema::hasColumn($tableName, 'uuid')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->after('id')->unique();
            });

            DB::table($tableName)
                ->whereNull('uuid')
                ->orderBy('id')
                ->select('id')
                ->chunk(100, function ($rows) use ($tableName) {
                    foreach ($rows as $row) {
                        DB::table($tableName)
                            ->where('id', $row->id)
                            ->update(['uuid' => (string) Str::uuid()]);
                    }
                });
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->tables) as $tableName) {
            if (! Schema::hasTable($tableName) || ! Schema::hasColumn($tableName, 'uuid')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropUnique($table->getTable().'_uuid_unique');
                $table->dropColumn('uuid');
            });
        }
    }
};
