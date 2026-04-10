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
        Schema::create('send_forms', function (Blueprint $table) {
            $table->id();
            $table->integer('registered_user')->default(0);
            $table->integer('user_id')->default(0);
            $table->integer('form_id')->default(0);
            $table->string('form_type')->nullable();
            $table->text('form_fields_json')->nullable();
            $table->string('form_name')->nullable();
            $table->string('uuid');
            $table->string('email');
            $table->string('status')->default('pending');
            $table->integer('added_by')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('send_forms');
    }
};
