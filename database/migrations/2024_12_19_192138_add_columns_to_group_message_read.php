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
        Schema::table('group_message_read', function (Blueprint $table) {
            $table->string('status')->default('unread');
            $table->dropColumn('read_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_message_read', function (Blueprint $table) {
            $table->dateTime('read_at')->nullable();
            $table->dropColumn('status');
        });
    }
};
