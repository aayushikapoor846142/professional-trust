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
        Schema::create('knowledge_bases', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->unique();
            $table->string('title');
            $table->longText('description');
            $table->text('link');
            $table->integer('category_id')->default(0);
            $table->integer('sort_order')->default(0);
            $table->integer('pin')->default(0);
            $table->integer('added_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_bases');
    }
};
