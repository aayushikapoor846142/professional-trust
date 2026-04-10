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
        Schema::create('working_hours', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->default(0);
            $table->integer('professional_id');
            $table->string('day');
            $table->string('from_time')->nullable();
            $table->string('no_break_time')->nullable();
            $table->string('to_time')->nullable();
            $table->string('break_starttime')->nullable();
            $table->string('break_endtime')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      
            Schema::dropIfExists('working_hours');
    
    }
};
