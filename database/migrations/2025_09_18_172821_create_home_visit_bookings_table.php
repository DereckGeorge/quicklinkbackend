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
        Schema::create('home_visit_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('home_visit_id')->constrained()->onDelete('cascade');
            $table->string('provider_id');
            $table->string('provider_name');
            $table->enum('provider_type', ['doctor', 'nurse', 'paramedic']);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('patient_name');
            $table->string('patient_phone');
            $table->text('patient_address');
            $table->decimal('patient_latitude', 10, 8);
            $table->decimal('patient_longitude', 11, 8);
            $table->datetime('scheduled_date');
            $table->string('time_slot');
            $table->text('visit_reason');
            $table->text('symptoms')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('TZS');
            $table->enum('status', ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->text('notes')->nullable();
            $table->datetime('actual_visit_time')->nullable();
            $table->datetime('completed_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_visit_bookings');
    }
};
