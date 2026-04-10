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
        Schema::table('case_with_professionals', function (Blueprint $table) {
            $table->string('priority')->default('medium')->after('added_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_with_professionals', function (Blueprint $table) {
            $table->dropColumn('priority');
        });
    }
};
