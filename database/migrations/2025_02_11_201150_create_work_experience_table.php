<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('work_experience', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_info_id')->constrained('personal_info')->onDelete('cascade');
            $table->date('from');
            $table->date('to');
            $table->string('position_title');
            $table->string('department');
            $table->decimal('monthly_salary', 10, 2);
            $table->string('salary_grade')->nullable();
            $table->string('step_increment')->nullable();
            $table->string('appointment_status');
            $table->enum('govt_service', ['Yes', 'No']);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('work_experience');
    }
};
