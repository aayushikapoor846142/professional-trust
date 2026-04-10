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
        Schema::table('appointment_booking', function (Blueprint $table) {
            $table->integer('added_by')->default('0');
        });
        Schema::table('working_hours', function (Blueprint $table) {
            $table->string('timezone')->nullable();
        });
        Schema::table('appointment_types', function (Blueprint $table) {
            $table->string('currency')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('working_hours', function (Blueprint $table) {
            $table->dropColumn('timezone');

        });
        Schema::table('appointment_booking', function (Blueprint $table) {
            $table->dropColumn('added_by');

        });
        Schema::table('appointment_types', function (Blueprint $table) {
            $table->dropColumn('currency');

        });

    }
};
