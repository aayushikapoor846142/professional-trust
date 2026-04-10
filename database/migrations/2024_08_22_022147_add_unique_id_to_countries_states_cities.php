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
        Schema::table('countries_states_cities', function (Blueprint $table) {
            
            // Add unique_id field to countries table without unique constraint
            Schema::table('countries', function (Blueprint $table) {
                $table->unsignedBigInteger('unique_id')->nullable()->after('id');
            });

            // Populate unique_id with unique values for existing rows
            DB::table('countries')->get()->each(function($row) {
                DB::table('countries')->where('id', $row->id)->update([
                    'unique_id' => mt_rand(1000000000, 9999999999) // Assign random 10-digit numbers
                ]);
            });

            // Now apply unique constraint to unique_id
            Schema::table('countries', function (Blueprint $table) {
                $table->unique('unique_id');
            });

            // Repeat the same process for states table
            Schema::table('states', function (Blueprint $table) {
                $table->unsignedBigInteger('unique_id')->nullable()->after('id');
            });

            DB::table('states')->get()->each(function($row) {
                DB::table('states')->where('id', $row->id)->update([
                    'unique_id' => mt_rand(1000000000, 9999999999)
                ]);
            });

            Schema::table('states', function (Blueprint $table) {
                $table->unique('unique_id');
            });

            // Repeat the same process for cities table
            Schema::table('cities', function (Blueprint $table) {
                $table->unsignedBigInteger('unique_id')->nullable()->after('id');
            });

            DB::table('cities')->get()->each(function($row) {
                DB::table('cities')->where('id', $row->id)->update([
                    'unique_id' => mt_rand(1000000000, 9999999999)
                ]);
            });

            Schema::table('cities', function (Blueprint $table) {
                $table->unique('unique_id');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('countries_states_cities', function (Blueprint $table) {
            // Remove unique_id field from countries table
            Schema::table('countries', function (Blueprint $table) {
                $table->dropColumn('unique_id');
            });

            // Remove unique_id field from states table
            Schema::table('states', function (Blueprint $table) {
                $table->dropColumn('unique_id');
            });

            // Remove unique_id field from cities table
            Schema::table('cities', function (Blueprint $table) {
                $table->dropColumn('unique_id');
            });
        });
    }
};
