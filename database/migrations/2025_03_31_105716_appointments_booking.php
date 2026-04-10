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
        Schema::create('appointment_booking', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->default(0);
            $table->integer('professional_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('time_type')->nullable();
            $table->date('appointment_date')->nullable();
            $table->string('meeting_duration')->nullable();
            $table->string('appointments_gap')->nullable();
            $table->integer('working_hours_id')->nullable();
            $table->integer('appointment_type_id')->nullable();
            $table->string('price')->nullable();
            $table->string('status')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('paid_date')->nullable();
            $table->integer('invoice_id')->nullable();
            $table->integer('sub_service_id')->nullable();
            $table->integer('professional_service_id')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      
            Schema::dropIfExists('appointment_booking');
    
    }
};
