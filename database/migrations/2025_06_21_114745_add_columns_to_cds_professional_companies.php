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
            $table->text('about_company')->after('company_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cds_professional_companies', function (Blueprint $table) {
            $table->dropColumn('about_company');
        });
    }
};
