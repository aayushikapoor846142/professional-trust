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
        Schema::create('seo_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->unique();
            $table->string('module_type')->nullable();
            $table->string('page_route')->nullable();  
            $table->integer('reference_id')->nullable();
            $table->string('meta_title')->nullable()->nullable();
            $table->longText('meta_keywords')->nullable();
            $table->longText('meta_description')->nullable();
            $table->integer('added_by'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_details');
    }
};
