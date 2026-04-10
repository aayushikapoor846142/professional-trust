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
        Schema::create('professionals', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->default(0);
            $table->string('view_profile_url')->nullable();
            $table->unsignedBigInteger('college_id')->default(0);
            $table->string('name')->nullable();
            $table->string('company')->nullable();
            $table->string('company_type')->nullable();
            $table->string('entitled_to_practise')->nullable();
            $table->boolean('entitled_to_practis_college_id')->default(0);
            $table->string('type')->nullable();
            $table->text('suspension_revocation_history')->nullable();
            $table->string('employment_company')->nullable();
            $table->string('employment_startdate')->nullable();
            $table->string('employment_country')->nullable();
            $table->string('employment_state')->nullable();
            $table->string('employment_city')->nullable();
            $table->string('employment_email')->nullable();
            $table->string('employment_phone')->nullable();
            $table->text('agentsinfo')->nullable();
            $table->string('license_historyclass')->nullable();
            $table->string('license_historystartdate')->nullable();
            $table->string('license_historyexpiry_date')->nullable();
            $table->string('license_history_status')->nullable();
            $table->bigInteger('added_by')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professionals');
    }
};
