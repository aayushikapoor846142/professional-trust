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
        Schema::create('predefined_case_stages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->default(0);
            $table->integer('user_id');
            $table->string('stage_type');
            $table->string('name');
            $table->longText('short_description');
            $table->decimal('fees')->default(0);
            $table->string('status')->default('pending');
            $table->string('sort_order')->default(0);
            $table->integer('added_by');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predefined_case_stages');
    }
};
