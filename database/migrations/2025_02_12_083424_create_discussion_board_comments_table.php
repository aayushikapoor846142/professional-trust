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
        Schema::create('discussion_board_comments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->default(0);
            $table->integer('discussion_boards_id')->default(0);
            $table->text('comments')->nullable();
            $table->string('files')->nullable();
            $table->integer('added_by')->default(0); 
            $table->integer('reply_to')->nullable()->default(0); 
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discussion_board_comments');
    }
};
