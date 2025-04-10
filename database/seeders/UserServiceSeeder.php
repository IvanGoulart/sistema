<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserServiceSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table('user_services')->insert([
      ['user_id' => 1, 'service_id' => 1],
      ['user_id' => 1, 'service_id' => 2],
      ['user_id' => 2, 'service_id' => 3],
      ['user_id' => 2, 'service_id' => 4],
      ['user_id' => 3, 'service_id' => 5],
      ['user_id' => 32, 'service_id' => 5],
      ['user_id' => 33, 'service_id' => 5],
      ['user_id' => 34, 'service_id' => 5],
      ['user_id' => 35, 'service_id' => 5],
      ['user_id' => 36, 'service_id' => 5],
    ]);
  }
}
