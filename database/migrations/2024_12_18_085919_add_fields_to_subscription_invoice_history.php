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
        Schema::table('subscription_invoice_history', function (Blueprint $table) {
            $table->date('next_invoice_date')->nullable(); 
            $table->string('stripe_invoice_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_invoice_history', function (Blueprint $table) {
            $table->dropColumn(['next_invoice_date', 'stripe_invoice_status']);
        });
    }
};
