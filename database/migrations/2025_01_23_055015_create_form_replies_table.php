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
        Schema::create('form_replies', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->unsigned();
            $table->integer('user_id')->default(0);
            $table->integer('form_id')->default(0);
            $table->string('form_uuid')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->text('field_reply')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_replies');
    }
};
