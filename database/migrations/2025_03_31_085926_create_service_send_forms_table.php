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
        Schema::create('service_send_forms', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id');
            $table->integer('user_id')->default(0);
            $table->integer('form_id')->comment('forms table id');
            $table->integer('service_form_id')->comment('service assesment forms table id');
            $table->string('form_type')->nullable();
            $table->text('form_fields_json')->nullable();
            $table->string('form_name')->nullable();
            $table->string('email');
            $table->string('status')->default('pending');
            $table->integer('added_by')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_send_forms');
    }
};
