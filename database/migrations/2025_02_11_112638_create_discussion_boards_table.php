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
        Schema::create('discussion_boards', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->default(0);
            $table->integer('category_id')->default(0);
            $table->string('topic_title')->nullable();
            $table->longText('description')->nullable();
            $table->integer('added_by')->default(0); 
            $table->integer('status')->default(0)->comment('0 Inactive, Active');
            $table->string('files')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discussion_boards');
    }
};
