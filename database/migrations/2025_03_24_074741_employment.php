<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('employment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personalID')->constrained('personal_info')->onDelete('cascade');
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->foreignId('designation_id')->constrained('designations')->onDelete('cascade'); // NEW
            $table->integer('stepIncrement');
            $table->integer('salaryGrade');
            $table->date('date_hired')->nullable(); // Add date_hired column
            $table->date('dateOfEffectivity')->nullable(); // Add dateOfEffectivity column
            $table->date('dateReleased')->nullable(); // Add dateReleased column
            $table->string('updatedBy');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('employment');
    }
};
