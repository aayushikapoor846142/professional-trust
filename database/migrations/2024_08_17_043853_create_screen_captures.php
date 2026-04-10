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
        Schema::create('screen_captures', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id');
            $table->string('name', 255)->default('0');
            $table->text('site_url')->nullable();
            $table->bigInteger('added_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('screen_captures');
    }
};
