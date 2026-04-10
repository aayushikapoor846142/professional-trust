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
        Schema::table('immigration_services', function (Blueprint $table) {
            // Drop columns that are no longer needed
            $table->dropColumn([
                'price',
                'amount_to_pay',
                'assessment_id',
                'hide_from_header_menu',
                'meta_title',
                'meta_description',
                'keywords',
                'featured_service',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('immigration_services', function (Blueprint $table) {
            // Re-add columns if needed to roll back the migration
            $table->string('price', 255)->nullable();
            $table->integer('amount_to_pay')->default(0);
            $table->string('assessment_id', 255)->nullable();
            $table->text('hide_from_header_menu')->nullable();
            $table->text('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('keywords')->nullable();
            $table->text('featured_service')->nullable();
        });
    }
};
