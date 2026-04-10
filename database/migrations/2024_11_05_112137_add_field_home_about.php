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
        Schema::table('home_settings', function (Blueprint $table) {
            $table->longText('about_desc')->nullable();
            $table->string('about_subtitle')->nullable();
            $table->string('about_title')->nullable();
        });

        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            //
        });
    }
};
