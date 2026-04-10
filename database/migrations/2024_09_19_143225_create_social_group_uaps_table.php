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
        Schema::create('social_group_uaps', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->unique();
            $table->integer('unauthorised_id')->nullable();
            $table->longText('social_media_groups')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_group_uaps');
    }
};
