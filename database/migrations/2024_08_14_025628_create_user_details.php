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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('cv_type', 255)->default('0');
            $table->string('gender', 100)->nullable();
            $table->integer('country_id')->default(0);
            $table->integer('state_id')->default(0);
            $table->integer('city_id')->default(0);
            $table->text('address')->nullable();
            $table->string('zip_code', 15)->nullable();
            $table->text('languages_known')->nullable();
            $table->string('cover_image', 200)->nullable();
            $table->string('date_of_birth', 20)->nullable();
            $table->integer('profile_complete')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
