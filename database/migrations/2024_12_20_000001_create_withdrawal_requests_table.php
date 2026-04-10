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
        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->unique();
            $table->unsignedBigInteger('user_id');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->unsignedBigInteger('banking_detail_id');
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->timestamp('request_date')->useCurrent();
            $table->timestamp('processed_date')->nullable();
            $table->string('file_upload')->nullable();
            $table->text('description')->nullable();
            $table->text('admin_notes')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('banking_detail_id')->references('id')->on('user_banking_details')->onDelete('cascade');
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes for better performance
            $table->index(['user_id', 'status']);
            $table->index('unique_id');
            $table->index('status');
            $table->index('request_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawal_requests');
    }
}; 