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
        Schema::create('immigration_types', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->unique();
            $table->bigInteger('added_by')->default(0);
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->text('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('immigration_types');
    }
};
