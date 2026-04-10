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
        Schema::create('professional_case_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->default(0);
            $table->integer('user_id')->default(0)->comment('request send by');
            $table->integer('case_id')->default(0);
            $table->integer('form_id')->default(0);
            $table->string('title')->nullable();
            $table->string('attachment')->nullable();
            $table->string('status')->default('pending');
            $table->string('request_type');
            $table->longText('message_body')->nullable();
            $table->integer('completed_by')->default(0);
            $table->date('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_case_requests');
    }
};
