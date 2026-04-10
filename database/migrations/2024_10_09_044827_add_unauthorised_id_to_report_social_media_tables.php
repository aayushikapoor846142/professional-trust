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
        Schema::table('report_social_media_content', function (Blueprint $table) {
            $table->integer('unauthorised_id')->nullable();
        });

        Schema::table('report_social_media_group', function (Blueprint $table) {
            $table->integer('unauthorised_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('report_social_media_content', function (Blueprint $table) {
            $table->dropColumn('unauthorised_id');
        });

        Schema::table('report_social_media_group', function (Blueprint $table) {
            $table->dropColumn('unauthorised_id');
        });
    }
};
