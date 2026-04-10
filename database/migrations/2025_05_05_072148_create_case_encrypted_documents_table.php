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
        Schema::create('case_encrypted_documents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->unique();
            $table->integer('case_id')->default(0);
            $table->string('folder_id')->default(0);
            $table->integer('no_of_files')->default(0);
            $table->text('password');
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_encrypted_documents');
    }
};
