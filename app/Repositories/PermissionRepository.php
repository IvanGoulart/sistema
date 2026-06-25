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
        // Papel por empresa: uma linha por (tenant, usuário).
        UserPermission::updateOrCreate(
            [
                'tenant_id' => session('tenant_id') ?? 1,
                'user_id' => $userId,
            ],
            [
                'code_permission' => $permissionCode,
            ]
        );
    }

    public function createPermission(int $userId, int $permissionCode): bool
    {
        // Idempotente por (tenant, usuário): um papel do usuário dentro da empresa ativa.
        return (bool) UserPermission::updateOrCreate(
            [
                'tenant_id' => session('tenant_id') ?? 1,
                'user_id' => $userId,
            ],
            [
                'code_permission' => $permissionCode,
            ]
        );
    }
}
