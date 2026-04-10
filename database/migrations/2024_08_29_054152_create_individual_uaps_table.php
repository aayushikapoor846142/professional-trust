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
        Schema::create('individual_uaps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unique_id')->unique();
            $table->bigInteger('unauthorised_id')->default(0);
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone_no')->nullable();
            $table->text('address')->nullable();
            $table->string('country')->nullable();
            $table->string('social_mediumn_link')->nullable();
            $table->string('why_uap')->nullable();
            $table->string('evidences')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('individual_uaps');
    }
};
