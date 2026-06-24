<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = Tenant::query()->firstOrFail()->id;

        $permissionIds = DB::table('permissions')->pluck('id', 'name');

        // Papel canônico por e-mail (papéis exclusivos: 1 linha por usuário).
        $rolesByEmail = [
            'admin@sistema.test' => 'admin',
            'profissional@sistema.test' => 'employee',
        ];

        $users = DB::table('users')->select('id', 'email')->get();

        foreach ($users as $user) {
            $roleName = $rolesByEmail[$user->email] ?? 'client';

            DB::table('user_permissions')->updateOrInsert(
                [
                    'user_id' => $user->id,
                ],
                [
                    'tenant_id' => $tenantId,
                    'code_permission' => $permissionIds[$roleName],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
