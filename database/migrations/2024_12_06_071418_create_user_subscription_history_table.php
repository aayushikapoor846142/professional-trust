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
        Schema::create('user_subscription_history', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->unique();
            $table->string('membership_plans_plan_id');
            $table->string('stripe_subscription_id')->nullable();
            $table->string('subscription_status')->nullable();
            $table->integer('user_id')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscription_history');
    }
};
