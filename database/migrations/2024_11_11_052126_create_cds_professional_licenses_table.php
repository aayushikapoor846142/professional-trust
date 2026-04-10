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
        Schema::create('cds_professional_licenses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->default(0);
            $table->integer('regulatory_body_id')->nullable();
            $table->string('entitled_to_pratice')->nullable();
            $table->string('license_number')->nullable();
            $table->date('license_start_date')->nullable();
            $table->string('license_status')->nullable();
            $table->string('country_of_practice')->nullable();
            $table->integer('do_you_more_license')->default(0);
            $table->integer('added_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cds_professional_licenses');
    }
};
