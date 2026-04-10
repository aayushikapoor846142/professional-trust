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
        Schema::create('uap_notification_mails', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->unique();
            $table->integer('uap_id');
            $table->date('mail_sent_on');
            $table->longText('mail_content');
            $table->string('mail_status')->default('sent');
            $table->string('mail_response')->nullable();
            $table->string('mail_tracking')->nullable();
            $table->date('next_mail_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uap_notification_mails');
    }
};
