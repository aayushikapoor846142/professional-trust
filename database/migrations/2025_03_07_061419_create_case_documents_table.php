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
        Schema::create('case_documents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('unique_id')->default(0);
            $table->integer('case_id')->comment('case id form case with professionals');
            $table->integer('folder_id')->comment('default folder form document folder,extra form case folder');
            $table->integer('user_id');
            $table->string('document_type');
            $table->string('file_name');
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
        Schema::dropIfExists('case_documents');
    }
};
