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
        Schema::create('professional_service_prices', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->unsignedBigInteger('actual_service_id');
            $table->unsignedBigInteger('professional_service_id');
            $table->unsignedBigInteger('type')->nullable();
            $table->decimal('professional_fees', 10, 2)->nullable();
            $table->decimal('consultancy_fees', 10, 2)->nullable();
            $table->unsignedBigInteger('documents')->nullable();
            $table->unsignedBigInteger('added_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_service_prices');
    }
};
