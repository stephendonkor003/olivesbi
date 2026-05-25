<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    private array $tables = [
        'agent_assignments',
        'assemblies',
        'audit_trails',
        'bill_adjustments',
        'blocks',
        'bop_rates',
        'buildings',
        'business_class_types',
        'business_classes',
        'business_owners',
        'business_types',
        'categories',
        'citizens',
        'customer_supports',
        'customer_types',
        'divisions',
        'entity_owners',
        'ghana_regions',
        'invoice_layout_templates',
        'invoices',
        'item_descriptions',
        'main_items',
        'mmdas',
        'newsletter_subscriptions',
        'otps',
        'permissions',
        'polygon_blocks',
        'polygons',
        'property_class_revenues',
        'property_classes',
        'property_types',
        'property_users',
        'rates',
        'report_uploads',
        'revenue_stream_types',
        'revenue_streams',
        'roles',
        'service_requests',
        'sub_items',
        'task_assignments',
        'users',
        'zones',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'id')) {
                continue;
            }

            if (! Schema::hasColumn($table, 'uuid')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->uuid('uuid')->nullable()->unique()->after('id');
                });
            }

            DB::table($table)
                ->whereNull('uuid')
                ->orderBy('id')
                ->select('id')
                ->chunkById(500, function ($rows) use ($table) {
                    foreach ($rows as $row) {
                        DB::table($table)
                            ->where('id', $row->id)
                            ->update(['uuid' => (string) Str::uuid()]);
                    }
                });
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->tables) as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'uuid')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                $blueprint->dropUnique($table.'_uuid_unique');
                $blueprint->dropColumn('uuid');
            });
        }
    }
};
