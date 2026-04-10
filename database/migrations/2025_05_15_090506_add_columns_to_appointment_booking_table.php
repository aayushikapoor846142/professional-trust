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
            $table->integer('paid_by')->after('payment_status')->nullable();
            $table->integer('mark_as_free')->after('paid_by')->default(0);
            $table->integer('free_by')->after('mark_as_free')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointment_booking', function (Blueprint $table) {
            $table->dropColumn('paid_by');
            $table->dropColumn('mark_as_free');
            $table->dropColumn('free_by');
        });
    }
};
