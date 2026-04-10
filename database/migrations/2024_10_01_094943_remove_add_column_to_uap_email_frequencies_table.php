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
        Schema::table('uap_email_frequencies', function (Blueprint $table) {
            $table->integer('mail_sequence')->nullable();
            $table->dropColumn('is_initial_mail');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uap_email_frequencies', function (Blueprint $table) {
        
        });
    }
};
