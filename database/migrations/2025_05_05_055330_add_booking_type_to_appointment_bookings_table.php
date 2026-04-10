<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('appointment_booking', function (Blueprint $table) {
            $table->enum('booking_type', ['general', 'booking_flow'])->default('general')->after('id');
        });
    }

    public function down()
    {
        Schema::table('appointment_booking', function (Blueprint $table) {
            $table->dropColumn('booking_type');
        });
    }

};
