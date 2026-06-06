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

        $user = User::firstOrCreate(
            ['email' => 'admin@salaofacil.digital'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('admin@2026'),
                'active'   => true,
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

        $this->command->info('Admin criado: admin@salaofacil.digital / admin@2026');
    }
}
