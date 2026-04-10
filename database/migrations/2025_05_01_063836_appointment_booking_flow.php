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
        Schema::create('appointment_booking_flow', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->unique(); 
            $table->string('title');
            $table->string('status')->nullable();
            $table->longText('description')->nullable();
            $table->integer('time_duration_id')->default(0);
            $table->integer('appointment_type_id')->default(0);
            $table->integer('location_id')->default(0);
            $table->string('appointment_mode')->nullable();
            $table->string('timezone')->nullable();
            $table->integer('service_id')->default(0);
            $table->integer('working_hours_id')->default(0);
            $table->integer('added_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_booking_flow');
    }
};
