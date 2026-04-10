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
        Schema::create('predefined_case_sub_stages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->default(0);
            $table->integer('predefined_case_stage_id');
            $table->integer('user_id')->comment('added user id');
            $table->string('name');
            $table->string('stage_type');
            $table->integer('type_id')->nullable();
            $table->text('case_documents')->nullable();
            $table->text('form_reply')->nullable();
            $table->string('status')->default('pending');
            $table->integer('sort_order')->defualt(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predefined_case_sub_stages');
    }
};
