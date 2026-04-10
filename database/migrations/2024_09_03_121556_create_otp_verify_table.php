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
        Schema::create('otp_verify', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->unique();  // Unique identifier for each record
            $table->string('email')->nullable()->index();  // Email address, indexed for faster searches
            $table->string('otp')->nullable();  // OTP code
            $table->dateTime('otp_expiry_time')->nullable();  // OTP expiry time
            $table->timestamps();  // Created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_verify');
    }
};
