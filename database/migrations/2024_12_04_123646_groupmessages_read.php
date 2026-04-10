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
       Schema::create('group_message_read', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->unique(); 
            $table->integer('group_message_id');
            $table->integer('user_id');
            $table->timestamp('read_at')->nullable();
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
        Schema::dropIfExists('group_message_read');

    }

};
