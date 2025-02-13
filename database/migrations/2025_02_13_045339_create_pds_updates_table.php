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
        Schema::create('pdsUpdates', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key (id)
            $table->string('employeeName'); // employeeID column
            $table->unsignedBigInteger('PDSId'); // PDSId column
            $table->timestamps(); // Optional: Adds created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pds_updates');
    }
};
