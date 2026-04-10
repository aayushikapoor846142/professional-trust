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
        Schema::create('uap_professionals', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->unique(); // Adding unique_id as a unique string column
            $table->string('name');
            $table->string('owner_name')->nullable();
            $table->longText('contact')->nullable();
            $table->longText('address')->nullable();
            $table->longText('email')->nullable();
            $table->longText('alternate_email')->nullable();
            $table->longText('social_media')->nullable();
            $table->longText('website_link')->nullable();
            $table->string('country', 255)->nullable();
            $table->string('city', 255)->nullable();
            $table->string('state', 255)->nullable();
            $table->string('is_publish', 255)->default('pending');
            $table->string('status', 255)->default('pending')->comment('default pending, after reference select in_progress');
            $table->string('invitation_status', 255)->nullable()->comment('default null,after reference pending,after accept accepted ');
            $table->bigInteger('reference_user_id')->default(0);
            $table->string('token')->nullable();
            $table->date('inviation_accept_date')->nullable();
            $table->bigInteger('added_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uap_professionals');
    }
};
