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
        Schema::table('individual_uaps', function (Blueprint $table) {
            $table->dropColumn('evidences');
            $table->dropColumn('suggestion');
        });
        Schema::table('corporate_uaps', function (Blueprint $table) {
            $table->dropColumn('evidences');
            $table->dropColumn('suggestion');
        });
        Schema::table('social_media_uaps', function (Blueprint $table) {
            $table->dropColumn('evidences');
            $table->dropColumn('suggestion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
