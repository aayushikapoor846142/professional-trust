<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->default(0);
            $table->integer('added_by');
            $table->integer('blocked_chat')->nullable();
            $table->integer('blocked_by')->nullable();
            $table->enum('chat_type', ['individual', 'case'])->nullable();
            $table->integer('reference_id')->default(0)->comment('id of job if chat_type = case');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chats');

    }
};
