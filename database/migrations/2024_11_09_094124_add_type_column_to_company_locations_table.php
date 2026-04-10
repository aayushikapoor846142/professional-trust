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
        Schema::table('company_locations', function (Blueprint $table) {
            $table->string('type_label')->nullable();
            $table->integer('is_primary')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_locations', function (Blueprint $table) {
            $table->dropColumn('type_label');
            $table->dropColumn('is_primary');
        });
    }
};
