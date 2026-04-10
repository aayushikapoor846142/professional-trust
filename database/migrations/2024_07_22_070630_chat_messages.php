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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->integer('chat_id');
            $table->longText('message');
            $table->string('attachment')->nullable();
            $table->integer('parent_id')->default(0)->comment('this will be used if we need to reply to particular message');;
            $table->string('sent_by');
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
        Schema::dropIfExists('chat_messages');

    }

};
