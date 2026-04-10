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
        Schema::create('uap_emails', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id');
            $table->string('token');
            $table->string('subject');
            $table->string('email');
            $table->integer('submited_profiles')->default(0);
            $table->integer('added_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uap_emails');
    }
};
