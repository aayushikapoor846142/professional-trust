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
        Schema::create('professional_agreement_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agreement_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('parent_id')->nullable(); // For replies
            $table->text('comment');
            $table->enum('status', ['active', 'inactive', 'deleted'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('agreement_id')->references('id')->on('professional_associate_agreements')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('professional_agreement_comments')->onDelete('cascade');

            // Indexes
            $table->index(['agreement_id', 'status']);
            $table->index(['parent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_agreement_comments');
    }
};
