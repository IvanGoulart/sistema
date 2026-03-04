<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run(): void
  {
    DB::table('services')->insert([
      ['name' => 'Serviço A', 'created_at' => now(), 'updated_at' => now()],
      ['name' => 'Serviço B', 'created_at' => now(), 'updated_at' => now()],
    ]);
  }
}
