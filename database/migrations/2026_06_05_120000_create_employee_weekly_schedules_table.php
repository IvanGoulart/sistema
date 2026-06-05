<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_weekly_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('users')->cascadeOnDelete();
            $table->tinyInteger('day_of_week'); // 0=Dom 1=Seg 2=Ter 3=Qua 4=Qui 5=Sex 6=Sáb
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();

            $table->unique(['tenant_id', 'employee_id', 'day_of_week'], 'emp_weekly_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_weekly_schedules');
    }
};
