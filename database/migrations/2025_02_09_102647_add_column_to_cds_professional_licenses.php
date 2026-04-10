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
            $table->string('class_level')->nullable();
            $table->string('title')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cds_professional_licenses', function (Blueprint $table) {
            $table->dropColumn('class_level');
            $table->dropColumn('title');
        });
    }
};
