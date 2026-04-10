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
        Schema::table('case_documents', function (Blueprint $table) {
            $table->integer('is_encrypted')->default(0);
            $table->integer('sort_order')->default(0);
            $table->integer('case_encrypted_documents_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_documents', function (Blueprint $table) {
            $table->dropColumn('is_encrypted');
            $table->dropColumn('sort_order');
            $table->integer('case_encrypted_documents_id')->default(0);
        });
    }
};
