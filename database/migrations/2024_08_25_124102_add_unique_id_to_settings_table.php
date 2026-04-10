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
        Schema::table('settings', function (Blueprint $table) {
            // Add unique_id field to settings table without unique constraint
            Schema::table('settings', function (Blueprint $table) {
                $table->unsignedBigInteger('unique_id')->nullable()->after('id');
            });

            // Populate unique_id with unique values for existing rows
            // Random 10-digit numbers are assigned to ensure uniqueness
            DB::table('settings')->get()->each(function($row) {
                DB::table('settings')->where('id', $row->id)->update([
                    'unique_id' => mt_rand(1000000000, 9999999999) // Assign random 10-digit numbers
                ]);
            });

            // Apply unique constraint to the unique_id field after population
            Schema::table('settings', function (Blueprint $table) {
                $table->unique('unique_id');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique(['unique_id']);
            $table->dropColumn('unique_id');
        });
    }
};
