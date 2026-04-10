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
        Schema::create('support_amounts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->unique(); // Adding unique_id as a unique string column
            $table->bigInteger('status')->default(0);
            $table->string('amount')->default(0);
            $table->bigInteger('added_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_amounts');
    }

};
