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
        Schema::create('uap_reconsiders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->default(0);
            $table->integer('uap_id')->nullable();
            $table->longText('description')->nullable();
            $table->string('evidences')->nullable();
            $table->integer('added_by')->default(0);
            $table->string('status')->default('pending');
            $table->integer('assigned_to')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uap_reconsiders');
    }
};
