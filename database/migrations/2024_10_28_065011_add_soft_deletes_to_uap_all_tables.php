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
        Schema::table('uap_all_tables', function (Blueprint $table) {
            $tables = [
                'category_levels',
                'levels',
                'level_tags',
                'uap_comments',
                'uap_evidences',
                'uap_level_tags',
                'uap_professionals',
                'uap_professional_sites',
                'uap_sites_screenshots',
                'uap_statuses',
                'evidence_comments',
                'reference_users'
            ];
    
            foreach ($tables as $table) {
                Schema::table($table, function (Blueprint $table) {
                    if (!Schema::hasColumn($table->getTable(), 'deleted_at')) {
                        $table->softDeletes();
                    }
                });
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uap_all_tables', function (Blueprint $table) {
            $tables = [
                'category_levels',
                'levels',
                'level_tags',
                'uap_comments',
                'uap_evidences',
                'uap_level_tags',
                'uap_professionals',
                'uap_professional_sites',
                'uap_sites_screenshots',
                'uap_statuses',
                'evidence_comments',
                'reference_users'
            ];
    
            foreach ($tables as $table) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        });
    }
};
