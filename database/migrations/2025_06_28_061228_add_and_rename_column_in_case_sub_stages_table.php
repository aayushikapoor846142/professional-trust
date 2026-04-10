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
            $table->dropColumn('type_id');
            $table->integer('form_id')->after('stage_type')->nullable();
            $table->string('folder_id')->after('form_id')->comment('case document folder table id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_sub_stages', function (Blueprint $table) {
           
        });
    }
};
