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
        Schema::create('social_media_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unique_id')->unique();
            $table->string('type')->nullable();
            $table->longText('first_name')->nullable();
            $table->longText('last_name')->nullable();
            $table->longText('email')->nullable();
            $table->string('status')->default('pending');
            $table->integer('status_updated_by')->default(0);
            $table->date('status_updated_at')->nullable();
            $table->string('level')->nullable();
            $table->string('reference_token')->nullable();
            $table->string('submitted_from')->nullable();
            $table->string('sid')->nullable();
            $table->longText('evidences')->nullable();
            $table->longText('suggestion')->nullable();
            $table->integer('mark_as_unauthorized')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_media_reports');
    }
};
