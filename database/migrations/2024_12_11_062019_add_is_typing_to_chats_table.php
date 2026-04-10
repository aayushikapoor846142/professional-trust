<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */public function up()
        {
            Schema::table('chats', function (Blueprint $table) {
                $table->boolean('is_typing')->default(false); // Column for typing status
                $table->unsignedBigInteger('typing_by')->nullable(); // Track the user who is typing
            });
        }

        public function down()
        {
            Schema::table('chats', function (Blueprint $table) {
                $table->dropColumn(['is_typing', 'typing_by']);
            });
        }

};
