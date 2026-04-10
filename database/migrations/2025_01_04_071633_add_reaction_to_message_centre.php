<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::create('message_centre_reactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->default(0);
            $table->integer('message_id');
            $table->string('reaction'); 
            $table->integer('added_by')->default(0); 
            $table->timestamps();
        });
        Schema::table('chat_messages', function (Blueprint $table) {
            if (Schema::hasColumn('chat_messages', 'reaction')) {
                $table->dropColumn('reaction');
            }
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_centre_reactions');
    }
};
