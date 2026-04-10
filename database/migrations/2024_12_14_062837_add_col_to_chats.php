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
        Schema::table('chats', function (Blueprint $table) {
            $table->integer('chat_request_id')->default(0);
            $table->integer('user1_typing')->default(0);
            $table->integer('user2_typing')->default(0);
            $table->integer('user1_id')->default(0);
            $table->integer('user2_id')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropColumn('chat_request_id');
            $table->dropColumn('user2_typing');
            $table->dropColumn('user1_typing');
            $table->dropColumn('user1_id');
            $table->dropColumn('user2_id');
        });
    }
};
