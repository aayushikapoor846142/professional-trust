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
        Schema::table('home_settings', function (Blueprint $table) {
            
            $table->string('uap_sec_title_1')->nullable();
            $table->string('uap_sec_title_2')->nullable();
            $table->string('uap_sec_title_3')->nullable();
            $table->string('uap_sec_img_1')->nullable();
            $table->string('uap_sec_img_2')->nullable();
            $table->string('uap_sec_img_3')->nullable();
            $table->longText('uap_sec_desc_1')->nullable();
            $table->longText('uap_sec_desc_2')->nullable();
            $table->longText('uap_sec_desc_3')->nullable();
            $table->string('uap_sec_main_title')->nullable();
            $table->string('uap_sec_main_desc')->nullable();

            

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            $table->dropColumn('uap_sec_title_1');
            $table->dropColumn('uap_sec_title_2');
            $table->dropColumn('uap_sec_title_3');
            $table->dropColumn('uap_sec_desc_1');
            $table->dropColumn('uap_sec_desc_2');
            $table->dropColumn('uap_sec_desc_3');
            $table->dropColumn('uap_sec_img_1');
            $table->dropColumn('uap_sec_img_2');
            $table->dropColumn('uap_sec_img_3');
            $table->dropColumn('uap_sec_main_title');
            $table->dropColumn('uap_sec_main_desc');
        });
    }

};
