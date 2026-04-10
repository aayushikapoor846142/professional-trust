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
        Schema::table('service_assesment_forms', function (Blueprint $table) {
            $table->string('service_id')->comment('immigration service table id')->change();
            $table->string('professional_service_id')->comment('professional serivce table id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_assesment_forms', function (Blueprint $table) {
            $table->string('service_id')->comment('professional service table id')->change();
            $table->dropColumn('professional_service_id');
        });
    }
};
