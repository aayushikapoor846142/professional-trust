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
        Schema::table('case_sub_stages', function (Blueprint $table) {
            $table->longText('description')->nullable()->after('predefined_case_sub_stage_id');
            $table->integer('added_by')->default(0)->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_sub_stages', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('added_by');
        });
    }
};
