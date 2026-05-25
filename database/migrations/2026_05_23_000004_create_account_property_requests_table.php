<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_property_requests', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('citizen_id')->nullable()->index();
            $table->string('owner_id')->nullable()->index();
            $table->string('assembly_code')->nullable()->index();
            $table->string('status')->default('pending')->index();
            $table->string('source')->default('openstreetmap');
            $table->string('parcel_name')->nullable();
            $table->text('display_name')->nullable();
            $table->string('address_line')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('osm_type')->nullable();
            $table->string('osm_id')->nullable();
            $table->string('osm_place_id')->nullable();
            $table->string('osm_class')->nullable();
            $table->string('osm_category')->nullable();
            $table->json('parcel_geojson')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_property_requests');
    }
};
