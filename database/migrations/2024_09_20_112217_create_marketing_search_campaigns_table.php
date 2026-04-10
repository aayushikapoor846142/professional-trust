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
        Schema::create('marketing_search_campaigns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unique_id')->unique()->nullable();
            $table->text('url')->nullable();
            $table->text('utm_source')->nullable();
            $table->text('utm_medium')->nullable();
            $table->text('utm_campaign')->nullable();
            $table->text('utm_terms')->nullable();
            $table->text('marketing_search_terms')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_search_campaigns');
    }
};
