<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('personal_info', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('name_extension')->nullable();
            $table->date('date_of_birth');
            $table->string('place_of_birth');
            $table->enum('sex', ['Male', 'Female']);
            $table->enum('civil_status', ['Single', 'Married', 'Widowed', 'Separated', 'Other']);
            $table->decimal('height', 5, 2);
            $table->decimal('weight', 5, 2);
            $table->string('blood_type')->nullable();
            $table->string('gsis_id')->nullable();
            $table->string('pagibig_id')->nullable();
            $table->string('philhealth_id')->nullable();
            $table->string('sss_no')->nullable();
            $table->string('tin_no')->nullable();
            $table->string('agency_employee_no')->nullable();
            $table->string('telephone_no')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('email')->nullable();

            // Citizenship-related columns
            $table->boolean('is_filipino')->default(false); // True if Filipino
            $table->boolean('is_dual_citizen')->default(false); // True if dual citizen
            $table->string('dual_citizen_type')->nullable(); // "byBirth" or "byNaturalization"
            $table->string('dual_citizen_country')->nullable(); // Country of dual citizenship

            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('personal_info');
    }
};
