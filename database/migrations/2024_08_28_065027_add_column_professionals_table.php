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
        Schema::table('professionals', function (Blueprint $table) {
            $table->integer('assigned_to')->default(0);
            $table->integer('is_linked')->default(0);
            $table->string('linked_user_id')->nullabel();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            $table->dropColumn('assigned_to'); // Drop the field in case of rollback
            $table->dropColumn('is_linked'); // Drop the field in case of rollback
            $table->dropColumn('linked_user_id'); // Drop the field in case of rollback
        });
    }
};
