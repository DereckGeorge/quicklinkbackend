<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->string('license_number')->nullable()->after('qualification');
            $table->string('clinic_name')->nullable()->after('license_number');
            $table->string('clinic_address')->nullable()->after('clinic_name');
        });
    }

    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn(['license_number', 'clinic_name', 'clinic_address']);
        });
    }
};


