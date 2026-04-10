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
            $table->longText('city')->nullable();
            $table->longText('state')->nullable();
            $table->longText('suggestion')->nullable();
        });

        Schema::table('corporate_uaps', function (Blueprint $table) {
            $table->dropColumn('owner_name');
            $table->longText('first_name')->nullable();
            $table->longText('last_name')->nullable();
            $table->longText('city')->nullable();
            $table->longText('state')->nullable();
            $table->longText('suggestion')->nullable();
        });

        Schema::rename('social_medium_uaps', 'social_media_uaps');

        Schema::table('social_media_uaps', function (Blueprint $table) {
            $table->longText('evidences')->nullable();
            $table->longText('suggestion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('individual_uaps', function (Blueprint $table) {
            $table->dropColumn('city');
            $table->dropColumn('state');
            $table->dropColumn('suggestion');
        });

        Schema::table('corporate_uaps', function (Blueprint $table) {
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('city');
            $table->dropColumn('state');
            $table->dropColumn('suggestion');
        });
        Schema::rename('social_media_uaps', to: 'social_medium_uaps');
        Schema::table('social_medium_uaps', function (Blueprint $table) {
            $table->dropColumn('evidences');
            $table->dropColumn('suggestion');
        });
    }
};
