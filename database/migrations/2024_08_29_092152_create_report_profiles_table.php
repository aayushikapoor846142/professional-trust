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
        Schema::create('report_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unique_id')->unique();
            $table->integer('added_by')->default(0);
            $table->string('title')->nullable();
            $table->longText('description')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_profiles');
    }
};
