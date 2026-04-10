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
        Schema::create('our_initiatives', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id'); 
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->string('short_title')->nullable();
            $table->longText('description')->nullable();
            $table->string('image')->nullable();
            $table->string('explore_content_link')->nullable();
            $table->string('report_content_link')->nullable();
            $table->integer('added_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('our_initiatives');
    }
};
