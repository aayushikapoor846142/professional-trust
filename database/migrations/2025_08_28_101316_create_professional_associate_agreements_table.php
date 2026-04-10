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
        Schema::create('professional_associate_agreements', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id');
            $table->unsignedBigInteger('associate_id');
            $table->unsignedBigInteger('professional_id'); 
            $table->string('template_name');
            $table->string('agreement_id');
             $table->longText('original_agreement'); 
            $table->longText('agreement'); 
            $table->longText('pdf')->nullable(); 
            $table->string('platform_fees')->nullable(); 
            $table->string('sharing_fees')->nullable(); 
            $table->integer('is_support_accept')->default(0)->comment('0 pending,1 accept,2 reject'); 
            $table->integer('is_associate_accept')->default(0)->comment('0 pending,1 accept,2 reject'); 
            $table->integer('added_by');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_associate_agreements');
    }
};
