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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->unique();
            $table->string('quotation_title')->default(0);
            $table->integer('service_id')->default(0);
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
        Schema::dropIfExists('quotations');
    }
};
