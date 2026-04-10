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
        Schema::create('case_quotations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->unique();
            $table->bigInteger('receipt_number')->default(0);
            $table->integer('case_id')->default(0);
            $table->integer('client_id')->default(0);
            $table->decimal('total_amount')->default(0);
            $table->integer('added_by')->default(0);
            $table->string('currency')->default('CAD');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_quotations');
    }
};
