<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScheduleSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table('schedules')->insert([
      [
        'employee_id' => 1,
        'client_id' => 31,
        'service_id' => 1,
        'day' => '2025-04-10',
        'hour' => '11:38:00',
        'cancel' => false,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'employee_id' => 2,
        'client_id' => 32,
        'service_id' => 2,
        'day' => '2025-04-11',
        'hour' => '14:00:00',
        'cancel' => false,
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'employee_id' => 3,
        'client_id' => 33,
        'service_id' => 3,
        'day' => '2025-04-12',
        'hour' => '09:30:00',
        'cancel' => false,
        'created_at' => now(),
        'updated_at' => now(),
      ],
    ]);
  }
}
