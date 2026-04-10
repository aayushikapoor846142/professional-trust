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
        Schema::create('case_sub_stages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->default(0);
            $table->integer('case_id')->comment('case id form case with professionals');
            $table->integer('stage_id');
            $table->integer('user_id')->comment('added user id');
            $table->integer('client_id')->comment('cases client id');
            $table->string('name');
            $table->string('stage_type');
            $table->integer('type_id')->nullable();
            $table->text('case_documents')->nullable();
            $table->text('form_reply')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_sub_stages');
    }
};
