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
        Schema::create('professional_complaints', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->unsigned();
            $table->string('info_anonymous')->nullable();
            $table->string('name')->nullable();
            $table->string('licensed_individual')->nullable();
            $table->text('company_address')->nullable();
            $table->text('website_url')->nullable();
            $table->string('facebook_page')->nullable();
            $table->text('twitter_page')->nullable();
            $table->text('instagram_gram')->nullable();
            $table->text('service_provided')->nullable();
            $table->string('report_to_local_authority')->nullable();
            $table->string('individual_name')->nullable();
            $table->text('submit_evidence')->nullable();
            $table->bigInteger('added_by')->unsigned();
            $table->integer('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_complaints');
    }
};
