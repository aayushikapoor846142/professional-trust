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
            
    
            $table->string('counter_title')->nullable();
            $table->string('counter_desc')->nullable();
             $table->string('uap_sec_sub_title_1')->nullable();
             $table->string('uap_sec_sub_title_2')->nullable();
             $table->string('uap_sec_sub_title_3')->nullable();
     
            

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
         
            $table->dropColumn('counter_title');
            $table->dropColumn('counter_desc');
            $table->dropColumn('uap_sec_sub_title_1');
            $table->dropColumn('uap_sec_sub_title_2');
            $table->dropColumn('uap_sec_sub_title_3');
            
        });
    }

};
