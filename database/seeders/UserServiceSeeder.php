<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserServiceSeeder extends Seeder
{
  public function run(): void
  {
    $userIds = DB::table('users')->pluck('id')->all();
    $serviceIds = DB::table('services')->pluck('id')->all();

    // Se não existir usuário/serviço, não tem como popular a pivot
    if (count($userIds) === 0 || count($serviceIds) === 0) {
      $this->command?->warn('Seed user_services ignorado: faltam registros em users ou services.');
      return;
    }

    // Exemplo: vincula cada usuário a 1-2 serviços aleatórios
    $rows = [];
    foreach ($userIds as $userId) {
      $pick = array_slice($serviceIds, 0, min(2, count($serviceIds)));
      foreach ($pick as $serviceId) {
        $rows[] = [
          'user_id' => $userId,
          'service_id' => $serviceId,
          'created_at' => now(),
          'updated_at' => now(),
        ];
      }
    }

    DB::table('user_services')->insert($rows);
  }
}
