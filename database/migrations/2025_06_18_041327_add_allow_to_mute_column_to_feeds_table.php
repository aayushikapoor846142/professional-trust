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
        Schema::table('feeds', function (Blueprint $table) {
            $table->integer('allow_to_mute')->after('allow_to_repost')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feeds', function (Blueprint $table) {
            $table->dropColumn('allow_to_mute');
        });
    }
};
