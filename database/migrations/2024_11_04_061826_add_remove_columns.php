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
        Schema::table('payment_transactions', function (Blueprint $table) {
            // $table->dropColumn('first_name');
            // $table->dropColumn('last_name');
            // $table->dropColumn('address');
            // $table->dropColumn('city');
            // $table->dropColumn('state');
            // $table->dropColumn('zip');
            // $table->dropColumn('country');
            // $table->dropColumn('email');
            // $table->dropColumn('phone');
            // $table->dropColumn('other_details');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->longText('other_details')->nullable();
            $table->longText('address')->nullable();
            $table->longText('city')->nullable();
            $table->longText('state')->nullable();
            $table->longText('zip')->nullable();
            $table->longText('country')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
