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
        Schema::table('discussion_board_comments', function (Blueprint $table) {
            $table->integer('mark_as_answer')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discussion_board_comments', function (Blueprint $table) {
            $table->dropColumn('mark_as_answer');
        });
    }
};
