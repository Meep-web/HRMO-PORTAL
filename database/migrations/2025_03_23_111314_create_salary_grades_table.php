<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('salaryGrade', function (Blueprint $table) {
            $table->integer('grade')->primary(); // Salary Grade as Primary Key
            $table->decimal('step1', 10, 2)->nullable();
            $table->decimal('step2', 10, 2)->nullable();
            $table->decimal('step3', 10, 2)->nullable();
            $table->decimal('step4', 10, 2)->nullable();
            $table->decimal('step5', 10, 2)->nullable();
            $table->decimal('step6', 10, 2)->nullable();
            $table->decimal('step7', 10, 2)->nullable();
            $table->decimal('step8', 10, 2)->nullable();
            
        });
        
    }

    public function down(): void
    {
        Schema::dropIfExists('salaryGrade');
    }
};
