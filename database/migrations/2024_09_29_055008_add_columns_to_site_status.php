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
        Schema::table('screen_captures', function (Blueprint $table) {
            $table->string('site_status')->default('active');
        });


        Schema::table('professional_sites', function (Blueprint $table) {
            $table->string('site_status')->default('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('screen_captures', function (Blueprint $table) {
            $table->dropColumn('site_status');
        });

        Schema::table('professional_sites', function (Blueprint $table) {
            $table->dropColumn('site_status');
        });
    }
};
