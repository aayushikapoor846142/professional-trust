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
        Schema::table('professional_sub_services', function (Blueprint $table) {
            $table->integer('minimum_fees')->default(0);
            $table->integer('maximum_fees')->default(0);
            $table->integer('professional_service_id')->default(0);
            $table->integer('tbd')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professional_sub_services', function (Blueprint $table) {
            $table->dropColumn('minimum_fees');
            $table->dropColumn('maximum_fees');
            $table->dropColumn('professional_service_id');
            $table->dropColumn('tbd');
        });
    }
};
