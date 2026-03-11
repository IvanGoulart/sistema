<?php


namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserServiceSeeder extends Seeder
{
  public function run(): void
  {
    $tenantId = Tenant::query()->firstOrFail()->id;

    $users = DB::table('users')->pluck('id');
    $services = DB::table('services')
      ->where('tenant_id', $tenantId)
      ->pluck('id');

    $rows = [];

    foreach ($users as $userId) {
      foreach ($services as $serviceId) {
        $rows[] = [
          'tenant_id' => $tenantId,
          'user_id' => $userId,
          'service_id' => $serviceId,
          'created_at' => now(),
          'updated_at' => now(),
        ];
      }
    }

    DB::table('user_services')->upsert(
      $rows,
      ['tenant_id', 'user_id', 'service_id'],
      ['updated_at']
    );
  }
}
