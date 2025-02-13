<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('civil_service_eligibility', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_info_id')->constrained('personal_info')->onDelete('cascade');
            $table->string('eligibility_name');
            $table->string('rating')->nullable();
            $table->date('date_of_exam');
            $table->string('place_of_exam');
            $table->string('license_number')->nullable();
            $table->date('license_validity')->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('civil_service_eligibility');
    }
};