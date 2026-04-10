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
        Schema::table('report_profiles', function (Blueprint $table) {
            $table->integer('professional_id')->default(0);
            $table->string('subject')->nullable();
            $table->string('reason')->nullable();
            $table->string('evidences')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report_profiles', function (Blueprint $table) {
            $table->dropColumn('professional_id'); // Drop the field in case of rollback
            $table->dropColumn('subject'); // Drop the field in case of rollback
            $table->dropColumn('reason'); // Drop the field in case of rollback
            $table->dropColumn('evidences'); // Drop the field in case of rollback
        });
    }
};
