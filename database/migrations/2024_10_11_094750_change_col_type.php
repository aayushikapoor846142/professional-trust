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
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('site_logo')->nullable()->default(null)->change();  // Change this line for your column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('site_logo')->nullable(false)->default('')->change();  // Reverse the change here
        });
    }
};
