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
        Schema::create('professional_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unique_id')->unique();
            $table->integer('parent_service_id')->nullable();
            $table->integer('service_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_services');
    }

};
