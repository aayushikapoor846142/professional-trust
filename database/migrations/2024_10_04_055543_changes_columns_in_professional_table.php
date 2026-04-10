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
        Schema::table('professionals', function (Blueprint $table) {
            $table->string('type')->comment('Company type')->nullable()->change();
            $table->string('company_type')->comment('License type')->nullable()->change();
            $table->dropColumn('company_formation_type');
            $table->dropColumn('company_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professional', function (Blueprint $table) {
            //
        });
    }
};
