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
        Schema::create('chat_invitations', function (Blueprint $table) {
            $table->id(); 
            $table->bigInteger('unique_id')->default(0);
            $table->string('email');
            $table->string('token')->unique(); 
            $table->integer('status')->default(0)->comment('0 for pending, 1 for accepted');
            $table->integer('added_by')->default(0); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_invitations');
    }
};
