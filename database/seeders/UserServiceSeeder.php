<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserServiceSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = Tenant::query()->firstOrFail()->id;

        // Apenas profissionais (employee) atendem serviços.
        $users = DB::table('users')
            ->join('user_permissions', 'user_permissions.user_id', '=', 'users.id')
            ->join('permissions', 'permissions.id', '=', 'user_permissions.code_permission')
            ->where('user_permissions.tenant_id', $tenantId)
            ->where('permissions.name', 'employee')
            ->pluck('users.id');

        $services = DB::table('services')
            ->where('tenant_id', $tenantId)
            ->pluck('id');

        $rows = [];

        foreach ($users as $userId) {
            foreach ($services as $serviceId) {
                $rows[] = [
                    'tenant_id' => $tenantId,
                    'user_id' => $userId,
                    'service_id' => $serviceId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('user_services')->upsert(
            $rows,
            ['tenant_id', 'user_id', 'service_id'],
            ['updated_at']
        );
    }
}
