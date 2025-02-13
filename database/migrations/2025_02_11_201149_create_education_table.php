<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('education', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_info_id')->constrained('personal_info')->onDelete('cascade');
            $table->string('level');
            $table->string('school');
            $table->string('degree')->nullable();
            $table->date('from')->nullable();
            $table->date('to')->nullable();
            $table->string('highest_level')->nullable();
            $table->integer('year_graduated')->nullable(); // Changed to integer
            $table->string('honors')->nullable();
            $table->timestamps();
        });
        
    }

    public function down() {
        Schema::dropIfExists('education');
    }
};
