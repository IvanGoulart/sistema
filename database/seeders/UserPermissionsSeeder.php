<?php


namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserPermissionsSeeder extends Seeder
{
  public function run(): void
  {
    $tenantId = Tenant::query()->firstOrFail()->id;

    $adminPermission = DB::table('permissions')
      ->where('name', 'admin')
      ->value('id');

    $clientPermission = DB::table('permissions')
      ->where('name', 'client')
      ->value('id');

    $users = DB::table('users')->pluck('id');

    foreach ($users as $userId) {
      DB::table('user_permissions')->updateOrInsert(
        [
          'tenant_id' => $tenantId,
          'user_id' => $userId,
        ],
        [
          'code_permission' => $userId == 1 ? $adminPermission : $clientPermission,
          'created_at' => now(),
          'updated_at' => now(),
        ]
      );
    }
  }
}
