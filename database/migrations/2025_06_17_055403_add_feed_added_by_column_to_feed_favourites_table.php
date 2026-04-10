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
        Schema::table('feed_favourites', function (Blueprint $table) {
            $table->integer('feed_added_by')->nullable()->after('feed_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feed_favourites', function (Blueprint $table) {
            $table->dropColumn('feed_added_by');
        });
    }
};
