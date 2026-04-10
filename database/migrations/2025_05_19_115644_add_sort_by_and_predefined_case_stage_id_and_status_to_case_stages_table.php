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
        Schema::table('case_stages', function (Blueprint $table) {
            $table->integer('sort_order')->after('fees')->default(0);
            $table->integer('predefined_case_stage_id')->after('sort_order')->default(0);
            $table->string('status')->after('predefined_case_stage_id')->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_stages', function (Blueprint $table) {
            $table->dropColumn('sort_order');
            $table->dropColumn('predefined_case_stage_id');
            $table->dropColumn('status');
        });
    }
};
