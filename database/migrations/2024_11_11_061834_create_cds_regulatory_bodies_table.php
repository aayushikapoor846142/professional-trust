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
        Schema::create('cds_regulatory_bodies', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->unique(); 
            $table->integer('regulatory_country_id')->default(0);
            $table->string('name',255)->nullable();
            $table->string('license_prefix',255)->nullable();
            $table->string('license_label')->nullable();
            $table->string('license_type_sub')->nullable();
            $table->integer('license_category_id')->nullable();
            $table->integer('license_type_id')->nullable();
            $table->text('comment')->nullable();
            $table->integer('added_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cds_regulatory_bodies');
    }
};
