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
        Schema::table('user_details', function (Blueprint $table) {
            $table->string('country_id')->nullable()->change();
            $table->string('state_id')->nullable()->change();
            $table->string('city_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->integer('country_id')->default(0)->change();
            $table->integer('state_id')->default(0)->change();
            $table->integer('city_id')->default(0)->change();
        });
    }
};
