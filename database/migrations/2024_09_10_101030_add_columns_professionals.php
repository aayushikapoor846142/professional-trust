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
            //
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('pin_code')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            //
            $table->dropColumn('address_line_1'); // Drop the field in case of rollback
            $table->dropColumn('address_line_2'); // Drop the field in case of rollback
            $table->dropColumn('pin_code'); // Drop the field in case of rollback

        });
    }
};
