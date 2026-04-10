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
        Schema::table('case_retain_agreements', function (Blueprint $table) {
            $table->string('signature_type', 255)->nullable(false)->after('agreement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_retain_agreements', function (Blueprint $table) {
             $table->dropColumn('signature_type');
        });
    }
};
