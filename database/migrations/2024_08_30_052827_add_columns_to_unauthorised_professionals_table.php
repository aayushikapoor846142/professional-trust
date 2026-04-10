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
        Schema::table('unauthorised_professionals', function (Blueprint $table) {
            $table->string('status')->default('pending');
            $table->integer('status_updated_by')->default(0);
            $table->date('status_updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unauthorised_professionals', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('status_updated_by');
            $table->dropColumn('status_updated_at');
        });
    }
};
