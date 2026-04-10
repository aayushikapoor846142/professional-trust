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
        Schema::table('appointment_booking_flow', function (Blueprint $table) {
            $table->unsignedBigInteger('professional_id')->nullable()->after('id');
        });

        Schema::table('appointment_types', function (Blueprint $table) {
            $table->unsignedBigInteger('added_by')->nullable()->after('id');
        });

        Schema::table('time_duration', function (Blueprint $table) {
            $table->unsignedBigInteger('added_by')->nullable()->after('id');
        });
    }

    public function down()
    {
        Schema::table('appointment_booking_flow', function (Blueprint $table) {
            $table->dropColumn('professional_id');
        });

        Schema::table('appointment_types', function (Blueprint $table) {
            $table->dropColumn('added_by');
        });

        Schema::table('time_duration', function (Blueprint $table) {
            $table->dropColumn('added_by');
        });
    }

};
