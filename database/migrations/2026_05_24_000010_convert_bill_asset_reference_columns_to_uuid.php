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

        if (Schema::hasColumn('bills', 'property_id')) {
            DB::statement('ALTER TABLE bills ALTER COLUMN property_id DROP DEFAULT');
            DB::statement('ALTER TABLE bills ALTER COLUMN property_id TYPE uuid USING NULLIF(property_id, \'\')::uuid');
        }

        if (Schema::hasColumn('bills', 'business_id')) {
            DB::statement('ALTER TABLE bills ALTER COLUMN business_id DROP DEFAULT');
            DB::statement('ALTER TABLE bills ALTER COLUMN business_id TYPE uuid USING NULLIF(business_id, \'\')::uuid');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('bills')) {
            return;
        }

        if (Schema::hasColumn('bills', 'property_id')) {
            DB::statement('ALTER TABLE bills ALTER COLUMN property_id TYPE varchar(255) USING property_id::text');
        }

        if (Schema::hasColumn('bills', 'business_id')) {
            DB::statement('ALTER TABLE bills ALTER COLUMN business_id TYPE varchar(255) USING business_id::text');
        }
    }
};
