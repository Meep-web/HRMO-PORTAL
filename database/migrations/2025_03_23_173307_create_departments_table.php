<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('departments', function (Blueprint $table) {
            $table->id(); // Auto-increment Primary Key
            $table->string('department_name')->unique();
        });
    }

    public function down() {
        Schema::dropIfExists('departments');
    }
};
