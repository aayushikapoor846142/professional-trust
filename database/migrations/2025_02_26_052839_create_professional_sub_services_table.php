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
        Schema::create('professional_sub_services', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->default(0);
            $table->integer('user_id')->default(0);
            $table->integer('service_id')->default(0);
            $table->integer('sub_services_type_id')->default(0);
            $table->string('professional_fees')->nullable();
            $table->string('consultancy_fees')->nullable();
            $table->integer('form_id')->nullable();
            $table->longText('description')->nullable();
            $table->integer('added_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_sub_services');
    }
};
