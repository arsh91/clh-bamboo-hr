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
        Schema::create('employee_data', function (Blueprint $table) {
            $table->id();
            $table->integer('emp_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('department')->nullable();
            $table->string('job_title')->nullable();
            $table->string('division')->nullable();
            $table->integer('empty_job_field')->nullable();
            $table->integer('empty_personal_field')->nullable();
            $table->integer('empty_emergency_field')->nullable();
            $table->integer('expire_date')->nullable();
            $table->integer('going_to_expire')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_data');
    }
};
