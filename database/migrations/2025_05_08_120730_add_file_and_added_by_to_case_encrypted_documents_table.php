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
        Schema::table('case_encrypted_documents', function (Blueprint $table) {
            $table->integer('added_by')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_encrypted_documents', function (Blueprint $table) {
            $table->dropColumn('added_by');
        });
    }
};
