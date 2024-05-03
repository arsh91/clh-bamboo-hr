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
        Schema::create('document_data', function (Blueprint $table) {
            $table->id();
            $table->integer('emp_table_id')->nullable();
            $table->integer('emp_id')->nullable();
            $table->integer('doc_id')->nullable();
            $table->string('doc_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_data');
    }
};
