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
        Schema::create('other_uap_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->default(0);
            $table->bigInteger('uap_id')->default(0);
            $table->string('meta_key', 255)->nullable();
            $table->text('meta_value')->nullable();
            $table->bigInteger('added_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('other_uap_details');
    }
};
