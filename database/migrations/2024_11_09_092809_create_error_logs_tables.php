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
        Schema::create('error_logs', function (Blueprint $table) {
            $table->id();
            $table->string('page_url');
            $table->text('error_message');
            $table->text('error_code')->nullable();
            $table->text('user_ip_address')->nullable();
            $table->text('user_location')->nullable();
            $table->integer('user_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('error_logs');
    }

};
