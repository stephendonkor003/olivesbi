<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assemblies', function (Blueprint $table) {
            if (! Schema::hasColumn('assemblies', 'currency_code')) {
                $table->string('currency_code', 3)->default('GHS')->after('assembly_category');
            }

            if (! Schema::hasColumn('assemblies', 'currency_symbol')) {
                $table->string('currency_symbol', 12)->default('GHS')->after('currency_code');
            }

            if (! Schema::hasColumn('assemblies', 'currency_name')) {
                $table->string('currency_name')->default('Ghanaian Cedi')->after('currency_symbol');
            }
        });
    }

    public function down(): void
    {
        Schema::table('assemblies', function (Blueprint $table) {
            if (Schema::hasColumn('assemblies', 'currency_name')) {
                $table->dropColumn('currency_name');
            }

            if (Schema::hasColumn('assemblies', 'currency_symbol')) {
                $table->dropColumn('currency_symbol');
            }

            if (Schema::hasColumn('assemblies', 'currency_code')) {
                $table->dropColumn('currency_code');
            }
        });
    }
};
