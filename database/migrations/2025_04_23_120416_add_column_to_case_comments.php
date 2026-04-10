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
        Schema::table('case_comments', function (Blueprint $table) {
            $table->string('sub_service_type_id')->comment('SUb service type table id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_comments', function (Blueprint $table) {
            $table->dropColumn('sub_service_type_id');
        });
    }
};
