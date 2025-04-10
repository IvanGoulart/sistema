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
  public function run()
  {
    DB::table('services')->insert([
      ['name' => 'Haircut', 'description' => 'Professional haircut service'],
      ['name' => 'Hair Coloring', 'description' => 'Hair coloring and highlights'],
      ['name' => 'Manicure', 'description' => 'Manicure and nail care'],
      ['name' => 'Pedicure', 'description' => 'Pedicure and foot care'],
      ['name' => 'Facial', 'description' => 'Facial treatments and skincare'],
    ]);
  }
}
