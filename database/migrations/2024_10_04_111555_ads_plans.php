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
        Schema::create('ads_plans', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id');
            $table->string('plan_name');
            $table->string('amount');
            $table->string('number_of_days');
            $table->string('added_by');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads_plans');

    }

};
