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
        Schema::create('immigration_services', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->unique();
            $table->integer('added_by')->default(0);
            $table->string('name', 255)->nullable();
            $table->string('slug', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('price', 255)->nullable();
            $table->integer('amount_to_pay')->default(0);
            $table->integer('parent_service_id')->default(0);
            $table->string('image', 255)->nullable();
            $table->string('assessment_id', 255)->nullable();
            $table->boolean('show_on_home')->default(0);
            $table->boolean('specialise_service')->default(0);
            $table->text('hide_from_header_menu')->nullable();
            $table->text('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('keywords')->nullable();
            $table->longText('tags')->nullable();
            $table->text('featured_service')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('immigration_services');
    }
};
