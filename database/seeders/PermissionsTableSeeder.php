<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsTableSeeder extends Seeder
{
  public function run(): void
  {
    $permissions = [
      ['name' => 'admin'],
      ['name' => 'employee'],
      ['name' => 'client'],
    ];

    foreach ($permissions as $permission) {
      DB::table('permissions')->updateOrInsert(
        ['name' => $permission['name']],
        [
          'created_at' => now(),
          'updated_at' => now()
        ]
      );
    }
  }
}
