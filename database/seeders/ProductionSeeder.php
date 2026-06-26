<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $admin     = Permission::firstOrCreate(['name' => 'admin']);
        Permission::firstOrCreate(['name' => 'employee']);
        Permission::firstOrCreate(['name' => 'client']);

        $tenant = Tenant::firstOrCreate(
            ['slug' => 'salao-facil'],
            [
                'name'      => 'Salao Facil',
                'email'     => 'admin@salaofacil.digital',
                'is_active' => true,
            ]
        );

        // Idempotente: reaplica senha/active/super-admin mesmo se o usuário já
        // existir (firstOrCreate antigo não atualizava nada num 2º deploy).
        // A senha vem de PROD_ADMIN_PASSWORD para não ficar fixa no código nem
        // ser sobrescrita sem querer — fallback admin@2026 no primeiro deploy.
        $password = env('PROD_ADMIN_PASSWORD', 'admin@2026');

        $user = User::updateOrCreate(
            ['email' => 'admin@salaofacil.digital'],
            [
                'name'           => 'Admin',
                'password'       => Hash::make($password),
                'active'         => true,
                'is_super_admin' => true, // dono da plataforma (papel GLOBAL)
            ]
        );

        DB::table('user_permissions')->insertOrIgnore([
            'tenant_id'       => $tenant->id,
            'user_id'         => $user->id,
            'code_permission' => $admin->id,
        ]);

        $tenant->users()->syncWithoutDetaching([
            $user->id => ['role' => 'admin', 'is_active' => true],
        ]);

        $this->command->info('Super-admin garantido: admin@salaofacil.digital (senha via PROD_ADMIN_PASSWORD; fallback admin@2026).');
    }
}
