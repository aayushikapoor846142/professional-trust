<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('professional_leaves', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->unique(); 
            $table->integer('professional_id');
            $table->integer('location_id');
            $table->date('leave_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('professional_leaves');
    }

};
