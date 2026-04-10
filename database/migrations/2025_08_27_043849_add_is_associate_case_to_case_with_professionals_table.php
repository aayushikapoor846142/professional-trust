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
            $table->integer('is_associate_case')->default(0)->after('professional_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_with_professionals', function (Blueprint $table) {
            $table->dropColumn('is_associate_case');
        });
    }
};
