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
        Schema::create('cds_professional_companies', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->default(0);
            $table->integer('user_id')->default(0);
            $table->string('company_name')->nullable();
            $table->string('owner_type')->nullable();
            $table->string('company_type')->nullable();
            $table->integer('no_of_directors')->default(0);
            $table->string('position_in_company')->nullable();
            $table->date('incorporation_date')->nullable();
            $table->date('employment_start_date')->nullable();
            $table->date('employment_end_date')->nullable();
            $table->string('status')->nullable();
            $table->integer('currently_working')->default(0);
            $table->integer('is_claimed')->default(0);
            $table->integer('claimed_company_id')->default(0);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cds_professional_companies');
    }
};
