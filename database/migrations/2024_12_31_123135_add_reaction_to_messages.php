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
        Schema::table('group_messages', function (Blueprint $table) {
            $table->string('reaction')->nullable();
        });
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->string('reaction')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_messages', function (Blueprint $table) {
            $table->dropColumn('reaction');
        });
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn('reaction');
        });
    }
};
