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
        Schema::create('company_complaints', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->default(0);
            $table->string('info_anonymous')->nullable();
            $table->string('company_name')->nullable();
            $table->text('company_address')->nullable();
            $table->string('website_url', 500)->nullable();
            $table->string('facebook_page')->nullable();
            $table->text('twitter_page')->nullable();
            $table->text('instagram_gram')->nullable();
            $table->text('service_provided')->nullable();
            $table->string('report_to_local_authority')->nullable();
            $table->string('individual_name')->nullable();
            $table->text('submit_evidence')->nullable();
            $table->integer('added_by')->default(0);
            $table->integer('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_complaints');
    }
};
