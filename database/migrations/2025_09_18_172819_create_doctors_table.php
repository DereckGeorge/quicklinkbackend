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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('specialty');
            $table->string('qualification')->nullable();
            $table->integer('experience')->default(0);
            $table->decimal('rating', 3, 2)->default(0);
            $table->string('image_url')->nullable();
            $table->json('available_days')->nullable();
            $table->string('available_time')->nullable();
            $table->decimal('consultation_fee', 10, 2)->default(0);
            $table->text('bio')->nullable();
            $table->json('languages')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
