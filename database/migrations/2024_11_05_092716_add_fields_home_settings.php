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
            $table->longText('initiative_1_desc')->nullable();
            $table->longText('initiative_2_desc')->nullable();
            $table->longText('initiative_3_desc')->nullable();
            $table->longText('initiative_4_desc')->nullable();
            $table->string('initiative_1_title')->nullable();
            $table->string('initiative_2_title')->nullable();
            $table->string('initiative_3_title')->nullable();
            $table->string('initiative_4_title')->nullable();

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
