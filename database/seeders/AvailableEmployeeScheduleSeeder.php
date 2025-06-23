<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AvailableEmployeeScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('available_employee_schedules')->insert([
            [
                'employee_id' => 1,
                'date' => Carbon::now()->addDay()->toDateString(), // Amanhã
                'start_time' => '08:00:00',
                'end_time' => '12:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employee_id' => 1,
                'date' => Carbon::now()->addDays(2)->toDateString(),
                'start_time' => '13:00:00',
                'end_time' => '17:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employee_id' => 2,
                'date' => Carbon::now()->addDay()->toDateString(),
                'start_time' => '09:00:00',
                'end_time' => '11:30:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
