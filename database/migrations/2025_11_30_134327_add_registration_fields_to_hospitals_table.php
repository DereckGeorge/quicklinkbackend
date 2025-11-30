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
        Schema::table('hospitals', function (Blueprint $table) {
            // Basic registration fields
            $table->boolean('is_polyclinic')->default(false)->after('name');
            $table->string('hfrs_number')->unique()->nullable()->after('is_polyclinic');
            $table->enum('verification_status', ['pending', 'documents_pending', 'under_review', 'verified', 'rejected'])->default('pending')->after('hfrs_number');
            $table->timestamp('verified_at')->nullable()->after('verification_status');
            $table->unsignedBigInteger('verified_by')->nullable()->after('verified_at');
            $table->text('rejection_reason')->nullable()->after('verified_by');
            
            // Physical address (JSON)
            $table->json('physical_address')->nullable()->after('address');
            
            // Contact details (JSON)
            $table->json('contact_details')->nullable()->after('phone_number');
            
            // Ownership and affiliation (JSON)
            $table->enum('ownership_type', ['government_public', 'fbo', 'ngo', 'private_for_profit'])->nullable()->after('contact_details');
            $table->json('affiliation')->nullable()->after('ownership_type');
            
            // Date operation began (JSON)
            $table->json('date_operation_began')->nullable()->after('affiliation');
            
            // Credentialing contact (JSON)
            $table->json('credentialing_contact')->nullable()->after('date_operation_began');
            
            // Registration legal compliance (JSON)
            $table->json('registration_legal_compliance')->nullable()->after('credentialing_contact');
            
            // Clinical services infrastructure (JSON)
            $table->json('clinical_services_infrastructure')->nullable()->after('registration_legal_compliance');
            
            // Key personnel staffing (JSON)
            $table->json('key_personnel_staffing')->nullable()->after('clinical_services_infrastructure');
            
            // Foreign key for verified_by
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn([
                'is_polyclinic',
                'hfrs_number',
                'verification_status',
                'verified_at',
                'verified_by',
                'rejection_reason',
                'physical_address',
                'contact_details',
                'ownership_type',
                'affiliation',
                'date_operation_began',
                'credentialing_contact',
                'registration_legal_compliance',
                'clinical_services_infrastructure',
                'key_personnel_staffing',
            ]);
        });
    }
};
