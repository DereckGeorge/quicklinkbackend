<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('home_visits', function (Blueprint $table) {
            $table->id();
            $table->string('provider_id');
            $table->string('provider_name');
            $table->enum('provider_type', ['doctor', 'nurse', 'paramedic']);
            $table->string('specialty');
            $table->string('provider_image_url')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('review_count')->default(0);
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('TZS');
            $table->string('location');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->integer('estimated_travel_time')->nullable();
            $table->json('available_days');
            $table->json('available_time_slots');
            $table->boolean('is_available')->default(true);
            $table->text('description');
            $table->json('services');
            $table->boolean('accepts_insurance')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_visits');
    }
};
