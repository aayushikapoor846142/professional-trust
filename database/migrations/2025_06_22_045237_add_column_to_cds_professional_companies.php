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
        Schema::table('cds_professional_companies', function (Blueprint $table) {
            $table->string('banner_image')->after('company_name')->nullable();
            $table->string('company_logo')->after('company_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cds_professional_companies', function (Blueprint $table) {
            $table->dropColumn('banner_image');
            $table->dropColumn('company_logo');
        });
    }
};
