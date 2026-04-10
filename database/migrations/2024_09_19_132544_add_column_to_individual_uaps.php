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
        Schema::table('individual_uaps', function (Blueprint $table) {
            $table->longText('google_review')->nullable();
            $table->longText('website')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('individual_uaps', function (Blueprint $table) {
            $table->dropColumn('google_review');
            $table->dropColumn('website');
        });
    }
};
