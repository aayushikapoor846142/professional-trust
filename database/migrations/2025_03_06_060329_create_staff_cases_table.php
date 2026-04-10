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
        Schema::create('staff_cases', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->default(0);
            $table->integer('staff_id');
            $table->integer('case_id');
            $table->integer('added_by');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_cases');
    }
};
