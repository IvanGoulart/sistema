<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Back-fill de papéis: profissionais existentes (usuários vinculados a serviços
 * em user_services) passam a ter a permissão canônica `employee`.
 *
 * Antes desta mudança nenhum usuário recebia `employee`, então as listagens de
 * profissionais caíam para "todos do tenant". Papéis são exclusivos: uma única
 * linha por usuário em user_permissions.
 */
return new class extends Migration
{
    public function up(): void
    {
        $employeeId = DB::table('permissions')->where('name', 'employee')->value('id');

        if (! $employeeId) {
            return;
        }

        // Profissionais = usuários com ao menos um serviço vinculado.
        $links = DB::table('user_services')
            ->select('user_id', DB::raw('MIN(tenant_id) as tenant_id'))
            ->groupBy('user_id')
            ->get();

        foreach ($links as $link) {
            DB::table('user_permissions')->updateOrInsert(
                ['user_id' => $link->user_id],
                [
                    'tenant_id' => $link->tenant_id ?? 1,
                    'code_permission' => $employeeId,
                ]
            );
        }
    }

    public function down(): void
    {
        // Back-fill de dados — não revertido para não apagar papéis legítimos.
    }
};
