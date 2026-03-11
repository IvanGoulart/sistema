<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;

class ServiceSeeder extends Seeder
{
  public function run(): void
  {
    $tenant = Tenant::first();

    DB::table('services')->insert([
      [
        'tenant_id' => $tenant->id,
        'name' => 'Serviço A',
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'tenant_id' => $tenant->id,
        'name' => 'Serviço B',
        'created_at' => now(),
        'updated_at' => now()
      ],
    ]);
  }
}
