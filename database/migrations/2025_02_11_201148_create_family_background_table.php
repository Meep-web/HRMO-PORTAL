<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('family_background', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_info_id')->constrained('personal_info')->onDelete('cascade');
            $table->string('spouse_surname')->nullable();
            $table->string('spouse_first_name')->nullable();
            $table->string('spouse_middle_name')->nullable();
            $table->string('spouse_name_extension')->nullable();
            $table->string('spouse_occupation')->nullable();
            $table->string('spouse_employer')->nullable();
            $table->string('spouse_telephone')->nullable();
            $table->string('father_surname');
            $table->string('father_first_name');
            $table->string('father_middle_name')->nullable();
            $table->string('father_name_extension')->nullable();
            $table->string('mother_surname');
            $table->string('mother_first_name');
            $table->string('mother_middle_name')->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('family_background');
    }
};
