<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
  public function run(): void
  {
    DB::table('users')->insert([
      [
        'name' => 'Admin',
        'email' => 'admin@sistema.test',
        'password' => Hash::make('password'),
        'created_at' => now(),
        'updated_at' => now(),
      ],
      [
        'name' => 'User 1',
        'email' => 'user1@sistema.test',
        'password' => Hash::make('password'),
        'created_at' => now(),
        'updated_at' => now(),
      ],
    ]);
  }
}
