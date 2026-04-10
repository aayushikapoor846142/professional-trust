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
        Schema::table('review_invitations', function (Blueprint $table) {
            $table->string('receiver_name')->after('email')->nullable();
            $table->text('message')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('review_invitations', function (Blueprint $table) {
            $table->dropColumn('receiver_name');
            $table->dropColumn('message');
        });
    }
};
