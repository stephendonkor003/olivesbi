<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bills')) {
            return;
        }

        if (Schema::hasTable('properties') && Schema::hasColumn('bills', 'property_id') && Schema::hasColumn('properties', 'uuid')) {
            DB::statement("
                UPDATE bills
                SET property_id = properties.uuid::text
                FROM properties
                WHERE bills.property_id IS NOT NULL
                    AND bills.property_id ~ '^[0-9]+$'
                    AND bills.property_id::bigint = properties.id
            ");
        }

        if (Schema::hasTable('businesses') && Schema::hasColumn('bills', 'business_id') && Schema::hasColumn('businesses', 'uuid')) {
            DB::statement("
                UPDATE bills
                SET business_id = businesses.uuid::text
                FROM businesses
                WHERE bills.business_id IS NOT NULL
                    AND bills.business_id ~ '^[0-9]+$'
                    AND bills.business_id::bigint = businesses.id
            ");
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('bills')) {
            return;
        }

        if (Schema::hasTable('properties') && Schema::hasColumn('bills', 'property_id') && Schema::hasColumn('properties', 'uuid')) {
            DB::statement("
                UPDATE bills
                SET property_id = properties.id::text
                FROM properties
                WHERE bills.property_id = properties.uuid::text
            ");
        }

        if (Schema::hasTable('businesses') && Schema::hasColumn('bills', 'business_id') && Schema::hasColumn('businesses', 'uuid')) {
            DB::statement("
                UPDATE bills
                SET business_id = businesses.id::text
                FROM businesses
                WHERE bills.business_id = businesses.uuid::text
            ");
        }
    }
};
