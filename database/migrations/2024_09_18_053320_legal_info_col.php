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
        //professional_id
        Schema::table('legal_info', function (Blueprint $table) {
            $table->integer('professional_id')->default(0);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('legal_info', function (Blueprint $table) {
            $table->dropColumn('professional_id');
        });

        //
    }
};
