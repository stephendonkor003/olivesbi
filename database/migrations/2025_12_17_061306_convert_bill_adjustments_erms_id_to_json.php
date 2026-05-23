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
        // First, convert existing comma-separated values to JSON arrays
        $billAdjustments = DB::table('bill_adjustments')
            ->whereNotNull('erms_id')
            ->get();

        foreach ($billAdjustments as $adjustment) {
            // Check if it's already JSON
            $decoded = json_decode($adjustment->erms_id, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                // Already JSON, skip
                continue;
            }

            // Convert comma-separated string to array
            if (is_string($adjustment->erms_id) && ! empty($adjustment->erms_id)) {
                $ermsArray = array_map('trim', explode(',', $adjustment->erms_id));
                DB::table('bill_adjustments')
                    ->where('id', $adjustment->id)
                    ->update(['erms_id' => json_encode($ermsArray)]);
            }
        }

        // Change column type to JSON
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE bill_adjustments ALTER COLUMN erms_id DROP DEFAULT');
            DB::statement('ALTER TABLE bill_adjustments ALTER COLUMN erms_id TYPE json USING erms_id::json');

            return;
        }

        Schema::table('bill_adjustments', function (Blueprint $table) {
            $table->json('erms_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert JSON arrays back to comma-separated strings
        $billAdjustments = DB::table('bill_adjustments')
            ->whereNotNull('erms_id')
            ->get();

        foreach ($billAdjustments as $adjustment) {
            $decoded = json_decode($adjustment->erms_id, true);

            if (is_array($decoded)) {
                $ermsString = implode(',', $decoded);
                DB::table('bill_adjustments')
                    ->where('id', $adjustment->id)
                    ->update(['erms_id' => $ermsString]);
            }
        }

        // Change column type back to string
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE bill_adjustments ALTER COLUMN erms_id DROP DEFAULT');
            DB::statement('ALTER TABLE bill_adjustments ALTER COLUMN erms_id TYPE varchar(255) USING erms_id::text');

            return;
        }

        Schema::table('bill_adjustments', function (Blueprint $table) {
            $table->string('erms_id')->nullable()->change();
        });
    }
};
