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
        Schema::table('case_with_associates', function (Blueprint $table) {
            $table->dropColumn('case_title'); 
            $table->dropColumn('case_description'); 
            $table->dropColumn('parent_service_id'); 
            $table->dropColumn('sub_service_id'); 
            $table->dropColumn('status'); 
            $table->integer('case_id')->after('associate_id');
            $table->integer('professional_id')->after('case_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_with_associates', function (Blueprint $table) {
            //
        });
    }
};
