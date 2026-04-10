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
        Schema::create('visa_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unique_id')->unique();
            $table->integer('parent_id')->default(0);
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->decimal('assessment_price', 11, 2)->default(0.00);
            $table->string('document_folders')->nullable();
            $table->integer('cv_type')->default(0);
            $table->string('eligible_type')->nullable();
            $table->bigInteger('added_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_services');
    }
};
