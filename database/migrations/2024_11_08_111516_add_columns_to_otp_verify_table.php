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
        Schema::table('otp_verify', function (Blueprint $table) {
            $table->integer('attempt')->default(0);
            $table->integer('resend_attempt')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('otp_verify', function (Blueprint $table) {
            $table->dropColumn('attempt');
            $table->dropColumn('resend_attempt');
        });
    }
};
