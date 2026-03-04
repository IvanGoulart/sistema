<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserPermissionsSeeder extends Seeder
{
  public function run(): void
  {
    $adminPermission = DB::table('permissions')
      ->where('name', 'admin')
      ->value('id');

    $clientPermission = DB::table('permissions')
      ->where('name', 'client')
      ->value('id');

    $users = DB::table('users')->pluck('id');

    foreach ($users as $userId) {
      DB::table('user_permissions')->insert([
        'user_id' => $userId,
        'code_permission' => $clientPermission,
        'created_at' => now(),
        'updated_at' => now()
      ]);
    }

    // exemplo: primeiro usuário vira admin
    DB::table('user_permissions')
      ->where('user_id', 1)
      ->update([
        'code_permission' => $adminPermission
      ]);
  }
}
