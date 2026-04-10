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
        Schema::table('professionals', function (Blueprint $table) {
            // Modify the college_id column to VARCHAR(255)
            $table->string('college_id', 255)->change();
            $table->string('entitled_to_practis_college_id', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            // Revert the college_id column to its previous type (e.g., INTEGER or whatever it was)
            // Assuming the original type was INTEGER, update this line accordingly.
            $table->bigInteger('college_id')->change();
            $table->bigInteger('entitled_to_practis_college_id')->change();
        });
    }
};
