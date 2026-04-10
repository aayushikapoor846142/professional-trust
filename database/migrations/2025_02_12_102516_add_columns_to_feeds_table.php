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
        Schema::table('feeds', function (Blueprint $table) {
            $table->integer('is_pin')->after('added_by')->default(0);
            $table->date('schedule_date')->after('is_pin')->nullable();
            $table->string('status')->after('schedule_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feeds', function (Blueprint $table) {
            $table->dropColumn('is_pin');
            $table->dropColumn('schedule_date');
            $table->dropColumn('status');
        });
    }
};
