<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('schedules', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('employee_id');
      $table->unsignedBigInteger('client_id');
      $table->unsignedBigInteger('service_id');
      $table->date('day');
      $table->time('hour');
      $table->boolean('cancel')->default(false);
      $table->timestamps();

      // Foreign key constraints
      $table
        ->foreign('employee_id')
        ->references('id')
        ->on('users')
        ->onDelete('cascade');
      $table
        ->foreign('client_id')
        ->references('id')
        ->on('users')
        ->onDelete('cascade');
      $table
        ->foreign('service_id')
        ->references('id')
        ->on('services')
        ->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('schedules');
  }
}
