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
        Schema::table('user_plan_feature_history', function (Blueprint $table) {
            // Change plan_limit column from integer to string to support 'unlimited' text
            $table->string('plan_limit')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_plan_feature_history', function (Blueprint $table) {
            // Change plan_limit column back to integer
            $table->integer('plan_limit')->nullable()->change();
        });
    }
};
