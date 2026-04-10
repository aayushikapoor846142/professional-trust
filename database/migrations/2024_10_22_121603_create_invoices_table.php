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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->unique();
            $table->integer('invoice_number')->default(0);
            $table->string('tax')->nullable();
            $table->string('sub_total')->nullable();
            $table->string('total_amount')->nullable();
            $table->string('currency')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_no')->nullable();
            $table->string('invoice_type')->nullable();
            $table->integer('transaction_id')->default(0);
            $table->integer('reference_id')->default(0);
            $table->string('payment_status')->default('pending');
            $table->date("paid_date")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
