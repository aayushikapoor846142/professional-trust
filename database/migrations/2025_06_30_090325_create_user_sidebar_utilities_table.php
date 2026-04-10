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
        Schema::create('user_sidebar_utilities', function (Blueprint $table) {
        $table->id();
         $table->bigInteger('unique_id')->default(0);
        $table->longText('user_id')->default(0);
          $table->integer('status')->default(0)->comment('0 = sidebar closed, 1 = sidebar open');
        $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sidebar_utilities');
    }
};
