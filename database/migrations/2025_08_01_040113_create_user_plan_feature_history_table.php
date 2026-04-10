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
        Schema::create('user_plan_feature_history', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id');
            $table->unsignedBigInteger('user_id');
            $table->string('module_name'); // staff, proposals, reviews, appointments, etc.
            $table->string('feature_key'); // staff_add, case_submit_proposal, reviews, appointment_booking, etc.
            $table->integer('plan_limit')->nullable(); // limit from plan
            $table->integer('current_usage')->nullable(); // current usage after this action
            $table->text('description')->nullable(); // additional details
            $table->json('metadata')->nullable(); // additional data in JSON format
            $table->bigInteger('added_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_plan_feature_history');
    }
};
