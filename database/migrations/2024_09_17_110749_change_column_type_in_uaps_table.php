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
        Schema::table('unauthorised_professionals', function (Blueprint $table) {
            $table->longText('first_name')->nullable()->change();
            $table->longText('last_name')->nullable()->change();
            $table->longText('email')->nullable()->change();
        });

        Schema::table('individual_uaps', function (Blueprint $table) {
            $table->longText('first_name')->nullable()->change();
            $table->longText('last_name')->nullable()->change();
            $table->longText('phone_no')->nullable()->change();
            $table->longText('address')->nullable()->change();
            $table->longText('country')->nullable()->change();  
            $table->longText('why_uap')->nullable()->change();  
            $table->longText('evidences')->nullable()->change();
        }); 

        Schema::table('corporate_uaps', function (Blueprint $table) {
            $table->longText('company_name')->nullable()->change();
            $table->longText('owner_name')->nullable()->change();
            $table->longText('phone_no')->nullable()->change();
            $table->longText('address')->nullable()->change();
            $table->longText('evidences')->nullable()->change();
            $table->longText('country')->nullable()->change();  
            $table->longText('why_uap')->nullable()->change();  
        }); 

        Schema::table('social_medium_uaps', function (Blueprint $table) {
            $table->longText('social_link')->nullable()->change();
            $table->longText('comments')->nullable()->change();
        }); 
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unauthorised_professionals', function (Blueprint $table) {
            //
        });
    }
};
