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
        Schema::create('user_banking_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->unique();
            $table->unsignedBigInteger('user_id');
            $table->string('bank_name');
            $table->string('account_holder_name');
            $table->string('account_number');
            $table->string('routing_number')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('iban')->nullable();
            $table->text('bank_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('zip_code')->nullable();
            $table->boolean('is_active')->default(false);
            $table->enum('account_type', ['savings', 'checking', 'business', 'other'])->default('savings');
            $table->string('currency', 3)->default('USD');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes for better performance
            $table->index(['user_id', 'is_active']);
            $table->index('unique_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_banking_details');
    }
}; 