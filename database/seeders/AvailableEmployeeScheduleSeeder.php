<?php


namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AvailableEmployeeScheduleSeeder extends Seeder
{
  public function run(): void
  {
    $tenantId = Tenant::query()->firstOrFail()->id;

    DB::table('available_employee_schedules')->insert([
      [
        'tenant_id' => $tenantId,
        'employee_id' => 1,
        'date' => '2026-03-10',
        'start_time' => '08:00:00',
        'end_time' => '12:00:00',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'tenant_id' => $tenantId,
        'employee_id' => 1,
        'date' => '2026-03-11',
        'start_time' => '13:00:00',
        'end_time' => '17:00:00',
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'tenant_id' => $tenantId,
        'employee_id' => 2,
        'date' => '2026-03-10',
        'start_time' => '08:00:00',
        'end_time' => '17:00:00',
        'created_at' => now(),
        'updated_at' => now(),
      ],
    ]);
  }
}
