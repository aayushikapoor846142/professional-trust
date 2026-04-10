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
        Schema::create('claim_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unique_id')->unique();
            $table->integer('professional_id')->default(0);
            $table->string('proof_of_identity')->nullable();
            $table->string('incorporation_certificate')->nullable();
            $table->string('license')->nullable();
            $table->string('alternate_contact_name')->nullable();
            $table->string('primary_contact_number')->nullable();
            $table->string('registered_domain_name')->nullable();
            $table->string('registered_office_address')->nullable();
            $table->string('registered_mailing_address')->nullable();
            $table->string('status')->default('pending');
            $table->bigInteger('added_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claim_profiles');
    }
};
