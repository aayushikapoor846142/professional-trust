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
        Schema::create('cds_license_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->default(0);
            $table->integer('license_id')->default(0);
            $table->string('history_class')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->nullable();
            $table->bigInteger('added_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cds_license_histories');
    }
};
