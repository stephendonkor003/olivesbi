<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $globalColumns = [
        'continent' => ['type' => 'string', 'length' => 100],
        'country' => ['type' => 'string', 'length' => 120],
        'country_code' => ['type' => 'string', 'length' => 3],
        'administrative_area' => ['type' => 'string', 'length' => 160],
        'city' => ['type' => 'string', 'length' => 160],
        'district' => ['type' => 'string', 'length' => 160],
        'address_line' => ['type' => 'string', 'length' => 255],
        'postal_code' => ['type' => 'string', 'length' => 50],
    ];

    private array $mapRequestColumns = [
        'continent' => ['type' => 'string', 'length' => 100],
        'country' => ['type' => 'string', 'length' => 120],
        'country_code' => ['type' => 'string', 'length' => 3],
        'administrative_area' => ['type' => 'string', 'length' => 160],
        'city' => ['type' => 'string', 'length' => 160],
        'district' => ['type' => 'string', 'length' => 160],
        'postal_code' => ['type' => 'string', 'length' => 50],
    ];

    public function up(): void
    {
        $this->addColumns('properties', $this->globalColumns);
        $this->addColumns('businesses', $this->globalColumns);
        $this->addColumns('account_property_requests', $this->mapRequestColumns);

        $this->backfillExistingRecords();
    }

    public function down(): void
    {
        $this->dropColumns('account_property_requests', array_keys($this->mapRequestColumns));
        $this->dropColumns('businesses', array_keys($this->globalColumns));
        $this->dropColumns('properties', array_keys($this->globalColumns));
    }

    private function addColumns(string $table, array $columns): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        Schema::table($table, function (Blueprint $tableBlueprint) use ($table, $columns) {
            foreach ($columns as $column => $definition) {
                if (! Schema::hasColumn($table, $column)) {
                    $tableBlueprint->string($column, $definition['length'])->nullable();
                }
            }
        });
    }

    private function dropColumns(string $table, array $columns): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $existingColumns = array_values(array_filter(
            $columns,
            fn (string $column) => Schema::hasColumn($table, $column)
        ));

        if ($existingColumns) {
            Schema::table($table, fn (Blueprint $tableBlueprint) => $tableBlueprint->dropColumn($existingColumns));
        }
    }

    private function backfillExistingRecords(): void
    {
        if (Schema::hasTable('properties') && Schema::hasColumn('properties', 'country')) {
            DB::table('properties')->whereNull('continent')->update(['continent' => 'Africa']);
            DB::table('properties')->whereNull('country')->update(['country' => 'Ghana']);
            DB::table('properties')->whereNull('country_code')->update(['country_code' => 'GH']);
            DB::table('properties')
                ->whereNull('address_line')
                ->update([
                    'address_line' => DB::raw("NULLIF(TRIM(CONCAT_WS(', ', digitaladdress, location, streetname)), '')"),
                ]);
        }

        if (Schema::hasTable('businesses') && Schema::hasColumn('businesses', 'country')) {
            DB::table('businesses')->whereNull('continent')->update(['continent' => 'Africa']);
            DB::table('businesses')->whereNull('country')->update(['country' => 'Ghana']);
            DB::table('businesses')->whereNull('country_code')->update(['country_code' => 'GH']);
            DB::table('businesses')
                ->whereNull('address_line')
                ->update([
                    'address_line' => DB::raw("NULLIF(TRIM(CONCAT_WS(', ', address, digitaladdress, location, street_name)), '')"),
                ]);
        }

        if (Schema::hasTable('account_property_requests') && Schema::hasColumn('account_property_requests', 'country')) {
            DB::table('account_property_requests')->whereNull('continent')->update(['continent' => 'Africa']);
            DB::table('account_property_requests')->whereNull('country')->update(['country' => 'Ghana']);
            DB::table('account_property_requests')->whereNull('country_code')->update(['country_code' => 'GH']);
        }
    }
};
