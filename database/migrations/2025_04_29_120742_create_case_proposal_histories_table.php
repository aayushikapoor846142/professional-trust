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
        Schema::create('case_proposal_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id'); 
            $table->integer('case_comment_id')->nullable();
            $table->integer('case_quotation_id')->comment('case_quotations table id');
            $table->integer('case_id')->nullable();
            $table->integer('added_by')->comment('professional id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_proposal_histories');
    }
};
