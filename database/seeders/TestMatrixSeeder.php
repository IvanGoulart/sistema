<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Matriz de credenciais para testar CADA permissão no navegador, incluindo o
 * isolamento entre empresas (admin de uma empresa não vê dados de outra).
 *
 * Auto-contido e idempotente (firstOrCreate/updateOrInsert): pode rodar sozinho
 * sem depender dos seeders legados (que assumem uma única empresa).
 *
 * Todas as senhas: "password". Veja a tabela de credenciais no CLAUDE.md.
 */
class TestMatrixSeeder extends Seeder
{
    public function run(): void
    {
        // Papéis canônicos por empresa (PermissionsTableSeeder já os criou).
        $permissionIds = DB::table('permissions')->pluck('id', 'name');

        // ── Empresas ────────────────────────────────────────────────────────
        // A já existe via migration de backfill (slug empresa-padrao).
        $empresaA = Tenant::firstOrCreate(
            ['slug' => 'empresa-padrao'],
            ['name' => 'Empresa Padrão', 'is_active' => true],
        );

        $empresaB = Tenant::firstOrCreate(
            ['slug' => 'salao-modelo'],
            ['name' => 'Salão Modelo', 'is_active' => true],
        );

        // ── Usuários (e-mail, nome, papel, empresa, super-admin, role no pivot)
        $matrix = [
            ['admin@sistema.test',        'Admin (Plataforma)',  'admin',    $empresaA, true,  'owner'],
            ['gerente@sistema.test',      'Gerente Salão A',     'admin',    $empresaA, false, 'admin'],
            ['profissional@sistema.test', 'Profissional A',      'employee', $empresaA, false, 'employee'],
            ['user1@sistema.test',        'Cliente A',           'client',   $empresaA, false, 'client'],
            ['admin-b@sistema.test',      'Admin Salão B',       'admin',    $empresaB, false, 'admin'],
            ['cliente-b@sistema.test',    'Cliente B',           'client',   $empresaB, false, 'client'],
        ];

        foreach ($matrix as [$email, $name, $role, $tenant, $isSuper, $pivotRole]) {
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'     => $name,
                    'password' => Hash::make('password'),
                    'active'   => 1,
                ],
            );

            // Super-admin é flag GLOBAL, fora do sistema de permissões por empresa.
            $user->forceFill(['is_super_admin' => $isSuper])->save();

            // Papel por empresa (1 linha por user+tenant).
            DB::table('user_permissions')->updateOrInsert(
                ['user_id' => $user->id, 'tenant_id' => $tenant->id],
                [
                    'code_permission' => $permissionIds[$role],
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ],
            );

            // Vínculo na pivot tenant_user.
            $tenant->users()->syncWithoutDetaching([
                $user->id => ['role' => $pivotRole, 'is_active' => true],
            ]);
        }

        // ── Dados de negócio por empresa (deixa o isolamento visível) ────────
        $this->seedBusinessData($empresaA, ['Corte A1', 'Corte A2']);
        $this->seedBusinessData($empresaB, ['Corte B1', 'Corte B2']);

        $this->command->info('Matriz de teste criada. Senha de todos: "password".');
    }

    /**
     * Cria serviços da empresa, vincula o(s) profissional(is) da empresa a eles
     * e gera disponibilidade — sempre buscando o id real do employee (sem hardcode).
     */
    private function seedBusinessData(Tenant $tenant, array $serviceNames): void
    {
        foreach ($serviceNames as $serviceName) {
            DB::table('services')->updateOrInsert(
                ['tenant_id' => $tenant->id, 'name' => $serviceName],
                ['created_at' => now(), 'updated_at' => now()],
            );
        }

        $serviceIds = DB::table('services')->where('tenant_id', $tenant->id)->pluck('id');

        // Apenas profissionais (employee) desta empresa atendem serviços.
        $employeeIds = DB::table('users')
            ->join('user_permissions', 'user_permissions.user_id', '=', 'users.id')
            ->join('permissions', 'permissions.id', '=', 'user_permissions.code_permission')
            ->where('user_permissions.tenant_id', $tenant->id)
            ->where('permissions.name', 'employee')
            ->pluck('users.id');

        $links = [];
        foreach ($employeeIds as $employeeId) {
            foreach ($serviceIds as $serviceId) {
                $links[] = [
                    'tenant_id'  => $tenant->id,
                    'user_id'    => $employeeId,
                    'service_id' => $serviceId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if ($links) {
            DB::table('user_services')->upsert(
                $links,
                ['tenant_id', 'user_id', 'service_id'],
                ['updated_at'],
            );
        }

        // Disponibilidade de exemplo para cada profissional da empresa.
        foreach ($employeeIds as $employeeId) {
            foreach ([['2026-06-29', '08:00:00', '12:00:00'], ['2026-06-30', '13:00:00', '17:00:00']] as [$date, $start, $end]) {
                DB::table('available_employee_schedules')->updateOrInsert(
                    [
                        'tenant_id'   => $tenant->id,
                        'employee_id' => $employeeId,
                        'date'        => $date,
                    ],
                    [
                        'start_time' => $start,
                        'end_time'   => $end,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                );
            }
        }
    }
}
