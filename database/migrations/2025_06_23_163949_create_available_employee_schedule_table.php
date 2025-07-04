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
        Schema::create('available_employee_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('date');              // Specific date of availability
            $table->time('start_time');        // Ex: 08:00
            $table->time('end_time');          // Ex: 12:00
            $table->timestamps();

            $table->foreign('employee_id')
                  ->references('id')
                  ->on('users') // Assuming 'users' is the table for employees
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('available_schedules');
    }
};
