<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pro_integrations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('pro_user_uuid')->nullable();
            $table->string('assembly_code')->nullable();
            $table->string('category', 24);
            $table->string('provider', 64);
            $table->string('label')->nullable();
            $table->text('credentials')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->foreign('pro_user_uuid')->references('uuid')->on('users')->nullOnDelete();
            $table->index(['pro_user_uuid', 'category', 'provider']);
            $table->index(['assembly_code', 'category']);
        });

        Schema::create('pro_payment_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('pro_user_uuid')->nullable();
            $table->uuid('requested_by_agent_uuid')->nullable();
            $table->uuid('integration_uuid')->nullable();
            $table->string('assembly_code')->nullable();
            $table->uuid('bill_uuid')->nullable();
            $table->uuid('waste_company_uuid')->nullable();
            $table->uuid('waste_service_plan_uuid')->nullable();
            $table->string('purpose')->default('bill');
            $table->string('payer_name')->nullable();
            $table->string('payer_email')->nullable();
            $table->string('payer_phone')->nullable();
            $table->text('description')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency_code', 3)->default('GHS');
            $table->string('provider', 64)->nullable();
            $table->string('payment_reference')->nullable()->unique();
            $table->text('payment_url')->nullable();
            $table->text('public_url')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('sent_email_at')->nullable();
            $table->timestamp('sent_sms_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();

            $table->foreign('pro_user_uuid')->references('uuid')->on('users')->nullOnDelete();
            $table->foreign('requested_by_agent_uuid')->references('uuid')->on('users')->nullOnDelete();
            $table->foreign('integration_uuid')->references('uuid')->on('pro_integrations')->nullOnDelete();
            $table->foreign('bill_uuid')->references('uuid')->on('bills')->nullOnDelete();
            $table->foreign('waste_company_uuid')->references('uuid')->on('waste_companies')->nullOnDelete();
            $table->foreign('waste_service_plan_uuid')->references('uuid')->on('waste_service_plans')->nullOnDelete();
            $table->index(['pro_user_uuid', 'status']);
            $table->index(['assembly_code', 'purpose']);
        });

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'pro_owner_uuid')) {
                $table->uuid('pro_owner_uuid')->nullable()->after('uuid');
                $table->foreign('pro_owner_uuid')->references('uuid')->on('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('users', 'agent_capabilities')) {
                $table->json('agent_capabilities')->nullable()->after('role_access');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'agent_capabilities')) {
                $table->dropColumn('agent_capabilities');
            }

            if (Schema::hasColumn('users', 'pro_owner_uuid')) {
                $table->dropForeign(['pro_owner_uuid']);
                $table->dropColumn('pro_owner_uuid');
            }
        });

        Schema::dropIfExists('pro_payment_requests');
        Schema::dropIfExists('pro_integrations');
    }
};
