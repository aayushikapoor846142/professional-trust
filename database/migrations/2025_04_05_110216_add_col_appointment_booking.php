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
            $table->integer('cancelled_by')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
        Schema::table('appointment_booking', function (Blueprint $table) {
            $table->dropColumn('cancelled_by');

        });
    }
};
