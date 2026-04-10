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
        Schema::table('claim_profiles', function (Blueprint $table) {
            $table->string('alt_country_code', 10)->nullable();
            $table->string('pri_country_code', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claim_profiles', function (Blueprint $table) {
            $table->dropColumn(['alt_country_code', 'pri_country_code']);
        });
    }
};
