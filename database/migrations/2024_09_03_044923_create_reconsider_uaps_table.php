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
        Schema::create('reconsider_uaps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unique_id')->unique();
            $table->string('subject')->nullable();
            $table->text('reason')->nullable();
            $table->bigInteger('added_by')->nullable();
            $table->string('evidences')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reconsider_uaps');
    }
};
