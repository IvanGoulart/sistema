<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    // Matriz de credenciais para testar cada permissão e o isolamento entre
    // empresas no navegador. TestMatrixSeeder é auto-contido (cria empresas,
    // usuários, papéis, serviços e disponibilidade). Os seeders legados
    // (UsersTableSeeder, UserPermissionsSeeder, ServiceSeeder, UserServiceSeeder,
    // AvailableEmployeeScheduleSeeder) assumiam uma única empresa e ficaram fora
    // da cadeia de dev — continuam no repo para referência.
    $this->call([
      PermissionsTableSeeder::class,
      TestMatrixSeeder::class,
    ]);
  }
}
