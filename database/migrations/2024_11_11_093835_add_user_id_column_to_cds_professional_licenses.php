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
        Schema::table('cds_professional_licenses', function (Blueprint $table) {
            $table->integer('user_id')->default(0);
            $table->integer('regulatory_country_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cds_professional_licenses', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('regulatory_country_id');
        });
    }
};
