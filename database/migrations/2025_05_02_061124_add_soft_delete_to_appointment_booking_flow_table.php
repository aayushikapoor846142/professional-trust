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
        Schema::table('appointment_booking_flow', function (Blueprint $table) {
            $table->softDeletes();

            // Change service_id from integer to string
            $table->string('service_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointment_booking_flow', function (Blueprint $table) {
            $table->dropSoftDeletes();

            // Revert service_id to integer
            $table->integer('service_id')->nullable()->change();
        });
    }
};
