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
        Schema::create('hospital_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->onDelete('cascade');
            $table->enum('document_type', [
                'official_application_letter',
                'operating_license',
                'hfrs_registration_certificate',
                'brela_certificate',
                'tin_certificate',
                'organizational_structure',
                'floor_plan',
                'facility_photo'
            ]);
            $table->string('file_name');
            $table->string('file_path'); // Relative path like 'hospital_documents/...'
            $table->unsignedBigInteger('file_size'); // Size in bytes
            $table->string('mime_type')->nullable();
            $table->enum('status', ['uploaded', 'verified', 'rejected'])->default('uploaded');
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
            
            // Index for faster queries
            $table->index(['hospital_id', 'document_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospital_documents');
    }
};
