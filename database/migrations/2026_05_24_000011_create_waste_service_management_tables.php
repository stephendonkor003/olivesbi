<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waste_companies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('registered_by_uuid')->nullable();
            $table->string('assembly_code')->nullable();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('service_area')->nullable();
            $table->string('address_line')->nullable();
            $table->string('continent')->nullable();
            $table->string('country')->nullable();
            $table->string('country_code', 3)->nullable();
            $table->string('administrative_area')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('registered_by_uuid')->references('uuid')->on('users')->nullOnDelete();
        });

        Schema::create('waste_service_plans', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('waste_company_uuid');
            $table->string('name');
            $table->string('waste_stream')->default('General waste');
            $table->string('default_frequency')->default('weekly');
            $table->decimal('service_charge', 12, 2)->default(0);
            $table->string('currency_code', 3)->default('GHS');
            $table->string('currency_symbol', 12)->default('GHS');
            $table->string('charge_period')->default('per pickup');
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->foreign('waste_company_uuid')->references('uuid')->on('waste_companies')->cascadeOnDelete();
        });

        Schema::create('waste_pickup_bookings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('user_uuid');
            $table->uuid('property_uuid')->nullable();
            $table->uuid('business_uuid')->nullable();
            $table->uuid('waste_company_uuid');
            $table->uuid('waste_service_plan_uuid');
            $table->string('frequency')->default('weekly');
            $table->date('scheduled_date');
            $table->time('scheduled_time');
            $table->timestamp('next_pickup_at')->nullable();
            $table->decimal('estimated_waste_kg', 10, 2)->nullable();
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->foreign('user_uuid')->references('uuid')->on('users')->cascadeOnDelete();
            $table->foreign('property_uuid')->references('uuid')->on('properties')->nullOnDelete();
            $table->foreign('business_uuid')->references('uuid')->on('businesses')->nullOnDelete();
            $table->foreign('waste_company_uuid')->references('uuid')->on('waste_companies')->restrictOnDelete();
            $table->foreign('waste_service_plan_uuid')->references('uuid')->on('waste_service_plans')->restrictOnDelete();
        });

        Schema::create('waste_pickups', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('waste_pickup_booking_uuid');
            $table->uuid('waste_company_uuid');
            $table->timestamp('scheduled_for');
            $table->string('status')->default('scheduled');
            $table->decimal('waste_amount_kg', 10, 2)->nullable();
            $table->timestamp('due_notified_at')->nullable();
            $table->timestamp('missed_notified_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('waste_pickup_booking_uuid')->references('uuid')->on('waste_pickup_bookings')->cascadeOnDelete();
            $table->foreign('waste_company_uuid')->references('uuid')->on('waste_companies')->restrictOnDelete();
        });

        Schema::create('waste_provider_changes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('waste_pickup_booking_uuid');
            $table->uuid('from_company_uuid')->nullable();
            $table->uuid('to_company_uuid');
            $table->uuid('from_plan_uuid')->nullable();
            $table->uuid('to_plan_uuid');
            $table->uuid('changed_by_uuid');
            $table->text('reason');
            $table->timestamp('changed_at');
            $table->timestamps();

            $table->foreign('waste_pickup_booking_uuid')->references('uuid')->on('waste_pickup_bookings')->cascadeOnDelete();
            $table->foreign('from_company_uuid')->references('uuid')->on('waste_companies')->nullOnDelete();
            $table->foreign('to_company_uuid')->references('uuid')->on('waste_companies')->restrictOnDelete();
            $table->foreign('from_plan_uuid')->references('uuid')->on('waste_service_plans')->nullOnDelete();
            $table->foreign('to_plan_uuid')->references('uuid')->on('waste_service_plans')->restrictOnDelete();
            $table->foreign('changed_by_uuid')->references('uuid')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waste_provider_changes');
        Schema::dropIfExists('waste_pickups');
        Schema::dropIfExists('waste_pickup_bookings');
        Schema::dropIfExists('waste_service_plans');
        Schema::dropIfExists('waste_companies');
    }
};
