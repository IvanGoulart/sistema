<?php

namespace App\Repositories;

use App\Interfaces\PermissionRepositoryInterface;
use App\Models\Permission;
use App\Models\UserPermission;

class PermissionRepository implements PermissionRepositoryInterface
{
    public function getAllPermissions()
    {
        $permissions = Permission::get();

        return $permissions;
    }

    public function updatePermission($userId, int $permissionCode)
    {
        // Papéis exclusivos: updateOrCreate por user_id garante uma única linha de permissão.
        UserPermission::updateOrCreate(
            [
                'user_id' => $userId,
            ],
            [
                'tenant_id' => session('tenant_id') ?? 1,
                'code_permission' => $permissionCode,
            ]
        );
    }

    public function createPermission(int $userId, int $permissionCode): bool
    {
        // Idempotente por user_id para manter papéis exclusivos (admin|employee|client).
        return (bool) UserPermission::updateOrCreate(
            [
                'user_id' => $userId,
            ],
            [
                'tenant_id' => session('tenant_id') ?? 1,
                'code_permission' => $permissionCode,
            ]
        );
    }
}
