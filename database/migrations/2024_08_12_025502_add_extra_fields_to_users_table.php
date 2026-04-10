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
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('unique_id')->default(0);
            $table->string('first_name', 50)->nullable();
            $table->string('last_name', 50)->nullable();
            $table->string('gender', 50)->nullable();
            $table->string('date_of_birth', 50)->nullable();
            $table->string('country_code', 10)->nullable();
            $table->unsignedTinyInteger('country_id')->default(0);
            $table->unsignedTinyInteger('state_id')->default(0);
            $table->unsignedTinyInteger('city_id')->default(0);
            $table->text('address')->nullable();
            $table->string('zip_code',50)->nullable();
            $table->string('profile_image',255)->nullable();
            $table->string('professional_type',100)->nullable();
            $table->integer('is_active')->default(0);
            $table->integer('is_verified')->default(0);
            $table->integer('social_connect')->default(0);
            $table->string('provider_id',255)->nullable();
            $table->text('languages_known',255)->nullable();
            $table->string('provider',255)->nullable();
            $table->integer('created_by')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
