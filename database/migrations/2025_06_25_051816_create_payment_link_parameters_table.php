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
        Schema::create('payment_link_parameters', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->default(0);
            $table->longText('user_id')->default(0);
            $table->longText('token')->nullable();
            $table->longText('invoice_id')->default(0);
            $table->longText('transaction_id')->default(0);
            $table->integer('added_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_link_parameters');
    }
};
