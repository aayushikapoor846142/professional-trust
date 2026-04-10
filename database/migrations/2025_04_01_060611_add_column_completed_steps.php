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
            $table->integer('completed_step')->default('0');
        });
        Schema::table('appointment_types', function (Blueprint $table) {
            $table->string('price')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointment_booking', function (Blueprint $table) {
            $table->dropColumn('completed_step');
        });
        Schema::table('appointment_types', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
