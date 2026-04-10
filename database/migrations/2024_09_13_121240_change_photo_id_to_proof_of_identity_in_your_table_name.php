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
        Schema::table('professional_documents', function (Blueprint $table) {
            $table->dropColumn("photo_id");
            $table->string('proof_of_identity')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professional_documents', function (Blueprint $table) {
            $table->dropColumn("proof_of_identity");
            $table->string('photo_id')->nullable();
        });
    }
};
