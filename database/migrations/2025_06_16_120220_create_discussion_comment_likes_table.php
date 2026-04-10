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
        Schema::create('discussion_comment_likes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('discussion_board_id')->default(0);
            $table->bigInteger('comment_id')->default(0);
            $table->string('comment_icon')->nullable();
            $table->integer('user_id')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discussion_comment_likes');
    }
};
