<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNOSATable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('NOSA', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->string('employeeName'); // Employee name
            $table->string('position'); // Position
            $table->string('department'); // Department
            $table->decimal('previousSalary', 10, 2); // Previous salary (decimal with 10 digits total, 2 decimal places)
            $table->decimal('newSalary', 10, 2); // New salary (decimal with 10 digits total, 2 decimal places)
            $table->date('dateOfEffectivity'); // Date of effectivity
            $table->date('dateReleased'); // Date released
            $table->integer('salaryGrade'); // Salary grade
            $table->integer('stepIncrement'); // Step increment
            $table->string('userName'); // User who created the record
            $table->timestamps(); // Timestamps (created_at and updated_at)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('NOSA');
    }
}