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
        Schema::create('professional_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unique_id')->unique();
            $table->string('incorporation_certification')->nullable();
            $table->string('license')->nullable();
            $table->integer('photo_id')->nullable();
            $table->integer('professional_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_documents');
    }

};
