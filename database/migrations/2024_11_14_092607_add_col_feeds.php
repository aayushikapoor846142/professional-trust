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
            $table->string('is_repost')->nullable()->default(0);
            $table->string('post_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feeds', function (Blueprint $table) {
            $table->dropColumn('is_repost');
            $table->dropColumn('post_id');

        });
    }
};
