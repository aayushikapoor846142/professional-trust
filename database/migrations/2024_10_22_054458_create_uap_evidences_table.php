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
        Schema::create('uap_evidences', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->unique();
            $table->integer('uap_id')->default(0);
            $table->string('name', 255)->nullable();
            $table->string('file_name', 255)->nullable();
            $table->string('file_type', 255)->nullable();
            $table->integer('added_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uap_evidences');
    }
};
