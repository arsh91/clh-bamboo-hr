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
        Schema::create('blank_field_data', function (Blueprint $table) {
            $table->id();
            $table->integer('emp_table_id')->nullable();
            $table->integer('emp_id')->nullable();
            $table->string('field_name')->nullable();
            $table->string('tab')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blank_field_data');
    }
};
